<?php

namespace App\Http\Controllers\Frontend;

use App\BlockchainTransaction;
use App\Cause;
use App\CauseLogs;
use App\FraudReport;
use App\Helpers\DonationHelpers;
use App\Helpers\FraudEngine;
use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use App\Services\DemoBlockchainService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlockchainPaymentController extends Controller
{
    public function showDonationForm($id)
    {
        $donation = Cause::where('id', $id)->where('status', 'publish')->first();

        if (empty($donation)) {
            return redirect()->back()->with(['msg' => __('Campaign not found'), 'type' => 'danger']);
        }

        $demoWallet = DemoBlockchainService::generateWalletAddress();
        $userWallet = auth()->check() ? auth()->user()->wallet_address : null;

        return view('frontend.donations.blockchain-donate', [
            'donation' => $donation,
            'demoWallet' => $demoWallet,
            'userWallet' => $userWallet,
        ]);
    }

    public function processDonation(Request $request)
    {
        $isAnonymous = $request->boolean('anonymous');

        $this->validate($request, [
            'campaign_id' => 'required|exists:causes,id',
            'amount' => 'required|numeric|min:0.001',
            'donor_name' => $isAnonymous ? 'nullable|string|max:191' : 'required|string|max:191',
            'donor_email' => $isAnonymous ? 'nullable|email|max:191' : 'required|email|max:191',
            'wallet_address' => ['required','string','regex:/^0x[a-fA-F0-9]{40}$/'],
        ], [
            'wallet_address.required' => __('Please connect your MetaMask wallet first'),
            'wallet_address.regex' => __('Invalid wallet address format'),
        ]);

        $campaign = Cause::findOrFail($request->campaign_id);

        if ($campaign->status !== 'pending' && $campaign->status !== 'publish') {
            return back()->with(['msg' => __('This campaign is not accepting donations'), 'type' => 'danger']);
        }

        /* ---- Payment integrity guard #1: wallet address validation (EIP-55 style) ---- */
        $addrCheck = \App\Helpers\PaymentGuard::validateAddress($request->wallet_address);
        if (!$addrCheck['ok']) {
            return back()->with(['msg' => __($addrCheck['reason']), 'type' => 'danger']);
        }

        /* ---- Payment integrity guard #2: donation velocity limit per donor wallet ---- */
        $donorWallet = strtolower($request->wallet_address);
        if (!\App\Helpers\PaymentGuard::walletVelocityOk($donorWallet)) {
            return back()->with(['msg' => __('Too many donations from this wallet in the last hour. Please try again later.'), 'type' => 'danger']);
        }

        $admin_charge = DonationHelpers::get_donation_charge_for_campaign_owner($request->amount);
        $total_amount = DonationHelpers::get_donation_total($request->amount, false);

        /* Fiat (USD) -> ETH conversion for the on-chain demo transaction */
        $ethAmount = round((float) $total_amount / 3450, 6);
        if ($ethAmount < 0.000001) {
            $ethAmount = 0.000001;
        }

        /* ---- Payment integrity guard #3: amount-mismatch tripwire ---- */
        $amountMismatch = \App\Helpers\PaymentGuard::amountMismatch(
            $request->input('confirmed_amount_eth') !== null ? (float) $request->input('confirmed_amount_eth') : null,
            $ethAmount
        );

        // Donation-level fraud checks (self-donation, velocity, patterns)
        FraudEngine::checkDonation($campaign, $donorWallet, $request->amount);

        // Anonymous donors get masked identity; signed-in users keep their account
        $donorName = $isAnonymous ? 'Anonymous Donor' : ($request->donor_name ?: 'Anonymous Donor');
        $donorEmail = $isAnonymous
            ? 'anon.' . Str::random(8) . '@medifund.demo'
            : $request->donor_email;

        // Create donation log linked to donor wallet + campaign
        $causeLog = CauseLogs::create([
            'email' => $donorEmail,
            'name' => $donorName,
            'cause_id' => $request->campaign_id,
            'amount' => $total_amount,
            'admin_charge' => $admin_charge,
            'payment_gateway' => 'blockchain_demo',
            'status' => 'complete',
            'track' => Str::random(10) . Str::random(10),
            'user_id' => (!$isAnonymous && auth()->check()) ? auth()->user()->id : null,
            'anonymous' => $isAnonymous ? 1 : 0,
            'payment_type' => 'crypto',
            'donation_status' => $amountMismatch ? 'mismatch_flagged' : 'confirmed',
            'donor_wallet_address' => $donorWallet,
        ]);

        // Process blockchain transaction (amount recorded in ETH)
        $blockchainTx = DemoBlockchainService::processDonation(
            $causeLog,
            $request->wallet_address,
            $ethAmount
        );

        // Ping the admin bell so every payment is instantly visible
        \App\Notification::create([
            'cause_log_id' => $causeLog->id,
            'title' => ($amountMismatch ? '? AMOUNT MISMATCH - ' : '') . 'New blockchain donation: ' . amount_with_currency_symbol($total_amount) . ' to "' . $campaign->title . '"',
            'type' => 'cause_log',
        ]);

        /* Raise the campaign total so progress bars match on-chain raisedAmount.
           Skipped while a donation is mismatch-flagged and held for review. */
        if (!$amountMismatch) {
            $campaign->increment('raised', $total_amount);
        }

        /* ---- Guard #3 aftermath: freeze + report on mismatch ---- */
        if ($amountMismatch) {
            FraudReport::create([
                'campaign_id' => $campaign->id,
                'fraud_score' => 55,
                'risk_level' => 'high',
                'check_results' => [
                    'type' => 'payment_integrity',
                    'flags' => ['amount_mismatch'],
                    'confirmed_eth' => $request->input('confirmed_amount_eth'),
                    'expected_eth' => $ethAmount,
                ],
                'evidence' => [
                    'amount_mismatch' => [
                        'pass' => false,
                        'points' => 0,
                        'detail' => "MetaMask confirmed {$request->input('confirmed_amount_eth')} ETH but server computed {$ethAmount} ETH for this donation — escrow row held pending review.",
                    ],
                ],
                'recommendation' => 'FLAG_FOR_REVIEW',
                'status' => 'flagged',
            ]);

            /* freeze the freshly created escrow row so milestone releases skip it */
            \Illuminate\Support\Facades\DB::table('escrow')
                ->where('cause_log_id', $causeLog->id)
                ->update(['disputed' => 1, 'updated_at' => now()]);
        }

        // Email the donor their blockchain receipt (log driver in demo)
        if (!$isAnonymous && filter_var($donorEmail, FILTER_VALIDATE_EMAIL)) {
            try {
                \Mail::html(view('emails.donation-confirmation', [
                    'name' => $donorName,
                    'amount' => amount_with_currency_symbol($total_amount),
                    'ethAmount' => $ethAmount,
                    'campaignTitle' => $campaign->title,
                    'txHash' => $blockchainTx->transaction_hash,
                    'receiptUrl' => route('donation.receipt', $causeLog->track),
                    'txUrl' => route('blockchain.transaction.show', $blockchainTx->transaction_hash),
                ])->render(), function ($message) use ($donorEmail, $campaign) {
                    $message->to($donorEmail)
                        ->subject(__('Thank you for your donation to "') . $campaign->title . '"');
                });
            } catch (\Throwable $e) {
                \Log::warning('Donation email failed: ' . $e->getMessage());
            }
        }

        return redirect()->route('blockchain.transaction.success', [
            'hash' => $blockchainTx->transaction_hash,
        ]);
    }

    public function transactionSuccess($hash)
    {
        $blockchainTx = BlockchainTransaction::where('transaction_hash', $hash)->first();

        if (empty($blockchainTx)) {
            return redirect()->route('homepage');
        }

        $causeLog = CauseLogs::find($blockchainTx->cause_log_id);
        $campaign = Cause::find($blockchainTx->campaign_id);

        return view('frontend.donations.blockchain-success', [
            'transaction' => $blockchainTx,
            'causeLog' => $causeLog,
            'campaign' => $campaign,
        ]);
    }

    public function showTransaction($hash)
    {
        $blockchainTx = BlockchainTransaction::where('transaction_hash', $hash)->first();

        if (empty($blockchainTx)) {
            return redirect()->route('homepage')->with(['msg' => __('Transaction not found'), 'type' => 'danger']);
        }

        $causeLog = CauseLogs::find($blockchainTx->cause_log_id);
        $campaign = Cause::find($blockchainTx->campaign_id);

        return view('frontend.donations.blockchain-transaction', [
            'transaction' => $blockchainTx,
            'causeLog' => $causeLog,
            'campaign' => $campaign,
        ]);
    }

    public function verifyTransaction($hash)
    {
        $result = DemoBlockchainService::verifyTransaction($hash);
        return response()->json($result);
    }

    /* ---------- PUBLIC BLOCKCHAIN EXPLORER ---------- */
    public function explorer(Request $request)
    {
        $q = trim($request->get('q', ''));

        $query = BlockchainTransaction::with('campaign')->orderByDesc('id');

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('transaction_hash', 'like', "%{$q}%")
                    ->orWhere('wallet_address', 'like', "%{$q}%")
                    ->orWhere('block_number', 'like', "%{$q}%");
            });
        }

        $transactions = $query->paginate(15)->withQueryString();

        $stats = [
            'total_tx'      => BlockchainTransaction::count(),
            'total_eth'     => (float) BlockchainTransaction::where('status', 'confirmed')->sum('amount'),
            'unique_donors' => BlockchainTransaction::distinct('wallet_address')->count('wallet_address'),
            'campaigns'     => BlockchainTransaction::distinct('campaign_id')->count('campaign_id'),
            'latest_block'  => (int) BlockchainTransaction::max('block_number'),
        ];

        return view('frontend.donations.blockchain-explorer', [
            'transactions' => $transactions,
            'stats'        => $stats,
            'q'            => $q,
        ]);
    }

    /* ---------- PRINTABLE DONATION RECEIPT ---------- */
    public function receipt($track)
    {
        $donation = CauseLogs::where('track', $track)
            ->where('status', 'complete')
            ->firstOrFail();

        $campaign = Cause::find($donation->cause_id);

        return view('frontend.donations.receipt', [
            'donation' => $donation,
            'campaign' => $campaign,
        ]);
    }
}

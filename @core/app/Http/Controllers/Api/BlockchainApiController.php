<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DemoBlockchainService;
use App\Helpers\FraudEngine;
use App\BlockchainTransaction;
use App\Cause;
use App\CauseLogs;
use App\Escrow;
use App\FraudReport;
use Illuminate\Http\Request;

class BlockchainApiController extends Controller
{
    /**
     * Connect wallet - store wallet address in session
     */
    public function connectWallet(Request $request)
    {
        $request->validate([
            'wallet_address' => 'required|string|size:42',
        ]);

        $walletAddress = strtolower($request->input('wallet_address'));
        $user = $request->user('web') ?? $request->user('admin');

        if ($user instanceof \App\User) {
            /* changing the payout wallet resets admin verification (mirrors UserDashboardController@connect_wallet) */
            $resetVerification = strtolower((string) $user->wallet_address) !== $walletAddress;

            $user->update([
                'wallet_address' => $walletAddress,
                'wallet_connected_at' => now(),
                'wallet_verified' => $resetVerification ? false : $user->wallet_verified,
                'wallet_verified_at' => $resetVerification ? null : $user->wallet_verified_at,
                'wallet_verified_by' => $resetVerification ? null : $user->wallet_verified_by,
            ]);

            \App\Helpers\AuditLogger::record('patient_wallet_connect_api', 'User', $user->id, [
                'wallet' => $walletAddress,
            ]);
        }

        if ($user) {
            session(['wallet_address' => $walletAddress]);
            session(['wallet_connected' => true]);
        }

        return response()->json([
            'success' => true,
            'wallet_address' => $walletAddress,
            'network' => DemoBlockchainService::NETWORK,
            'balance' => number_format((float) ($user->demo_eth_balance ?? 0), 4) . ' ETH',
            'role' => $user->role ?? null,
            'wallet_verified' => (bool) ($user->wallet_verified ?? false),
            'message' => 'Account connected successfully',
        ]);
    }

    /**
     * Disconnect wallet
     */
    public function disconnectWallet(Request $request)
    {
        session()->forget(['wallet_address', 'wallet_connected']);

        return response()->json(['success' => true]);
    }

    /**
     * Get wallet status
     */
    public function walletStatus(Request $request)
    {
        $connected = session('wallet_connected', false);
        $address = session('wallet_address', null);

        return response()->json([
            'connected' => $connected,
            'wallet_address' => $address,
            'network' => DemoBlockchainService::NETWORK,
        ]);
    }

    /**
     * Process blockchain donation (demo mode)
     */
    public function processDonation(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required|exists:causes,id',
            'amount' => 'required|numeric|min:1',
            'wallet_address' => 'required|string|size:42',
        ]);

        $campaign = Cause::findOrFail($request->campaign_id);
        $donorWallet = strtolower($request->wallet_address);

        /* spend from the logged-in donor's funded account */
        $donorUser = auth()->guard('web')->user();
        if ($donorUser && strtolower((string) $donorUser->wallet_address) === $donorWallet) {
            $ethCost = medifund_usd_to_eth($request->amount);
            if ((float) $donorUser->demo_eth_balance < $ethCost) {
                return response()->json([
                    'success' => false,
                    'message' => __('Insufficient funds in your account. Faucet balance: :bal ETH.', ['bal' => number_format((float) $donorUser->demo_eth_balance, 4)]),
                ], 422);
            }
            $donorUser->decrement('demo_eth_balance', $ethCost);
        }

        // Donation-level fraud checks (self-donation, velocity, patterns)
        $donationFraud = FraudEngine::checkDonation($campaign, $donorWallet, $request->amount);

        // Create donation log
        $causeLog = CauseLogs::create([
            'cause_id' => $campaign->id,
            'user_id' => auth()->guard('web')->id(),
            'amount' => $request->amount,
            'payment_gateway' => 'blockchain_demo',
            'payment_type' => 'crypto',
            'status' => 'complete',
            'donor_wallet_address' => $donorWallet,
            'donation_status' => 'confirmed',
            'transaction_id' => DemoBlockchainService::generateTransactionHash(),
        ]);

        // Process through blockchain service
        $blockchainTx = DemoBlockchainService::processDonation(
            $causeLog,
            $request->wallet_address,
            $request->amount
        );

        // Run fraud engine
        $fraudResult = FraudEngine::analyzeCampaign($campaign);

        return response()->json([
            'success' => true,
            'transaction' => [
                'hash' => $blockchainTx->transaction_hash,
                'short_hash' => $blockchainTx->short_hash,
                'block_number' => $blockchainTx->block_number,
                'amount' => $blockchainTx->formatted_amount,
                'currency' => $blockchainTx->currency,
                'network' => $blockchainTx->network,
                'gas_fee' => $blockchainTx->gas_fee,
                'status' => $blockchainTx->status,
                'explorer_url' => DemoBlockchainService::getExplorerUrl($blockchainTx->transaction_hash),
            ],
            'escrow' => [
                'status' => 'held',
                'message' => 'Funds held in escrow until milestone verification',
            ],
            'fraud' => [
                'score' => $fraudResult['score'],
                'risk_level' => $fraudResult['risk_level'],
                'recommendation' => $fraudResult['recommendation'],
                'donation_flagged' => $donationFraud['flagged'],
                'donation_flags' => $donationFraud['flags'],
            ],
            'campaign' => [
                'id' => $campaign->id,
                'title' => $campaign->title,
                'raised' => $campaign->fresh()->raised,
                'amount' => $campaign->amount,
            ],
        ]);
    }

    /**
     * Verify transaction
     */
    public function verifyTransaction(Request $request)
    {
        $request->validate([
            'hash' => 'required|string',
        ]);

        $result = DemoBlockchainService::verifyTransaction($request->hash);

        return response()->json($result);
    }

    /**
     * Get campaign blockchain data
     */
    public function campaignData(Request $request, $campaignId)
    {
        $campaign = Cause::findOrFail($campaignId);

        $transactions = BlockchainTransaction::where('campaign_id', $campaignId)
            ->orderBy('created_at', 'desc')
            ->get();

        $escrowEntries = Escrow::where('campaign_id', $campaignId)
            ->orderBy('created_at', 'desc')
            ->get();

        $fraudReport = FraudReport::where('campaign_id', $campaignId)->first();

        return response()->json([
            'campaign' => [
                'id' => $campaign->id,
                'title' => $campaign->title,
                'raised' => $campaign->raised,
                'amount' => $campaign->amount,
                'patient_name' => $campaign->patient_name,
                'hospital_name' => $campaign->hospital_name,
                'fraud_score' => $campaign->fraud_score,
            ],
            'transactions' => $transactions->map(fn($tx) => [
                'hash' => $tx->transaction_hash,
                'short_hash' => $tx->short_hash,
                'amount' => $tx->formatted_amount,
                'type' => $tx->transaction_type,
                'status' => $tx->status,
                'block_number' => $tx->block_number,
                'gas_fee' => $tx->gas_fee,
                'created_at' => $tx->created_at->diffForHumans(),
            ]),
            'escrow' => $escrowEntries->map(fn($e) => [
                'amount' => amount_with_currency_symbol($e->amount),
                'status' => $e->status,
                'donor' => substr($e->donor_wallet_address, 0, 10) . '...',
                'tx_hash' => substr($e->blockchain_tx_hash, 0, 14) . '...',
                'created_at' => $e->created_at->diffForHumans(),
            ]),
            'fraud' => $fraudReport ? [
                'score' => $fraudReport->fraud_score,
                'risk_level' => $fraudReport->risk_level,
                'checks' => $fraudReport->check_results,
                'recommendation' => $fraudReport->recommendation,
            ] : null,
        ]);
    }

    /**
     * Get network stats
     */
    public function networkStats()
    {
        return response()->json([
            'network' => DemoBlockchainService::NETWORK,
            'total_transactions' => BlockchainTransaction::count(),
            'confirmed_transactions' => BlockchainTransaction::where('status', 'confirmed')->count(),
            'total_donations' => CauseLogs::where('payment_gateway', 'blockchain_demo')->where('status', 'complete')->sum('amount'),
            'total_escrow' => Escrow::where('status', 'held')->sum('amount'),
            'campaigns' => Cause::count(),
            'active_campaigns' => Cause::where('status', 'publish')->count(),
        ]);
    }

    /**
     * Release escrow funds (admin action)
     */
    public function releaseEscrow(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required|exists:causes,id',
            'amount' => 'required|numeric|min:1',
            'milestone' => 'required|string',
        ]);

        $escrow = Escrow::where('campaign_id', $request->campaign_id)
            ->where('status', 'held')
            ->first();

        if (!$escrow) {
            return response()->json(['error' => 'No escrow funds found'], 404);
        }

        $escrow->update([
            'status' => 'released',
            'release_reason' => $request->milestone,
            'released_at' => now(),
        ]);

        $tx = DemoBlockchainService::processWithdrawal(
            Cause::find($request->campaign_id),
            $request->amount,
            session('wallet_address', '0x0000000000000000000000000000000000000000')
        );

        return response()->json([
            'success' => true,
            'message' => 'Escrow funds released',
            'transaction' => [
                'hash' => $tx->transaction_hash,
            'amount' => $request->amount,
            'admin_charge' => \App\Helpers\DonationHelpers::get_donation_charge_for_campaign_owner($request->amount),
                'milestone' => $request->milestone,
            ],
        ]);
    }
}

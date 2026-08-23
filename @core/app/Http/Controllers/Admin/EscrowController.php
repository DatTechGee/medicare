<?php

namespace App\Http\Controllers\Admin;

use App\Cause;
use App\Escrow;
use App\Mail\BasicMail;
use App\Notification;
use App\Services\DemoBlockchainService;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EscrowController extends Controller
{
    public function index()
    {
        $existingIds = Cause::whereIn('id', Escrow::query()->select('campaign_id')->distinct())
            ->pluck('id');

        $stats = Escrow::whereIn('campaign_id', $existingIds)
            ->selectRaw('campaign_id,
                SUM(CASE WHEN status = \'held\' AND disputed = 0 THEN amount ELSE 0 END) AS held_available,
                SUM(CASE WHEN status = \'held\' AND disputed = 1 THEN amount ELSE 0 END) AS held_frozen,
                SUM(CASE WHEN status = \'released\' THEN amount ELSE 0 END) AS released_total,
                SUM(CASE WHEN status = \'refunded\' THEN amount ELSE 0 END) AS refunded_total
            ')
            ->groupBy('campaign_id')
            ->get()
            ->keyBy('campaign_id');

        $campaigns = Cause::whereIn('id', $stats->keys())
            ->orderByDesc('id')
            ->get();

        $owners = User::whereIn('id', $campaigns->pluck('user_id')->filter()->unique())->get()->keyBy('id');

        $totals = [
            'held' => (float) $stats->sum('held_available'),
            'frozen' => (float) $stats->sum('held_frozen'),
            'released' => (float) $stats->sum('released_total'),
            'refunded' => (float) $stats->sum('refunded_total'),
            'platform_income' => (float) \App\CauseLogs::where('status', 'complete')->sum('admin_charge'),
        ];

        $heldDonations = Escrow::whereIn('campaign_id', $existingIds)
            ->where('status', 'held')
            ->orderByDesc('id')
            ->get();

        return view('backend.donations.escrow')->with([
            'campaigns' => $campaigns,
            'stats' => $stats,
            'owners' => $owners,
            'totals' => $totals,
            'heldDonations' => $heldDonations,
        ]);
    }

    public function disburse(Request $request)
    {
        $this->validate($request, [
            'campaign_id' => 'required|integer',
            'amount' => 'required|numeric|min:0.01',
            'admin_note' => 'nullable|string|max:1000',
        ]);

        $cause = Cause::findOrFail($request->campaign_id);

        /* ---------- fraud & verification gates (hard blocks) ---------- */

        if ($cause->status !== 'publish') {
            return redirect()->back()->with(['msg' => __('Blocked: this campaign has not been approved/published yet.'), 'type' => 'danger']);
        }

        $trustedStatus = in_array($cause->verification_status, ['verified', 'approved'], true);
        if ((int) ($cause->fraud_score ?? 0) >= 70 && !$trustedStatus) {
            return redirect()->back()->with(['msg' => __('Blocked: fraud score :score/100 is high and the campaign is not verified. Verify the campaign before disbursing.', ['score' => $cause->fraud_score]), 'type' => 'danger']);
        }

        $frozen = Escrow::where('campaign_id', $cause->id)->where('status', 'held')->where('disputed', 1)->sum('amount');
        if ((float) $frozen > 0) {
            return redirect()->back()->with(['msg' => __('Blocked: :amount is frozen on this campaign (dispute/fraud hold). Resolve it first.', ['amount' => number_format((float) $frozen, 2)]), 'type' => 'danger']);
        }

        $available = (float) Escrow::where('campaign_id', $cause->id)->where('status', 'held')->where('disputed', 0)->sum('amount');
        $amount = round((float) $request->amount, 2);
        if ($amount > $available + 0.001) {
            return redirect()->back()->with(['msg' => __('Blocked: requested :req exceeds escrow balance :avail.', ['req' => number_format($amount, 2), 'avail' => number_format($available, 2)]), 'type' => 'danger']);
        }

        /* payout wallet must exist AND be verified by an admin */
        $owner = $cause->user_id ? User::find($cause->user_id) : null;
        $wallet = $cause->wallet_address ?: optional($owner)->wallet_address;
        $walletVerified = (bool) ($cause->wallet_verified || optional($owner)->wallet_verified);

        if (empty($wallet)) {
            return redirect()->back()->with(['msg' => __('Blocked: no receiving wallet declared for this campaign.'), 'type' => 'danger']);
        }
        if (!$walletVerified) {
            return redirect()->back()->with(['msg' => __('Blocked: receiving wallet is not verified by an admin yet (ownership unproven).'), 'type' => 'danger']);
        }

        /* ---------- execute disbursement ---------- */

        $tx = DB::transaction(function () use ($cause, $amount, $wallet) {
            $blockchainTx = DemoBlockchainService::processWithdrawal($cause, $amount, strtolower($wallet));

            $remaining = $amount;
            $rows = Escrow::where('campaign_id', $cause->id)
                ->where('status', 'held')
                ->where('disputed', 0)
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            foreach ($rows as $row) {
                if ($remaining <= 0.001) {
                    break;
                }
                if ($row->amount <= $remaining + 0.001) {
                    $row->update(['status' => 'released', 'released_at' => now()]);
                    $remaining -= (float) $row->amount;
                } else {
                    Escrow::create([
                        'campaign_id' => $row->campaign_id,
                        'cause_log_id' => $row->cause_log_id,
                        'amount' => $remaining,
                        'donor_wallet_address' => $row->donor_wallet_address,
                        'blockchain_tx_hash' => $blockchainTx->transaction_hash,
                        'status' => 'released',
                        'released_at' => now(),
                    ]);
                    $row->update(['amount' => round((float) $row->amount - $remaining, 2)]);
                    $remaining = 0;
                }
            }

            return $blockchainTx;
        });

        \App\Helpers\AuditLogger::record('escrow_disbursement', 'Cause', $cause->id, [
            'amount' => $amount,
            'wallet' => $wallet,
            'tx_hash' => $tx->transaction_hash,
            'note' => $request->admin_note,
        ]);

        Notification::create([
            'user_campaign_id' => $cause->id,
            'title' => __('Funds disbursed: ') . number_format($amount, 2) . ' USD',
            'type' => 'user_campaign',
        ]);

        $patientEmail = optional($owner)->email;
        if ($patientEmail) {
            try {
                Mail::to($patientEmail)->send(new BasicMail([
                    'subject' => __('Your campaign funds have been disbursed'),
                    'message' => __('The admin has released') . ' <b>' . number_format($amount, 2) . '</b> '
                        . __('from escrow to your verified wallet') . ': <code>' . substr($wallet, 0, 10) . '…</code><br>'
                        . __('Transaction') . ': <code>' . $tx->transaction_hash . '</code>',
                ]));
            } catch (\Exception $e) {
            }
        }

        return redirect()->back()->with([
            'msg' => __('Disbursed') . ' ' . number_format($amount, 2) . ' → ' . substr($wallet, 0, 10) . '… | TX: ' . substr($tx->transaction_hash, 0, 18) . '…',
            'type' => 'success',
        ]);
    }

    public function refund(Request $request)
    {
        $this->validate($request, [
            'escrow_id' => 'required|integer',
            'admin_note' => 'nullable|string|max:1000',
        ]);

        $row = Escrow::where('status', 'held')->where('disputed', 0)->find($request->escrow_id);
        if (!$row) {
            return redirect()->back()->with(['msg' => __('Blocked: donation not found or is not refundable (already released/refunded/disputed).'), 'type' => 'danger']);
        }

        $cause = Cause::find($row->campaign_id);
        if (!$cause) {
            return redirect()->back()->with(['msg' => __('Blocked: campaign for this donation no longer exists.'), 'type' => 'danger']);
        }

        $donationLog = \App\CauseLogs::find($row->cause_log_id);
        $donorWallet = $row->donor_wallet_address ?: optional($donationLog)->donor_wallet_address;
        if (empty($donorWallet)) {
            return redirect()->back()->with(['msg' => __('Blocked: no donor wallet recorded for this donation — cannot send the refund.'), 'type' => 'danger']);
        }

        /* reduce what the campaign reports as raised */
        DB::transaction(function () use ($cause, $row) {
            if (method_exists($cause, 'decrement')) {
                $cause->decrement('raised', round((float) $row->amount, 2));
            }
            $row->update(['status' => 'refunded', 'released_at' => now()]);
        });

        $blockchainTx = DemoBlockchainService::processRefund($cause, (float) $row->amount, strtolower($donorWallet));

        \App\Helpers\AuditLogger::record('escrow_refund', 'Cause', $cause->id, [
            'amount' => $row->amount,
            'donor_wallet' => $donorWallet,
            'tx_hash' => $blockchainTx->transaction_hash,
            'note' => $request->admin_note,
        ]);

        Notification::create([
            'user_campaign_id' => $cause->id,
            'title' => __('A donation of ') . number_format((float) $row->amount, 2) . ' USD ' . __('was refunded to the donor'),
            'type' => 'user_campaign',
        ]);

        return redirect()->back()->with([
            'msg' => __('Refunded') . ' ' . number_format((float) $row->amount, 2) . ' → donor ' . substr($donorWallet, 0, 10) . '… | TX: ' . substr($blockchainTx->transaction_hash, 0, 18) . '…',
            'type' => 'success',
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Cause;
use App\Escrow;
use App\Helpers\FlashMsg;
use App\Helpers\FraudEngine;
use App\Http\Controllers\Controller;
use App\Milestone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MilestoneController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Escrow refund workflow: return every held escrow row of a campaign to its
     * donor (status=refunded), emit on-chain refund transactions, flip donation
     * logs to refunded and notify each donor. Used when a campaign is flagged
     * after donations have already flowed in.
     */
    public function refundEscrow(Request $request, $campaignId)
    {
        $campaign = Cause::findOrFail($campaignId);

        $reason = trim((string) $request->input('reason')) ?: 'Campaign flagged for fraud — funds returned';

        /* only rows still sitting in the pool can be refunded */
        $rows = Escrow::where('campaign_id', $campaignId)
            ->where('status', 'held')
            ->get();

        if ($rows->isEmpty()) {
            return back()->with(['msg' => __('No held escrow rows for this campaign — nothing to refund.'), 'type' => 'warning']);
        }

        foreach ($rows as $row) {
            /* on-chain refund transaction back to the donor wallet */
            $tx = \App\Services\DemoBlockchainService::processWithdrawal(
                $campaign,
                (float) $row->amount,
                $row->donor_wallet_address ?: '0x0000000000000000000000000000000000000000'
            );
            /* mark it as a refund rather than a withdrawal */
            $tx->update(['transaction_type' => 'refund']);

            $row->update(['status' => 'refunded', 'disputed' => 0]);

            /* donor-facing state + notification */
            $log = \App\CauseLogs::find($row->cause_log_id);
            if ($log) {
                $log->update(['donation_status' => 'refunded']);
                \App\Notification::create([
                    'cause_log_id' => $log->id,
                    'title' => '↩ Refund issued: ' . amount_with_currency_symbol($log->amount) . ' from "' . $campaign->title . '" — ' . $reason,
                    'type' => 'refund',
                ]);
            }
        }

        /* pull the campaign offline so no new money enters */
        if ($campaign->status === 'publish') {
            $campaign->update(['status' => 'draft']);
        }

        \App\Helpers\AuditLogger::record('escrow_refund', 'Cause', $campaignId, [
            'refunded_rows' => $rows->count(),
            'total_amount' => (float) $rows->sum('amount'),
            'reason' => $reason,
        ]);

        return back()->with([
            'msg' => __('Refunded :n escrow row(s) totalling :amt. Donors notified.', ['n' => $rows->count(), 'amt' => amount_with_currency_symbol((float) $rows->sum('amount'))]),
            'type' => 'success',
        ]);
    }

    /** Freeze all held escrow rows without refunding — pending investigation */
    public function freezeEscrow(Request $request, $campaignId)
    {
        $n = Escrow::where('campaign_id', $campaignId)->where('status', 'held')->where('disputed', 0)->update(['disputed' => 1]);

        if (!$n) {
            return back()->with(['msg' => __('Nothing to freeze — no active escrow rows.'), 'type' => 'warning']);
        }

        \App\Helpers\AuditLogger::record('escrow_freeze', 'Cause', $campaignId, ['frozen_rows' => $n]);

        return back()->with(['msg' => __('Frozen :n escrow row(s) pending review.', ['n' => $n]), 'type' => 'success']);
    }

    /** Blacklist a beneficiary wallet across the platform */
    public function blacklistWallet(Request $request, $campaignId)
    {
        $campaign = Cause::findOrFail($campaignId);
        $wallet = $campaign->wallet_address;

        $campaign->forceFill(['wallet_verified' => 0])->save();

        \App\FraudReport::create([
            'campaign_id' => $campaignId,
            'fraud_score' => 100,
            'risk_level' => 'critical',
            'check_results' => [
                'type' => 'manual_action',
                'flags' => ['wallet_blacklisted'],
                'wallet' => $wallet,
            ],
            'evidence' => [
                'wallet_blacklisted' => [
                    'pass' => false,
                    'points' => 0,
                    'detail' => 'Beneficiary wallet manually blacklisted by admin: ' . ($wallet ?? 'unknown'),
                ],
            ],
            'recommendation' => 'BLOCKED',
            'status' => 'flagged',
            'reviewed_by' => Auth::id(),
        ]);

        \App\Helpers\AuditLogger::record('wallet_blacklist', 'Cause', $campaignId, ['wallet' => $wallet]);

        return back()->with(['msg' => __('Wallet blacklisted and campaign marked BLOCKED.'), 'type' => 'danger']);
    }

    public function index($campaignId)
    {
        $campaign = Cause::findOrFail($campaignId);
        $milestones = Milestone::where('campaign_id', $campaignId)->orderBy('id', 'desc')->get();
        $escrowTotal = Escrow::where('campaign_id', $campaignId)->where('status', 'held')->where('disputed', 0)->sum('amount');
        $releasedTotal = Milestone::where('campaign_id', $campaignId)->where('status', 'released')->sum('amount');

        return view('backend.campaign-milestones', [
            'campaign' => $campaign,
            'milestones' => $milestones,
            'escrowTotal' => $escrowTotal,
            'releasedTotal' => $releasedTotal,
        ]);
    }

    public function store(Request $request, $campaignId)
    {
        $this->validate($request, [
            'title' => 'required|string',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'proof_document' => 'nullable|string',
            'proof_notes' => 'nullable|string',
        ]);

        $campaign = Cause::findOrFail($campaignId);

        $milestone = Milestone::create([
            'campaign_id' => $campaignId,
            'title' => $request->title,
            'description' => $request->description,
            'amount' => $request->amount,
            'status' => !empty($request->proof_document) ? 'proof_submitted' : 'pending',
            'proof_document' => $request->proof_document,
            'proof_notes' => $request->proof_notes,
        ]);

        return redirect()->back()->with(['msg' => __('Milestone Created'), 'type' => 'success']);
    }

    public function submitProof(Request $request, $campaignId, $id)
    {
        $this->validate($request, [
            'proof_document' => 'required|string',
            'proof_notes' => 'required|string',
        ]);

        $milestone = Milestone::findOrFail($id);
        $milestone->update([
            'proof_document' => $request->proof_document,
            'proof_notes' => $request->proof_notes,
            'status' => 'proof_submitted',
        ]);

        return redirect()->back()->with(['msg' => __('Proof Submitted for Review'), 'type' => 'success']);
    }

    public function verify(Request $request, $campaignId, $id)
    {
        $this->validate($request, [
            'action' => 'required|in:verify,reject',
        ]);

        $milestone = Milestone::findOrFail($id);

        if ($request->action === 'verify') {
            $escrowTotal = Escrow::where('campaign_id', $milestone->campaign_id)
                ->where('status', 'held')
                ->where('disputed', 0)
                ->sum('amount');
            if ($escrowTotal >= $milestone->amount) {
                $milestone->update([
                    'status' => 'released',
                    'verified_at' => now(),
                    'released_at' => now(),
                    'verified_by' => Auth::id(),
                ]);

                Escrow::where('campaign_id', $milestone->campaign_id)
                    ->where('status', 'held')
                    ->where('disputed', 0)
                    ->orderBy('id')
                    ->limit(1)
                    ->update([
                        'status' => 'released',
                        'released_at' => now(),
                    ]);

                $cause = Cause::find($milestone->campaign_id);
                if ($cause) {
                    $cause->update([
                        'raised' => max(0, $cause->raised - $milestone->amount),
                    ]);
                }

                /* payout goes to the campaign owner's verified patient wallet */
                $owner = ($cause && $cause->user_id) ? \App\User::find($cause->user_id) : null;
                if ($owner && $owner->wallet_verified && !empty($owner->wallet_address)) {
                    $payoutWallet = strtolower($owner->wallet_address);
                    $payoutTarget = 'patient';
                } else {
                    $payoutWallet = '0x' . bin2hex(random_bytes(20));
                    $payoutTarget = 'unverified (held in platform treasury)';
                }

                $tx = \App\Services\DemoBlockchainService::processWithdrawal(
                    $cause,
                    $milestone->amount,
                    $payoutWallet
                );

                \App\Helpers\AuditLogger::record('milestone_release', 'Milestone', $milestone->id, [
                    'campaign_id' => $milestone->campaign_id,
                    'amount' => (float) $milestone->amount,
                    'payout_wallet' => $payoutWallet,
                    'payout_target' => $payoutTarget,
                    'tx_hash' => $tx->transaction_hash,
                ]);

                return redirect()->back()->with([
                    'msg' => __('Milestone verified — :amt disbursed to :target (:addr). Tx: :hash', [
                        'amt' => amount_with_currency_symbol($milestone->amount),
                        'target' => $owner && !empty($owner->name) ? $owner->name : 'beneficiary',
                        'addr' => substr($payoutWallet, 0, 8) . '…' . substr($payoutWallet, -6),
                        'hash' => substr($tx->transaction_hash, 0, 14) . '…',
                    ]),
                    'type' => 'success',
                ]);
            } else {
                return redirect()->back()->with(['msg' => __('Insufficient escrow balance'), 'type' => 'danger']);
            }
        } else {
            $milestone->update([
                'status' => 'rejected',
                'verified_at' => now(),
                'verified_by' => Auth::id(),
            ]);

            \App\Helpers\AuditLogger::record('milestone_reject', 'Milestone', $milestone->id, [
                'campaign_id' => $milestone->campaign_id,
            ]);

            return redirect()->back()->with(['msg' => __('Milestone Rejected'), 'type' => 'danger']);
        }
    }
}

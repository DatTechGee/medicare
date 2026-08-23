<?php

namespace App\Http\Controllers\Admin;

use App\Cause;
use App\CauseCategory;
use App\CauseLogs;
use App\EventAttendance;
use App\EventPaymentLogs;
use App\Helpers\DataTableHelpers\Donation;
use App\Helpers\DataTableHelpers\General;
use App\Http\Controllers\Controller;
use App\DonationWithdraw;
use App\Mail\BasicMail;
use App\Mail\DonationMessage;
use App\Mail\PaymentSuccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Yajra\DataTables\Contracts\DataTable;
use Yajra\DataTables\DataTables;

class WithdrawController extends Controller
{
    private const BASE_PATH = 'backend.donations.';

    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('permission:donation-withdraw-list|donation-withdraw-edit|donation-withdraw-delete|donation-withdraw-view',['only' => ['all_donation_withdraw']]);
        $this->middleware('permission:donation-withdraw-edit',['only' => ['edit_donation_withdraw','update_donation_withdraw','Withdraw_Approval']]);
        $this->middleware('permission:donation-withdraw-delete',['only' => ['delete_donation_withdraw','bulk_action']]);
        $this->middleware('permission:donation-withdraw-view',['only' => ['view_donation_withdraw']]);
    }

    public function all_donation_withdraw(Request $request)
    {
        if ($request->ajax()){
            $withdraw = DonationWithdraw::orderBy('id','desc')->get();
            return DataTables::of($withdraw)
                ->addIndexColumn()
                ->addColumn('checkbox',function ($row){
                    return General::bulkCheckbox($row->id);
                })
                ->addColumn('info',function ($row){
                    return Donation::withdrawInfoColumn($row);
                })
                ->addColumn('status',function ($row){
                    return General::statusSpan($row->payment_status);
                })
                ->addColumn('fraud',function ($row){
                    $cause = $row->cause;
                    if (!$cause) return '<span class="text-muted">—</span>';
                    $score = (int) ($cause->fraud_score ?? 0);
                    $color = $score >= 50 ? 'danger' : ($score >= 25 ? 'warning' : 'success');
                    $html = '<span class="alert alert-'.$color.'" style="display:inline-block;padding:2px 10px;">'.__('Risk').': '.$score.'</span>';

                    $requester = $row->user;
                    if ($requester && !empty($requester->wallet_address)) {
                        $html .= $requester->wallet_verified
                            ? '<br><small class="text-success"><i class="ti-check"></i> '.__('Wallet verified').'</small>'
                            : '<br><small class="text-warning"><i class="ti-timer"></i> '.__('Wallet unverified').'</small>';
                    } else {
                        $html .= '<br><small class="text-muted"><i class="ti-wallet"></i> '.__('No wallet').'</small>';
                    }
                    return $html;
                })
                ->addColumn('action', function($row){
                    $action = '';
                    $admin = auth()->guard('admin')->user();
                    if ($admin->can('permission:donation-withdraw-delete')){
                        $action .= General::deletePopover(route('admin.donations.withdraw.delete',$row->id));
                    }
                    if ($admin->can('permission:donation-withdraw-view')){
                        $action .= General::viewIcon(route('admin.donations.withdraw.view',$row->id));
                    }
                    if ($admin->can('donation-withdraw-edit')){
                        if($row->payment_status !== 'approved'){
                            $action .= General::editIcon(route('admin.donations.withdraw.edit',$row->id));
                        }
                        /* one-click disbursement (runs the fraud gate server-side) */
                        if(in_array($row->payment_status,['pending','processing'])){
                            $csrf = csrf_token();
                            $confirm = __('Release :amt to the patient wallet? Fraud checks will run before payout.', ['amt' => amount_with_currency_symbol($row->withdraw_request_amount)]);
                            $action .= <<<HTML
<form method="post" action="{$this->approvalUrl($row->id)}" style="display:inline-block;" onsubmit="return confirm('{$confirm}');">
    <input type="hidden" name="_token" value="{$csrf}">
    <button type="submit" class="btn btn-success btn-xs mb-3 mr-1" title="Disburse to patient"><i class="ti-arrow-right"></i></button>
</form>
HTML;
                        }
                    }

                    return $action;
                })
                ->rawColumns(['action','checkbox','info','status','fraud'])
                ->make(true);

        }
        return  view(self::BASE_PATH . 'donation-withdraw');
    }


    private function approvalUrl($id)
    {
        return route('admin.donations.withdraw.approval', $id);
    }

    public function edit_donation_withdraw($id)
    {

        $withdraw = DonationWithdraw::findOrFail($id);
        return view(self::BASE_PATH . 'edit-withdraw')->with([
            'withdraw' => $withdraw
        ]);
    }

    public function update_donation_withdraw(Request $request)
    {

        $this->validate($request, [
            'transaction_id' => 'nullable|string',
            'payment_information' => 'nullable|string',
            'additional_comment_by_admin' => 'nullable|string',
            'payment_receipt' => 'nullable|mimes:pdf,jpg,jpeg,png',
        ]);

        $withdraw = DonationWithdraw::find($request->withdraw_id);
        $withdraw_able_amount = optional($withdraw->cause)->raised - optional($withdraw->cause)->withdraws->where('payment_status', 'approved')->pluck('withdraw_request_amount')->sum();

        if ($withdraw->withdraw_request_amount > $withdraw_able_amount) {
            return redirect()->back()->with(['msg' => __("withdaw able amount is less than requested amount"),'type' => 'danger']);
        }

        if ($request->file('payment_receipt')) {

            if (file_exists('assets/uploads/donation-withdraw/' . $withdraw->payment_receipt)) {
                @unlink('assets/uploads/donation-withdraw/' . $withdraw->payment_receipt);
            }
            $attachment = $request->file('payment_receipt');
            $attachmentName = 'payment_receipt_' . uniqid('', true) . '.' . $attachment->getClientOriginalExtension();
            $folder_path = 'assets/uploads/donation-withdraw/';
            $attachment->move($folder_path, $attachmentName);
        } else {
            $attachmentName = $withdraw->payment_receipt;
        }

        DonationWithdraw::findOrFail($request->withdraw_id)->update([
            'transaction_id' => $request->transaction_id,
            'payment_information' => $request->payment_information,
            'additional_comment_by_admin' => $request->additional_comment_by_admin,
            'payment_receipt' => $attachmentName,
            'payment_status' => $request->payment_status,
        ]);

        $user_email = optional($withdraw->user)->email;
        if ($user_email) {
           try{
                Mail::to($user_email)->send(new BasicMail([
                    'subject' => __('Your donation withdrawal Status Has Been Change'),
                    'message' => __('Status is ') . ": " . $request->payment_status . '<br>' . $request->additional_comment_by_admin
                ]));
           }catch(\Exception $e){
             return  redirect()->back()->with(['msg' => __('Donation Withdraw Updated').' '.__('Mail Send Failed'), 'type' => 'success']);
           }
        }


        return redirect()->back()->with(['msg' => __('Donation Withdraw Updated...'), 'type' => 'success']);
    }


    public function Withdraw_Approval($id)
    {
        $withdraw_approval = DonationWithdraw::findOrFail($id);

        $cause_relation = $withdraw_approval->cause;

        /* ---------- FRAUD GATE: re-run the engine immediately before releasing money ---------- */
        try {
            $fraud = \App\Helpers\FraudEngine::analyzeCampaign($cause_relation);
        } catch (\Throwable $e) {
            $fraud = ['score' => 100, 'risk_level' => 'high', 'recommendation' => 'FLAG_FOR_REVIEW'];
        }
        $blockedByGate = $cause_relation->status === 'flagged'
            || ($fraud['risk_level'] ?? 'low') === 'high';

        /* payout only to a verified wallet */
        $requester = optional($withdraw_approval->user);
        $walletReady = $requester && !empty($requester->wallet_address) && $requester->wallet_verified;

        if ($blockedByGate || !$walletReady) {
            $reason = $blockedByGate
                ? __('Disbursement BLOCKED by fraud engine — campaign risk score :score/100 (:risk risk). Review the campaign before releasing funds.', ['score' => $fraud['score'] ?? '?', 'risk' => $fraud['risk_level'] ?? 'high'])
                : __('Disbursement held — patient payout wallet is not connected/verified yet. Funds remain in escrow.');

            \App\Helpers\AuditLogger::record('withdraw_disbursement_blocked', 'DonationWithdraw', $id, [
                'reason' => $blockedByGate ? 'fraud_gate' : 'wallet_not_verified',
                'campaign_status' => $cause_relation->status,
                'fraud_score' => $fraud['score'] ?? null,
                'wallet_verified' => (bool) ($requester->wallet_verified ?? false),
            ]);

            return redirect()->back()->with(['msg' => $reason, 'type' => 'danger']);
        }

        $raised_amount = $cause_relation->raised;
        $user_withdraw_amount_request = $withdraw_approval->withdraw_request_amount;

        $already_approved = DonationWithdraw::where('donation_id', $cause_relation->id)
            ->where('payment_status', 'approved')->where('id', '!=', $id)
            ->sum('withdraw_request_amount');
        $user_withdrawable_amount = ($raised_amount - $already_approved);

        $deduction_raised_amount = ($raised_amount - $user_withdraw_amount_request);

        $cause_id = $cause_relation->id;
        Cause::where('id', $cause_id)->update(['raised' => $deduction_raised_amount]);
        DonationWithdraw::where('id', $id)->update(['payment_status' => 'approved']);

        /* disburse on-chain to the patient's verified payout wallet (wallet already gate-checked above) */
        $cause = Cause::find($cause_id);
        if ($cause) {
            \App\Services\DemoBlockchainService::processWithdrawal(
                $cause,
                (float) $user_withdraw_amount_request,
                strtolower($requester->wallet_address)
            );
            $payoutNote = __('Disbursed on-chain to verified wallet :addr', ['addr' => substr($requester->wallet_address, 0, 8) . '…']);
        } else {
            $payoutNote = __('Funds held in platform treasury.');
        }

        \App\Helpers\AuditLogger::record('withdraw_disbursement', 'DonationWithdraw', $withdraw_approval->id, [
            'amount' => (float) $user_withdraw_amount_request,
            'wallet' => $requester ? $requester->wallet_address : null,
            'verified' => (bool) ($requester ? $requester->wallet_verified : false),
        ]);

        if ($user_withdrawable_amount > $user_withdraw_amount_request) {
            DonationWithdraw::where('id', $id)->update(['transaction_status' => 'not-full-paid']);
        } else {
            DonationWithdraw::where('id', $id)->update(['transaction_status' => 'full-paid']);
        }

        $user_email = $withdraw_approval->user->email;
       try{
            Mail::to($user_email)->send(new BasicMail([
                'subject' => __('Your donation withdrawal request has been approved and you will get back your withdrawalbe amount soon.'),
                'message' => "Status is : Changed"
            ]));
       }catch(\Exception $e){
           return redirect()->back()->with(['msg' => __('Donation Withdraw Approved...').' '.__('Mail Send Failed').' — '.$payoutNote, 'type' => 'success']);
       }

        return redirect()->back()->with(['msg' => __('Donation Withdraw Approved...').' — '.$payoutNote, 'type' => 'success']);

    }


    public function delete_donation_withdraw(Request $request, $id)
    {
        $data = DonationWithdraw::findOrFail($id);
         if (file_exists('assets/uploads/donation-withdraw/'.$data->payment_receipt)) {
              @unlink('assets/uploads/donation-withdraw/'.$data->payment_receipt);
           }

        DonationWithdraw::findOrFail($id)->delete();
        return redirect()->back()->with([ 'msg' => __('Donation Withdraw Deleted...'), 'type' => 'danger ']);
    }

    public function bulk_action(Request $request)
    {
        $all = DonationWithdraw::findOrFail($request->ids);
        foreach ($all as $item) {
             if (file_exists('assets/uploads/donation-withdraw/' . $item->payment_receipt)) {
                @unlink('assets/uploads/donation-withdraw/' . $item->payment_receipt);
             }
            $item->delete();
        }
        return response()->json(['status' => 'ok']);
    }

    public function view_donation_withdraw($id){
        $withdraw = DonationWithdraw::findOrFail($id);
        return view(self::BASE_PATH.'donation-withdraw-view')->with(['withdraw' => $withdraw]);
    }


}

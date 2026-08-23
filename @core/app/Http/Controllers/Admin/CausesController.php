<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use App\Blog;
use App\Cause;
use App\CauseCategory;
use App\CauseLogs;
use App\EventAttendance;
use App\EventPaymentLogs;
use App\Gift;
use App\Helpers\DataTableHelpers\Donation;
use App\Helpers\DataTableHelpers\General;
use App\Helpers\FlashMsg;
use App\Helpers\FraudEngine;
use App\Http\Controllers\Controller;
use App\Language;
use App\Mail\BasicMail;
use App\Mail\DonationMessage;
use App\Mail\PaymentSuccess;
use App\Recuring;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class CausesController extends Controller
{
    private const BASE_PATH = 'backend.donations.';

    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('permission:donation-list|donation-create|donation-edit|donation-delete',['only' => 'all_donation','donated_donors']);
        $this->middleware('permission:donation-create',['only' => 'new_donation','store_donation']);
        $this->middleware('permission:donation-edit',['only' => 'edit_donation','update_donation','clone_donation','donation_approve']);
        $this->middleware('permission:donation-delete',['only' => 'delete_donation','bulk_action']);
        /* ==== pending cause permission ====*/
        $this->middleware('permission:donation-pending-cause',['only' => 'all_pending_donation']);
        /* ==== donation settings ====*/
        $this->middleware('permission:donation-settings',['only' => 'update_settings','settings']);
        $this->middleware('permission:donation-pending-cause',['only' => 'all_pending_donation']);
        /* ==== donation payment log ====*/
        $this->middleware('permission:donation-payment-list|donation-payment-edit|donation-payment-delete',['only' => 'donation_payment_logs']);
        $this->middleware('permission:donation-payment-edit',['only' => 'approve_donation_payment','donation_reminder']);
        $this->middleware('permission:donation-payment-delete',['only' => 'delete_donation_payment_logs','donation_payment_logs_bulk_action']);
    }

    public function all_donation(Request $request)
    {
        if ($request->ajax()){
            $data = Cause::select('*')->orderBy('id','desc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('checkbox',function ($row){
                    return General::bulkCheckbox($row->id);
                })
                ->addColumn('info',function ($row){
                    return Donation::infoColumn($row);
                })
                ->addColumn('image',function ($row){
                    return General::image($row->image);
                })
                ->addColumn('category',function ($row){
                    return donation_category_by_id($row->categories_id);
                })
                ->addColumn('verification',function ($row){
                    $status = $row->verification_status ?? 'pending';
                    $colors = [
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'under_review' => 'info',
                    ];
                    $color = $colors[$status] ?? 'secondary';
                    $icons = [
                        'pending' => 'fas fa-clock',
                        'approved' => 'fas fa-check-circle',
                        'rejected' => 'fas fa-times-circle',
                        'under_review' => 'fas fa-search',
                    ];
                    $icon = $icons[$status] ?? 'fas fa-question';
                    $fraudScore = $row->fraud_score ?? '-';
                    return '<span class="badge badge-'.$color.'"><i class="'.$icon.'"></i> '.ucfirst(str_replace('_',' ',$status)).'</span><br><small class="text-muted">Score: '.$fraudScore.'</small>';
                })
                ->addColumn('action', function($row){
                    $action = '';
                    $action .= General::viewIcon(route('frontend.donations.single',$row->slug));
                    $admin = auth()->guard('admin')->user();
                    if ($admin->can('donation-delete')){
                        $action .= General::deletePopover(route('admin.donations.delete',$row->id));
                    }
                    if ($admin->can('donation-edit')){
                        $action .= General::editIcon(route('admin.donations.edit',$row->id));
                        $action .= General::cloneIcon(route('admin.donations.clone'),$row->id);
                        $action .= General::anchor(route('admin.donations.donors',$row->id),__('Donors'));
                        $action .= '<a href="'.route('admin.milestones.index',$row->id).'" class="btn btn-sm btn-success" title="Milestones"><i class="fas fa-road"></i></a> ';
                        $action .= Donation::aboutUpdate($row->id);
                        $action .= Donation::comments($row->id);

                        if ($row->created_by === 'user' && $row->status === 'pending'){
                            $action .= Donation::campaignApprove($row->id);
                        }

                        /* wallet + document verification controls */
                        $walletShort = substr((string) $row->wallet_address, 0, 10) . '…';
                        $action .= '<form method="POST" action="' . route('admin.causes.verify.wallet', $row->id) . '" style="display:inline-block;margin:2px" onsubmit="return confirm(\'Verify this campaign receiving wallet?\')">'
                            . csrf_field()
                            . '<button type="submit" class="btn btn-sm btn-' . ($row->wallet_verified ? 'warning' : 'info') . '" title="' . e($walletShort) . '"><i class="fas fa-wallet"></i> '
                            . ($row->wallet_verified ? __('Unverify Wallet') : __('Verify Wallet')) . '</button></form>';
                        $action .= '<form method="POST" action="' . route('admin.campaign.verify_document', $row->id) . '" style="display:inline-block;margin:2px" onsubmit="return confirm(\'Run document integrity check?\')">'
                            . csrf_field()
                            . '<button type="submit" class="btn btn-sm btn-primary" title="SHA-256 tamper check"><i class="fas fa-file-shield"></i> '
                            . ($row->document_verified_at ? __('Doc Recheck') : __('Doc Check')) . '</button></form>';
                    }
                    return $action;
                })
                ->rawColumns(['action','checkbox','image','info','verification'])
                ->make(true);
        }
        $all_donations = Cause::orderBy('id','desc')->paginate(12);
        return view(self::BASE_PATH . 'all-donations', compact('all_donations'));

    }
    public function all_pending_donation(Request $request)
    {
        if ($request->ajax()){
            $data = Cause::select('*')->where(['status' => 'pending'])->orderBy('id','desc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('checkbox',function ($row){
                    return General::bulkCheckbox($row->id);
                })
                ->addColumn('info',function ($row){
                    return Donation::infoColumn($row);
                })
                ->addColumn('image',function ($row){
                    return General::image($row->image);
                })
                ->addColumn('category',function ($row){
                    return donation_category_by_id($row->categories_id);
                })
                ->addColumn('action', function($row){
                    $action = '';
                    $action .= General::deletePopover(route('admin.donations.delete',$row->id));
                    $action .= General::editIcon(route('admin.donations.edit',$row->id));
                    $action .= General::viewIcon(route('frontend.donations.single',$row->slug));
                    $action .= General::cloneIcon(route('admin.donations.clone'),$row->id);
                    $action .= Donation::aboutUpdate($row->id);
                    $action .= Donation::comments($row->id);

                    if ($row->created_by === 'user' && $row->status === 'pending'){
                        $action .= Donation::campaignApprove($row->id);
                        /* admin manual fraud flag - blocks the campaign everywhere */
                        $action .= '<form method="POST" action="' . route('admin.donations.flag.fraud', $row->id) . '" style="display:inline-block;margin:2px" onsubmit="return confirm(\'Flag this campaign as FRAUD? It will be blocked from donations and the owner notified.\')">'
                            . csrf_field()
                            . '<button type="submit" class="btn btn-danger btn-sm btn-icon" title="Flag as fraud"><i class="fas fa-flag"></i> ' . __('Fraud') . '</button></form>';
                    }

                    return $action;
                })
                ->rawColumns(['action','checkbox','image','info'])
                ->make(true);
        }
        return view(self::BASE_PATH . 'pending-donations');

    }

    public function new_donation()
    {
        $all_category = CauseCategory::where(['status' => 'publish'])->get();
        $all_gifts= Gift::where(['creator_id'=> Auth::guard('admin')->id(),'creator_type'=>'admin','status' => 'publish'])->get();
        return view(self::BASE_PATH . 'new-donation')->with(['all_category' => $all_category, 'all_gifts' => $all_gifts]);
    }

    public function store_donation(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string',
            'slug' => 'nullable|string',
            'cause_content' => 'required|string',
            'amount' => 'required|string',
            'status' => 'required|string',
            'image' => 'nullable|string',
            'meta_tags' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'deadline' => 'nullable|string',
            'meta_title' => 'nullable|string',
            'image_gallery' => 'nullable|string',
            'medical_document' => 'nullable|string',
            'excerpt' => 'nullable|string',
            'gift_status' => 'nullable|string',
            'featured' => 'nullable|string',
            'emmergency' => 'nullable|string',
            'reward' => 'nullable|string',
            'monthly_donation_status' => 'nullable|string',
            'emmergency_title' => 'nullable|string',
            'categories_id' => 'required|string',
            'og_meta_title' => 'nullable|string',
            'og_meta_description' => 'nullable|string',
            'og_meta_image' => 'nullable|string',
            'patient_name' => 'nullable|string',
            'hospital_name' => 'nullable|string',
            'medical_details' => 'nullable|string',
        ], [
            'title.required' => __('title is required'),
            'cause_content.required' => __('donation content is required'),
            'amount.required' => __('amount is required'),
            'status.required' => __('status is required'),
            'categories_id.required' => __('category is required'),
        ]);
        $faq_item = $request->faq ?? ['title' => ['']];

        $slug = !empty($request->slug) ? Str::slug($request->slug ) : Str::slug($request->title);
        $slug_check = Cause::where(['slug' => $slug])->count();
        $cause_slug = $slug_check >= 1 ? $slug.'-2' : $slug;

        $cause = Cause::create([
            'cause_update_id' => 0,
            'title' => $request->title,
            'slug' => $cause_slug,
            'cause_content' => $request->cause_content,
            'amount' => $request->amount,
            'status' => $request->status,
            'image' => $request->image,
            'deadline' => $request->deadline,
            'image_gallery' => $request->image_gallery,
            'medical_document' => $request->medical_document,
            'faq' => serialize($faq_item),
            'admin_id' => Auth::guard('admin')->user()->id,
            'created_by' => 'admin',
            'excerpt' => $request->excerpt,
            'meta_title' => $request->meta_title,
            'categories_id' => $request->categories_id,
            'meta_tags' => $request->meta_tags,
            'meta_description' => $request->meta_description,
            'featured' => $request->featured,
            'gift_status' => $request->gift_status,
            'emmergency' => $request->emmergency,
            'reward' => $request->reward,
            'monthly_donation_status' => $request->monthly_donation_status,
            'emmergency_title' => $request->emmergency_title,
            'og_meta_title' => $request->og_meta_title,
            'og_meta_description' => $request->og_meta_description,
            'og_meta_image' => $request->og_meta_image,
            'patient_name' => $request->patient_name,
            'hospital_name' => $request->hospital_name,
            'medical_details' => $request->medical_details,
            'verification_status' => 'pending',
        ]);

        $cause->gift()->attach($request->gifts);

        // Advisory only — the admin reviews and verifies manually
        try {
            $fraudResult = FraudEngine::analyzeCampaign($cause);
            $cause->update(['fraud_score' => $fraudResult['score'] ?? 0]);
        } catch (\Throwable $e) {
            $fraudResult = ['score' => 0, 'risk_level' => 'low'];
        }

        return redirect()->back()->with([
            'msg' => __('New Cause Added') . ' | ' . __('Fraud Score') . ': ' . ($fraudResult['score'] ?? 0) . '/100 (' . ucfirst($fraudResult['risk_level'] ?? 'low') . ' Risk)',
            'type' => 'success'
        ]);
    }

    public function edit_donation($id)
    {
        $donation = Cause::findOrFail($id);
        $all_category = CauseCategory::all();
        $all_gifts= Gift::where(['creator_id'=> Auth::guard('admin')->id(),'creator_type'=>'admin','status' => 'publish'])->get();
        return view(self::BASE_PATH . 'edit-donations')->with(['donation' => $donation, 'all_category' => $all_category,'all_gifts'=>$all_gifts]);
    }

    public function verify_wallet(Request $request, $id)
    {
        $campaign = Cause::findOrFail($id);
        $unverify = $request->input('unverify', false);

        if ($unverify) {
            $campaign->wallet_verified = 0;
            $campaign->wallet_verified_at = null;
            $campaign->save();
        } else {
            $campaign->wallet_verified = 1;
            $campaign->wallet_verified_at = now();
            $campaign->save();
        }

        \App\Helpers\AuditLogger::record($unverify ? 'wallet_unverify' : 'wallet_verify', 'Cause', $id, [
            'wallet' => $campaign->wallet_address,
        ]);

        // rescore so the fraud report reflects the wallet state
        $fraudResult = \App\Helpers\FraudEngine::analyzeCampaign($campaign);

        return redirect()->back()->with([
            'msg' => ($unverify ? __('Wallet verification revoked') : __('Receiving wallet verified')) . ' | ' . __('Fraud Score') . ': ' . $fraudResult['score'] . '/100 (' . ucfirst($fraudResult['risk_level']) . ' Risk)',
            'type' => $fraudResult['risk_level'] === 'high' ? 'danger' : 'success',
        ]);
    }

    public function update_donation(Request $request)
    {

        $this->validate($request, [
            'title' => 'required|string',
            'slug' => 'nullable|string',
            'cause_content' => 'required|string',
            'amount' => 'required|string',
            'status' => 'required|string',
            'image' => 'nullable|string',
            'meta_tags' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_title' => 'nullable|string',
            'deadline' => 'nullable|string',
            'excerpt' => 'nullable|string',
            'image_gallery' => 'nullable|string',
            'medical_document' => 'nullable|string',
            'featured' => 'nullable|string',
            'emmergency' => 'nullable|string',
            'gift_status' => 'nullable|string',
            'reward' => 'nullable|string',
            'monthly_donation_status' => 'nullable|string',
            'emmergency_title' => 'nullable|string',
            'categories_id' => 'required|string',
            'patient_name' => 'nullable|string',
            'hospital_name' => 'nullable|string',
            'medical_details' => 'nullable|string',
        ],
            [
                'title.required' => __('title is required'),
                'cause_content.required' => __('donation content is required'),
                'amount.required' => __('amount is required'),
                'status.required' => __('status is required'),
                'categories_id.required' => __('category is required'),
            ]);

        $cause =  Cause::findOrFail($request->donation_id);
        $cause->gift()->detach();
        $cause->gift()->attach($request->gifts);
        $faq_item = $request->faq ?? ['title' => ['']];

        $slug = !empty($request->slug) ? Str::slug($request->slug) : Str::slug($request->title);
        $slug_check = Cause::where(['slug' => $slug])->count();
        $cause_slug = $slug_check > 1 ? $slug.'-3' : $slug;

        Cause::findOrFail($request->donation_id)->update([
            'title' => $request->title,
            'slug' => $cause_slug,
            'cause_content' => $request->cause_content,
            'amount' => $request->amount,
            'status' => $request->status,
            'image' => $request->image,
            'meta_tags' => $request->meta_tags,
            'meta_description' => $request->meta_description,
            'deadline' => $request->deadline,
            'image_gallery' => $request->image_gallery,
            'medical_document' => $request->medical_document,
            'faq' => serialize($faq_item),
            'meta_title' => $request->meta_title,
            'excerpt' => $request->excerpt,
            'categories_id' => $request->categories_id,
            'featured' => $request->featured,
            'emmergency' => $request->emmergency,
            'reward' => $request->reward,
            'monthly_donation_status' => $request->monthly_donation_status,
            'gift_status' => $request->gift_status,
            'emmergency_title' => $request->emmergency_title,
            'og_meta_title' => $request->og_meta_title,
            'og_meta_description' => $request->og_meta_description,
            'og_meta_image' => $request->og_meta_image,
            'patient_name' => $request->patient_name,
            'hospital_name' => $request->hospital_name,
            'medical_details' => $request->medical_details,
        ]);

        $updatedCause = Cause::findOrFail($request->donation_id);
        $fraudResult = FraudEngine::analyzeCampaign($updatedCause);

        return redirect()->back()->with([
            'msg' => __('Cause Updated...') . ' | ' . __('Fraud Score') . ': ' . $fraudResult['score'] . '/100',
            'type' => 'success'
        ]);
    }

    public function delete_donation(Request $request, $id)
    {
        Cause::findOrFail($id)->delete();
        return redirect()->back()->with(['msg' => __('Donation Deleted...'), 'type' => 'danger']);
    }

    public function clone_donation(Request $request)
    {
        $donation_details = Cause::findOrFail($request->item_id);
        Cause::create([
            'cause_update_id' => 0,
            'title' => $donation_details->title,
            'slug' => !empty($donation_details->slug) ? $donation_details->slug : \Str::slug($donation_details->title),
            'cause_content' => $donation_details->cause_content,
            'amount' => $donation_details->amount,
            'status' => 'draft',
            'image' => $donation_details->image,
            'meta_tags' => $donation_details->meta_tags,
            'meta_description' => $donation_details->meta_description,
            'deadline' => $donation_details->deadline,
            'image_gallery' => $donation_details->image_gallery,
            'medical_document' => $donation_details->medical_document,
            'faq' => $donation_details->faq,
            'admin_id' => $donation_details->admin_id,
            'created_by' => 'admin',
            'meta_title' => $donation_details->meta_title,
            'categories_id' => $donation_details->categories_id,
            'featured' => $donation_details->featured,
            'gift_status' => $donation_details->gift_status,
            'emmergency' => $donation_details->emmergency,
            'reward' => $donation_details->reward,
            'emmergency_title' => $donation_details->emmergency_title,
            'excerpt' => $donation_details->excerpt,
            'og_meta_title' => $request->og_meta_title,
            'og_meta_description' => $request->og_meta_description,
            'og_meta_image' => $request->og_meta_image,
        ]);

        return redirect()->back()->with(['msg' => __('Cause Cloned...'), 'type' => 'success']);
    }


    public function donation_payment_logs(Request $request)
    {
        if ($request->ajax()){
            $donation_logs =  CauseLogs::select('*')->orderBy('id','desc');
            return DataTables::of($donation_logs)
                ->addIndexColumn()
                ->addColumn('checkbox',function ($row){
                    return General::bulkCheckbox($row->id);
                })
                ->addColumn('info',function ($row){
                    return Donation::paymentInfoColumn($row);
                })
                ->addColumn('status',function ($row){
                    return General::statusSpan($row->status);
                })
                ->addColumn('action', function($row){
                    $admin = auth()->guard('admin')->user();
                    $action = '';
                    if ($admin->can('donation-payment-delete')){
                        $action .= General::deletePopover(route('admin.donations.payment.delete',$row->id));
                    }
                    if ($admin->can('donation-payment-edit')){
                        if($row->payment_gateway == 'manual_payment' && $row->status == 'pending'){
                            $action .= General::paymentAccept(route('admin.donations.payment.approve',$row->id));
                        }

                        if($row->payment_gateway == 'manual_payment' && !empty($row->manual_payment_attachment)){
                            $action .= General::viewAttachment($row);
                        }
                        if($row->status == 'complete'){
                            $action .= General::invoiceBtn(route('frontend.donation.invoice.generate'),$row->id);
                        }
                        if(!empty($row->user_id) && $row->status == 'pending'){
                            $action .= General::reminderMail(route('admin.donation.reminder'),$row->id);
                        }
                        $action .= General::change_status(route('admin.donation.change.status'),$row->id, $row->email);
                    }

                    return $action;
                })
                ->rawColumns(['action','checkbox','info','status'])
                ->make(true);
        }
        return view(self::BASE_PATH . 'donation-payment-logs-all');
    }

    public function delete_donation_payment_logs(Request $request, $id)
    {
         Recuring::where('cause_log_id',$id)->delete();
         $log = CauseLogs::findOrFail($id);
         $cause = Cause::find($log->cause_id);
         
         $cause_amount = ($cause->raised - $log->amount);
         $cause->raised = $cause_amount < 0 ? 0 : $cause_amount;
       
         if($minus_amount < 1){
            $minus_amount = 0;
         }
         $cause->raised = $minus_amount;
         $cause->save();
         $log->delete();

        return redirect()->back()->with(['msg' => __('Donation Payment Log Deleted..'), 'type' => 'danger']);
    }

    public function approve_donation_payment(Request $request, $id)
    {


        $payment_logs = CauseLogs::findOrFail($id);
        $payment_logs->status = 'complete';
        $payment_logs->save();

        //update donation raised amount
        
        $event_details = Cause::findOrFail($payment_logs->cause_id);
        
        if($payment_logs->added_in_raised_amount == 0){
            $event_details->raised = (int)$event_details->raised + (int)$payment_logs->amount;
            $event_details->save();

            // book the platform fee and hold the net amount in platform escrow
            $owner_fee = \App\Helpers\DonationHelpers::get_donation_charge_for_campaign_owner($payment_logs->amount);
            if ((float) ($payment_logs->admin_charge ?? 0) <= 0 && $owner_fee > 0) {
                $payment_logs->admin_charge = round($owner_fee, 2);
                $payment_logs->save();
            }
            \App\Escrow::firstOrCreate(
                ['cause_log_id' => $payment_logs->id],
                [
                    'campaign_id' => $event_details->id,
                    'amount' => round(max((float) $payment_logs->amount - (float) ($payment_logs->admin_charge ?? 0), 0), 2),
                    'donor_wallet_address' => $payment_logs->donor_wallet_address,
                    'status' => 'held',
                ]
            );
        }
        
       

        $site_title = get_static_option('site_' . get_default_language() . '_title');
        $customer_subject = __('Your donation payment success for') . ' ' . $site_title;
        $admin_subject = __('You have a new donation payment from') . ' ' . $site_title;
        $admin_mail = get_static_option('site_global_email');
        $donation_notify_mail = get_static_option('donation_notify_mail') ??  $admin_mail;
        try{
            Mail::to($donation_notify_mail)->send(new DonationMessage($payment_logs, $admin_subject, 'owner'));
            Mail::to($payment_logs->email)->send(new DonationMessage($payment_logs, $customer_subject, 'customer'));
        }catch(\Exception $e){
            return redirect()->back()->with(['msg' => __('Manual Payment Accept Success, but mail not send'), 'type' => 'success']);
        }
     
        return redirect()->back()->with(['msg' => __('Manual Payment Accept Success'), 'type' => 'success']);
    }

    public function bulk_action(Request $request)
    {
        $logs = Cause::whereIn('id',$request->ids);

        if(!empty($logs->recurrings)){
            foreach ($logs->recurrings as $data){
                $data->delete();
            }
        }

            $logs->delete();
        return response()->json(['status' => 'ok']);
    }

     public function donation_payment_logs_bulk_action(Request $request)
    {
        $logs = CauseLogs::find($request->ids);

        foreach ($logs as $log){
            $cause = Cause::find($log->cause_id);
            $cause->raised = ($cause->raised - $log->amount);
            $cause->save();
            $log->delete();
        }

        return response()->json(['status' => 'ok']);
    }

    public function donation_report(Request $request)
    {
        $order_data = '';
        $query = CauseLogs::query();
        if (!empty($request->start_date)) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if (!empty($request->payment_status)) {
            $query->where(['status' => $request->payment_status]);
        }
        $error_msg = __('select start & end date to generate event payment report');
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $query->orderBy('id', 'DESC');
            $order_data = $query->paginate($request->items);
            $error_msg = '';
        }

        return view(self::BASE_PATH . 'donation-report')->with([
            'order_data' => $order_data,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'items' => $request->items,
            'payment_status' => $request->payment_status,
            'error_msg' => $error_msg
        ]);
    }

    //
    public function donation_reminder(Request $request)
    {
        $order_details = CauseLogs::findOrFail($request->id);
        $data['subject'] = __('your donation is still in pending at') . ' ' . get_static_option('site_title');
        $data['message'] = __('hello') . ' ' . $order_details->name . '<br>';
        $data['message'] .= __('your event booking') . ' #' . $order_details->id . ' ';
        $data['message'] .= __('is still in pending, to complete your donation go to');
        $data['message'] .= ' <a href="' . route('user.home') . '">' . __('your dashboard') . '</a>';

        //send mail while order status change
        try{
            Mail::to($order_details->email)->send(new BasicMail($data));
        }catch(\Exception $e){
            return redirect()->back()->with(['msg' => $e->getMessage(), 'type' => 'danger']);
        }
       

        return redirect()->back()->with(['msg' => __('Reminder Mail Send Success'), 'type' => 'success']);
    }

    public function change_status(Request $request)
    {
        $request->validate([
           'status'=> 'nullable'
        ]);


        $status = $request->status;
        $message = $request->message ?? '';
        $user_mail = $request->email;

        $order_details = CauseLogs::findOrFail($request->id);

        $order_details->status = $status;
        $order_details->save();
        
          //update donation raised amount
        $event_details = Cause::findOrFail($order_details->cause_id);
        $event_details->raised = (int)$event_details->raised + (int)$order_details->amount;
        $event_details->save();
        

        $data['subject'] = __('Your donation status has been changed in : ') . '(' . ucfirst($status). ')' . ' in ' . get_static_option('site_title');
        $data['message'] = __('hello') . ' ' . $order_details->name . '<br>';
        $data['message'] =  __('Your donation status has been changed in : ') . '(' . ucfirst($status)
            . ')'. '<br>';

        if(!empty($request->message)):
             $data['message'] .= $message . '<br>';
        endif;

        $data['message'] .= ' <a href="' . route('user.home') . '">' . __('your dashboard') . '</a>';

        try{
            Mail::to($user_mail)->send(new BasicMail($data));
        }catch(\Exception $e){
            return redirect()->back()->with(['msg' => $e->getMessage(), 'type' => 'danger']);
        }

        return redirect()->back()->with(['msg' => __('Donation Status Changed Successfully..'), 'type' => 'success']);
    }


    public function settings()
    {
        return view(self::BASE_PATH . 'settings');
    }

    public function update_settings(Request $request)
    {
        $this->validate($request, [
            'donation_charge_active_deactive_button' => 'nullable|string',
            'charge_amount_type' => 'nullable|string',
            'charge_amount' => 'nullable|string',
            'donation_deadline_text' => 'nullable|string',
        ]);
        $fields = [
            'donation_charge_active_deactive_button',
            'charge_amount_type',
            'charge_amount',
            'donation_button_text',
            'donation_raised_text',
            'donation_goal_text',
            'site_events_post_items',
            'donation_single_form_button_text',
            'donation_single_recent_donation_text',
            'donation_custom_amount',
            'donation_default_amount',
            'donation_notify_mail',
            'donation_single_faq_title',
            'cause_single_donate_button_text',
            'cause_single_donate_sidebar_text',
            'donation_success_page_title',
            'donation_success_page_description',
            'donation_cancel_page_title',
            'donation_cancel_page_description',
            'donation_single_page_countdown_status',
            'donation_charge_form',
            'user_campaign_metadata_status',
            'allow_user_to_add_custom_tip_in_donation',
            'donation_deadline_text',
            'donation_medical_document_button_text',
            'emmergency_donation_text',
            'releated_donation_text',
            'donation_medical_document_button_show_hide',
            'donation_flag_show_hide',
            'donation_descriptions_show_hide',
            'donation_updates_show_hide',
            'donation_comments_show_hide',
            'donation_faq_show_hide',

            'donation_social_icons_show_hide',
            'donation_recent_donors_show_hide',
            'donation_single_reward_heading',
            'donation_single_reward_image',
            'donation_single_reward_title',
            'donation_login_user_donate_show_hide',
            'minimum_donation_amount',
            'minimum_donation_amount',

            'donation_custom_amount_once',
            'donation_custom_amount_monthly',
            'how_many_days_ago_user_get_recuring_notification',
            'donor_page_post_items',
        ];

        foreach ($fields as $field) {
            update_static_option($field, $request->$field);
        }

        return redirect()->back()->with(FlashMsg::settings_update());
    }


    public function donation_approve(Request $request)
    {
        $this->validate($request, [
            'id' => 'required'
        ]);
        $msg = __('Approve Success');
        $cause = Cause::findOrFail($request->id);
        $cause->status = 'publish';
        $cause->save();

        \App\Helpers\AuditLogger::record('campaign_approve', 'Cause', $cause->id, ['status' => 'publish']);

        /* seal the medical document hash at approval time (tamper-evidence anchor) */
        \App\Services\DocumentIntegrityService::stamp($cause);

        /* run full fraud analysis on approval so score is fresh at go-live */
        try { \App\Helpers\FraudEngine::analyzeCampaign($cause); } catch (\Throwable $e) {}

        $user_details = User::find($cause->user_id);
        if ($user_details->email){
            try{
                Mail::to($user_details->email)->send(new BasicMail([
                    'subject' => __('your campaign is approve'),
                    'message' => __('congrats').'<br>'.__('your campaign is now live'),
                ]));
            }catch(\Exception $e){
                return back()->with(['msg' => $msg, 'type' => 'success']);
                return redirect()->back()->with(['msg' => $msg.' '.__(',notification mail send failed'), 'type' => 'success']);
            }

            $msg .= ' '.__(',notification mail send');
        }
        return back()->with(['msg' => $msg, 'type' => 'success']);
    }

    /**
     * Admin manually flags a user-submitted campaign as fraud.
     * Marks it flagged in DB, writes a FraudReport, and notifies the owner.
     * Mirror it on-chain with: npx hardhat run scripts/flag-campaign.js --network localhost (id + score via env)
     */
    public function flag_fraud(Request $request, $id)
    {
        $cause = Cause::findOrFail($id);
        $score = max((int) ($cause->fraud_score ?? 0), 85);

        $cause->status = 'flagged';
        $cause->fraud_score = $score;
        if (Schema::hasColumn('causes', 'verification_status')) {
            $cause->verification_status = 'rejected';
        }
        $cause->save();

        \App\FraudReport::create([
            'campaign_id' => $cause->id,
            'fraud_score' => $score,
            'risk_level' => 'high',
            'check_results' => [
                'type' => 'admin_manual_flag',
                'flags' => ['admin_review_rejected'],
            ],
            'evidence' => [
                'admin_manual_flag' => [
                    'pass' => false,
                    'points' => 0,
                    'detail' => 'Campaign rejected by administrator during manual review of pending submission.',
                ],
            ],
            'recommendation' => 'BLOCKED',
            'status' => 'flagged',
        ]);

        \App\Helpers\AuditLogger::record('campaign_flag_fraud', 'Cause', $cause->id, ['fraud_score' => $score]);

        try {
            $owner = User::find($cause->user_id);
            if ($owner && $owner->email) {
                Mail::to($owner->email)->send(new BasicMail([
                    'subject' => __('Your campaign was rejected'),
                    'message' => __('Our review team flagged your campaign for policy violations. It cannot accept donations. Contact support if you believe this is a mistake.'),
                ]));
            }
        } catch (\Throwable $e) {}

        return back()->with(['msg' => __('Campaign flagged as fraud and blocked from donations'), 'type' => 'danger']);
    }

    /** Recompute the medical document SHA-256 and compare against the approval-time seal */
    public function verify_document(Request $request, $causeId)
    {        $cause = Cause::findOrFail($causeId);
        $result = \App\Services\DocumentIntegrityService::verify($cause);

        switch ($result['status']) {
            case 'match':
                $cause->forceFill(['document_verified_at' => now()])->save();
                $msg = __('Document integrity VERIFIED — file matches its approval-time SHA-256 seal.');
                $type = 'success';
                \App\Helpers\AuditLogger::record('document_verify', 'Cause', $causeId, [
                    'result' => 'match',
                    'hash' => substr((string) $result['stored'], 0, 16),
                ]);
                break;
            case 'mismatch':
                $msg = __('TAMPER ALERT: document content differs from the sealed hash recorded at approval!');
                $type = 'danger';
                \App\Helpers\AuditLogger::record('document_tamper_alert', 'Cause', $causeId, [
                    'result' => 'mismatch',
                    'stored' => substr((string) $result['stored'], 0, 16),
                    'computed' => substr((string) $result['computed'], 0, 16),
                ]);
                break;
            case 'unsealed':
                $hash = \App\Services\DocumentIntegrityService::stamp($cause);
                $msg = __('No previous seal found. Document is now sealed with SHA-256: ') . substr((string) $hash, 0, 16) . '…';
                $type = 'info';
                break;
            case 'missing_file':
                $msg = __('Sealed hash exists but the referenced file is missing from storage!');
                $type = 'danger';
                break;
            default:
                $msg = __('This campaign has no medical document attached.');
                $type = 'warning';
        }

        return back()->with(['msg' => $msg, 'type' => $type]);
    }


        public function single_variant()
        {
            return view(self::BASE_PATH.'single-page-variant');
        }

        public function update_single_variant(Request $request)
        {
            $this->validate($request, [
                'donation_single_page_variant' => 'required|string'
            ]);
            update_static_option('donation_single_page_variant', $request->donation_single_page_variant);
            return redirect()->back()->with(['msg' => __('Donation Single Page Variant Updated..'), 'type' => 'success']);
        }

    public function donated_donors($id){
        if (empty($id) && !is_int($id)){
            abort(404);
        }
        $cause = Cause::findOrfail($id);
        $selected_winner = !empty($cause->winners_donation_ids) ? json_decode($cause->winners_donation_ids) : [];
        $winners = [];
        if (!empty($selected_winner)){
            $winners = CauseLogs::whereIn('id',$selected_winner)->get();
        }
        if (\request()->ajax()){
            $donation_logs = CauseLogs::where(['status' => 'complete','cause_id' => $id])->orderBy('id','desc')->get();
            return DataTables::of($donation_logs)
                ->addIndexColumn()
                ->addColumn('checkbox',function ($row){
                    return General::bulkCheckbox($row->id);
                })
                ->addColumn('info',function ($row){
                    return Donation::paymentInfoColumn($row);
                })
                ->addColumn('status',function ($row){
                    return General::statusSpan($row->status);
                })
                ->addColumn('action', function($row){
                    $admin = auth()->guard('admin')->user();
                    $action = '';
                    if ($admin->can('donation-payment-delete')){
                        $action .= General::deletePopover(route('admin.donations.payment.delete',$row->id));
                    }
                    if ($admin->can('donation-payment-edit')){
                        if($row->payment_gateway == 'manual_payment' && $row->status == 'pending'){
                            $action .= General::paymentAccept(route('admin.donations.payment.approve',$row->id));
                        }
                        if($row->status == 'complete'){
                            $action .= General::invoiceBtn(route('frontend.donation.invoice.generate'),$row->id);
                        }
                        if(!empty($row->user_id) && $row->status == 'pending'){
                            $action .= General::reminderMail(route('admin.donation.reminder'),$row->id);
                        }
                    }

                    return $action;
                })
                ->rawColumns(['action','checkbox','info','status'])
                ->make(true);
        }

        return view(self::BASE_PATH .'donation-payment-logs',compact('id','cause','winners'));
    }

    public function type(){
        return response()->json(["success" => true]);
    }

}

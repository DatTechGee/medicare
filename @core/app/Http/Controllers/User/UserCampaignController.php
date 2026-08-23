<?php

namespace App\Http\Controllers\User;

use App\Cause;
use App\CauseCategory;
use App\Gift;
use App\Helpers\FraudEngine;
use App\Http\Controllers\Controller;
use App\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\BasicMail;
use Illuminate\Support\Str;

class UserCampaignController extends Controller
{
    public const BASE_PATH = 'frontend.user.dashboard.';
  
    public function __construct(){
        $this->middleware('auth');
    }
  
    public function all_campaign(){
        $auth_id = auth()->guard('web')->user()->id;
        $all_donations = Cause::where('user_id',$auth_id)->get();
        return view(self::BASE_PATH.'campaigns.all-campaigns')->with(['all_donations' => $all_donations]);
    }
  
    public function new_campaign(){
        $all_category = CauseCategory::where(['status' => 'publish'])->get();
        $all_gifts = Gift::where(['creator_id'=> Auth::guard('web')->id(),'creator_type'=>'user','status' => 'publish'])->get();
        return view(self::BASE_PATH.'campaigns.new-campaign')->with(['all_category' => $all_category, 'all_gifts'=>$all_gifts]);
    }
  
    public function store_campaign(Request $request){
        $this->validate($request,[
            'title' => 'required|string',
            'slug' => 'nullable|string',
            'cause_content' => 'required|string',
            'amount' => 'required|string',
            'status' => 'nullable|string',
            'image' => 'nullable|string',
            'meta_tags' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'deadline' => 'nullable|string',
            'meta_title' => 'nullable|string',
            'excerpt' => 'nullable|string',
            'categories_id' => 'required|string',
            'og_meta_title' => 'nullable|string',
            'og_meta_description' => 'nullable|string',
            'og_meta_image' => 'nullable|string',
            'wallet_address' => ['nullable','string','regex:/^0x[a-fA-F0-9]{40}$/'],
            'patient_name' => 'nullable|string|max:191',
            'hospital_name' => 'nullable|string|max:191',
        ],[
            'title.required' => __('title is required'),
            'cause_content.required' => __('donation content is required'),
            'amount.required' => __('amount is required'),
            'status.required' => __('status is required'),
            'categories_id.required' => __('category is required'),
            'wallet_address.regex' => __('Invalid wallet address format'),
        ]);

        $faq_item = $request->faq ?? ['title' => ['']];


        $slug = !empty($request->slug) ? Str::slug($request->slug ) : Str::slug($request->title);
        $slug_check = Cause::where(['slug' => $slug])->count();
        $cause_slug = $slug_check >= 1 ? $slug.'-2' : $slug;

       $campaign_id =  Cause::create([
            'cause_update_id' => 0,
            'title' => $request->title,
            'slug' =>  $cause_slug,
            'cause_content' => $request->cause_content,
            'amount' => $request->amount,
            'status' => 'pending',
            'image' => $request->image,
            'deadline' => $request->deadline,
            'image_gallery' => $request->image_gallery,
            'medical_document' => $request->medical_document,
            'faq' => serialize($faq_item),
            'user_id' => Auth::guard('web')->user()->id,
            'created_by' => 'user',
            'excerpt' => $request->excerpt,
            'meta_title' => $request->meta_title,
            'categories_id' => $request->categories_id,
            'meta_tags' => $request->meta_tags,
            'meta_description' => $request->meta_description,
            'og_meta_title' => $request->og_meta_title,
            'og_meta_description' => $request->og_meta_description,
            'og_meta_image' => $request->og_meta_image,
            'gift_status' => $request->gift_status,
            'wallet_address' => strtolower($request->wallet_address ?? optional(Auth::guard('web')->user())->wallet_address),
            'patient_name' => $request->patient_name,
            'hospital_name' => $request->hospital_name,
        ]);

        if(!empty($campaign_id)){
            Notification::create([
               'user_campaign_id'=>$campaign_id->id,
               'title'=> __('New user campaign created'),
               'type'=> __('user_campaign'),
            ]);

            // Fraud engine runs in advisory mode only: the score is stored for the
            // admin's reference. All verification decisions are made by the admin.
            try {
                $fraudResult = FraudEngine::analyzeCampaign($campaign_id);
            } catch (\Throwable $e) {
                $fraudResult = ['score' => 0, 'risk_level' => 'low', 'recommendation' => 'PENDING_REVIEW', 'evidence' => []];
            }
            $campaign_id->update([
                'verification_status' => 'pending',
                'fraud_score' => $fraudResult['score'] ?? 0,
            ]);

          $campaign_id->gift()->attach($request->gifts);
        }


      	$msg = __('notify to admin');
        $admin_email = get_static_option('site_global_email');
        $message = __('Hello').'<br>';
        $message .= '<p>'.__('A new campaign created by');
        $message .= ' '.optional(auth()->guard('web')->user())->name;
        $message .= ' '.__('checkout admin panel for approve it.').'</p>';
        try {
            Mail::to($admin_email)->send(new BasicMail([
                'subject' => __('a new campaign created by user'),
                'message' => $message
            ]));
        }catch (\Exception $e){
            $msg = __('notify to admin failed');
        }



        $flash = [
            'msg' => __('Campaign submitted! It is now waiting for admin approval — the review team checks every detail manually.').' '.$msg,
            'type' => 'success',
            'fraud_report' => [
                'score'         => $fraudResult['score'] ?? 0,
                'risk_level'    => $fraudResult['risk_level'] ?? 'low',
                'recommendation'=> $fraudResult['recommendation'] ?? 'PENDING_REVIEW',
                'status'        => $campaign_id->verification_status ?? null,
                'draft'         => false,
                'evidence'      => collect($fraudResult['evidence'] ?? [])
                    ->map(function ($e, $key) {
                        return [
                            'check'  => $key,
                            'pass'   => (bool) ($e['pass'] ?? false),
                            'points' => (int) ($e['points'] ?? 0),
                            'detail' => $e['detail'] ?? '',
                        ];
                    })->values()->all(),
            ],
        ];

        return redirect()->route('user.campaign.new')->with($flash);
    }
  
    public function edit_campaign($id){

        $donation = Cause::find($id);
        $all_category = CauseCategory::all();
        $all_gifts = Gift::where(['creator_id'=> Auth::guard('web')->id(),'creator_type'=>'user','status' => 'publish'])->get();

        return view('frontend.user.dashboard.campaigns.edit-campaign')->with([
            'donation' => $donation,
            'all_category' => $all_category,
            'all_gifts' => $all_gifts
        ]);
    }

    public function update_campaign(Request $request){
        $this->validate($request,[
            'title' => 'required|string',
            'slug' => 'nullable|string',
            'cause_content' => 'required|string',
            'amount' => 'required|string',
            'status' => 'nullable|string',
            'image' => 'nullable|string',
            'meta_tags' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_title' => 'nullable|string',
            'deadline' => 'nullable|string',
            'excerpt' => 'nullable|string',
            'categories_id' => 'required|string',
            'wallet_address' => ['nullable','string','regex:/^0x[a-fA-F0-9]{40}$/'],
            'patient_name' => 'nullable|string|max:191',
            'hospital_name' => 'nullable|string|max:191',
        ],
            [
                'title.required' => __('title is required'),
                'cause_content.required' => __('donation content is required'),
                'amount.required' => __('amount is required'),
                'status.required' => __('status is required'),
                'categories_id.required' => __('category is required'),
            ]);
        $faq_item = $request->faq ?? ['title' => ['']];

        $slug = !empty($request->slug) ? Str::slug($request->slug) : Str::slug($request->title);
        $slug_check = Cause::where(['slug' => $slug])->count();
        $cause_slug = $slug_check > 1 ? $slug.'-3' : $slug;

        $cause = Cause::findOrFail($request->donation_id);
        abort_unless($cause->user_id === auth()->guard('web')->id(), 403);
        $cause->gift()->detach();
        $cause->gift()->attach($request->gifts);

        $cause->update([
            'title' => $request->title,
            'slug' => $cause_slug,
            'cause_content' => $request->cause_content,
            'amount' => $request->amount,
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
            'og_meta_title' => $request->og_meta_title,
            'og_meta_description' => $request->og_meta_description,
            'og_meta_image' => $request->og_meta_image,
            'gift_status' => $request->gift_status,
            'wallet_address' => strtolower($request->wallet_address ?? optional(Auth::guard('web')->user())->wallet_address),
            'patient_name' => $request->patient_name,
            'hospital_name' => $request->hospital_name,
        ]);

        // Advisory rescore only — verification status remains under admin control
        try {
            $fraudResult = FraudEngine::analyzeCampaign($cause);
            $cause->update(['fraud_score' => $fraudResult['score'] ?? 0]);
            $score_msg = ' | ' . __('Fraud Score') . ': ' . $fraudResult['score'] . '/100';
        } catch (\Throwable $e) {
            $score_msg = '';
        }

        return redirect()->back()->with(['msg' => __('Campaign Updated...') . $score_msg, 'type' => 'success']);
    }

    public function delete_campaign(Request $request,$id){
        Cause::where(['user_id' => auth()->guard('web')->user()->id,'id' => $id])->delete();
        return redirect()->back()->with(['msg' => __('Campaign Deleted...'),'type' => 'danger']);
    }

}

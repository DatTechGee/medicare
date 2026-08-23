<?php

namespace App\Http\Controllers\Api;
use App\Cause;
use App\Helpers\MediaHelper;
use App\Http\Controllers\Controller;
use App\Mail\BasicMail;
use App\Notification;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class UserCampaignController extends Controller
{
    public function user_created_campaign_list()
    {
        $auth_id = auth()->guard('sanctum')->user()->id;
        $all_donations = Cause::where('user_id',$auth_id)->get();
        
        $filter_donation = $all_donations->transform(function($item){
            
        $image_url = null;
            if(!empty($item->image)){
                $img_details = get_attachment_image_by_id($item->image);
                $item->image = $img_details['img_url'] ?? null;
            }
            return $item;
        });
        
        
        return response()->json([
            'all_campaigns' => $filter_donation,
            
        ]);
    }

    public function user_created_campaign_store(Request $request)
    {

        $validate = Validator::make($request->all(),[
            'title' => 'required|string',
            'cause_content' => 'required|string',
            'amount' => 'required|string',
            'categories_id' => 'required|string',
        ],[
            'title.required' => __('title is required'),
            'cause_content.required' => __('donation content is required'),
            'amount.required' => __('amount is required'),
            'status.required' => __('status is required'),
            'categories_id.required' => __('category is required'),
        ]);


        if ($validate->fails()){
            return response()->json([
                'validation_errors' => $validate->messages()
            ])->setStatusCode(422);
        }


         $faq_item =  $request->faq ? json_decode($request->faq,true) : NULL;


        $slug = !empty($request->slug) ? Str::slug($request->slug ) : Str::slug($request->title);
        $slug_check = Cause::where(['slug' => $slug])->count();
        $cause_slug = $slug_check < 1 ? $slug.'-2' : $slug;

        $last_image_id = null;
        if($request->file('image')){
            MediaHelper::insert_media_image_for_api($request);
            $last_image_id = DB::getPdo()->lastInsertId();
        }

        $campaign_id =  Cause::create([
            'cause_update_id' => 0,
            'title' => $request->title,
            'slug' =>  $cause_slug,
            'cause_content' => $request->cause_content,
            'amount' => $request->amount,
            'status' => 'pending',
            'image' => $last_image_id,
            'deadline' => $request->deadline ?? null,
            'medical_document' => $request->medical_document ?? null,
            'faq' => serialize($faq_item),
            'user_id' => Auth::guard('sanctum')->user()->id,
            'created_by' => 'user',
            'excerpt' => $request->excerpt ?? null,
            'meta_title' => $request->meta_title ?? null,
            'categories_id' => $request->categories_id,
            'meta_tags' => $request->meta_tags ?? null,
            'meta_description' => $request->meta_description ?? null,
            'og_meta_title' => $request->og_meta_title ?? null,
            'og_meta_description' => $request->og_meta_description ?? null,
            'og_meta_image' => $request->og_meta_image ?? null,
            'gift_status' => $request->gift_status ?? null,
        ]);


        if(!empty($campaign_id)){
            Notification::create([
                'user_campaign_id'=>$campaign_id->id,
                'title'=> __('New user campaign created'),
                'type'=> __('user_campaign'),
            ]);

            $campaign_id->gift()->attach($request->gifts) ?? [];
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

        return response()->json([
            'msg' => __('New Campaign Added, Waiting for admin approval').' '.$msg,
            'type' => 'success'
        ]);
    }

    public function user_created_campaign_update(Request $request, $id)
    {

        $validate = Validator::make($request->all(),[
            'title' => 'required|string',
            'cause_content' => 'required|string',
            'amount' => 'required|string',
            'categories_id' => 'required|string',
        ],[
            'title.required' => __('title is required'),
            'cause_content.required' => __('donation content is required'),
            'amount.required' => __('amount is required'),
            'status.required' => __('status is required'),
            'categories_id.required' => __('category is required'),
        ]);


        if ($validate->fails()){
            return response()->json([
                'validation_errors' => $validate->messages()
            ])->setStatusCode(422);
        }


         $faq_item =  $request->faq ? json_decode($request->faq,true) : NULL;
         
        $slug = !empty($request->slug) ? Str::slug($request->slug) : Str::slug($request->title);
        $slug_check = Cause::where(['slug' => $slug])->count();
        $cause_slug = $slug_check > 1 ? $slug.'-3' : $slug;

        $cause = Cause::find($id);
        $cause->gift()->detach();
        $cause->gift()->attach($request->gifts);

        $last_image_id = $cause->image;
        if($request->file('image')){
            MediaHelper::insert_media_image_for_api($request);
            $last_image_id = DB::getPdo()->lastInsertId();
        }

        Cause::findOrFail($id)->update([
            'title' => $request->title,
            'slug' => $cause_slug,
            'cause_content' => $request->cause_content,
            'amount' => $request->amount,
            'image' => $last_image_id,
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
        ]);

        return response()->json([
            'msg' => __('Campaign Updated Successfully'),
            'type' => 'success'
        ]);
    }

    public function user_created_campaign_delete($id)
    {
        $cause = Cause::findOrFail($id);
        $cause->delete();

        return response()->json([
            'msg' => __('Campaign Deleted Successfully'),
            'type' => 'danger'
        ]);
    }

    public function user_campaign_permission()
    {
        $user = User::where('id',Auth::guard('sanctum')->user()->id)->first();
        return response()->json([
           'user_campaign_permission' => $user->campaign_permission
        ]);
    }


}

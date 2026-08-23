<?php

namespace App\Http\Controllers\Api;


use App\Admin;
use App\Cause;
use App\CauseLogs;
use App\Comment;
use App\Country;
use App\EventAttendance;
use App\EventPaymentLogs;
use App\Helpers\FlashMsg;
use App\Helpers\MediaHelper;
use App\Http\Controllers\Controller;
use App\Mail\BasicMail;
use App\Mail\DonationWithdrawRequest;
use App\RewardRedeem;
use App\SupportTicket;
use App\SupportTicketMessage;
use App\SupportTicketDepartment;
use App\User;
use App\UserFollow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'email' => 'required|email|max:191',
            'password' => 'required',
        ]);

        if ($validate->fails()){
            return response()->json([
                'validation_errors' => $validate->messages()
            ])->setStatusCode(422);
        }
        
        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'message' => __('invalid Email'),
            ])->setStatusCode(422);
        }
        
        $user = User::select('id', 'email', 'password')->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => __('Invalid Email or Password')
            ])->setStatusCode(422);
        } else {
            $token = $user->createToken(Str::slug(get_static_option('site_title', 'zaika')) . 'api_keys')->plainTextToken;

            return response()->json([
                'users' => $user,
                'token' => $token,
            ]);
        }
    }



    //register api
    public function register(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'full_name' => 'required|max:191',
            'email' => 'required|email|unique:users|max:191',
            'username' => 'required|unique:users|max:191',
            'password' => 'required|min:6|max:191',
            'country_id' => 'required',
            'state' => 'nullable',
        ]);
        if ($validate->fails()){
            return response()->json([
                'validation_errors' => $validate->messages()
            ],422);
        }
        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'message' => __('invalid Email'),
            ])->setStatusCode(422);;
        }
        //todo check user exists or not
        $userInfo =  User::where('email' , $request->email)->orWhere('username' , $request->username)->first();
        if(!is_null($userInfo)){
             $token = $userInfo->createToken(Str::slug(get_static_option('site_title', 'fundorex')) . 'api_keys')->plainTextToken;
                return response()->json([
                    'users' => $userInfo,
                    'token' => $token,
                ]);
        }
            $user = User::create([
                'name' => $request->full_name,
                'email' => $request->email,
                'username' => $request->username,
                'country_id' => $request->country_id,
                'city' => $request->city,
                'password' => Hash::make($request->password),
            ]);

            if (!is_null($user)) {
                $token = $user->createToken(Str::slug(get_static_option('site_title', 'fundorex')) . 'api_keys')->plainTextToken;
                return response()->json([
                    'users' => $user,
                    'token' => $token,
                ]);
            }

        return response()->json([
            'message' => __('Something Went Wrong'),
        ])->setStatusCode(422);
    }

    // send otp
    public function sendOTPSuccess(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'user_id' => 'required|integer',
            'email_verified' => 'required|integer',
        ]);
        if ($validate->fails()){
            return response()->json([
                'validation_errors' => $validate->messages()
            ])->setStatusCode(422);
        }
        
        if(!in_array($request->email_verified,[0,1])){
            return response()->json([
                'message' => __('email verify code must have to be 1 or 0'),
            ])->setStatusCode(422);
        }
        
        $user = User::where('id', $request->user_id)->update([
            'email_verified' =>  $request->email_verified
        ]);
         
         if(is_null($user)){
            return response()->json([
                'message' => __('Something went wrong, plese try after sometime,'),
            ])->setStatusCode(422);
         }
         
        return response()->json([
            'message' => __('Email Verify Success'),
        ]);
    }   
    
     public function sendOTP(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'email' => 'required',
        ]);
        if ($validate->fails()){
            return response()->json([
                'validation_errors' => $validate->messages()
            ])->setStatusCode(422);
        }
        $otp_code = sprintf("%d", random_int(1234, 9999));
        $user_email = User::where('email', $request->email)->first();

        if (!is_null($user_email)) {
            try {
                $message_body = __('Here is your otp code') . ' <span class="verify-code">' . $otp_code . '</span>';
                Mail::to($request->email)->send(new BasicMail([
                    'subject' => __('Your OTP Code'),
                    'message' => $message_body
                ]));
            } catch (\Exception $e) {
                return response()->json([
                    'message' => $e->getMessage(),
                ])->setStatusCode(422);
            }

            return response()->json([
                'email' => $request->email,
                'otp' => $otp_code,
            ]);
            
        }
        
        return response()->json([
            'message' => __('Email Does not Exists'),
        ])->setStatusCode(422);

    }

    //reset password
    public function resetPassword(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'email' => 'required',
            'password' => 'required',
        ]);
        if ($validate->fails()){
            return response()->json([
                'validation_errors' => $validate->messages()
            ])->setStatusCode(422);
        }
        $email = $request->email;
        $user = User::select('email')->where('email', $email)->first();
        if (!is_null($user)) {
            User::where('email', $user->email)->update([
                'password' => Hash::make($request->password),
            ]);
            return response()->json([
                'message' => 'success',
            ]);
        } else {
            return response()->json([
                'message' => __('Email Not Found'),
            ])->setStatusCode(422);
        }
    }

    //logout
    public function logout(){
        auth()->user()->tokens()->delete();
        return response()->json([
            'message' => __('Logout Success'),
        ]);
    }

    //User Profile
    public function profile(){
        $user_id = auth('sanctum')->user();

        $user = User::with('country:id,name')->select('id','name','email','phone','image','zipcode','city','country_id','address','state')
            ->where('id',$user_id->id)->first();

        $image_url = null;
        if(!empty($user->image)){
            $img_details = get_attachment_image_by_id($user->image);
            $image_url = $img_details['img_url'] ?? null;
        }
        $user->image = $image_url ?  : null;

        return response()->json([
            'user_details' => $user
        ]);
    }

//    change password after login
    public function changePassword(Request $request){
        $validate = Validator::make($request->all(),[
            'current_password' => 'required|min:6',
            'new_password' => 'required|min:6',
        ]);
        if ($validate->fails()){
            return response()->json([
                'validation_errors' => $validate->messages()
            ])->setStatusCode(422);
        }

        $user = User::select('id','password')->where('id', auth('sanctum')->user()->id)->first();
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => __('Current Password is Wrong'),
            ])->setStatusCode(422);
        }
        User::where('id',auth('sanctum')->user()->id)->update([
            'password' => Hash::make($request->new_password),
        ]);
        return response()->json([
            'current_password' => $request->current_password,
            'new_password' => $request->new_password,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth('sanctum')->user();
        $user_id = auth('sanctum')->user()->id;

        $this->validate($request, [
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:users,id,'.$request->user_id,
            'phone' => 'nullable|string|max:191',
            'state' => 'nullable|string|max:191',
            'city' => 'nullable|string|max:191',
            'country_id' => 'nullable|string|max:191',
            'address' => 'nullable|string',
        ], [
            'name.' => __('name is required'),
            'email.required' => __('email is required'),
            'email.email' => __('provide valid email'),
        ]);


        if($request->file('image')){
            MediaHelper::insert_media_image_for_api($request);
            $last_image_id = DB::getPdo()->lastInsertId();
        }

        User::find($user_id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'image' => $last_image_id ?? $user->image,
                'phone' => $request->phone,
                'state' => $request->state,
                'city' => $request->city,
                'country_id' => $request->country_id,
                'address' => $request->address,
                'zipcode' => $request->zipcode
                ]);

        return response()->json(['success' => true]);
    }


    public function country_list()
    {
        $country = Country::select('id', 'name')->orderBy('name', 'asc')->get();
        return response()->json([
            'countries' => $country
        ]);
    }


    public function dashboard(){
        $user = auth('sanctum')->user();
        $event_attendances = EventAttendance::where('user_id', $user->id)->count();
        $donation = CauseLogs::where('user_id',$user->id)->count();
        $campaigns = Cause::where('user_id',$user->id)->count();
        $total_reward_points = CauseLogs::where('user_id', $user->id)->sum('reward_point');

        return response()->json([
            'events_booked' => $event_attendances,
            'total_donation' => $donation,
            'total_campaigns' => $campaigns,
            'total_reward_points' => $total_reward_points,
        ]);
    }

    public function user_donations(){

        $user = auth('sanctum')->user();
        $donations = CauseLogs::with('cause:id,title')
                        ->select('id','status','cause_id','amount','payment_gateway','created_at')
                        ->where('user_id',$user->id)->orderBy('id', 'DESC')->paginate(10)->withQueryString();

        return response()->json([
            'donations' => $donations,
        ]);
    }

    public function followed_user_campaigns(){

        $auth_user = auth('sanctum')->user();
        $all_follower_donations = UserFollow::select('id','user_type','campaign_owner_id')->where(['follow_status' => 'follow','user_id'=> $auth_user->id])->get()
            ->transform(function($item){
                if($item->user_type == 'user'){
                    $item->campaign_owner_name = User::findOrFail($item->campaign_owner_id)->name;
                    $item->campaign_owner_campaign_item = Cause::where('user_id',$item->campaign_owner_id)->count();
                }elseif ($item->user_type == 'admin'){
                    $item->campaign_owner_name = Admin::findOrFail($item->campaign_owner_id)->name;
                    $item->campaign_owner_campaign_item = Cause::where('admin_id',$item->campaign_owner_id)->count();
                }
                return $item;
            });

        return response()->json([
           'data' =>$all_follower_donations
        ]);
    }


    public function followed_user_campaigns_list($id)
    {

        if(empty($id)){
            abort(404);
        }

        $user = auth('sanctum')->user();
        $user_follow_data = UserFollow::where(['user_id' =>$user->id, 'campaign_owner_id' => $id])->get();

        foreach ($user_follow_data as $data){
            $all_following_user_campaigns = Cause::select('id','title','amount','raised','image','deadline')->where('user_id',$data->campaign_owner_id)->orWhere('admin_id',$data->campaign_owner_id)->paginate(20)->withQueryString();
            $filter_data = $all_following_user_campaigns->through(function($item){
                $item->reamaining_time = $item->deadline ?? null;
                $image_url = null;
                if(!empty($item->image)){
                    $img_details = get_attachment_image_by_id($item->image);
                    $item->image = $img_details['img_url'] ?? null;
                }
                return $item;
            });

        }

        $pagination = [
            "current_page" => $all_following_user_campaigns->currentPage(),
            "last_page" => $all_following_user_campaigns->lastPage(),
            "per_page" => $all_following_user_campaigns->perPage(),
            "path" => $all_following_user_campaigns->path(),
            "links" => $all_following_user_campaigns->getUrlRange(0,$all_following_user_campaigns->lastPage()),
            "data" => $filter_data
        ];

        return response()->json([
            'data' => $pagination,
            'campaign_user_id' => $id
        ]);
    }

    public function reward_points()
    {
        $user = auth('sanctum')->user();
        $all_reward_points =  CauseLogs::with('cause:id,title')->select('id','cause_id','reward_point','reward_amount','created_at')->where('user_id', $user->id)->where('reward_point', '>',0)->get();

        return response()->json([
            'reward_points' => $all_reward_points,
        ]);
    }

    public function allTickets()
    {
        $all_tickets = SupportTicket::select('id','title','description','subject','priority','status')
        ->where('user_id',auth('sanctum')->id())->orderBy('id','Desc')
        ->paginate(10)
        ->withQueryString();
        
        return response()->json([
            'user_id'=> auth('sanctum')->id(),
            'tickets' => $all_tickets,
        ]);
    }


    public function get_all_tickets(){
        $user_id = auth('sanctum')->user()->id;
        $all_tickets = SupportTicket::where('user_id', $user_id)->paginate(10)->withQueryString();

        return $all_tickets;
    }

    public function single_ticket($id){

        $user_id = auth('sanctum')->user()->id;

        $ticket_details = SupportTicket::where('user_id', $user_id)
            ->where("id",$id)
            ->first();
            $all_messages = SupportTicketMessage::where(['support_ticket_id' => $id])->get()->transform(function ($item){
            $item->attachment = !empty($item->attachment) ? asset('assets/uploads/ticket/'.$item->attachment) : null;
            return $item;
        });

        return response()->json(["ticket_details" => $ticket_details,"all_messages" => $all_messages]);
    }

    public function fetch_support_chat($ticket_id){
        $all_messages = SupportTicketMessage::where(['support_ticket_id' => $ticket_id])->get()->transform(function ($item){
            $item->attachment = !empty($item->attachment) ? asset('assets/uploads/ticket/'.$item->attachment) : null;
            $item->message = purify_html($item->message);

            return $item;
        });
        return response()->json($all_messages);
    }

    public function send_support_chat(Request $request,$ticket_id){

        $this->validate($request, [
            'user_type' => 'nullable|string|max:191',
            'message' => 'required',
            'send_notify_mail' => 'nullable|string',
            'file' => 'nullable',
        ]);

        $ticket_info = SupportTicketMessage::create([
            'support_ticket_id' => $ticket_id,
            'type' => $request->user_type ?? 'customer',
            'message' => $request->message,
            'notify' => $request->send_notify_mail ? 'on' : 'off',
            'attachment' => null,
        ]);

        if ($request->hasFile('file')) {
            $uploaded_file = $request->file;
            $file_extension = $uploaded_file->getClientOriginalExtension();
            $file_name = pathinfo($uploaded_file->getClientOriginalName(), PATHINFO_FILENAME) . time() . '.' . $file_extension;
            $uploaded_file->move('assets/uploads/ticket', $file_name);
            $ticket_info->attachment = $file_name;
            $ticket_info->save();
        }

        $ticket = $ticket_info->toArray();
        $ticket["attachment"] = empty($ticket["attachment"]) ? null : asset('assets/uploads/ticket' . $ticket["attachment"]);

        return response()->json($ticket);
    }

    public function viewTickets(Request $request,$id= null)
    {
        $all_messages = SupportTicketMessage::where(['support_ticket_id'=>$id])->get()->transform(function($item){
            $item->attachment = !empty($item->attachment) ? asset('assets/uploads/ticket/'.$item->attachment) : null;
            return $item;
        });
        $q = $request->q ?? '';
        return response()->success([
            'ticket_id'=>$id,
            'all_messages' =>$all_messages,
            'q' =>$q,
        ]);
    }

    public function createTicket(Request $request){

        $uesr_info = auth('sanctum')->user()->id;

        $this->validate($request,[
            'title' => 'required|string|max:191',
            'subject' => 'required|string|max:191',
            'priority' => 'required|string|max:191',
            'description' => 'required|string',
            'departments' => 'nullable|string',
        ]);

        $ticket = SupportTicket::create([
            'title' => $request->title,
            'via' => $request->via,
            'operating_system' => null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'description' => $request->description,
            'subject' => $request->subject,
            'status' => 'open',
            'priority' => $request->priority,
            'user_id' => $uesr_info,
            'admin_id' => null,
            'departments' => $request->departments ?? 1
        ]);

        $msg = get_static_option('support_ticket_success_message') ?? __('Thanks for contact us, we will reply soon');
        return response()->json(["msg" => $msg,"ticket" => $ticket]);
    }


    public function reward_redeem_available_amount($user_id)
    {
        if(empty($user_id)){
            abort(404);
        }

        $cause_log_total_amount = CauseLogs::where([ 'user_id' => $user_id,'status' => 'complete'])->get()->pluck('reward_amount')->sum();
        $rewared_amount = $cause_log_total_amount;


        $all_withdraws = RewardRedeem::where([ 'user_id' => $user_id])->where('payment_status', '!=', 'reject')->get()->pluck('withdraw_request_amount')->sum();
        ///dd($all_withdraws);
        $available_amount = ($rewared_amount < $all_withdraws) ? 0 : $rewared_amount - $all_withdraws;

        return response()->json([
            'widrawable_available_amount' => $available_amount
        ]);

    }


    public function reward_redeem_submit(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'payment_gateway' => 'required',
            'withdraw_request_amount' => 'required',
            'payment_account_details' => 'required',
            'additional_comment_by_user' => 'nullable',
        ]);
        if ($validate->fails()){
            return response()->json([
                'validation_errors' => $validate->messages()
            ])->setStatusCode(422);
        }

        $user_id = auth('sanctum')->user()->id;
        $cause_log_total_amount = CauseLogs::where([ 'user_id' => $user_id,'status' => 'complete'])->get()->pluck('reward_amount')->sum();

        if($request->withdraw_request_amount < 1){
            return response()->json([
                'msg' => __('you can not withdraw less than').' '.amount_with_currency_symbol(1), 'type' => 'error'
            ])->setStatusCode(422);
        }

        $rewared_amount = $cause_log_total_amount;
        $all_withdraws = RewardRedeem::where([ 'user_id' => $user_id])->where('payment_status', '!=', 'reject')->get()->pluck('withdraw_request_amount')->sum();
        $available_amount = ($rewared_amount < $all_withdraws) ? 0 : $rewared_amount - $all_withdraws;

        if ($request->withdraw_request_amount > $available_amount) {
            return response()->json([ 'msg' => __('your redeem amount is greater than your reward amount'), 'type' => 'error'])->setStatusCode(422);
        }

        RewardRedeem::create([
            'donation_id' => null,
            'donation_log_id' => null,
            'user_id' => $user_id,
            'payment_gateway' => $request->payment_gateway,
            'withdraw_request_amount' => $request->withdraw_request_amount,
            'payment_account_details' => $request->payment_account_details,
            'additional_comment_by_user' => $request->additional_comment_by_user,
        ]);

        $admin_mail = get_static_option('site_global_email');

        try{
            Mail::to($admin_mail)->send(new DonationWithdrawRequest([
                'subject' => __('You have new reward redeem Message'),
                'user_name' => $user_details->name ?? __('user not found'),
                'amount' => $request->withdraw_request_amount,
            ]));

        }catch(\Exception $e){
            return response()->json(['msg' => $e->getMessage(), 'type' => 'error'])->setStatusCode(500);
        }

        return response()->json(['msg' => __('Your Redeem Request has been sent'), 'type' => 'success']);

    }


    public function user_follow_store(Request $request)
    {

        $request->validate([
            'user_id' => 'required',
            'campaign_owner_id' => 'required',
            'user_type' => 'required',
            'text' => 'required',
        ]);

        $auth_id = auth('sanctum')->user()->id;
        $campaign_owner_id = $request->campaign_owner_id;

        $user_type = $request->user_type;
        $text = $request->text == __('Follow') ? __('Following') : __('Follow');

        $follow_data = UserFollow::where('user_id', $auth_id)
            ->where('campaign_owner_id', $campaign_owner_id )
            ->first();

        if (!$follow_data) {
            $user_follow = UserFollow::create([
                'user_id' => $auth_id,
                'campaign_owner_id' => $campaign_owner_id,
                'user_type' => $user_type,
                'follow_status' => 'follow',
            ]);

            return response()->json([
                'type' => $user_follow ? 'success' : 'fail',
                'text' => $text,
                'follow_status' => $user_follow->follow_status,
            ]);
        }

        if ($follow_data) {
            $follow_data->update([
                'follow_status' => $follow_data->follow_status == 'follow' ? 'unfollow' : 'follow',
                'user_type' => $user_type,
            ]);
        }

        return response()->json([
            'type' => 'updated',
            'follow_status' => $follow_data->follow_status,
        ]);
    }


    public function check_followed($id){

        $auth_id = auth('sanctum')->user()->id;
        $user_follow = UserFollow::where([
            'user_id' => $auth_id,
            'campaign_owner_id' => $id,
            'follow_status' => 'follow',
        ])->first();

        if(!empty($user_follow)){
            return response()->json(true);
        }
        return response()->json(false);
    }


    public function user_event_booking()
    {
        $auth_id = auth('sanctum')->user()->id;
        $event_attendances = EventAttendance::with('event:id,date')->where('user_id',$auth_id)->orderBy('id', 'DESC')->paginate(10)->withQueryString();
        $filter_event_attendance = $event_attendances->transform(function($item){
            $item->payment_gateway = optional($item->log)->package_gateway;
            $item->payment_status = optional($item->log)->status;
            unset($item->log);
            return $item;
        });

        $event_pagination = [
            "current_page" => $event_attendances->currentPage(),
            "last_page" => $event_attendances->lastPage(),
            "per_page" => $event_attendances->perPage(),
            "path" => $event_attendances->path(),
            "links" => $event_attendances->getUrlRange(0,$event_attendances->lastPage()),
            "data" => $filter_event_attendance
        ];

        return response()->json([
            'booked_events' => $event_pagination
        ]);
    }

    public function event_order_cancel($id)
    {
        if(empty($id)){
            abort(404);
        }
        $auth_id = auth('sanctum')->user()->id;
        $order_details = EventAttendance::where(['id' => $id, 'user_id' => $auth_id])->first();

        EventAttendance::where('id', $order_details->id)->update([
            'status' => 'cancel'
        ]);

        $event_payment_log = EventPaymentLogs::where(['attendance_id' => $id])->first();
        $admin_mail = !empty(get_static_option('event_attendance_receiver_mail')) ? get_static_option('event_attendance_receiver_mail') : get_static_option('site_global_email');
        //send mail to admin
        $data['subject'] = __('one of your event booking order has been cancelled');
        $data['message'] = __('hello') . '<br>';
        $data['message'] .= __('your event attendance id') . ' #' . $order_details->id . ' ';
        $data['message'] .= __('has been cancelled by the user.');

        try{
            Mail::to($admin_mail)->send(new BasicMail($data));
        }catch(\Exception $e){
            return response()->json(['msg' => $e->getMessage(), 'type' => 'danger']);
        }

        if (!empty($event_payment_log)) {
            //send mail to customer
            $data['subject'] = __('your event booking has benn cancelled');
            $data['message'] = __('hello') . $event_payment_log->name . '<br>';
            $data['message'] .= __('your event attendance id') . ' #' . $order_details->id . ' ';
            $data['message'] .= __('booking status has been changed to cancel.');

            //send mail while order status change
            try{
                Mail::to($event_payment_log->email)->send(new BasicMail($data));
            }catch(\Exception $e){
                return response()->json(['msg' => $e->getMessage(), 'type' => 'danger']);
            }
        }

        return response()->json([
            'msg' => __('Order Canceled successfully..'), 'type' => 'success'
        ]);
    }


    public function comment_store(Request $request)
    {

        $validated = Validator::make($request->all(), [
            'comment_content' => 'required'
        ]);

        if($validated->fails()){
            return response()->json([
               'error' => $validated->messages()
            ]);
        }

        $auth_user = auth('sanctum')->user();

        $content = Comment::create([
            'cause_id' => $request->cause_id,
            'user_id' => $request->user_id ?? $auth_user->id,
            'commented_by' => $request->commented_by,
            'comment_content' => purify_html($request->comment_content),
        ]);

        Mail::to(get_static_option('site_global_email'))->send(new BasicMail([
            'subject' => __('You have a comment from') . ' ' . get_static_option('site_title'),
            'message' => __('you have a new comment submitted by') . ' ' . $auth_user->name . ' ' . __('Email') . ' ' . $auth_user->email . ' .' . __('check admin panel for more info'),
        ]));

        return response()->json([
            'msg' => __('Your comment sent successfully'),
            'type' => 'success',
            'status' => 'ok',
            'content' => $content,
        ]);
    }
    
    public function all_ticket_department()
    {
        $content = SupportTicketDepartment::select('id','name')->get();
        
       return response()->json([
            'content' => $content
        ]);
        
    }
    




}

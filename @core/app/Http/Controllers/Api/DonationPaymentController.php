<?php

namespace App\Http\Controllers\Api;

use App\Admin;
use App\Cause;
use App\CauseCategory;
use App\CauseLogs;
use App\CauseUpdate;
use App\Comment;
use App\Events;
use App\EventsCategory;
use App\Helpers\DonationHelpers;
use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use App\Notification;
use App\Recuring;
use App\Reward;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DonationPaymentController extends Controller
{
    public function pay(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191',
            'cause_id' => 'required|string',
            'amount' => 'required|string',
            'anonymous' => 'nullable|string',
            'selected_payment_gateway' => 'required|string',
        ],
            [
                'name.required' => __('Name field is required'),
                'email.required' => __('Email field is required'),
                'amount.required' => __('Amount field is required'),
            ]
        );

        if($validate->fails()){
            return response()->json([
                'validation_errors' => $validate->messages()
            ])->setStatusCode(422);
        }

        $selected_payment_gateway = $request->selected_payment_gateway;
        $manual_payment_attachment = $request->manual_payment_attachment;
        $validation = $request;
        $user_id = auth('sanctum')->user()->id;


        if(Auth::guard('sanctum')->check()) {
            if (!empty($cause->user_id) && $cause->user_id == $user_id) {
                return response()->json(['msg' => ('Campaign owner can not donate on this campaign..!'), 'type' => 'error']);
            }
        }

        $minimum_donation_amount = get_static_option('minimum_donation_amount');
        $msg = __('Minimum Donation Amount is : ');
        if (!empty($minimum_donation_amount) && $request->amount < $minimum_donation_amount) {
            return response()->json(['msg' => $msg . amount_with_currency_symbol($minimum_donation_amount) , 'type' => 'error']);
        }

        $cause_details = Cause::find($request->cause_id);
        if (empty($cause_details)) {
            return response()->json(['msg' => __('donation cause not found'), 'type' => 'danger']);
        }

        $admin_charge = $request->has('admin_tip') ? $request->admin_tip : DonationHelpers::get_donation_charge($request->amount, false);
        $amount = $request->amount;
        $minimum_goal_amount = Reward::where('status','publish')->orderBy('reward_goal_from','asc')->get()->min('reward_goal_from');

        if($cause_details->reward == 'on' && auth()->guard('sanctum')->check() && $amount >= $minimum_goal_amount){

            $reward_point = Reward::select('reward_point')
                ->where('status', 'publish')
                ->where('reward_goal_from', '<=', $amount)
                ->where('reward_goal_to', '>=', $amount)
                ->first();

            $reward_point = optional($reward_point)->reward_point ?? 0;
            if($reward_point > 0){
                $reward_amount = $reward_point / get_static_option('reward_amount_for_point');
            }
        }

            $payment_log_id = CauseLogs::create([
                'recuring_token' => NULL,
                'email' => $request->email ?? '',
                'name' => $request->name ?? '',
                'cause_id' => $request->cause_id,
                'gift_id' => $request->gift_id ?? NULL,
                'amount' => $amount,
                'admin_charge' => $admin_charge ?? NULL,
                'reward_point' => $reward_point ?? NULL,
                'reward_amount' => $reward_amount ?? NULL,
                'anonymous' => !is_null($request->anonymous) ? 1 : 0,
                'payment_gateway' => $selected_payment_gateway,
                'user_id' => $user_id,
                'status' => 'pending',
                'track' => Str::random(10) . Str::random(10),
                'custom_fields' => NULL,
                'attachments' =>  NULL,
            ])->id;

            $donation_payment_details = CauseLogs::find($payment_log_id);

            if(!empty($payment_log_id)){
                Notification::create([
                    'cause_log_id'=>$payment_log_id,
                    'title'=> 'New donation payment done',
                    'type' =>'cause_log',
                ]);
            }

            if ($selected_payment_gateway == 'manual_payment') {
                $this->validate($validation, [
                    'manual_payment_attachment' => 'required|file'
                ], ['manual_payment_attachment.required' => __('Bank Attachment Required')]);

                $fileName = time().'.'.$manual_payment_attachment->extension();
                $manual_payment_attachment->move('assets/uploads/attachment/', $fileName);
                CauseLogs::where(['cause_id'=>$request->cause_id, 'user_id'=> $user_id])->update(['manual_payment_attachment' => $fileName]);

                event(new Events\DonationSuccess([
                    'donation_log_id' => $donation_payment_details->id,
                    'transaction_id' => Str::random(14),
                ]));

                $current = CauseLogs::find($payment_log_id);
                $current->status = 'pending';
                $current->save();

                    return response()->json([
                        'order_id'=>$payment_log_id,
                        'msg' => __('Donation Success'),
                        'type' => 'success',
                        'success_url' =>  route('frontend.donation.payment.success',['id'=> random_int(123456,999999).$payment_log_id.random_int(123456,999999)]),
                        'cancel_url' =>  route('frontend.donation.payment.cancel'),
                    ]);

                }else{
                    event(new Events\DonationSuccess([
                        'donation_log_id' => $payment_log_id,
                        'transaction_id' => Str::random(14),
                    ]));
                 }

                $current = CauseLogs::find($payment_log_id);
                $current->status = 'pending';
                $current->save();

               return response()->json([
                   'msg'=> __('Donation Success'),'type'=> 'success',
                   'order_id' => $payment_log_id,
                   'success_url' =>  route('frontend.donation.payment.success',['id'=> random_int(123456,999999).$payment_log_id.random_int(123456,999999)]),
                   'cancel_url' =>  route('frontend.donation.payment.cancel'),
               ]);

            }


    public function payment_status_update($id){

        if(empty($id)){
            abort(404);
        }
        $order_details = CauseLogs::find($id);

        if(!is_null($order_details)){
            $order_details->status = 'complete';
            $order_details->save();

            return response()->json([
                'msg'=> __('Payment status updated'),
                'type' => 'success'
            ]);
        }

        return response()->json([
            'msg' => __('Payment status update failed'),
            'type'=> 'error'
        ]);
    }


}

<?php

namespace App\Http\Controllers\Api;

use App\Admin;
use App\Cause;
use App\CauseCategory;
use App\CauseLogs;
use App\CauseUpdate;
use App\Comment;
use App\EventAttendance;
use App\EventPaymentLogs;
use App\Events;
use App\EventsCategory;
use App\Http\Controllers\Controller;
use App\Mail\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EventPaymentController extends Controller
{
    public function pay(Request $request)
    {

        $validate = Validator::make($request->all(),[
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191',
        ],
            [
                'name.required' => __('Name field is required'),
                'email.required' => __('Email field is required')
            ]);

            if($validate->fails()){
                return response()->json([
                    'validation_errors' => $validate->messages()
                ]);
            }

        if (!get_static_option('disable_guest_mode_for_event_module') && !auth()->guard('sanctum')->check()){
            return response()->json(['type' => 'warning','msg' => __('login to place an order')]);
        }

        $selected_payment_gateway = $request->selected_payment_gateway ?? payment_gateway;
        $manual_payment_attachment = $request->manual_payment_attachment;
        $user_id = auth('sanctum')->id();

        $event_detials = Events::find($request->event_id);
        $event_attendance_id = EventAttendance::create([
            'custom_fields' => NULL,
            'status' => 'pending',
            'event_name' => $event_detials->title,
            'event_cost' => $event_detials->cost,
            'quantity' => $request->quantity,
            'event_id' => $request->event_id,
            'checkout_type' => NULL,
            'user_id' => $user_id,
            'attachment' => NULL
        ])->id;

        $event_details = EventAttendance::find($event_attendance_id);
        $event_info = Events::find($event_details->event_id);


            if (!empty($event_info->cost) && $event_info->cost > 0){
                $validate_two = Validator::make($request->all(),[
                    'selected_payment_gateway' => 'required|string'
                ],[
                    'selected_payment_gateway.required' => __('Select A Payment Method')
                ]);

                if($validate_two->fails()){
                    return response()->json([
                        'validation_error' => $validate_two->messages()
                    ])->setStatusCode(422);
                }
            }

            $payment_log_id = EventPaymentLogs::create([
                'email' =>  $request->email,
                'name' =>  $request->name,
                'event_name' => $event_details->event_name,
                'event_cost' => ($event_details->event_cost * $event_details->quantity),
                'package_gateway' => $request->selected_payment_gateway,
                'attendance_id' =>  $event_attendance_id,
                'status' => 'pending',
                'track' => Str::random(10). Str::random(10),
            ])->id;


        if ($selected_payment_gateway == 'manual_payment') {

            $validate_three =  Validator::make($request->all(), [
                'manual_payment_attachment' => 'required|file'
            ], ['manual_payment_attachment.required' => __('Bank Attachment Required')]);

            if($validate_three->fails()){
                return response()->json([
                    'validation_error' => $validate_three->messages()
                ])->setStatusCode(422);
            }

            $fileName = time().'.'.$manual_payment_attachment->extension();
            $manual_payment_attachment->move('assets/uploads/attachment/', $fileName);
            EventPaymentLogs::where(['attendance_id'=>$event_attendance_id])->update(['manual_payment_attachment' => $fileName]);


            event(new Events\AttendanceBooking([
                'attendance_id' => $event_attendance_id,
                'transaction_id' => Str::random(14)
            ]));


            $current = EventPaymentLogs::find($payment_log_id);
            $current->status = 'pending';
            $current->save();

            return response()->json([
                'order_id'=>$payment_log_id,
                'msg' => __('Event Booking Success'),
                'type' => 'success',
                'success_url' =>  route('frontend.event.payment.success',['id'=> random_int(123456,999999).$payment_log_id.random_int(123456,999999)]),
                'cancel_url' =>  route('frontend.event.payment.cancel',['id'=> random_int(123456,999999).$payment_log_id.random_int(123456,999999)]),
            ]);

        }else{

            event(new Events\AttendanceBooking([
                'attendance_id' => $event_attendance_id,
                'transaction_id' => Str::random(14)
            ]));
        }

        $current = EventPaymentLogs::find($payment_log_id);
        $current->status = 'pending';
        $current->save();

        return response()->json([
            'msg'=> __('Event Booking Success'),'type'=> 'success',
            'order_id' => $payment_log_id,
            'success_url' =>  route('frontend.event.payment.success', ['id'=> random_int(123456,999999).$payment_log_id.random_int(123456,999999)]),
            'cancel_url' =>  route('frontend.event.payment.cancel',['id'=> random_int(123456,999999).$payment_log_id.random_int(123456,999999)]),
        ]);

    }

    public function payment_status_update($id){

        if(empty($id)){
            abort(404);
        }
        $order_details = EventPaymentLogs::find($id);

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

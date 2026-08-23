<?php

namespace App\Http\Controllers\Frontend;

use Anand\LaravelPaytmWallet\Facades\PaytmWallet;
use App\CauseLogs;
use App\EventAttendance;
use App\EventPaymentLogs;
use App\Events;
use App\Events\JobApplication;
use App\Helpers\DonationHelpers;
use App\Helpers\PaymentGatewayRequestHelper;
use App\Http\Controllers\Controller;
use App\Http\Traits\PaytmTrait;
use App\Mail\ContactMessage;
use App\Mail\PaymentSuccess;
use App\Order;
use App\____PaymentGateway\PaymentGatewaySetup;
use App\PaymentLogs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use KingFlamez\Rave\Facades\Rave;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Razorpay\Api\Api;
use Stripe\Charge;
use Mollie\Laravel\Facades\Mollie;
use Stripe\Stripe;
use Unicodeveloper\Paystack\Facades\Paystack;
use Xgenious\Paymentgateway\Facades\XgPaymentGateway;
use function App\Http\Traits\getChecksumFromArray;

class EventPaymentLogsController extends Controller
{
    private const CANCEL_ROUTE = 'frontend.event.payment.cancel';
    private const SUCCESS_ROUTE = 'frontend.event.payment.success';

    const DONATION_SUCCESS_ROUTE = 'frontend.donation.payment.success';
    const DONATION_CANCEL_ROUTE = 'frontend.donation.payment.cancel';

    private const JOB_CANCEL_ROUTE = 'frontend.job.payment.cancel';
    private const JOB_SUCCESS_ROUTE = 'frontend.job.payment.success';

    public function booking_payment_form(Request $request){

         $user_id = Auth::guard('web')->id();

        $this->validate($request,[
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191',
           ],
            [
                'name.required' => __('Name field is required'),
                'email.required' => __('Email field is required')
            ]);

        if (!get_static_option('disable_guest_mode_for_event_module') && !auth()->guard('web')->check()){
            return back()->with(['type' => 'warning','msg' => __('login to place an order')]);
        }

        $attendance_id = $this->store_event_booking_data($request,$user_id);
        if(is_array($attendance_id)){
            return back()->with($attendance_id);
        }
        $event_details = EventAttendance::find($attendance_id);
        $event_info = Events::find($event_details->event_id);
        $event_payment_details = EventPaymentLogs::where('attendance_id',$attendance_id)->first();

        if (!empty($event_info->cost) && $event_info->cost > 0){
            $this->validate($request,[
                'selected_payment_gateway' => 'required|string'
            ],[
                'selected_payment_gateway.required' => __('Select A Payment Method')
            ]);
        }

        if (empty($event_payment_details)){
            $payment_log_id = EventPaymentLogs::create([
                'email' =>  $request->email,
                'name' =>  $request->name,
                'event_name' =>  $event_details->event_name,
                'event_cost' =>  ($event_details->event_cost * $event_details->quantity),
                'package_gateway' =>  $request->selected_payment_gateway,
                'attendance_id' => $attendance_id,
                'status' =>  'pending',
                'track' =>  Str::random(10). Str::random(10),
            ])->id;
            $event_payment_details = EventPaymentLogs::find($payment_log_id);
        }


        //have to work on below code
        if ($request->selected_payment_gateway === 'paypal'){

            $paypal = $this->getPaypalPay();
            $paypal->setExchangeRate(74);

            $response = $paypal->charge_customer(
                $this->common_charge_customer_data($event_details,$event_payment_details,route('frontend.event.paypal.ipn'))
            );
            session()->put('attendance_id',$event_details->id);
            return redirect()->away($response);

        }elseif ($request->selected_payment_gateway === 'paytm'){

            $paytm = PaymentGatewayRequestHelper::paytm();
            $response = $paytm->charge_customer(
                $this->common_charge_customer_data($event_details,$event_payment_details,route('frontend.event.paytm.ipn'))
            );
            return $response;

        }elseif ($request->selected_payment_gateway === 'manual_payment'){

            $this->validate($request, [
                'manual_payment_attachment' => 'required|file'
            ], ['manual_payment_attachment.required' => __('Bank Attachment Required')]);

            $fileName = time().'.'.$request->manual_payment_attachment->extension();
            $request->manual_payment_attachment->move('assets/uploads/attachment/', $fileName);
            EventPaymentLogs::where(['attendance_id'=> $attendance_id])->update(['manual_payment_attachment' => $fileName]);

            $order_id = Str::random(6).$event_payment_details->attendance_id.Str::random(6);
            return redirect()->route(self::SUCCESS_ROUTE,$order_id);

        }elseif ($request->selected_payment_gateway === 'stripe'){

            $stripe = PaymentGatewayRequestHelper::stripe();

            $response = $stripe->charge_customer(
                $this->common_charge_customer_data($event_details,$event_payment_details,route('frontend.event.stripe.ipn'))
            );
            return $response;
        }
        elseif ($request->selected_payment_gateway === 'razorpay'){

            $razorpay = PaymentGatewayRequestHelper::razorpay();

            $redirect_url = $razorpay->charge_customer(
                $this->common_charge_customer_data($event_details,$event_payment_details,route('frontend.event.razorpay.ipn'))
            );
            return $redirect_url;

        }
        elseif ($request->selected_payment_gateway == 'paystack'){

            $paystack = PaymentGatewayRequestHelper::paystack();

            $redirect_url = $paystack->charge_customer(
                $this->common_charge_customer_data($event_details,$event_payment_details,route('frontend.event.paystack.ipn'),'event')
            );
            return $redirect_url;

        }
        elseif ($request->selected_payment_gateway == 'mollie'){

            $mollie = PaymentGatewayRequestHelper::mollie();

            $response = $mollie->charge_customer(
                $this->common_charge_customer_data($event_details,$event_payment_details,route('frontend.event.mollie.ipn'))
            );
            return $response;

        }elseif ($request->selected_payment_gateway == 'flutterwave'){

            $flutterwave = PaymentGatewayRequestHelper::flutterwave();

            $response = $flutterwave->charge_customer(
                $this->common_charge_customer_data($event_details,$event_payment_details,route('frontend.event.flutterwave.ipn'))
            );
            return $response;

        }elseif ($request->selected_payment_gateway === 'payfast') {

            $payfast = PaymentGatewayRequestHelper::payfast();

            $response = $payfast->charge_customer(
                $this->common_charge_customer_data($event_details,$event_payment_details,route('frontend.event.payfast.ipn'))
            );
            return $response;

        } elseif ($request->selected_payment_gateway === 'midtrans') {

            $midtrans = PaymentGatewayRequestHelper::midtrans();

            $response = $midtrans->charge_customer(
                $this->common_charge_customer_data($event_details,$event_payment_details,route('frontend.event.midtrans.ipn'))
            );
            return $response;
        }

        elseif ($request->selected_payment_gateway === 'cashfree') {

            $cashfree = PaymentGatewayRequestHelper::cashfree();

            $response = $cashfree->charge_customer(
                $this->common_charge_customer_data($event_details,$event_payment_details,route('frontend.event.cashfree.ipn'))
            );
            return $response;
        }

        elseif ($request->selected_payment_gateway === 'instamojo') {

            $instamojo = PaymentGatewayRequestHelper::instamojo();

            $response = $instamojo->charge_customer(
                $this->common_charge_customer_data($event_details,$event_payment_details,route('frontend.event.instamojo.ipn'))
            );
            return $response;
        }

        elseif ($request->selected_payment_gateway === 'marcadopago') {

            $marcadopago = PaymentGatewayRequestHelper::marcadopago();

            $response = $marcadopago->charge_customer(
                $this->common_charge_customer_data($event_details,$event_payment_details,route('frontend.event.marcadopago.ipn'))
            );
            return $response;
        }

        elseif ($request->selected_payment_gateway === 'squareup') {

            $squareup = PaymentGatewayRequestHelper::squareup();

            $response = $squareup->charge_customer(
                $this->common_charge_customer_data($event_details,$event_payment_details,route('frontend.event.squreup.ipn'))
            );
            return $response;
        }

        elseif ($request->selected_payment_gateway === 'cinetpay') {

            $cinetpay = PaymentGatewayRequestHelper::cinetpay();

            $response = $cinetpay->charge_customer(
                $this->common_charge_customer_data($event_details,$event_payment_details,route('frontend.event.cinetpay.ipn'))
            );
            return $response;
        }

        elseif ($request->selected_payment_gateway === 'paytabs') {

            $paytabs = PaymentGatewayRequestHelper::paytabs();

            $response = $paytabs->charge_customer(
                $this->common_charge_customer_data($event_details,$event_payment_details,route('frontend.event.paytabs.ipn'))
            );
            return $response;
        }

        elseif ($request->selected_payment_gateway === 'billplz') {

            $billplz = PaymentGatewayRequestHelper::billplz();

            $response = $billplz->charge_customer(
                $this->common_charge_customer_data($event_details,$event_payment_details,route('frontend.event.billplz.ipn'))
            );
            return $response;
        }
        elseif ($request->selected_payment_gateway === 'toyyibpay') {

            $toyyibpay = PaymentGatewayRequestHelper::toyyibpay();

            $response = $toyyibpay->charge_customer(
                $this->common_charge_customer_data($event_details,$event_payment_details,route('frontend.event.toyyibpay.ipn'))
            );
            return $response;
        }elseif ($request->selected_payment_gateway === 'pagali') {

            $pagali = PaymentGatewayRequestHelper::pagali();

            $response = $pagali->charge_customer(
                $this->common_charge_customer_data($event_details,$event_payment_details,route('frontend.event.pagali.ipn'))
            );
            return $response;
        }elseif ($request->selected_payment_gateway === 'sitesway') {

            $sitesway = PaymentGatewayRequestHelper::sitesway();

            $response = $sitesway->charge_customer(
                $this->common_charge_customer_data($event_details,$event_payment_details,route('frontend.event.sitesway.ipn'))
            );
            return $response;
        }elseif ($request->selected_payment_gateway === 'authorizenet') {

            $authorizenet = PaymentGatewayRequestHelper::authorizenet();

            $response = $authorizenet->charge_customer(
                $this->common_charge_customer_data($event_details,$event_payment_details,route('frontend.event.authorizenet.ipn'))
            );
            return $response;
        }

        return redirect()->route('homepage');
    }


    public function authorizenet_ipn()
    {
        $authorizenet = PaymentGatewayRequestHelper::authorizenet();
        $payment_data = $authorizenet->ipn_response();
        return $this->common_ipn_data($payment_data);
    }
    public function sitesway_ipn()
    {
        $sitesway = PaymentGatewayRequestHelper::sitesway();
        $payment_data = $sitesway->ipn_response();
        return $this->common_ipn_data($payment_data);
    }
    public function pagali_ipn()
    {
        $pagali = PaymentGatewayRequestHelper::pagali();
        $payment_data = $pagali->ipn_response();
        return $this->common_ipn_data($payment_data);
    }
    public function toyyibpay_ipn()
    {
        $toyyibpay = PaymentGatewayRequestHelper::toyyibpay();
        $payment_data = $toyyibpay->ipn_response();
        return $this->common_ipn_data($payment_data);
    }
    public function paypal_ipn()
    {
        $paypal = $this->getPaypalPay();
        $payment_data = $paypal->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function paytm_ipn()
    {
        $paytm = PaymentGatewayRequestHelper::paytm();

        $payment_data = $paytm->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function flutterwave_ipn(Request $request)
    {
        $flutterwave = PaymentGatewayRequestHelper::flutterwave();

        $payment_data = $flutterwave->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function stripe_ipn(Request $request)
    {
        $stripe = PaymentGatewayRequestHelper::stripe();

        $payment_data = $stripe->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function razorpay_ipn(Request $request)
    {
        $razorpay = PaymentGatewayRequestHelper::razorpay();

        $payment_data = $razorpay->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function payfast_ipn(Request $request)
    {
        $payfast = PaymentGatewayRequestHelper::payfast();
        $payment_data = $payfast->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function mollie_ipn()
    {
        $mollie =PaymentGatewayRequestHelper::mollie();

        $payment_data = $mollie->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function midtrans_ipn()
    {
        $midtrans = PaymentGatewayRequestHelper::midtrans();
        $payment_data = $midtrans->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function cashfree_ipn()
    {
        $cashfree = PaymentGatewayRequestHelper::cashfree();

        $payment_data = $cashfree->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function instamojo_ipn()
    {
        $instamojo = PaymentGatewayRequestHelper::instamojo();

        $payment_data = $instamojo->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function marcadopago_ipn()
    {
        $marcadopago =PaymentGatewayRequestHelper::marcadopago();

        $payment_data = $marcadopago->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function squreup_ipn()
    {
        $squareup = PaymentGatewayRequestHelper::squareup();

        $payment_data = $squareup->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function cinetpay_ipn()
    {
        $cinetpay = PaymentGatewayRequestHelper::cinetpay();

        $payment_data = $cinetpay->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function paytabs_ipn()
    {
        $paytabs = PaymentGatewayRequestHelper::paytabs();

        $payment_data = $paytabs->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function billplz_ipn()
    {
        $billplz = PaymentGatewayRequestHelper::billplz();

        $payment_data = $billplz->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function getPaypalPay()
    {
        $paypal = PaymentGatewayRequestHelper::paypal();
        return $paypal;
    }



   private function common_charge_customer_data($event_details,$event_payment_details,$ipn_route,$payment_type = null)
   {

        $data = [
            'amount' =>$event_details->event_cost * $event_details->quantity,
            'title' =>  $event_payment_details->name ?? '',
            'description' => 'Payment For Event Attendance Id: #'.$event_details->id.' Payer Name: '.$event_payment_details->name.' Payer Email:'.$event_payment_details->email,
            'order_id' =>$event_details->id,
            'track' =>  $event_payment_details->track,
            'cancel_url' => route(self::CANCEL_ROUTE, $event_payment_details->attendance_id),
            'success_url' => route(self::SUCCESS_ROUTE, random_int(333333,999999).$event_payment_details->attendance_id.random_int(333333,999999)),
            'email' => $event_payment_details->email,
            'name' => $event_payment_details->name,
            'payment_type' => $payment_type,
            'ipn_url' => $ipn_route
        ];



        return $data;
    }

   private function common_ipn_data($payment_data)
    {
        if (isset($payment_data['status']) && $payment_data['status'] === 'complete') {
            event(new Events\AttendanceBooking([
                'attendance_id' => $payment_data['order_id'],
                'transaction_id' => $payment_data['transaction_id']
            ]));
            $order_id = Str::random(6) . $payment_data['order_id']. Str::random(10);
            return redirect()->route(self::SUCCESS_ROUTE, $order_id);
        }

        $order_id = Str::random(6) . $payment_data['order_id']. Str::random(10);
        return redirect()->route(self::CANCEL_ROUTE, $order_id);
    }

    private function common_ipn_data_donation($payment_data)
    {
        if (isset($payment_data['status']) && $payment_data['status'] === 'complete'){
            event(new Events\DonationSuccess([
                'donation_log_id' => $payment_data['order_id'],
                'transaction_id' => $payment_data['transaction_id'],
            ]));
            $order_id = Str::random(6) . $payment_data['order_id']. Str::random(6);
            return redirect()->route(self::DONATION_SUCCESS_ROUTE, $order_id);
        }
        $order_id = Str::random(6) . $payment_data['order_id']. Str::random(6);
        return redirect()->route(self::DONATION_CANCEL_ROUTE, $order_id);
    }

    public function common_ipn_data_job($payment_data){

        if (isset($payment_data['status']) && $payment_data['status'] === 'complete') {
            event(new JobApplication([
                'transaction_id' => $payment_data['transaction_id'],
                'job_application_id' =>$payment_data['order_id']
            ]));
            $order_id = Str::random(6) . $payment_data['order_id']. Str::random(6);
            return redirect()->route(self::JOB_SUCCESS_ROUTE,$order_id);
        }
        $order_id = Str::random(6) . $payment_data['order_id']. Str::random(6);
        return redirect()->route(self::JOB_CANCEL_ROUTE,$order_id);
    }


    public function store_event_booking_data(Request $request, $user_id){

        $validated_data = $this->get_filtered_data_from_request(get_static_option('event_attendance_form_fields'),$request);
        $all_attachment = $validated_data['all_attachment'];
        $all_field_serialize_data = $validated_data['field_data'];

        $new_data = $all_field_serialize_data;
        unset($new_data['manual_payment_attachment']);

        $event_detials = Events::find($request->event_id);
        $event_attendance_id = EventAttendance::create([
            'custom_fields' => serialize($new_data),
            'status' => 'pending',
            'event_name' => $event_detials->title,
            'event_cost' => $event_detials->cost,
            'quantity' => $request->quantity,
            'event_id' => $request->event_id,
            'checkout_type' => !empty($request->checkout_type) ? $request->checkout_type : '',
            'user_id' => $user_id,
            'attachment' => serialize($all_attachment)
        ])->id;

        if (App::isLocal()){
            //have to set condition for redirect in payment page with payment information
            if (!empty(get_static_option('site_payment_gateway'))) {

                $succ_msg = get_static_option('event_attendance_mail_subject');
                $success_message = !empty($succ_msg) ? $succ_msg : __('Thanks for your Booking. we will get back to you very soon.');
                $order_mail = get_static_option('event_attendance_receiver_mail') ? get_static_option('event_attendance_receiver_mail') : get_static_option('site_global_email');

                if ($event_detials->cost == 0 || empty(get_static_option('site_payment_gateway'))){
                    Mail::to($order_mail)->send(new ContactMessage($all_field_serialize_data, $all_attachment, __('Your have an event booking for').' '.$event_detials->title));
                    return redirect()->back()->with(['msg' => $success_message, 'type' => 'success']);
                }

                return $event_attendance_id;

            }

            $success_message = __('Thanks for your Booking. we will get back to you very soon.');
            return redirect()->back()->with(['msg' => $success_message, 'type' => 'success']);
        }


        $google_captcha_result = google_captcha_check($request->captcha_token);
        if ($google_captcha_result['success']) {

            //have to set condition for redirect in payment page with payment information
            if (!empty(get_static_option('site_payment_gateway'))) {

                $succ_msg = get_static_option('event_attendance_mail_' . get_user_lang() . '_subject');
                $success_message = !empty($succ_msg) ? $succ_msg : __('Thanks for your Booking. we will get back to you very soon.');
                $order_mail = get_static_option('event_attendance_receiver_mail') ? get_static_option('event_attendance_receiver_mail') : get_static_option('site_global_email');

                if ($event_detials->cost == 0 || empty(get_static_option('site_payment_gateway'))){

                    try{
                        Mail::to($order_mail)->send(new ContactMessage($all_field_serialize_data, $all_attachment, __('Your have an event booking for').' '.$event_detials->title));
                    }catch(\Eception $e){
                        return  redirect()->back()->with([
                            'type' => 'danger',
                            'msg' => $e->getMessage()
                        ]);
                    }

                    return ['msg' => $success_message, 'type' => 'success'];
                }
                //return redirect()->route('frontend.event.booking.confirm', $event_attendance_id);
                return $event_attendance_id;

            }
            $success_message = __('Thanks for your Booking. we will get back to you very soon.');
            return ['msg' => $success_message, 'type' => 'success'];
        }else{
            return ['msg' => __('google reacpatch error, please try after some time!!'), 'type' => 'danger'];
        }
        return ['msg' => __('Something goes wrong, Please try again later !!'), 'type' => 'danger'];
    }

    public function get_filtered_data_from_request($option_value,$request){

        $all_attachment = [];
        $all_quote_form_fields = (array) json_decode($option_value);
        $all_field_type = isset($all_quote_form_fields['field_type']) ? (array) $all_quote_form_fields['field_type'] : [];
        $all_field_name = isset($all_quote_form_fields['field_name']) ? $all_quote_form_fields['field_name'] : [];
        $all_field_required = isset($all_quote_form_fields['field_required'])  ? (object) $all_quote_form_fields['field_required'] : [];
        $all_field_mimes_type = isset($all_quote_form_fields['mimes_type']) ? (object) $all_quote_form_fields['mimes_type'] : [];

        //get field details from, form request
        $all_field_serialize_data = $request->all();
        unset($all_field_serialize_data['_token']);
        if (isset($all_field_serialize_data['captcha_token'])){
            unset($all_field_serialize_data['captcha_token']);
        }


        if (!empty($all_field_name)){
            foreach ($all_field_name as $index => $field){
                $is_required = !empty($all_field_required) && property_exists($all_field_required,$index) ? $all_field_required->$index : '';
                $mime_type = !empty($all_field_mimes_type) && property_exists($all_field_mimes_type,$index) ? $all_field_mimes_type->$index : '';
                $field_type = isset($all_field_type[$index]) ? $all_field_type[$index] : '';
                if (!empty($field_type) && $field_type == 'file'){
                    unset($all_field_serialize_data[$field]);
                }
                $validation_rules = !empty($is_required) ? 'required|': '';
                $validation_rules .= !empty($mime_type) ? $mime_type : '';

                //validate field
                $this->validate($request,[
                    $field => $validation_rules
                ]);

                if ($field_type == 'file' && $request->hasFile($field)) {
                    $filed_instance = $request->file($field);
                    $file_extenstion = $filed_instance->getClientOriginalExtension();
                    $attachment_name = 'attachment-'.Str::random(32).'-'. $field .'.'. $file_extenstion;
                    $filed_instance->move('assets/uploads/attachment/applicant', $attachment_name);
                    $all_attachment[$field] = 'assets/uploads/attachment/applicant/' . $attachment_name;
                }
            }
        }
        return [
            'all_attachment' => $all_attachment,
            'field_data' => $all_field_serialize_data
        ];
    }



}

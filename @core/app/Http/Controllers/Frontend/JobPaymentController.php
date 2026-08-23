<?php

namespace App\Http\Controllers\Frontend;
use App\Events\JobApplication;
use App\Helpers\DonationHelpers;
use App\____PaymentGateway\PaymentGatewaySetup;
use App\Helpers\PaymentGatewayRequestHelper;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\JobApplicant;
use App\Jobs;
use App\Mail\BasicMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use KingFlamez\Rave\Facades\Rave;
use Mollie\Laravel\Facades\Mollie;
use Razorpay\Api\Api;
use Stripe\Charge;
use Stripe\Stripe;
use Xgenious\Paymentgateway\Facades\XgPaymentGateway;


class JobPaymentController extends Controller
{
    private const CANCEL_ROUTE = 'frontend.job.payment.cancel';
    private const SUCCESS_ROUTE = 'frontend.job.payment.success';

    public function store_jobs_applicant_data(Request $request)
    {
        $jobs_details = Jobs::find($request->job_id);
        $this->validate($request,[
            'email' => 'required|email',
            'name' => 'required|string',
            'job_id' => 'required',
        ],[
            'email.required' => __('email is required'),
            'email.email' => __('enter valid email'),
            'name.required' => __('name is required'),
            'job_id.required' => __('must apply to any job'),
        ]);
        if (!empty($jobs_details->application_fee_status) && $jobs_details->application_fee > 0){
            $this->validate($request,[
                'selected_payment_gateway' => 'required|string'
            ],
                ['selected_payment_gateway.required' => __('You must have to select a payment gateway')]);
        }

        if (!empty($jobs_details->application_fee_status) && $jobs_details->application_fee > 0 && $request->selected_payment_gateway == 'manual_payment'){
            $this->validate($request,[
                'transaction_id' => 'required|string'
            ],
                ['transaction_id.required' => __('You must have to provide your transaction id')]);
        }

        $job_applicant_id = JobApplicant::create([
            'jobs_id' => $request->job_id,
            'payment_gateway' => $request->selected_payment_gateway,
            'email' => $request->email,
            'name' => $request->name,
            'application_fee' => $request->application_fee,
            'track' => Str::random(30),
            'payment_status' => 'pending',
        ])->id;

        $all_attachment = [];
        $all_quote_form_fields = (array) json_decode(get_static_option('apply_job_page_form_fields'));
        $all_field_type = $all_quote_form_fields['field_type'] ?? [];
        $all_field_name = $all_quote_form_fields['field_name'] ?? [];
        $all_field_required = $all_quote_form_fields['field_required'] ?? [];
        $all_field_required = (object) $all_field_required;
        $all_field_mimes_type = $all_quote_form_fields['mimes_type'] ?? [];
        $all_field_mimes_type = (object) $all_field_mimes_type;

        //get field details from, form request
        $all_field_serialize_data = $request->all();
        unset($all_field_serialize_data['_token'],$all_field_serialize_data['job_id'],$all_field_serialize_data['name'],$all_field_serialize_data['email'],$all_field_serialize_data['selected_payment_gateway']);

        if (!empty($all_field_name)){
            foreach ($all_field_name as $index => $field){
                $is_required = property_exists($all_field_required,$index) ? $all_field_required->$index : '';
                $mime_type = property_exists($all_field_mimes_type,$index) ? $all_field_mimes_type->$index : '';
                $field_type = $all_field_type[$index] ?? '';
                if (!empty($field_type) && $field_type == 'file'){
                    unset($all_field_serialize_data[$field]);
                }
                $validation_rules = !empty($is_required) ? 'required|': '';
                $validation_rules .= !empty($mime_type) ? $mime_type : '';

                $this->validate($request,[
                    $field => $validation_rules
                ]);

                if ($field_type == 'file' && $request->hasFile($field)) {
                    $filed_instance = $request->file($field);
                    $file_extenstion = $filed_instance->getClientOriginalExtension();
                    $attachment_name = 'attachment-'.$job_applicant_id.'-'. $field .'.'. $file_extenstion;
                    $filed_instance->move('assets/uploads/attachment/applicant', $attachment_name);
                    $all_attachment[$field] = 'assets/uploads/attachment/applicant/' . $attachment_name;
                }
            }
        }

        //update database
        JobApplicant::where('id',$job_applicant_id)->update([
            'form_content' => serialize($all_field_serialize_data),
            'attachment' => serialize($all_attachment)
        ]);
        $job_applicant_details = JobApplicant::where('id',$job_applicant_id)->first();

        //check it application fee applicable or not
        if (!empty($jobs_details->application_fee_status) && $jobs_details->application_fee > 0){
            //have to redirect  to payment gateway route

            if($job_applicant_details->payment_gateway === 'paypal'){

                $paypal = PaymentGatewayRequestHelper::paypal();

                $response = $paypal->charge_customer(
                    $this->common_charge_customer_data($job_applicant_details,$jobs_details,route('frontend.job.paypal.ipn'))
                );

                session()->put('job_application_id',$job_applicant_details->id);
                return redirect()->away($response);


            }elseif ($job_applicant_details->payment_gateway === 'paytm'){

                $paytm = PaymentGatewayRequestHelper::paytm();

                $response = $paytm->charge_customer(
                    $this->common_charge_customer_data($job_applicant_details,$jobs_details,route('frontend.job.paytm.ipn'))
                );
                return $response;

            }elseif ($job_applicant_details->payment_gateway === 'manual_payment'){

                return redirect()->route(self::SUCCESS_ROUTE,random_int(666666,999999).$job_applicant_details->id.random_int(999999,999999));

            }elseif ($job_applicant_details->payment_gateway === 'stripe'){

                $stripe = PaymentGatewayRequestHelper::stripe();

                $response = $stripe->charge_customer(
                    $this->common_charge_customer_data($job_applicant_details,$jobs_details,route('frontend.job.stripe.ipn'))
                );
                return $response;

            }elseif ($job_applicant_details->payment_gateway === 'razorpay'){

                $razorpay = PaymentGatewayRequestHelper::razorpay();

                $redirect_url = $razorpay->charge_customer(
                    $this->common_charge_customer_data($job_applicant_details,$jobs_details,route('frontend.job.razorpay.ipn'))
                );
                return $redirect_url;

            }elseif ($job_applicant_details->payment_gateway === 'paystack'){
                $paystack = PaymentGatewayRequestHelper::paystack();

                $redirect_url = $paystack->charge_customer(
                    $this->common_charge_customer_data($job_applicant_details,$jobs_details,route('frontend.event.paystack.ipn'),'job')
                );
                return $redirect_url;

            }elseif ($job_applicant_details->payment_gateway === 'mollie'){

                $mollie = PaymentGatewayRequestHelper::mollie();

                $response = $mollie->charge_customer(
                    $this->common_charge_customer_data($job_applicant_details,$jobs_details,route('frontend.job.mollie.ipn'))
                );
                return $response;

            }elseif ($job_applicant_details->payment_gateway === 'flutterwave'){

                $flutterwave = PaymentGatewayRequestHelper::flutterwave();

                $response = $flutterwave->charge_customer(
                    $this->common_charge_customer_data($job_applicant_details,$jobs_details,route('frontend.job.flutterwave.ipn'))
                );
                return $response;

            }elseif ($job_applicant_details->payment_gateway === 'payfast') {

                $payfast = PaymentGatewayRequestHelper::payfast();

                $response = $payfast->charge_customer(
                    $this->common_charge_customer_data($job_applicant_details,$jobs_details,route('frontend.job.payfast.ipn'))
                );
                return $response;

            } elseif ($job_applicant_details->payment_gateway === 'midtrans') {

                $midtrans = PaymentGatewayRequestHelper::midtrans();

                $response = $midtrans->charge_customer(
                    $this->common_charge_customer_data($job_applicant_details,$jobs_details,route('frontend.job.midtrans.ipn'))
                );
                return $response;
            }

            elseif ($job_applicant_details->payment_gateway === 'cashfree') {


                $cashfree = PaymentGatewayRequestHelper::cashfree();
                $response = $cashfree->charge_customer(
                    $this->common_charge_customer_data($job_applicant_details,$jobs_details,route('frontend.job.cashfree.ipn'))
                );
                return $response;
            }

            elseif ($job_applicant_details->payment_gateway === 'instamojo') {

                $instamojo = PaymentGatewayRequestHelper::instamojo();

                $response = $instamojo->charge_customer(
                    $this->common_charge_customer_data($job_applicant_details,$jobs_details,route('frontend.job.instamojo.ipn'))
                );
                return $response;
            }

            elseif ($job_applicant_details->payment_gateway === 'marcadopago') {

                $marcadopago = PaymentGatewayRequestHelper::marcadopago();

                $response = $marcadopago->charge_customer(
                    $this->common_charge_customer_data($job_applicant_details,$jobs_details,route('frontend.job.marcadopago.ipn'))
                );
                return $response;
            }

            elseif ($job_applicant_details->payment_gateway === 'squareup') {

                $squareup = PaymentGatewayRequestHelper::squareup();

                $response = $squareup->charge_customer(
                    $this->common_charge_customer_data($job_applicant_details,$jobs_details,route('frontend.job.squreup.ipn'))
                );
                return $response;
            }

            elseif ($job_applicant_details->payment_gateway === 'cinetpay') {

                $cinetpay = PaymentGatewayRequestHelper::cinetpay();

                $response = $cinetpay->charge_customer(
                    $this->common_charge_customer_data($job_applicant_details,$jobs_details,route('frontend.job.cinetpay.ipn'))
                );
                return $response;
            }

            elseif ($job_applicant_details->payment_gateway === 'paytabs') {

                $paytabs = PaymentGatewayRequestHelper::paytabs();

                $response = $paytabs->charge_customer(
                    $this->common_charge_customer_data($job_applicant_details,$jobs_details,route('frontend.job.paytabs.ipn'))
                );
                return $response;
            }

            elseif ($job_applicant_details->payment_gateway === 'billplz') {

                $billplz = PaymentGatewayRequestHelper::billplz();

                $response = $billplz->charge_customer(
                    $this->common_charge_customer_data($job_applicant_details,$jobs_details,route('frontend.job.billplz.ipn'))
                );
                return $response;
            }elseif ($job_applicant_details->payment_gateway === 'toyyibpay') {

                $toyyibpay = PaymentGatewayRequestHelper::toyyibpay();

                $response = $toyyibpay->charge_customer(
                    $this->common_charge_customer_data($job_applicant_details,$jobs_details,route('frontend.job.toyyibpay.ipn'))
                );
                return $response;
            }elseif ($job_applicant_details->payment_gateway === 'pagali') {

                $pagali = PaymentGatewayRequestHelper::pagali();

                $response = $pagali->charge_customer(
                    $this->common_charge_customer_data($job_applicant_details,$jobs_details,route('frontend.job.pagali.ipn'))
                );
                return $response;
            }elseif ($job_applicant_details->payment_gateway === 'sitesway') {

                $sitesway = PaymentGatewayRequestHelper::sitesway();

                $response = $sitesway->charge_customer(
                    $this->common_charge_customer_data($job_applicant_details,$jobs_details,route('frontend.job.sitesway.ipn'))
                );
                return $response;
            }elseif ($job_applicant_details->payment_gateway === 'authorizenet') {

                $authorizenet = PaymentGatewayRequestHelper::authorizenet();

                $response = $authorizenet->charge_customer(
                    $this->common_charge_customer_data($job_applicant_details,$jobs_details,route('frontend.job.authorizenet.ipn'))
                );
                return $response;
            }


            return redirect()->route('homepage');

        }else{
            $succ_msg = get_static_option('apply_job_success_message');
            $success_message = !empty($succ_msg) ? $succ_msg : __('Your Application Is Submitted Successfully!!');

            event(new JobApplication([
                'transaction_id' => null,
                'job_application_id' => $job_applicant_details->id
            ]));
            return redirect()->back()->with(['msg' => $success_message, 'type' => 'success']);
        }
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
        $paypal = PaymentGatewayRequestHelper::paypal();
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
        $mollie = PaymentGatewayRequestHelper::mollie();

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
        $marcadopago = PaymentGatewayRequestHelper::marcadopago();

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

    public function getPaypalPay(){
        $paypal = PaymentGatewayRequestHelper::paypal();

        return $paypal;
    }

    private function common_charge_customer_data($job_applicant_details,$jobs_details,$ipn_route,$payment_type = null)
    {
        $data = [
            'amount' => $job_applicant_details->application_fee,
            'title' =>   $job_applicant_details->name,
            'order_id' => $jobs_details->id,
            'track' => $job_applicant_details->track,
            'cancel_url' => route(self::CANCEL_ROUTE, $jobs_details->id),
            'success_url' =>  route(self::SUCCESS_ROUTE, random_int(333333,999999).$jobs_details->id.random_int(333333,999999)),
            'email' =>  $job_applicant_details->email,
            'name' =>  $job_applicant_details->name,
            'payment_type' => $payment_type,
            'ipn_url' => $ipn_route,
            'description' => __('Payment For Job Application Id:'). '#'.$job_applicant_details->id.' '.__('Job Title:').' '.$jobs_details->title.' 
            '.__('Applicant Name:').' '.$job_applicant_details->name.' '.__('Applicant Email:').' '.$job_applicant_details->email,
        ];

        return $data;
    }

    private function common_ipn_data($payment_data){

        if (isset($payment_data['status']) && $payment_data['status'] === 'complete') {
            event(new JobApplication([
                'transaction_id' => $payment_data['transaction_id'],
                'job_application_id' =>$payment_data['order_id']
            ]));
            $order_id = Str::random(6) . $payment_data['order_id']. Str::random(6);
            return redirect()->route(self::SUCCESS_ROUTE,$order_id);
        }
        $order_id = Str::random(6) . $payment_data['order_id']. Str::random(6);
        return redirect()->route(self::CANCEL_ROUTE,$order_id);
    }


}

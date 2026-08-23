<?php

namespace App\Http\Controllers\Frontend;

use App\Cause;
use App\CauseLogs;
use App\Helpers\DonationHelpers;
use App\Helpers\FlashMsg;
use App\Helpers\PaymentGatewayRequestHelper;
use App\Http\Controllers\Controller;
use App\Events;
use App\Notification;
use App\Recuring;
use App\Reward;
use App\Services\DemoBlockchainService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use MercadoPago\Payment;
use Xgenious\Paymentgateway\Facades\XgPaymentGateway;


class CausesLogController extends Controller
{
    const SUCCESS_ROUTE = 'frontend.donation.payment.success';
    const CANCEL_ROUTE = 'frontend.donation.payment.cancel';

    public function store_donation_logs(Request $request)
    {

        $gift_amount_validation_condition = !empty($request->gift_id) ? 'nullable' : 'required';
        $requring_validation = !is_null($request->cid) || !is_null($request->id) ? 'nullable' : 'required';

        if(!is_null($request->id)){
            $log_exists = CauseLogs::findOrFail($request->id) ;
        }

        $this->validate($request, [
            'name' => ''.$requring_validation.'|string|max:191',
            'email' => ''.$requring_validation.'|email|max:191',
            'cause_id' => ''.$requring_validation.'|string',
            'amount' => ''.$gift_amount_validation_condition.'|string',
            'anonymous' => 'nullable|string',
            'selected_payment_gateway' => 'required|string',
        ],
            [
                'name.required' => __('Name field is required'),
                'email.required' => __('Email field is required'),
                'amount.required' => __('Amount field is required'),
            ]
        );

        $selected_payment_gateway = $request->selected_payment_gateway;

        //Checking campaigns are owner campaigns or not
            $cause = Cause::find($request->cause_id);
            $admin_id = Auth::guard('admin')->id();
            $user_id = Auth::guard('web')->id();
            

           if(Auth::guard('admin')->check()) {
               if (!empty($cause->admin_id) && $cause->admin_id == $admin_id) {
                   return  back()->with(FlashMsg::item_delete('Campaign owner can not donate on this campaign..!'));
               }
            }

            if(Auth::guard('web')->check()) {
                if (!empty($cause->user_id) && $cause->user_id == $user_id) {
                    return  back()->with(FlashMsg::item_delete('Campaign owner can not donate on this campaign..!'));
                }
            }
        //Checking campaigns are owner campaigns or not



        $minimum_donation_amount = get_static_option('minimum_donation_amount');
        $msg = __('Minimum Donation Amount is : ');
        if (!empty($minimum_donation_amount) && $request->amount < $minimum_donation_amount) {
            return back()->with(FlashMsg::settings_delete($msg . amount_with_currency_symbol($minimum_donation_amount)));
        }

        if ($selected_payment_gateway !== 'blockchain_demo' && empty(get_static_option($request->selected_payment_gateway. '_gateway'))) {
            return back()->with(['msg' => __('your selected payment gateway is disable, please select avialble payment gateway'), 'type' => 'danger']);
        }

        $cause_details = Cause::find($request->cause_id);
        if (empty($cause_details)) {
            return back()->with(['msg' => __('donation cause not found'), 'type' => 'danger']);
        }
        $admin_charge = $request->has('admin_tip') ? $request->admin_tip : DonationHelpers::get_donation_charge($request->amount, false);

        $amount = $request->amount;

        $minimum_goal_amount = Reward::where('status','publish')->orderBy('reward_goal_from','asc')->get()->min('reward_goal_from');

        if($cause_details->reward == 'on' && auth()->guard('web')->check() && $amount >= $minimum_goal_amount){

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

        if (!empty($request->order_id)) {
            $payment_log_id = $request->order_id;
        } else {

            if(empty($log_exists)){
                $log_exists  = [];
            }

            $name = $request->name;
            $email = $request->email;
            $cause_id = $request->cause_id;
            $gift_id = $request->gift_id;
            $cid = $request->cid;
            $payment_type = $request->payment_type;
            $captcha_token = $request->captcha_token;
            $admin_tip = $request->admin_tip;

            $anonymous = $request->anonymous;
            $manual_payment_attachment = $request->manual_payment_attachment;
            $validation = $request;


            $data_unset_old_fields = $request;
            unset(
                $data_unset_old_fields['cid'],
                $data_unset_old_fields['cause_id'],
                $data_unset_old_fields['payment_type'],
                $data_unset_old_fields['captcha_token'],
                $data_unset_old_fields['amount'],
                $data_unset_old_fields['name'],
                $data_unset_old_fields['email'],
                $data_unset_old_fields['admin_tip'],
                $data_unset_old_fields['selected_payment_gateway'],
                $data_unset_old_fields['manual_payment_attachment'],
                $data_unset_old_fields['custom_admin_tip'],
                $data_unset_old_fields['payment_gateway'],
                $data_unset_old_fields['gift_id'],
            );


          //Custom Fields Code
            $validated_data = $this->get_filtered_data_from_request(get_static_option('donation_page_form_fields'),$data_unset_old_fields);
            $all_attachment = $validated_data['all_attachment'];
            $all_field_serialize_data = $validated_data['field_data'];


          //Custom Fields Code
            $payment_log_id = CauseLogs::create([
                'recuring_token' => $log_exists->recuring_token ?? ($payment_type == 'monthly' ? Str::random(20) : null),
                'email' => $log_exists->email ?? $email ?? '',
                'name' => $log_exists->name ?? $name ?? '',
                'cause_id' => $log_exists->cause_id ?? $cause_id,
                'gift_id' => $log_exists->gift_id ?? $gift_id ?? null,
                'amount' => $amount,
                'admin_charge' =>$log_exists->admin_charge ??  $admin_charge ?? null,
                'reward_point' =>  $log_exists->reward_point ??  $reward_point ?? null,
                'reward_amount' => $log_exists->reward_amount ?? $reward_amount ?? null,
                'anonymous' => $log_exists->anonymous ?? !empty($anonymous) ? 1 : 0,
                'payment_gateway' =>  $log_exists->payment_gateway ?? $selected_payment_gateway,
                'user_id' => $log_exists->user_id ?? (auth()->check() ? auth()->user()->id : null),
                'status' => $log_exists->status ?? 'pending',
                'track' =>  $log_exists->track ?? (Str::random(10) . Str::random(10)),
                'custom_fields' => json_encode($all_field_serialize_data) ?? [],
                'attachments' => json_encode($all_attachment) ?? [],
            ])->id;
        }

        $payment_type = '';
        if($payment_type == 'monthly'){
            Recuring::create([
                'cause_log_id'=>$payment_log_id,
                'expire_date' => Carbon::now()->addMonth(1)
            ]);
        }

        $donation_payment_details = CauseLogs::find($payment_log_id);
        $total_amount = DonationHelpers::get_donation_total($amount, false, $admin_tip ?? null);

        if(!empty($payment_log_id)){
           Notification::create([
               'cause_log_id'=>$payment_log_id,
               'title'=> 'New donation payment done',
               'type' =>'cause_log',
           ]);
        }

        if ($selected_payment_gateway === 'paypal') {
            
            try{
                $paypal = $this->getPaypalPay();
                $paypal->setExchangeRate(get_exchange_rate('USD'));
                $response = $paypal->charge_customer(
                    $this->common_charge_customer_data($total_amount,$donation_payment_details,route('frontend.donation.paypal.ipn'))
                );
    
                return $response;
            }catch(\Exception $e){
                return back()->with(['msg' => $e->getMessage(),'type' => 'danger']);
            }

        } elseif ($selected_payment_gateway === 'paytm') {

            try{
                $paytm = PaymentGatewayRequestHelper::paytm();
                $response = $paytm->charge_customer(
                    $this->common_charge_customer_data($total_amount,$donation_payment_details,route('frontend.donation.paytm.ipn'))
                );
    
                return $response;
            }catch(\Exception $e){
                return back()->with(['msg' => $e->getMessage(),'type' => 'danger']);
            }

        } elseif ($selected_payment_gateway === 'manual_payment') {
            $this->validate($validation, [
                'manual_payment_attachment' => 'required|file'
            ], ['manual_payment_attachment.required' => __('Bank Attachment Required')]);

            $fileName = time().'.'.$manual_payment_attachment->extension();
            $manual_payment_attachment->move('assets/uploads/attachment/', $fileName);

            CauseLogs::where(['cause_id'=> $cause_id])->update(['manual_payment_attachment' => $fileName]);
            $order_id = Str::random(6) . $donation_payment_details->id . Str::random(6);
            return redirect()->route(self::SUCCESS_ROUTE, $order_id);

        } elseif ($selected_payment_gateway=== 'stripe') {

           try{
                $stripe = PaymentGatewayRequestHelper::stripe();
                $response = $stripe->charge_customer(
                    $this->common_charge_customer_data($total_amount,$donation_payment_details,route('frontend.donation.stripe.ipn'))
                );
                return $response;
           }catch(\Exception $e){
                return back()->with(['msg' => $e->getMessage(),'type' => 'danger']);
            }
            

        } elseif ($selected_payment_gateway === 'razorpay') {

           try{
                $razorpay = PaymentGatewayRequestHelper::razorpay();
                $redirect_url = $razorpay->charge_customer(
                    $this->common_charge_customer_data($total_amount,$donation_payment_details,route('frontend.donation.razorpay.ipn'))
                );
                return $redirect_url;
           }catch(\Exception $e){
                return back()->with(['msg' => $e->getMessage(),'type' => 'danger']);
            }

        } elseif ($selected_payment_gateway === 'paystack') {
            
            
            try{
                $paystack = PaymentGatewayRequestHelper::paystack();
    
                $redirect_url = $paystack->charge_customer(
                    $this->common_charge_customer_data($total_amount,$donation_payment_details,route('frontend.event.paystack.ipn'),'donation')
                );
                return $redirect_url;
            }catch(\Exception $e){
                return back()->with(['msg' => $e->getMessage(),'type' => 'danger']);
            }


        } elseif ($selected_payment_gateway === 'mollie') {

            try{
                $mollie = PaymentGatewayRequestHelper::mollie();
    
                $response = $mollie->charge_customer(
                    $this->common_charge_customer_data($total_amount,$donation_payment_details,route('frontend.donation.mollie.ipn'))
                );
                return $response;
            }
            catch(\Exception $e){
                return back()->with(['msg' => $e->getMessage(),'type' => 'danger']);
            }

        } elseif ($selected_payment_gateway === 'flutterwave') {

            try{
                $flutterwave = PaymentGatewayRequestHelper::flutterwave();
    
                $response = $flutterwave->charge_customer(
                    $this->common_charge_customer_data($total_amount,$donation_payment_details,route('frontend.donation.flutterwave.ipn'))
                );
                return $response;
            }
            catch(\Exception $e){
                return back()->with(['msg' => $e->getMessage(),'type' => 'danger']);
            }

        } elseif ($selected_payment_gateway === 'payfast') {

            
            try{
                $payfast = PaymentGatewayRequestHelper::payfast();
    
                $response = $payfast->charge_customer(
                    $this->common_charge_customer_data($total_amount,$donation_payment_details,route('frontend.donation.payfast.ipn'))
                );
                return $response;
            }
            catch(\Exception $e){
                return back()->with(['msg' => $e->getMessage(),'type' => 'danger']);
            }


          } elseif ($selected_payment_gateway === 'midtrans') {

            try{
                $midtrans = PaymentGatewayRequestHelper::midtrans();
    
                $response = $midtrans->charge_customer(
                      $this->common_charge_customer_data($total_amount,$donation_payment_details,route('frontend.donation.midtrans.ipn'))
                    );
    
                return $response;
            }
            catch(\Exception $e){
                return back()->with(['msg' => $e->getMessage(),'type' => 'danger']);
            }
         }

        elseif ($selected_payment_gateway === 'cashfree') {

            try{
                $cashfree = PaymentGatewayRequestHelper::cashfree();
    
                $response = $cashfree->charge_customer(
                    $this->common_charge_customer_data($total_amount,$donation_payment_details,route('frontend.donation.cashfree.ipn'))
                );
                return $response;
            }
            catch(\Exception $e){
                return back()->with(['msg' => $e->getMessage(),'type' => 'danger']);
            }
        }

        elseif ($selected_payment_gateway === 'instamojo') {

            try{
                $instamojo = PaymentGatewayRequestHelper::instamojo();
    
                $response = $instamojo->charge_customer(
                    $this->common_charge_customer_data($total_amount,$donation_payment_details,route('frontend.donation.instamojo.ipn'))
                );
                return $response;
            } catch(\Exception $e){
                return back()->with(['msg' => $e->getMessage(),'type' => 'danger']);
            }
        }

        elseif ($selected_payment_gateway === 'marcadopago') {

            try{
                $marcadopago = PaymentGatewayRequestHelper::marcadopago();
    
                $response = $marcadopago->charge_customer(
                    $this->common_charge_customer_data($total_amount,$donation_payment_details,route('frontend.donation.marcadopago.ipn'))
                );
                return $response;
            }
            catch(\Exception $e){
                return back()->with(['msg' => $e->getMessage(),'type' => 'danger']);
            }
        }

        elseif ($selected_payment_gateway === 'squareup') {

            
            try{
                $squareup = PaymentGatewayRequestHelper::squareup();
                $response = $squareup->charge_customer(
                    $this->common_charge_customer_data($total_amount,$donation_payment_details,route('frontend.donation.squreup.ipn'))
                );
                return $response;
            }
            catch(\Exception $e){
                return back()->with(['msg' => $e->getMessage(),'type' => 'danger']);
            }
        }

        elseif ($selected_payment_gateway === 'cinetpay') {

            
            try{
                $cinetpay = PaymentGatewayRequestHelper::cinetpay();
    
                $response = $cinetpay->charge_customer(
                    $this->common_charge_customer_data($total_amount,$donation_payment_details,route('frontend.donation.cinetpay.ipn'))
                );
                return $response;
            }
             catch(\Exception $e){
                return back()->with(['msg' => $e->getMessage(),'type' => 'danger']);
            }
        }

        elseif ($selected_payment_gateway === 'paytabs') {

            
            try{
                $paytabs = PaymentGatewayRequestHelper::paytabs();
        
                $response = $paytabs->charge_customer(
                    $this->common_charge_customer_data($total_amount,$donation_payment_details,route('frontend.donation.paytabs.ipn'))
                );
                return $response;
            }
            catch(\Exception $e){
                return back()->with(['msg' => $e->getMessage(),'type' => 'danger']);
            }
        }


        elseif ($selected_payment_gateway === 'billplz') {

            try{
                $billplz = PaymentGatewayRequestHelper::billplz();
    
                $response = $billplz->charge_customer(
                    $this->common_charge_customer_data($total_amount,$donation_payment_details,route('frontend.donation.billplz.ipn'))
                );
                return $response;
            }
            catch(\Exception $e){
                return back()->with(['msg' => $e->getMessage(),'type' => 'danger']);
            }
        }

        elseif ($selected_payment_gateway === 'zitopay') {

            try{

                $zitopay = PaymentGatewayRequestHelper::zitopay();

                $response = $zitopay->charge_customer(
                    $this->common_charge_customer_data($total_amount,$donation_payment_details,route('frontend.donation.zitopay.ipn'))
                );
                return $response;
            }
            catch(\Exception $e){
                return back()->with(['msg' => $e->getMessage(),'type' => 'danger']);
            }
        }
        elseif ($selected_payment_gateway === 'toyyibpay') {

            try{

                $toyyibpay = PaymentGatewayRequestHelper::toyyibpay();

                $response = $toyyibpay->charge_customer(
                    $this->common_charge_customer_data($total_amount,$donation_payment_details,route('frontend.donation.toyyibpay.ipn'))
                );
                return $response;
            }
            catch(\Exception $e){
                return back()->with(['msg' => $e->getMessage(),'type' => 'danger']);
            }
        }elseif ($selected_payment_gateway === 'pagali') {

            try{

                $toyyibpay = PaymentGatewayRequestHelper::pagali();

                $response = $toyyibpay->charge_customer(
                    $this->common_charge_customer_data($total_amount,$donation_payment_details,route('frontend.donation.pagali.ipn'))
                );
                return $response;
            }
            catch(\Exception $e){
                return back()->with(['msg' => $e->getMessage(),'type' => 'danger']);
            }
        }elseif ($selected_payment_gateway === 'sitesway') {

            try{

                $toyyibpay = PaymentGatewayRequestHelper::sitesway();

                $response = $toyyibpay->charge_customer(
                    $this->common_charge_customer_data($total_amount,$donation_payment_details,route('frontend.donation.sitesway.ipn'))
                );
                return $response;
            }
            catch(\Exception $e){
                return back()->with(['msg' => $e->getMessage(),'type' => 'danger']);
            }
        }elseif ($selected_payment_gateway === 'authorizenet') {

            try{

                $toyyibpay = PaymentGatewayRequestHelper::authorizenet();

                $response = $toyyibpay->charge_customer(
                    $this->common_charge_customer_data($total_amount,$donation_payment_details,route('frontend.donation.authorizenet.ipn'))
                );
                return $response;
            }
            catch(\Exception $e){
                return back()->with(['msg' => $e->getMessage(),'type' => 'danger']);
            }
        }

        elseif ($selected_payment_gateway === 'blockchain_demo') {

            $walletAddress = $request->wallet_address ?? DemoBlockchainService::generateWalletAddress();

            $donationPaymentDetails = CauseLogs::find($payment_log_id);

            $blockchainTx = DemoBlockchainService::processDonation(
                $donationPaymentDetails,
                $walletAddress,
                $total_amount
            );

            $order_id = Str::random(6) . $payment_log_id . Str::random(6);
            return redirect()->route('blockchain.transaction.success', [
                'hash' => $blockchainTx->transaction_hash,
            ]);
        }


        return redirect()->route('homepage');
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
        $cinetpay =PaymentGatewayRequestHelper::cinetpay();

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

    public function zitopay_ipn()
    {
        $zitopay = PaymentGatewayRequestHelper::zitopay();

        $payment_data = $zitopay->ipn_response();
        return $this->common_ipn_data($payment_data);
    }
    public function toyyibpay_ipn()
    {
        $toyyibpay = PaymentGatewayRequestHelper::toyyibpay();
        $payment_data = $toyyibpay->ipn_response();
        return $this->common_ipn_data($payment_data);
    }
    public function pagali_ipn()
    {
        $pagali = PaymentGatewayRequestHelper::pagali();
        $payment_data = $pagali->ipn_response();
        return $this->common_ipn_data($payment_data);
    }
    public function sitesway_ipn()
    {
        $sitesway = PaymentGatewayRequestHelper::sitesway();
        $payment_data = $sitesway->ipn_response();
        return $this->common_ipn_data($payment_data);
    }
    public function authorizenet_ipn()
    {
        $authorize = PaymentGatewayRequestHelper::authorizenet();
        $payment_data = $authorize->ipn_response();
        return $this->common_ipn_data($payment_data);
    }



    private function common_charge_customer_data($total_amount,$donation_payment_details, $ipn_route, $payment_type = null)
    {
        $data = [
                'amount' => $total_amount,
                'title' => __('Payment For Donation:') . ' ' . optional($donation_payment_details->cause)->title ?? '',
                'description' => __('Payment For Donation:') . ' ' . optional($donation_payment_details->cause)->title ?? ''.' #'.$donation_payment_details->id,
                'order_id' => $donation_payment_details->id,
                'track' => $donation_payment_details->track,
                'cancel_url' => route(self::CANCEL_ROUTE, $donation_payment_details->id),
                'success_url' => route(self::SUCCESS_ROUTE, random_int(333333, 999999) . $donation_payment_details->id . random_int(333333, 999999)),
                'email' => $donation_payment_details->email, // user email
                'name' => $donation_payment_details->name, // user name
                'payment_type' => $payment_type, // which kind of payment your are receiving
                'ipn_url' => $ipn_route
             ];
        return $data;

    }

    private function common_ipn_data($payment_data)
    {
        if (isset($payment_data['status']) && $payment_data['status'] === 'complete'){
            event(new Events\DonationSuccess([
                'donation_log_id' => $payment_data['order_id'],
                'transaction_id' => $payment_data['transaction_id'],
            ]));
            $order_id = Str::random(6) . $payment_data['order_id']. Str::random(6);
            return redirect()->route(self::SUCCESS_ROUTE, $order_id);
        }

        return redirect()->route(self::CANCEL_ROUTE);
    }


    public function getPaypalPay()
    {
        $paypal = PaymentGatewayRequestHelper::paypal();
        return $paypal;
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

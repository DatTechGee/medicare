@extends('frontend.frontend-page-master')
@section('site-title')
    {{ __(' Checkout : ') .$gift_name }}
@endsection
@section('page-title')
    {{ __(' Gift Checkout : ') . $gift_name}}
@endsection
@section('page-meta-data')
    <meta name="description" content="{{get_static_option('donation_page_meta_description')}}">
    <meta name="tags" content="{{get_static_option('donation_page_meta_tags')}}">
@endsection
@section('content')
    <section class="donation-content-area padding-top-120 padding-bottom-90" id="donation_gift_checkout_form_wrapper">
        <div class="container">
            <form action="{{route('frontend.donations.log.store')}}" method="post"
                  enctype="multipart/form-data" class="donation-form-wrapper">
                @csrf
                <input type="hidden" name="gift_id" value="{{$gift->id}}">
                <input type="hidden" name="cause_id" value="{{$donation->id}}">
                <input type="hidden" name="amount" value="{{$gift->amount}}">
                <input type="hidden" name="payment_gateway" id="payment_gateway">
                @if(!empty(get_static_option('allow_user_to_add_custom_tip_in_donation')))
                    <input type="hidden" name="admin_tip" value="{{\App\Helpers\DonationHelpers::get_donation_charge($gift->amount)}}">
                @endif

                <div class="row">
                    <div class="col-lg-6 justify-content-center">
                         <div class="donation_wrapper">
                                <div class="btn-wrapper">
                                    <a href="{{route('frontend.donations.single',$donation->slug)}}" class="goback-btn">{{__('Go Back')}}</a>
                                </div>

                                <x-msg.success/>
                                <x-msg.error/>
                                @php
                                    $auth_user = \Illuminate\Support\Facades\Auth::guard('web');
                                    $auth_name = $auth_user->check() ? $auth_user->user()->name : '';
                                    $auth_email = $auth_user->check() ? $auth_user->user()->email : '';
                                @endphp

                                <div class="form-group">
                                    <input type="text" name="name" value="{{$auth_name}}" class="form-control" placeholder="{{__('Enter Name')}}">
                                </div>
                                <div class="form-group">
                                    <input type="email" name="email" value="{{$auth_email}}" class="form-control" placeholder="{{__('Enter Email')}}">
                                </div>

                                {!! render_form_field_for_frontend(get_static_option('donation_page_form_fields')) !!}
                                {!! render_payment_gateway_for_form() !!}

                                @if(!empty(get_static_option('manual_payment_gateway')))
                                    <div class="form-group manual_payment_transaction_field">
                                        <div class="label">{{__('Attach Your Bank Document')}}</div>
                                        <input class="form-control btn btn-warning btn-sm" type="file" name="manual_payment_attachment">
                                        <p class="help-info">{!! get_manual_payment_description() !!}</p>
                                    </div>
                                @endif

                                <div class="donate-seperate-page-button">
                                    <div class="btn-wrapper">
                                        <button type="submit" class="boxed-btn reverse-color btn-sm">{{__('Pay Now')}}</button>
                                    </div>
                                </div>

                            </div>

                    </div>

                    <div class="col-lg-4">
                        <div class="donation-amount-details-wrapper">
                            <h3 class="title">{{__('Your Donation Details')}}</h3>

                            <div class="your-area-donation-wrap">
                                <div class="thumb">
                                    {!! render_image_markup_by_attachment_id($donation->image,'','thumb') !!}
                                </div>
                                <div class="content">
                                    <h4 class="title">{{$donation->title}}</h4>
                                    <span class="created_by">{{__('Created By')}}
                                        @if($donation->created_by === 'user')
                                            {{\App\User::find($donation->user_id)->name ?? __('Anonymous')}}
                                        @else
                                            {{\App\Admin::find($donation->admin_id)->name ?? __('Anonymous')}}
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div class="gift-info-wrapp">
                                <h3 class="gift-title">{{__('Gift Title:')}} <span>{{$gift->title ?? ''}}</span></h3>
                                <div class="gift-information-details">
                                    @php
                                    $colors = ['success','info','warning','danger'];
                                    @endphp
                                    <strong>{{__('Gifts:')}}</strong>
                                    @foreach(json_decode($gift->gifts) ?? [] as $key=> $item)
                                        <span class="badge badge-{{$colors[$key % count($colors)]}}">{{$item ?? ''}}</span>
                                    @endforeach
                                    <p>{{$gift->description}}</p>
                                    <p class="estimate-delivery-date">{{__('Estimate Delivery Date:')}}  <span class="text-primary">{{$gift->delivery_date ?? ''}}</span></p>

                                </div>
                            </div>
                            <ul>
                                <li><span>{{__('Payable Amount')}}</span> <span class="price donation_amount">{{amount_with_currency_symbol($gift->amount) ?? 0}}</span></li>
                                 @if(empty(get_static_option('donation_charge_form')) || get_static_option('donation_charge_form') === 'donor')
                                    <li>
                                        <span>{{get_static_option('site_title')}} {{__('tip')}}</span>
                                        <span class="price admin_tip">
                                            @if(!empty(get_static_option('allow_user_to_add_custom_tip_in_donation')))
                                            <span class="input-wrap"><input type="number" name="custom_admin_tip" min="1" value="{{\App\Helpers\DonationHelpers::get_donation_charge($gift->amount)}}"></span>
                                            @else
                                           <span class="amount"> {{\App\Helpers\DonationHelpers::get_donation_charge($gift->amount,true)}}</span>
                                            @endif
                                        </span>
                                    </li>
                                @endif
                                <li class="total"><span>{{__('Total')}}</span> <span class="price total_amount">{{\App\Helpers\DonationHelpers::get_donation_total($gift->amount,true) }}</span></li>
                            </ul>

                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        var defaulGateway = $('#site_global_payment_gateway').val();
        $('.payment-gateway-wrapper ul li[data-gateway="'+defaulGateway+'"]').addClass('selected');

        $(document).on('click','.payment-gateway-wrapper > ul > li',function (e) {
            e.preventDefault();
            var gateway = $(this).data('gateway');
            $('#payment_gateway').val(gateway);
            $(this).addClass('selected').siblings().removeClass('selected');
            $('#site_global_payment_gateway').val(gateway);
            if(gateway == 'manual_payment'){
                $('.manual_payment_transaction_field').addClass('show');
            }else{
                $('.manual_payment_transaction_field').removeClass('show');
            }
            $('.payment-gateway-wrapper').find(('input')).val(gateway);
        });
        
        
        //write code for admin tip
        //Donation Charge
        $(document).on('keyup change', 'input[name="custom_admin_tip"]', function () {
            var el = $(this);
           calcCustomTip(el);
        });
        
        function calcCustomTip(el){
            var currentVal = el.val();
            var changeVal;
            if(currentVal > 0){
                changeVal = currentVal
            }else{
                el.val(1);
                changeVal = 1
            }
            $('input[name="admin_tip"]').val(changeVal);
            updateDonationAmount();
        }
        
        
        //todo have to modify this function
        function updateDonationAmount(){
            var donation_amount_user_input = $('input[name="amount"]').val();
            var admin_tip = $('input[name="admin_tip"]').val();

            $.ajax({
                url: "{{ route('frontend.get.donation.charges.by.ajax') }}",
                type: 'post',
                dataType: 'JSON',
                data: {
                    _token: "{{csrf_token()}}",
                    amount: donation_amount_user_input,
                    admin_tip: admin_tip,
                },
                success: function (data) {
                    var parent = $('#donation_gift_checkout_form_wrapper');
                    parent.find('.donation_amount').text(data.donation_amount);
                    parent.find('.admin_tip .amount').text(data.tip);
                    parent.find('.total_amount').text(data.total);
                }
            });
        }
                
                
    </script>
@endsection


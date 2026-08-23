@extends('frontend.user.dashboard.user-master')
@section('section')
        <div class="ud-section-header">
            <h3><i class="fas fa-hand-holding-heart"></i>{{auth()->guard('web')->user()->isPatient() ? __('Donations Received') : __('My Donations')}}</h3>
            @if(!auth()->guard('web')->user()->isPatient())
                <a href="{{route('frontend.donations')}}" class="ud-action-btn primary"><i class="fas fa-bullhorn"></i> {{__('Donate Again')}}</a>
            @endif
        </div>
        @if(count($donation) > 0)
        <div class="ud-card" style="padding:0;overflow:hidden;">
        <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col">{{get_static_option('donation_page_name')}} {{__('Info')}}</th>
                        <th scope="col">{{__('Payment Status')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($donation as $data)
                        <tr>
                            <td scope="row">
                                <div class="user-dahsboard-order-info-wrap">
                                    <h5 class="title">
                                        @if(!empty($data->cause))
                                            <a href="{{route('frontend.donations.single',optional($data->cause)->slug)}}">{{optional($data->cause)->title}}</a>
                                        @else
                                            <div class="text-warning">{{__('This item is not available or removed')}}</div>
                                        @endif
                                    </h5>
                                    <small class="d-block"><strong>{{get_static_option('donation_page_name')}} {{__('ID:')}}</strong> #{{$data->id}}</small>
                                    <small class="d-block"><strong>{{__('Amount:')}}</strong> {{amount_with_currency_symbol($data->amount)}}</small>

                                    @php
                                        $gifts = optional($data->gift)->gifts ;
                                        $colors = ['warning','info','primary','success'];
                                    @endphp
                                    @if(!empty($data->gift))
                                        <strong>{{__('Gifts')}}:</strong>
                                        @foreach (json_decode($gifts) ?? [] as $key=> $item)
                                            <span class="badge badge-{{$colors[$key % count($colors)]}}">{{$item ?? ''}}</span>
                                        @endforeach
                                        <small class="d-block mt-2"><strong>{{__('Gift Title :')}}</strong> {{optional($data->gift)->title ?? ''}}</small>
                                        <small class="d-block"><strong>{{__('Gift Delivery Date :')}}</strong> {{optional($data->gift)->delivery_date}}</small>
                                    @endif

                                    @php
                                        $all_custom_fields = json_decode($data->custom_fields) ?? [];
                                    @endphp
                                    @if(!empty($all_custom_fields))
                                        @foreach($all_custom_fields ?? [] as $key=> $field)
                                            <small class="d-block"><strong class="text-dark ">{{ ucfirst($key) . ' : ' }}</strong>{{!is_object($field) ? $field : ''}}</small>
                                        @endforeach
                                    @endif

                                    @if(!empty($data->point))
                                        <small class="d-block"><strong>{{__('Reward Point')}}:</strong> {{$data->point}}</small>
                                    @endif
                                    <small class="d-block"><strong>{{__('Payment Gateway:')}}</strong> {{str_replace('_',' ',__($data->payment_gateway))}}</small>

                                    @if(!empty($data->donor_wallet_address))
                                        <small class="d-block"><strong>{{__('Donor Wallet:')}}</strong> <code style="font-size:11px;">{{substr($data->donor_wallet_address,0,8)}}...{{substr($data->donor_wallet_address,-6)}}</code></small>
                                    @endif
                                    @if(!empty($data->blockchain_transaction_hash))
                                        <small class="d-block"><strong>{{__('Tx Hash:')}}</strong>
                                            <a href="{{route('blockchain.transaction.show',$data->blockchain_transaction_hash)}}" target="_blank" title="{{__('View on blockchain explorer')}}" style="text-decoration:none;">
                                                <code style="font-size:11px;color:#627EEA;">{{substr($data->blockchain_transaction_hash,0,14)}}... <i class="fas fa-cubes" style="font-size:9px;"></i></code>
                                            </a>
                                        </small>
                                    @endif
                                    @if(!empty($data->anonymous) && $data->anonymous)
                                        <span class="badge badge-secondary"><i class="fas fa-user-secret"></i> {{__('Anonymous Donation')}}</span>
                                    @endif

                                    @if($data->payment_gateway == 'manual_payment')
                                         <small class="d-block">{{__('Attachment :')}}<a class="btn btn-info btn-sm pull-right" href="{{url('assets/uploads/attachment/'.$data->manual_payment_attachment)}}" target="_blank">{{__('View Attachment')}}</a></small>
                                    @endif


                                    <small class="d-block"><strong>{{__('Date:')}}</strong> {{date_format($data->created_at,'d M Y')}}</small>
                                    @if($data->status == 'complete')
                                        <form action="{{route('frontend.donation.invoice.generate')}}"  method="post">
                                            @csrf
                                            <input type="hidden" name="id" id="invoice_generate_order_field" value="{{$data->id}}">
                                            <button class="btn btn-secondary btn-small" type="submit">{{__('Invoice')}}</button>
                                        </form>
                                        <a href="{{route('donation.receipt',$data->track)}}" target="_blank" class="btn btn-info btn-small mt-1" style="display:inline-flex;align-items:center;gap:6px;"><i class="fas fa-receipt"></i> {{__('Receipt')}}</a>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($data->status == 'pending')
                                    <span class="alert alert-warning text-capitalize alert-sm alert-small">{{__($data->status)}}</span>
                                    @if( $data->payment_gateway != 'manual_payment')
                                        <form action="{{route('frontend.donations.log.store')}}" method="post" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="order_id" value="{{$data->id}}" >
                                            <input type="hidden" name="cause_id" value="{{$data->cause_id}}" >
                                            <input type="hidden" name="amount" value="{{$data->amount}}">
                                            <input type="hidden" name="name" value="{{$data->name}}" >
                                            <input type="hidden" name="email" value="{{$data->email}}" >
                                            <input type="hidden" name="selected_payment_gateway" value="{{$data->payment_gateway}}">
                                            <button type="submit" class="small-btn btn-success margin-top-20">{{__('Pay Now')}}</button>
                                        </form>
                                    @endif
                                    <form action="{{route('user.dashboard.donation.order.cancel')}}" method="post">
                                        @csrf
                                        <input type="hidden" name="order_id" value="{{$data->id}}">
                                        <button type="submit" class="small-btn btn-danger margin-top-10">{{__('Cancel')}}</button>
                                    </form>
                                @elseif($data->status == 'cancel')
                                    <span class="alert alert-danger text-capitalize alert-sm alert-small" style="display: inline-block">{{__($data->status)}}</span>
                                @else
                                    <span class="alert alert-success text-capitalize alert-sm alert-small" style="display: inline-block">{{__($data->status)}}</span>
                                @endif

                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="blog-pagination">
            {{ $donation->links() }}
        </div>
        @else
            <div class="alert alert-warning text-center" style="padding:34px 20px;">
                <i class="fas fa-inbox" style="font-size:30px;display:block;margin-bottom:12px;opacity:.7;"></i>
                <strong>{{__('No Donations Yet')}}</strong><br>
                @if(!auth()->guard('web')->user()->isPatient())
                    {{__('Browse verified patient campaigns and make your first donation via MetaMask.')}}
                    <br><a href="{{route('frontend.donations')}}" class="btn btn-info btn-sm mt-3">{{__('Browse Campaigns')}}</a>
                @endif
            </div>
        @endif

@endsection

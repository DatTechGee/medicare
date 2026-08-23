@extends('frontend.frontend-page-master')

@section('site-title')
    {{__('Recuring payment of : ' .optional($donation->cause)->title )}}
@endsection

@section('page-title')

    {{__('Recuring Payment of : '.optional($donation->cause)->title )}}
@endsection

@section('style')
    <x-media.css/>
@endsection


@section('content')
       <div class="container py-5 my-5">
           <div class="row justify-content-center">
               <div class="col-lg-6">
                    <h3 class="title my-3 text-center mb-4">{{__('Recuring Payment Section')}}</h3>
                   <div class="card">
                       <div class="card-header ">
                           <h6 class="title text-center">{{ optional($donation->cause)->title }}</h6>
                       </div>

                       <div class="card-body">
                           <x-msg.error/>
                           <x-msg.success/>

                           <h3 class="price-wrap mb-4 text-center">{{__('Payable Amount : ')}}{{amount_with_currency_symbol($donation->amount)}}</h3>
                           <form action="{{route('frontend.donations.log.store')}}" method="post" enctype="multipart/form-data" class="donation-form-wrapper">

                               @csrf
                               <input type="hidden" name="cause_id" value="{{optional($donation->cause)->id}}">
                               <input type="hidden" name="id" value="{{$donation->id}}">
                               <input type="hidden" name="amount" value="{{$donation->amount}}">
                               <input type="hidden" name="selected_payment_gateway" value="{{$donation->payment_gateway}}">

                               @if($donation->payment_gateway == 'manual_payment')
                                   <div class="form-group">
                                       <div class="label">{{__('Attach Your Bank Document')}}</div>
                                       <input class="form-control btn btn-warning btn-sm" type="file" name="manual_payment_attachment">
                                       <span class="help-info">{!! get_manual_payment_description() !!}</span>
                                   </div>
                               @endif

                               <button type="submit" class="btn btn-success btn-block">{{__('Pay Now')}}</button>
                           </form>
                       </div>
                   </div>
               </div>
           </div>
       </div>
@endsection

@section('scripts')
    <script src="{{asset('assets/frontend/js/jQuery.rProgressbar.min.js')}}"></script>

    <x-media.js
            :deleteRoute="route('user.upload.media.file.delete')"
            :imgAltChangeRoute="route('user.upload.media.file.alt.change')"
            :allImageLoadRoute="route('user.upload.media.file.all')">
    </x-media.js>
@endsection

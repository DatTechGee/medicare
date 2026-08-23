@extends('backend.admin-master')
@section('site-title')
    {{__('Company Settings')}}
@endsection
@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-12 mt-5">
                @include('backend.partials.message')
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title"> {{__('Company Page Settings')}}</h4>
                        <form action="{{route('admin.company.settings')}}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label for="site_title">{{__('Company Name')}}</label>
                                <input type="text" name="company_name"  class="form-control" value="{{get_static_option('company_name')}}">
                            </div>

                            <div class="form-group">
                                <label for="site_tag_line">{{__('Company Address')}}</label>
                                <input type="text" name="company_address"  class="form-control" value="{{get_static_option('company_address')}}">
                            </div>

                            <div class="form-group">
                                <label for="site_tag_line">{{__('Company Email')}}</label>
                                <input type="text" name="company_email"  class="form-control" value="{{get_static_option('company_email')}}">
                            </div>

                            <div class="form-group">
                                <label for="site_tag_line">{{__('Company Phone')}}</label>
                                <input type="text" name="company_phone"  class="form-control" value="{{get_static_option('company_phone')}}">
                            </div>

                            <button id="update" type="submit" class="btn btn-primary mt-4 pr-4 pl-4">{{__('Update Changes')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')

    <script>
        (function($){
            "use strict";
            $(document).ready(function(){
                <x-btn.update/>
            });
        }(jQuery));
    </script>
@endsection

@extends('frontend.user.dashboard.user-master')
@section('site-title')
    {{__('User Verify')}}
@endsection

@section('style')
    <x-media.css/>
@endsection

@section('section')
    <div class="dashboard-form-wrapper">

        <div class="row">
            <div class="col-md-12">
                @php
                    $auth_user_user_verify_status = \Illuminate\Support\Facades\Auth::guard('web')->user()->user_verify_status;
                @endphp
                <div class="tab-pane active" role="tabpanel">
                    @if($auth_user_user_verify_status == 2)
                        <div class="alert alert-success text-center"><strong>{{__('Verified')}}</strong></div>
                    @endif
                <h2 class="title">{{__('User Verify')}}</h2>
            </div>
        </div>
        <form action="{{route('user.home.verify.update')}}" method="post" enctype="multipart/form-data">
            @csrf

            <div class="row">
            <div class="form-group mt-5 col-md-6">
                <label for="image">{{get_static_option('nid_image_label') ??__('NID Image')}}</label>
                <div class="media-upload-btn-wrapper">
                    <div class="img-wrap">
                         {!! render_attachment_preview_for_admin($user_details->user_verify_nid) !!}
                    </div>
                    <input type="hidden" name="user_verify_nid" value="{{ $user_details->user_verify_nid }}">
                    <button type="button" class="btn btn-info media_upload_form_btn" data-btntitle="{{__('Select Image')}}" data-modaltitle="{{__('Upload NID')}}" data-toggle="modal" data-target="#media_upload_modal">
                        {{__('Upload Image')}}
                    </button>
                </div>
                <small>{{__('Recommended image size 150x150')}}</small>
            </div>

            <div class="form-group col-md-6 mt-5">
                <label for="image">{{get_static_option('driving_license_image_label') ?? __('Address Image')}}</label>
                <div class="media-upload-btn-wrapper">
                    <div class="img-wrap">
                        {!! render_attachment_preview_for_admin($user_details->user_verify_address) !!}
                    </div>
                    <input type="hidden" name="user_verify_address" value="{{ $user_details->user_verify_address }}">
                    <button type="button" class="btn btn-info media_upload_form_btn" data-btntitle="{{__('Select Image')}}" data-modaltitle="{{__('Upload Image')}}" data-toggle="modal" data-target="#media_upload_modal">
                        {{__('Upload Image')}}
                    </button>
                </div>
                <small>{{__('Recommended image size 150x150')}}</small>
            </div>


            </div>

            <div class="btn-wrapper">
                <button id="update" type="submit" class="boxed-btn reverse-color">{{__('Save changes')}}</button>
            </div>
        </form>
   </div>



    <x-media.markup
            :userUpload="true"
            :imageUploadRoute="route('user.upload.media.file')">
    </x-media.markup>
@endsection

@section('scripts')
    <script>
        <x-btn.update/>
    </script>
    <x-media.js
            :deleteRoute="route('user.upload.media.file.delete')"
            :imgAltChangeRoute="route('user.upload.media.file.alt.change')"
            :allImageLoadRoute="route('user.upload.media.file.all')">
    </x-media.js>
@endsection

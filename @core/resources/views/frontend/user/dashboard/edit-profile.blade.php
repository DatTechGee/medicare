@extends('frontend.user.dashboard.user-master')
@section('style')
    <x-media.css/>
@endsection
@section('section')
    <div class="dashboard-form-wrapper">
        <h2 class="title">{{__('Edit Profile')}}</h2>
        <form action="{{route('user.profile.update')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="name">{{__('Name')}}</label>
                <input type="text" class="form-control" id="name" name="name" value="{{$user_details->name}}">
            </div>
            <div class="form-group">
                <label for="email">{{__('Email')}}</label>
                <input type="text" class="form-control" id="email" name="email" value="{{$user_details->email}}">
            </div>
            <div class="form-group">
                <label for="phone">{{__('Phone')}}</label>
                <input type="tel" class="form-control" id="phone" name="phone" value="{{$user_details->phone}}">
            </div>

            <div class="form-group">
                <label for="country">{{__('Country')}}</label>
                <select id="country" class="form-control" name="country_id">
                    @foreach($all_countries as $country)
                        <option value="{{$country->id}}" {{ $user_details->country_id == $country->id ? 'selected' : '' }}>{{$country->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="state">{{__('State')}}</label>
                <input type="text" class="form-control" id="state" name="state" value="{{$user_details->state}}">
            </div>
            <div class="form-group">
                <label for="city">{{__('City')}}</label>
                <input type="text" class="form-control" id="city" name="city" value="{{$user_details->city}}">
            </div>
            <div class="form-group">
                <label for="zipcode">{{__('Zipcode')}}</label>
                <input type="text" class="form-control" id="zipcode" name="zipcode" value="{{$user_details->zipcode}}">
            </div>
            <div class="form-group">
                <label for="address">{{__('Address')}}</label>
                <input type="text" class="form-control" id="address" name="address" value="{{$user_details->address}}">
            </div>
            <div class="form-group">
                <label for="image">{{__('Image')}}</label>
                <div class="media-upload-btn-wrapper">
                    <div class="img-wrap">
                        {!! render_attachment_preview_for_admin($user_details->image) !!}
                    </div>
                    <input type="hidden" name="image">
                    <button type="button" class="btn btn-info media_upload_form_btn" data-btntitle="{{__('Select Image')}}" data-modaltitle="{{__('Upload Image')}}" data-toggle="modal" data-target="#media_upload_modal">
                        {{__('Upload Image')}}
                    </button>
                </div>
                <small>{{__('Recommended image size 150x150')}}</small>
            </div>

            <div class="btn-wrapper">
              <button type="submit" class="boxed-btn reverse-color">{{__('Save changes')}}</button>
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
         (function($){
            "use strict";
            $(document).ready(function () {
                $('#country option[value="{{auth('web')->user()->country}}"]').attr('selected',true);
				$('#country').niceSelect('update');
                $(document).on('click','.mobile_nav',function(e){
                  e.preventDefault(); 
                   $(this).parent().toggleClass('show');
               });
            });
         })(jQuery);
    </script>
    <x-media.js
            :deleteRoute="route('user.upload.media.file.delete')"
            :imgAltChangeRoute="route('user.upload.media.file.alt.change')"
            :allImageLoadRoute="route('user.upload.media.file.all')">
    </x-media.js>
@endsection

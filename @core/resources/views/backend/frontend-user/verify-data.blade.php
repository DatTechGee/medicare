@extends('backend.admin-master')
@section('site-title')
    {{__('User Verify Data')}}
@endsection

@section('style')
    <x-media.css/>
@endsection
@section('content')

            <div class="col-lg-12 col-ml-12 padding-bottom-30">
                <div class="row">
                    <div class="col-12 mt-5">
                        <div class="card">
                            <div class="card-body">
                                <x-msg.error/>
                                <x-msg.success/>
                                <h4 class="header-title"> {{__('User Verify Data')}}
                                    <a class="btn btn-info btn-xs pull-right" href="{{route('admin.all.frontend.user')}}"> {{__('All User')}}</a>
                                </h4>

                                <ul>
                                    <li class="list-item my-2">{{__('ID')}} {{$user_verify->id}}</li>
                                    <li class="list-item my-2">{{__('Name : ')}} {{$user_verify->name}}</li>

                                    @if(!empty($user_verify->user_verify_nid) && !empty($user_verify->user_verify_address))

                                    @php
                                        $nid_img = get_attachment_image_by_id($user_verify->user_verify_nid) ?? NULL;
                                        $add_img = get_attachment_image_by_id($user_verify->user_verify_address) ?? NULL;
                                    @endphp

                                    <li class="list-item my-2">{{__('NID Document') }}
                                        {!! render_attachment_preview_for_admin($user_verify->user_verify_nid) !!}
                                        <a href="{{$nid_img['img_url']}}" class="badge badge-primary px-1 py-1 mt-2" target="_blank">{{__('Click to View')}}</a>
                                    </li>
                                    <li class="list-item my-2">{{ __('Address Document')}}
                                        {!! render_attachment_preview_for_admin($user_verify->user_verify_address) !!}
                                        <a href="{{$add_img['img_url']}}" class="badge badge-primary px-1 py-1 mt-2" target="_blank">{{__('Click to View')}}</a>
                                    </li>
                                     @else
                                        <li>
                                            <div class="alert alert-warning">{{__('No document available')}}</div>
                                        </li>
                                     @endif
                                </ul>

                                @if($user_verify->user_verify_status == 1)
                                    <div class="approve mt-5">
                                        <a href="{{route('admin.frontend.user.verify.update',$user_verify->id)}}" class="btn btn-success btn-md">{{__('Verify Now')}}</a>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>

    <x-media.markup/>
@endsection

@section('script')
    <x-media.js/>
@endsection


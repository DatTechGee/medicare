@extends('backend.admin-master')

@section('site-title')
    {{__('View Notification')}}
@endsection
@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-lg-12">
                <div class="margin-top-40"></div>
                <x-msg.success/>
                <x-msg.error/>
            </div>
            <div class="col-lg-12 mt-5">
                <div class="card">
                    <div class="card-body">
                        <div class="header-wrapp">
                            <h4 class="header-title">{{__('View Notification Page')}}  </h4>
                            <div class="header-title">
                                <a href="{{ route('admin.notification') }}"
                                   class="btn btn-primary mt-4 pr-4 pl-4">{{__('All Notifications')}}</a>
                            </div>
                        </div>

                        <ul>
                            <li>{{__('Title :')}} <span>{{ __($notification->title) ?? ''}}</span> </li>
                            <li>{{__('Notification Type :')}}  <span>{{ __(str_replace('_',' ',ucwords($notification->type))) }}</span> </li>
                            <li>{{__('Notification Date :')}}  <span>{{$notification->created_at->diffForHumans()}}</span> </li>
                        </ul>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection


@extends('backend.admin-master')

@section('site-title')
    {{__('All Donation Gifts')}}
@endsection

@section('style')
    @include('backend.partials.datatable.style-enqueue')
    <x-media.css/>
@endsection

@section('content')
    @php
        $colors = ['success','primary','warning','danger','info','dark'];
    @endphp
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-lg-12">
                <div class="margin-top-40"></div>
                <x-msg.error/>
                <x-msg.success/>
            </div>
            <div class="col-lg-12 mt-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{__('All Donation Gifts')}}</h4>
                        <div class="bulk-delete-wrapper">
                            @can('donation-category-delete')
                                <x-bulk-action/>
                            @endcan
                            @can('donation-gift-create')
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="btn-wrapper pull-right mb-3">
                                            <a href="{{route('admin.donations.gift.create')}}" class="btn btn-info text-white">{{__('Add New Gift')}}</a>
                                        </div>
                                    </div>
                                </div>
                            @endcan
                        </div>

                        <div class="table-wrap table-responsive">
                            <table class="table table-default">
                                <thead>
                                <x-bulk-th/>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Title')}}</th>
                                <th>{{__('Image')}}</th>
                                <th>{{__('Amount')}}</th>
                                <th>{{__('Delivery Date')}}</th>
                                <th>{{__('Gifts')}}</th>
                                <th>{{__('Status')}}</th>
                                <th>{{__('Action')}}</th>
                                </thead>
                                <tbody>
                                @foreach($all_gifts as $data)
                                    <tr>
                                        <td>
                                            <x-bulk-delete-checkbox :id="$data->id"/>
                                        </td>
                                        <td>{{$data->id}}</td>
                                        <td>{{$data->title}}</td>
                                        <td>{!! render_attachment_preview_for_admin($data->image) !!}</td>
                                        <td>{{ amount_with_currency_symbol($data->amount) }}</td>
                                        <td>{{$data->delivery_date}}</td>
                                        <td>
                                            @php
                                                $gifts_decoded = json_decode($data->gifts) ?? [];
                                            @endphp

                                            @foreach($gifts_decoded as $key => $gift)
                                                <span class="badge badge-{{$colors[$key % count($colors)]}}">{{$gift ?? ''}}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            <x-status-span :status="$data->status"/>
                                        </td>
                                        <td>
                                            @can('donation-gift-create')
                                                <x-delete-popover :url="route('admin.donations.gift.delete',$data->id)"/>
                                            @endcan
                                            @can('donation-gift-edit')
                                                <a href="{{route('admin.donations.gift.edit',$data->id)}}" class="btn btn-primary btn-xs mb-3 mr-1 category_edit_btn">
                                                    <i class="ti-pencil"></i>
                                                </a>
                                            @endcan

                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('backend.partials.media-upload.media-upload-markup')
@endsection
@section('script')
    <script src="{{asset('assets/backend/js/dropzone.js')}}"></script>
    <script>

        //Date Picker
        flatpickr('.date-flat', {
            enableTime: false,
            dateFormat: "d-m-Y",
        });
        <x-btn.submit/>
        <x-btn.update/>

        (function ($) {
            "use strict";
            $(document).ready(function () {
                <x-bulk-action-js :url="route('admin.donations.gift.bulk.action')"/>
            });
        })(jQuery)
    </script>
    @include('backend.partials.datatable.script-enqueue')
    @include('backend.partials.media-upload.media-js')
    <x-repeater/>
@endsection

@extends('backend.admin-master')
@section('title', __('Pending Campaigns'))
@section('style')
    @include('backend.partials.datatable.style-enqueue')
    <x-media.css/>
@endsection
@section('content')
<div class="bg-d-900 border border-[#e8edf5] rounded-2xl overflow-hidden fu">
    <div class="px-6 py-4 border-b border-[#e8edf5] flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center"><i class="fas fa-clock text-amber-400 text-sm"></i></div>
            <span class="text-sm font-bold text-d-100">Pending Review</span>
            <span class="px-2.5 py-1 rounded-lg bg-amber-500/10 text-amber-400 text-[10px] font-bold uppercase tracking-wider">Awaiting Approval</span>
        </div>
        @can('donation-delete')
            <x-bulk-action/>
        @endcan
    </div>
    <div class="p-6">
        @include('backend/partials/message')
        @include('backend/partials/error')
        <div class="table-wrap table-responsive">
            <table class="table table-default w-full" id="all_blog_table">
                <thead>
                <x-bulk-th/>
                <th>{{__('ID')}}</th>
                <th>{{__('Title')}}</th>
                <th>{{__('Image')}}</th>
                <th>{{__('Category')}}</th>
                <th>{{__('Action')}}</th>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

@include('backend.partials.media-upload.media-upload-markup')
@endsection

@section('script')
    <script src="{{asset('assets/backend/js/dropzone.js')}}"></script>
    <script>
        (function ($) {
            "use strict";
            $(document).ready(function () {
                <x-bulk-action-js :url="route('admin.donations.bulk.action')"/>
            })
        })(jQuery)
    </script>
    @include('backend.partials.datatable.script-enqueue' ,['only_js' => true])
    @include('backend.partials.media-upload.media-js')
    <script type="text/javascript">
        $(function () {

            $(document).ready(function (){
                $('.table-wrap > table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('admin.donations.pending.all') }}",
                    columns: [
                        {data: 'checkbox', name: '', orderable: false, searchable: false},
                        {data: 'id', name: 'id'},
                        {data: 'info', name: '' ,orderable: false, searchable: false},
                        {data: 'image', name: '' ,orderable: false, searchable: false},
                        {data: 'category', name: ''},
                        {data: 'action', name: '', orderable: false, searchable: false},
                    ]
                });
            });

        });
    </script>
@endsection

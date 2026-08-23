@extends('backend.admin-master')
@section('title', __('Donation Logs'))
@section('style')
    @include('backend.partials.datatable.style-enqueue')
@endsection
@section('content')
<div class="bg-d-900 border border-[#e8edf5] rounded-2xl overflow-hidden fu">
    <div class="px-6 py-4 border-b border-[#e8edf5] flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-t-500/10 flex items-center justify-center"><i class="fas fa-receipt text-t-400 text-sm"></i></div>
            <span class="text-sm font-bold text-d-100">All Donation Logs</span>
        </div>
        <div class="flex items-center gap-2">
            <select name="bulk_option" id="bulk_option" class="bg-d-800 border border-[#e8edf5] rounded-xl px-3 py-2 text-xs text-d-200 outline-none focus:border-t-500 transition">
                <option value="">{{{__('Bulk Action')}}}</option>
                <option value="delete">{{{__('Delete')}}}</option>
            </select>
            <button class="px-4 py-2 rounded-xl bg-d-800 border border-[#e8edf5] hover:bg-[#f2f6fc] text-d-200 text-xs font-semibold transition" id="bulk_delete_btn">{{__('Apply')}}</button>
        </div>
    </div>
    <div class="p-6">
        <x-msg.error/>
        <x-msg.success/>
        <div class="data-tables datatable-primary table-responsive table-wrap">
            <table id="all_user_table" class="w-full">
                <thead class="text-capitalize">
                <tr>
                    <th class="no-sort">
                        <div class="mark-all-checkbox">
                            <input type="checkbox" class="all-checkbox">
                        </div>
                    </th>
                    <th>{{__('ID')}}</th>
                    <th>{{__('Info')}}</th>
                    <th>{{__('Status')}}</th>
                    <th>{{__('Action')}}</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
    @include('backend.partials.datatable.script-enqueue',['only_js' => true])
    <script type="text/javascript">
        $(function () {
            <x-bulk-action-js :url="route('admin.donations.payment.bulk.action')"/>

            $(document).ready(function (){
                $('.table-wrap > table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('admin.donations.payment.logs') }}",
                    columns: [
                        {data: 'checkbox', name: '', orderable: false, searchable: false},
                        {data: 'id', name: 'id'},
                        {data: 'info', name: '' ,orderable: false, searchable: false},
                        {data: 'status'},
                        {data: 'action', name: '', orderable: false, searchable: false},
                    ]
                });
            });

        });
    </script>
@endsection

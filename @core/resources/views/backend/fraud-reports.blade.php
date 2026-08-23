@extends("backend.admin-master")
@section("title", __("Fraud Reports"))
@section("content")
<div class="bg-d-900 border border-[#e8edf5] rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full" id="fraudReportsTable">
            <thead><tr class="border-b border-[#e8edf5]">
                <th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider"><input type="checkbox" id="select-all" class="rounded border-[#e8edf5] bg-d-800 text-t-500"></th>
                <th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">ID</th>
                <th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Campaign</th>
                <th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Score</th>
                <th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Risk Level</th>
                <th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Recommendation</th>
                <th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Date</th>
                <th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Action</th>
            </tr></thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@endsection
@push("script")
<script>
$(function(){
    $("#fraudReportsTable").DataTable({processing:true,serverSide:true,ajax:"{{route('admin.fraud.reports')}}",columns:[
        {data:"checkbox",orderable:false,searchable:false},{data:"id"},{data:"campaign_title",name:"campaign.title"},
        {data:"score_badge",orderable:false,searchable:false},{data:"risk_badge",orderable:false,searchable:false},
        {data:"status_badge",orderable:false,searchable:false},{data:"recommendation"},{data:"created_at"},
        {data:"action",orderable:false,searchable:false}
    ],order:[[1,"desc"]],language:{emptyTable:"<span class='text-d-500'>No fraud reports found</span>"}});
    $("#select-all").on("click",function(){$("input[type='checkbox']").not(this).prop("checked",this.checked)});
});
</script>
@endpush

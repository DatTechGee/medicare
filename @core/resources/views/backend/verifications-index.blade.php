@extends("backend.admin-master")
@section("title", __("Campaign Verifications"))
@section("content")
<div class="bg-d-900 border border-[#e8edf5] rounded-2xl overflow-hidden">
    <div class="px-6 py-4 border-b border-[#e8edf5] flex items-center justify-between">
        <div class="flex items-center gap-2.5"><div class="w-8 h-8 rounded-lg bg-t-500/10 flex items-center justify-center"><i class="fas fa-check-double text-t-400 text-sm"></i></div><span class="text-sm font-bold text-d-100">Verification Management</span></div>
        <span class="px-3 py-1 rounded-lg bg-amber-500/10 text-amber-400 text-xs font-semibold"><i class="fas fa-clock mr-1"></i>{{$pendingCount}} Pending</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full" id="verificationsTable">
            <thead><tr class="border-b border-[#e8edf5]">
                <th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider"><input type="checkbox" id="select-all" class="rounded border-[#e8edf5] bg-d-800 text-t-500"></th>
                <th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">ID</th>
                <th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Campaign</th>
                <th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Type</th>
                <th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Verified By</th>
                <th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Date</th>
                <th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Action</th>
            </tr></thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div x-data="{show:false,formAction:''}" x-show="show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
    <div @click.outside="show=false" class="bg-d-900 border border-[#e8edf5] rounded-2xl shadow-2xl w-full max-w-md p-6">
        <h3 class="text-sm font-bold text-d-100 mb-4">Confirm Verification</h3>
        <p class="text-xs text-d-400 mb-4">Are you sure you want to verify this item?</p>
        <form :action="formAction" method="POST">@csrf
            <div class="mb-4"><label class="block text-[11px] text-d-500 font-semibold uppercase mb-1.5">Notes (Optional)</label><textarea name="notes" rows="2" class="w-full bg-d-800 border border-[#e8edf5] rounded-xl px-4 py-2.5 text-sm text-d-100 placeholder-d-600 focus:border-t-500 focus:ring-1 focus:ring-t-500/30 outline-none transition"></textarea></div>
            <div class="flex gap-3">
                <button type="button" @click="show=false" class="flex-1 px-4 py-2.5 rounded-xl bg-d-800 border border-[#e8edf5] text-xs font-semibold text-d-300 hover:bg-[#f2f6fc] transition">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-green-500 hover:bg-green-600 text-white text-xs font-semibold transition"><i class="fas fa-check mr-1"></i>Verify</button>
            </div>
        </form>
    </div>
</div>
@endsection
@push("script")
<script>
$(function(){
    $("#verificationsTable").DataTable({processing:true,serverSide:true,ajax:"{{route('admin.verifications.all')}}",columns:[
        {data:"checkbox",orderable:false,searchable:false},{data:"id"},{data:"campaign_title",name:"campaign.title"},
        {data:"type_badge",orderable:false,searchable:false},{data:"status_badge",orderable:false,searchable:false},
        {data:"verified_by"},{data:"created_at"},{data:"action",orderable:false,searchable:false}
    ],order:[[1,"desc"]],language:{emptyTable:"<span class='text-d-500'>No verifications found</span>"}});
    $("#select-all").on("click",function(){$("input[type='checkbox']").not(this).prop("checked",this.checked)});
    $(document).on("click",".verify-btn",function(){var el=$(this);el.closest("[x-data]").get(0).__x.$data.formAction='{{route("admin.verification.verify","")}}/'+el.data("id");el.closest("[x-data]").get(0).__x.$data.show=true});
    $(document).on("click",".reject-btn",function(){var id=$(this).data("id");if(confirm("Are you sure?")){$.post('{{route("admin.verification.reject","")}}/'+id,{_token:"{{csrf_token()}}"}).done(function(){location.reload()})}});
});
</script>
@endpush

@extends("backend.admin-master")
@section("title", __("Verification Details"))
@section("content")
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-d-900 border border-[#e8edf5] rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-base font-bold text-d-100 flex items-center gap-2"><i class="fas fa-check-circle text-t-400"></i>Verification Details</h3>
            <a href="{{route("admin.verifications.all")}}" class="text-[11px] font-semibold text-t-400 hover:text-t-300 transition"><i class="fas fa-arrow-left mr-1"></i>Back to List</a>
        </div>
        <div class="space-y-4">
            <div class="flex items-center justify-between py-3 border-b border-[#e8edf5]"><span class="text-xs font-medium text-d-500">Campaign</span><span class="text-sm text-d-100 font-medium">{{optional($verification->campaign)->title ?? "-"}}</span></div>
            <div class="flex items-center justify-between py-3 border-b border-[#e8edf5]"><span class="text-xs font-medium text-d-500">Type</span><span class="px-2.5 py-1 rounded-lg bg-t-500/10 text-t-400 text-xs font-semibold">{{ucfirst($verification->type)}}</span></div>
            <div class="flex items-center justify-between py-3 border-b border-[#e8edf5]"><span class="text-xs font-medium text-d-500">Status</span>
                <span class="px-2.5 py-1 rounded-lg text-xs font-semibold {{$verification->status==="verified"?"bg-green-500/10 text-green-400":($verification->status==="rejected"?"bg-red-500/10 text-red-400":"bg-amber-500/10 text-amber-400")}}">{{ucfirst($verification->status)}}</span></div>
            <div class="flex items-center justify-between py-3 border-b border-[#e8edf5]"><span class="text-xs font-medium text-d-500">Verified By</span><span class="text-sm text-d-100">{{$verification->verified_by ?? "-"}}</span></div>
            <div class="flex items-center justify-between py-3"><span class="text-xs font-medium text-d-500">Date</span><span class="text-sm text-d-100">{{optional($verification->created_at)->format("d M Y H:i")}}</span></div>
        </div>
    </div>
    <div>
        @if($verification->status === "pending")
        <div class="bg-d-900 border border-[#e8edf5] rounded-2xl p-6">
            <h4 class="text-sm font-bold text-d-100 mb-4">Actions</h4>
            <form action="{{route("admin.verification.verify",$verification->id)}}" method="POST" class="mb-3">@csrf
                <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white text-xs font-semibold transition"><i class="fas fa-check mr-1"></i>Approve</button>
            </form>
            <form action="{{route("admin.verification.reject",$verification->id)}}" method="POST">@csrf
                <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white text-xs font-semibold transition"><i class="fas fa-times mr-1"></i>Reject</button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection

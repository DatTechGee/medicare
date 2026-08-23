@extends("backend.admin-master")
@section("title", __("Transaction Detail") . " #" . $transaction->id)
@section("content")
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-d-900 border border-[#e8edf5] rounded-2xl p-6">
        <h3 class="text-base font-bold text-d-100 mb-6 flex items-center gap-2"><i class="fas fa-link text-t-400"></i>Blockchain Transaction Details</h3>
        <div class="space-y-4">
            <div class="flex items-center justify-between py-3 border-b border-[#e8edf5]"><span class="text-xs font-medium text-d-500">Status</span>
                @if($transaction->status==="confirmed")<span class="px-2.5 py-1 rounded-lg bg-green-500/10 text-green-400 text-xs font-semibold"><i class="fas fa-check mr-1"></i>Confirmed</span>
                @elseif($transaction->status==="pending")<span class="px-2.5 py-1 rounded-lg bg-amber-500/10 text-amber-400 text-xs font-semibold"><i class="fas fa-clock mr-1"></i>Pending</span>
                @else<span class="px-2.5 py-1 rounded-lg bg-red-500/10 text-red-400 text-xs font-semibold"><i class="fas fa-times mr-1"></i>Failed</span>@endif</div>
            <div class="flex items-center justify-between py-3 border-b border-[#e8edf5]"><span class="text-xs font-medium text-d-500">Amount</span><span class="text-lg font-extrabold text-t-400">{{$transaction->formatted_amount}}</span></div>
            <div class="py-3 border-b border-[#e8edf5]"><span class="text-xs font-medium text-d-500 block mb-2">Transaction Hash</span><code class="font-mono text-[11px] text-t-400 bg-t-500/10 px-3 py-1.5 rounded-lg break-all inline-block">{{$transaction->transaction_hash}}</code></div>
            <div class="py-3 border-b border-[#e8edf5]"><span class="text-xs font-medium text-d-500 block mb-2">Wallet Address</span><code class="font-mono text-[11px] text-d-300 bg-d-800 px-3 py-1.5 rounded-lg break-all inline-block">{{$transaction->wallet_address}}</code></div>
            <div class="flex items-center justify-between py-3 border-b border-[#e8edf5]"><span class="text-xs font-medium text-d-500">Block Number</span><span class="text-xs text-d-200 font-mono">#{{$transaction->block_number}}</span></div>
            <div class="flex items-center justify-between py-3 border-b border-[#e8edf5]"><span class="text-xs font-medium text-d-500">Network</span><span class="px-2 py-0.5 rounded-md bg-t-500/10 text-t-400 text-[10px] font-semibold">{{$transaction->network}}</span></div>
            <div class="flex items-center justify-between py-3 border-b border-[#e8edf5]"><span class="text-xs font-medium text-d-500">Currency</span><span class="text-xs text-d-200">{{$transaction->currency}}</span></div>
            <div class="flex items-center justify-between py-3 border-b border-[#e8edf5]"><span class="text-xs font-medium text-d-500">Type</span><span class="px-2 py-0.5 rounded-md bg-t-500/10 text-t-400 text-[10px] font-semibold">{{ucfirst($transaction->transaction_type)}}</span></div>
            <div class="flex items-center justify-between py-3 border-b border-[#e8edf5]"><span class="text-xs font-medium text-d-500">Gas Fee</span><span class="text-xs text-d-200">{{$transaction->gas_fee}}</span></div>
            <div class="flex items-center justify-between py-3 border-b border-[#e8edf5]"><span class="text-xs font-medium text-d-500">Confirmed At</span><span class="text-xs text-d-200">{{$transaction->confirmed_at ? $transaction->confirmed_at->format("M d, Y h:i:s A") : "-"}}</span></div>
            <div class="flex items-center justify-between py-3"><span class="text-xs font-medium text-d-500">Created At</span><span class="text-xs text-d-200">{{$transaction->created_at->format("M d, Y h:i:s A")}}</span></div>
        </div>
    </div>
    <div class="space-y-6">
        <div class="bg-d-900 border border-[#e8edf5] rounded-2xl p-6">
            <h4 class="text-sm font-bold text-d-100 mb-4">Campaign Info</h4>
            @if($transaction->campaign)
            <div class="space-y-3">
                <div class="flex justify-between text-xs"><span class="text-d-500 font-medium">Title</span><span class="text-d-200 font-medium">{{optional($transaction->campaign)->title}}</span></div>
                <div class="flex justify-between text-xs"><span class="text-d-500 font-medium">Goal</span><span class="text-d-200 font-medium">{{amount_with_currency_symbol(optional($transaction->campaign)->amount)}}</span></div>
                <div class="flex justify-between text-xs"><span class="text-d-500 font-medium">Raised</span><span class="text-d-200 font-medium">{{amount_with_currency_symbol(optional($transaction->campaign)->raised)}}</span></div>
            </div>
            @else <p class="text-xs text-d-500">No campaign data</p>@endif
        </div>
        <div class="bg-d-900 border border-[#e8edf5] rounded-2xl p-6">
            <h4 class="text-sm font-bold text-d-100 mb-4">Donation Log</h4>
            @if($transaction->causeLog)
            <div class="space-y-3">
                <div class="flex justify-between text-xs"><span class="text-d-500 font-medium">Donor</span><span class="text-d-200 font-medium">{{optional($transaction->causeLog)->name}}</span></div>
                <div class="flex justify-between text-xs"><span class="text-d-500 font-medium">Email</span><span class="text-d-200 font-medium">{{optional($transaction->causeLog)->email}}</span></div>
                <div class="flex justify-between text-xs"><span class="text-d-500 font-medium">Amount</span><span class="text-d-200 font-medium">{{amount_with_currency_symbol(optional($transaction->causeLog)->amount)}}</span></div>
                <div class="flex justify-between text-xs"><span class="text-d-500 font-medium">Status</span><span class="px-2 py-0.5 rounded-md bg-green-500/10 text-green-400 text-[10px] font-semibold">{{optional($transaction->causeLog)->status}}</span></div>
            </div>
            @else <p class="text-xs text-d-500">No log data</p>@endif
        </div>
        <a href="{{route("admin.blockchain.all")}}" class="block w-full px-4 py-2.5 rounded-xl bg-t-500 hover:bg-t-600 text-white text-xs font-semibold text-center transition"><i class="fas fa-arrow-left mr-1"></i>Back to List</a>
    </div>
</div>
@endsection

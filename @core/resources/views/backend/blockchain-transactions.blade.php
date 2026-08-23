@extends("backend.admin-master")
@section("title", __("Blockchain Transactions"))
@section("content")
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-t-500 to-t-600 p-5 text-white sc fu"><i class="fas fa-link text-2xl opacity-80 mb-2"></i><div class="text-2xl font-extrabold">{{$totalTransactions}}</div><div class="text-xs opacity-80 font-medium">Total Transactions</div></div>
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-green-500 to-green-600 p-5 text-white sc fu"><i class="fas fa-wallet text-2xl opacity-80 mb-2"></i><div class="text-2xl font-extrabold">{{number_format($totalVolume, 8)}} ETH</div><div class="text-xs opacity-80 font-medium">Total Volume</div></div>
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 p-5 text-white sc fu"><i class="fas fa-clock text-2xl opacity-80 mb-2"></i><div class="text-2xl font-extrabold">{{$pendingTx}}</div><div class="text-xs opacity-80 font-medium">Pending Transactions</div></div>
</div>
<div class="bg-d-900 border border-[#e8edf5] rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full" id="blockchainTxTable">
            <thead><tr class="border-b border-[#e8edf5]">
                <th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider"><input type="checkbox" id="select-all" class="rounded border-[#e8edf5] bg-d-800 text-t-500"></th>
                <th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">ID</th>
                <th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Campaign</th>
                <th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Wallet</th>
                <th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Transaction Hash</th>
                <th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Amount</th>
                <th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Type</th>
                <th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Status</th>
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
    $("#blockchainTxTable").DataTable({processing:true,serverSide:true,ajax:"{{route('admin.blockchain.all')}}",columns:[
        {data:"checkbox",orderable:false,searchable:false},{data:"id"},{data:"campaign",name:"campaign.title"},
        {data:"wallet_short",name:"wallet_address"},{data:"hash_short",name:"transaction_hash"},
        {data:"formatted_amount",name:"amount"},{data:"type_badge",orderable:false,searchable:false},
        {data:"status_badge",orderable:false,searchable:false},{data:"created_at"},
        {data:"action",orderable:false,searchable:false}
    ],order:[[1,"desc"]],language:{emptyTable:"<span class='text-d-500'>No transactions found</span>"}});
    $("#select-all").on("click",function(){$("input[type='checkbox']").not(this).prop("checked",this.checked)});
});
</script>
@endpush

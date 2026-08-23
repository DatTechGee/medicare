@extends("backend.admin-master")

@section("title", __("Patient Wallets"))

@section("content")
<div class="mb-6 flex items-center justify-between flex-wrap gap-3">
    <div class="flex items-center gap-2.5">
        <div class="w-10 h-10 rounded-xl bg-[#4285f4]/10 flex items-center justify-center"><i class="fas fa-wallet text-[#4285f4]"></i></div>
        <div>
            <h4 class="m-0 font-extrabold text-d-100">{{__('Payout Wallet Verification')}}</h4>
            <p class="text-xs text-d-500 m-0 mt-0.5">{{__('Verify patient wallets before releasing campaign funds on-chain')}}</p>
        </div>
    </div>
    <form method="get" class="flex items-center gap-2">
        <select name="status" class="bg-d-800 border border-[#e8edf5] rounded-xl px-3 py-2 text-xs text-d-200 outline-none">
            <option value="">{{__('All wallets')}}</option>
            <option value="pending" {{request('status')==='pending'?'selected':''}}>{{__('Pending')}} ({{$pendingCount}})</option>
            <option value="verified" {{request('status')==='verified'?'selected':''}}>{{__('Verified')}}</option>
        </select>
        <input type="text" name="search" value="{{request('search')}}" placeholder="{{__('Search name / email / wallet…')}}" class="bg-d-800 border border-[#e8edf5] rounded-xl px-3 py-2 text-xs text-d-200 w-56 outline-none focus:border-[#4285f4]">
        <button class="px-4 py-2 rounded-xl bg-[#4285f4] hover:bg-[#2f6fe0] text-white text-xs font-semibold transition"><i class="fas fa-filter mr-1"></i>{{__('Filter')}}</button>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 fu">
    @forelse($patients as $p)
    <div class="sc bg-d-900 border border-[#e8edf5] rounded-2xl p-5 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-[2px]" style="background:{{ $p->wallet_verified ? 'linear-gradient(90deg,#22c55e,#86efac)' : 'linear-gradient(90deg,#f59e0b,#fcd34d)' }};"></div>
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center font-bold text-white shrink-0" style="background:linear-gradient(135deg,#4285f4,#2563eb);">{{substr($p->name,0,1)}}</div>
                <div class="min-w-0">
                    <div class="text-sm font-bold text-d-100 truncate">{{$p->name}}</div>
                    <div class="text-[11px] text-d-500 truncate">{{$p->email}}</div>
                </div>
            </div>
            @if($p->wallet_verified)
                <span class="px-2 py-1 rounded-lg bg-green-500/10 text-green-400 text-[10px] font-bold whitespace-nowrap"><i class="fas fa-check-circle mr-1"></i>{{__('Verified')}}</span>
            @else
                <span class="px-2 py-1 rounded-lg bg-amber-500/10 text-amber-400 text-[10px] font-bold whitespace-nowrap"><i class="fas fa-clock mr-1"></i>{{__('Pending')}}</span>
            @endif
        </div>

        <div class="rounded-xl bg-d-800 border border-[#e8edf5] px-3.5 py-3 mb-3">
            <div class="text-[9.5px] font-bold uppercase tracking-widest text-d-500 mb-1">{{__('Payout Wallet')}}</div>
            <code class="font-mono text-[11px] break-all {{$p->wallet_verified ? 'text-green-400' : 'text-d-200'}}">{{strtolower($p->wallet_address)}}</code>
        </div>

        <div class="flex items-center justify-between text-[11px] text-d-500 mb-4">
            <span><i class="fas fa-clock mr-1"></i>{{optional($p->wallet_connected_at)->diffForHumans() ?? '-'}}</span>
            <span>@if($p->wallet_verified && $p->wallet_verified_at)<i class="fas fa-shield-alt mr-1"></i>{{__('verified')}} {{optional($p->wallet_verified_at)->diffForHumans()}}@endif</span>
        </div>

        <div class="flex gap-2">
            @if(!$p->wallet_verified)
            <form method="post" action="{{route('admin.patient.wallet.verify',$p->id)}}" class="flex-1">
                @csrf
                <button class="w-full px-4 py-2.5 rounded-xl bg-gradient-to-r from-[#4285f4] to-[#2563eb] text-white text-xs font-semibold transition hover:opacity-90"><i class="fas fa-check mr-1"></i>{{__('Verify Wallet')}}</button>
            </form>
            @else
            <form method="post" action="{{route('admin.patient.wallet.reject',$p->id)}}" class="flex-1" onsubmit="return confirm('{{__('Revoke verification for this wallet? Payouts will be blocked until re-verified.')}}')">
                @csrf
                <button class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#e8edf5] hover:bg-red-50 hover:text-red-500 hover:border-red-200 text-d-300 text-xs font-semibold transition"><i class="fas fa-ban mr-1"></i>{{__('Revoke Verification')}}</button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <div class="col-span-full bg-d-900 border border-[#e8edf5] rounded-2xl p-12 text-center">
        <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-d-800 flex items-center justify-center"><i class="fas fa-wallet text-d-500 text-xl"></i></div>
        <h5 class="text-sm font-bold text-d-100 m-0">{{__('No connected wallets yet')}}</h5>
        <p class="text-xs text-d-500 mt-1 mb-0">{{__('Wallets appear here when patients connect MetaMask from their dashboard.')}}</p>
    </div>
    @endforelse
</div>

<div class="mt-6">{{$patients->links()}}</div>
@endsection

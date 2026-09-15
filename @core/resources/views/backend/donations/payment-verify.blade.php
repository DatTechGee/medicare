@extends("backend.admin-master")
@section("title", __("Payment Verify"))
@section("content")

<div class="mb-6 flex items-center gap-3">
    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#4285f4] to-[#2563eb] flex items-center justify-center shadow-lg shadow-[#4285f4]/20">
        <i class="fas fa-credit-card text-white text-sm"></i>
    </div>
    <div>
        <h2 class="text-lg font-bold text-d-100">{{__("Payment Verify")}}</h2>
        <p class="text-xs text-d-500">{{__("Monitor donations, verify wallets and manage payment flows.")}}</p>
    </div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card card-body p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(66,133,244,.1);color:#4285f4;"><i class="fas fa-coins"></i></div>
            <div><p class="text-[10px] font-bold text-d-500 uppercase m-0">Total Raised</p><p class="text-lg font-extrabold text-d-100 m-0">{{amount_with_currency_symbol($totalRaised)}}</p></div>
        </div>
    </div>
    <div class="card card-body p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(34,197,94,.1);color:#22c55e;"><i class="fas fa-check-double"></i></div>
            <div><p class="text-[10px] font-bold text-d-500 uppercase m-0">Confirmed Payments</p><p class="text-lg font-extrabold text-d-100 m-0">{{$totalDonations}}</p></div>
        </div>
    </div>
    <div class="card card-body p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(251,191,36,.1);color:#fbbf24;"><i class="fas fa-hourglass-half"></i></div>
            <div><p class="text-[10px] font-bold text-d-500 uppercase m-0">Pending Wallet Verif.</p><p class="text-lg font-extrabold text-amber-500 m-0">{{$pendingWalletVerifications}}</p></div>
        </div>
    </div>
    <div class="card card-body p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(239,68,68,.1);color:#ef4444;"><i class="fas fa-paper-plane"></i></div>
            <div><p class="text-[10px] font-bold text-d-500 uppercase m-0">Pending Withdrawals</p><p class="text-lg font-extrabold text-red-500 m-0">{{$pendingWithdrawals}}</p></div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
    <a href="{{route("admin.donations.payment.logs")}}" class="card card-body p-5 flex items-center gap-4 hover:shadow-lg transition group">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center group-hover:scale-110 transition" style="background:rgba(66,133,244,.1);color:#4285f4;"><i class="fas fa-list text-lg"></i></div>
        <div><p class="text-sm font-bold text-d-100 m-0">Donation Payment Logs</p><p class="text-xs text-d-500 m-0">All confirmed & pending donations</p></div>
    </a>
    <a href="{{route("admin.patient.wallets")}}" class="card card-body p-5 flex items-center gap-4 hover:shadow-lg transition group">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center group-hover:scale-110 transition" style="background:rgba(251,191,36,.1);color:#fbbf24;"><i class="fas fa-wallet text-lg"></i></div>
        <div><p class="text-sm font-bold text-d-100 m-0">Wallet Verification</p><p class="text-xs text-d-500 m-0">Verify patient MetaMask wallets</p></div>
    </a>
    @canany(["donation-withdraw-list"])
    <a href="{{route("admin.all.donation.withdraw.request")}}" class="card card-body p-5 flex items-center gap-4 hover:shadow-lg transition group">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center group-hover:scale-110 transition" style="background:rgba(239,68,68,.1);color:#ef4444;"><i class="fas fa-paper-plane text-lg"></i></div>
        <div><p class="text-sm font-bold text-d-100 m-0">Withdrawal Requests</p><p class="text-xs text-d-500 m-0">Approve or reject payout requests</p></div>
    </a>
    <a href="{{route("admin.donations.escrow.index")}}" class="card card-body p-5 flex items-center gap-4 hover:shadow-lg transition group">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center group-hover:scale-110 transition" style="background:rgba(99,102,241,.1);color:#6366f1;"><i class="fas fa-vault text-lg"></i></div>
        <div><p class="text-sm font-bold text-d-100 m-0">Escrow Disbursements</p><p class="text-xs text-d-500 m-0">Release funds from smart-contract escrow</p></div>
    </a>
    @endcanany
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="px-5 py-4 border-b border-[#e8edf5] flex items-center justify-between">
            <h3 class="text-sm font-bold text-d-100 m-0"><i class="fas fa-wave-square mr-2 text-t-400"></i>{{__("Recent Confirmed Donations")}}</h3>
            <a href="{{route("admin.donations.payment.logs")}}" class="text-[11px] font-semibold text-[#4285f4] hover:underline">View All <i class="fas fa-arrow-right ml-1"></i></a>
        </div>
        @if($recentDonations->isEmpty())
            <div class="text-center py-10 text-d-500 text-sm">No confirmed donations yet.</div>
        @else
        <div class="overflow-x-auto">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Campaign</th>
                        <th>Donor</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentDonations as $d)
                    <tr>
                        <td class="ps-4 text-xs text-d-400 font-medium">{{$d->id}}</td>
                        <td class="text-xs font-semibold text-d-100">{{Str::limit(optional($d->cause)->title ?? '-', 35)}}</td>
                        <td class="text-xs text-d-300">{{optional($d->user)->name ?? $d->name ?? 'Anonymous'}}</td>
                        <td class="text-xs font-bold text-d-100">{{amount_with_currency_symbol($d->amount)}}</td>
                        <td>
                            @if($d->status === 'complete')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-500/10 text-green-500"><i class="fas fa-check"></i>Confirmed</span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-500"><i class="fas fa-clock"></i>{{ucfirst($d->status)}}</span>
                            @endif
                        </td>
                        <td class="text-[11px] text-d-500">{{$d->created_at->diffForHumans()}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

@endsection
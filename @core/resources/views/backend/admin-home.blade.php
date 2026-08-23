@extends("backend.admin-master")
@section("title", __("Dashboard"))
@section("content")
<?php
    $totalCampaigns = \App\Cause::count();
    $activeCampaigns = \App\Cause::where("status","publish")->count();
    $pendingReview = \App\Cause::where("status","pending")->count();
    $totalRaised = \App\CauseLogs::where("status","complete")->sum("amount");
    $blockchainTxCount = \App\BlockchainTransaction::count();
    $pendingVerifications = \App\Verification::where("status","pending")->count();
    $flaggedCampaigns = \App\FraudReport::where("status","flagged")->count();
    $totalEscrow = \App\Escrow::where("status","held")->count();
    $recentDonations = \App\CauseLogs::where("status","complete")->with("cause")->latest()->take(6)->get();
    $recentTx = \App\BlockchainTransaction::with("campaign")->latest()->take(5)->get();
    $fraudStats = \App\Helpers\FraudEngine::getDashboardStats();

    /* donation flow - last 14 days */
    $dailyRaw = \App\CauseLogs::where("status","complete")
        ->where("created_at",">=",now()->subDays(13)->startOfDay())
        ->selectRaw("DATE(created_at) as d, SUM(amount) as s")
        ->groupBy("d")->pluck("s","d")->toArray();
    $flowLabels = []; $flowData = [];
    for ($i=13; $i>=0; $i--) {
        $day = now()->subDays($i)->toDateString();
        $flowLabels[] = now()->subDays($i)->format("d M");
        $flowData[] = isset($dailyRaw[$day]) ? round((float)$dailyRaw[$day],2) : 0;
    }
    /* top campaigns by progress */
    $topCampaigns = \App\Cause::where("status","publish")->where("raised",">",0)
        ->orderByDesc("raised")->take(5)->get(["id","title","slug","raised","amount"]);

    /* pending disbursements: withdrawal requests awaiting admin approval/payout */
    $pendingDisbursements = \App\DonationWithdraw::where("payment_status","pending")->orderByDesc("id")->take(5)->get();
    $pendingDisburseCount = \App\DonationWithdraw::where("payment_status","pending")->count();
    $approvedUnpaid = \App\DonationWithdraw::where("payment_status","approved")->where("transaction_status","!=","full-paid")->count();

    /* pending campaign posts submitted by patients */
    $pendingPosts = \App\Cause::with("user")->where("status","pending")->orderByDesc("id")->take(5)->get();
?>

<div class="relative overflow-hidden rounded-2xl mb-6 fu" style="background:linear-gradient(120deg,#ffffff 0%,#f4f8ff 55%,#eef4ff 100%);border:1px solid #dce7f7;box-shadow:0 2px 12px rgba(30,41,59,.05);">
    <div class="absolute inset-0" style="background:radial-gradient(500px 200px at 85% 20%,rgba(66,133,244,.10),transparent 60%),radial-gradient(400px 180px at 10% 90%,rgba(14,165,164,.08),transparent 60%);"></div>
    <div class="relative px-7 py-6 flex items-center justify-between flex-wrap gap-4">
        <div>
            <div class="text-[11px] font-bold uppercase tracking-widest text-d-500 mb-1">{{date('l, d F Y')}}</div>
            <h4 class="text-xl font-extrabold text-d-100 m-0">{{__('Welcome back')}}{{auth()->guard('admin')->check() ? ', '.explode(' ',auth()->guard('admin')->user()->name)[0] : ''}} <span style="color:#4285f4;">&bull;</span> {{__('MediFund Control Center')}}</h4>
            <p class="text-xs text-d-400 mt-1.5 mb-0"><i class="fas fa-shield-alt mr-1" style="color:#4285f4;"></i>{{__('Screening campaigns, verifying hospitals and tracking on-chain donations')}}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{route('admin.donations.new')}}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-gradient-to-r from-[#4285f4] to-[#2563eb] hover:from-[#2f6fe0] hover:to-[#1e56c8] text-white text-xs font-semibold transition shadow-lg shadow-[#4285f4]/30"><i class="fas fa-plus text-[10px]"></i>{{__('New Campaign')}}</a>
            <a href="{{route('admin.fraud.dashboard')}}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-white hover:bg-[#f2f6fc] border border-[#dbe5f2] text-d-200 text-xs font-semibold transition"><i class="fas fa-flag text-red-400 text-[10px]"></i>{{__('Fraud Center')}}</a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="sc bg-d-900 border border-[#e8edf5] rounded-2xl p-5 relative overflow-hidden fu">
        <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-[#4285f4] to-[#5b9bf8]"></div>
        <div class="flex items-center justify-between">
            <div><div class="text-3xl font-extrabold tracking-tight">{{$totalCampaigns}}</div><div class="text-[11px] text-d-500 font-medium uppercase tracking-wider mt-1">Total Campaigns</div></div>
            <div class="w-11 h-11 rounded-xl bg-[#4285f4]/10 flex items-center justify-center"><i class="fas fa-file-medical text-[#4285f4]"></i></div>
        </div>
    </div>
    <div class="sc bg-d-900 border border-[#e8edf5] rounded-2xl p-5 relative overflow-hidden fu">
        <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-green-500 to-green-400"></div>
        <div class="flex items-center justify-between">
            <div><div class="text-3xl font-extrabold tracking-tight text-green-400">{{$activeCampaigns}}</div><div class="text-[11px] text-d-500 font-medium uppercase tracking-wider mt-1">Active</div></div>
            <div class="w-11 h-11 rounded-xl bg-green-500/10 flex items-center justify-center"><i class="fas fa-check-circle text-green-400"></i></div>
        </div>
    </div>
    <div class="sc bg-d-900 border border-[#e8edf5] rounded-2xl p-5 relative overflow-hidden fu">
        <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-amber-500 to-amber-400"></div>
        <div class="flex items-center justify-between">
            <div><div class="text-3xl font-extrabold tracking-tight text-amber-400">{{$pendingReview}}</div><div class="text-[11px] text-d-500 font-medium uppercase tracking-wider mt-1">Pending Review</div></div>
            <div class="w-11 h-11 rounded-xl bg-amber-500/10 flex items-center justify-center"><i class="fas fa-clock text-amber-400"></i></div>
        </div>
    </div>
    <div class="sc bg-d-900 border border-[#e8edf5] rounded-2xl p-5 relative overflow-hidden fu">
        <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-[#4285f4] to-[#5b9bf8]"></div>
        <div class="flex items-center justify-between">
            <div><div class="text-3xl font-extrabold tracking-tight text-[#4285f4]">{{amount_with_currency_symbol($totalRaised)}}</div><div class="text-[11px] text-d-500 font-medium uppercase tracking-wider mt-1">Total Raised</div><div class="text-[10px] text-d-600 mt-0.5">&asymp; {{number_format($totalRaised/3450, 2)}} ETH on-chain</div></div>
            <div class="w-11 h-11 rounded-xl bg-[#4285f4]/10 flex items-center justify-center"><i class="fas fa-coins text-[#4285f4]"></i></div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <a href="{{route("admin.fraud.reports")}}" class="sc bg-d-900 border border-[#e8edf5] rounded-2xl p-5 relative overflow-hidden fu">
        <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-red-500 to-red-400"></div>
        <div class="flex items-center justify-between">
            <div><div class="text-2xl font-extrabold tracking-tight text-red-400">{{$flaggedCampaigns}}</div><div class="text-[11px] text-d-500 font-medium uppercase tracking-wider mt-1">Flagged</div></div>
            <div class="w-11 h-11 rounded-xl bg-red-500/10 flex items-center justify-center"><i class="fas fa-exclamation-triangle text-red-400"></i></div>
        </div>
    </a>
    <a href="{{route("admin.verifications.all")}}" class="sc bg-d-900 border border-[#e8edf5] rounded-2xl p-5 relative overflow-hidden fu">
        <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-amber-500 to-amber-400"></div>
        <div class="flex items-center justify-between">
            <div><div class="text-2xl font-extrabold tracking-tight text-amber-400">{{$pendingVerifications}}</div><div class="text-[11px] text-d-500 font-medium uppercase tracking-wider mt-1">Hospital Verifications</div></div>
            <div class="w-11 h-11 rounded-xl bg-amber-500/10 flex items-center justify-center"><i class="fas fa-hospital text-amber-400"></i></div>
        </div>
    </a>
    <a href="{{route("admin.blockchain.all")}}" class="sc bg-d-900 border border-[#e8edf5] rounded-2xl p-5 relative overflow-hidden fu">
        <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-[#4285f4] to-[#5b9bf8]"></div>
        <div class="flex items-center justify-between">
            <div><div class="text-2xl font-extrabold tracking-tight text-[#4285f4]">{{$blockchainTxCount}}</div><div class="text-[11px] text-d-500 font-medium uppercase tracking-wider mt-1">Blockchain Txns</div></div>
            <div class="w-11 h-11 rounded-xl bg-[#4285f4]/10 flex items-center justify-center"><i class="fas fa-link text-[#4285f4]"></i></div>
        </div>
    </a>
    <div class="sc bg-d-900 border border-[#e8edf5] rounded-2xl p-5 relative overflow-hidden fu">
        <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-[#4285f4] to-[#5b9bf8]"></div>
        <div class="flex items-center justify-between">
            <div><div class="text-2xl font-extrabold tracking-tight text-[#4285f4]">{{$totalEscrow}}</div><div class="text-[11px] text-d-500 font-medium uppercase tracking-wider mt-1">In Escrow</div></div>
            <div class="w-11 h-11 rounded-xl bg-[#4285f4]/10 flex items-center justify-center"><i class="fas fa-lock text-[#4285f4]"></i></div>
        </div>
    </div>
</div>

{{-- ============ ACTION REQUIRED: disbursements + patient posts ============ --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Pending payments to disburse --}}
    <div class="bg-d-900 border border-[#e8edf5] rounded-2xl overflow-hidden fu">
        <div class="px-6 py-4 border-b border-[#e8edf5] flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center"><i class="fas fa-hand-holding-usd text-amber-400 text-sm"></i></div>
                <span class="text-sm font-bold text-d-100">Pending Payments to Disburse</span>
            </div>
            <span class="text-[10px] font-bold px-2 py-1 rounded-full {{ $pendingDisburseCount > 0 ? 'bg-amber-500/15 text-amber-400' : 'bg-green-500/15 text-green-400'}}">{{$pendingDisburseCount}} awaiting</span>
        </div>
        <div class="px-6 py-3">
            @if($pendingDisburseCount === 0)
                <p class="text-xs text-d-500 py-3 m-0"><i class="fas fa-check-circle text-green-400 mr-1"></i>{{__('No pending payouts. All withdrawal requests are settled.')}}</p>
            @else
                @foreach($pendingDisbursements as $wd)
                    <div class="flex items-center justify-between py-2 border-b border-[#eef2f8] last:border-0">
                        <div class="min-w-0">
                            <a href="{{route('admin.donations.withdraw.view', $wd->id)}}" class="text-xs font-semibold text-d-200 hover:text-[#4285f4] truncate block">{{optional($wd->cause)->title ?? ('Withdrawal #'.$wd->id)}}</a>
                            <span class="text-[10px] text-d-600">{{optional($wd->user)->username ?? 'user #'.$wd->user_id}} &bull; requested {{\Carbon\Carbon::parse($wd->created_at)->diffForHumans()}}</span>
                        </div>
                        <div class="text-right shrink-0 ml-3">
                            <div class="text-xs font-bold text-amber-500">{{amount_with_currency_symbol($wd->withdraw_request_amount ?? $wd->withdraw_amount)}}</div>
                            <a href="{{route('admin.all.donation.withdraw.request')}}" class="text-[10px] font-semibold text-[#4285f4]">{{__('Review')}} &rarr;</a>
                        </div>
                    </div>
                @endforeach
                <div class="pt-2">
                    <a href="{{route('admin.all.donation.withdraw.request')}}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#4285f4] hover:underline"><i class="fas fa-external-link-alt text-[9px]"></i>{{__('Open withdrawal queue')}}</a>
                </div>
            @endif
            @if($approvedUnpaid > 0)
                <p class="text-[10px] text-d-600 mt-2 mb-0"><i class="fas fa-info-circle mr-1"></i>{{$approvedUnpaid}} {{__('approved payout(s) not yet marked full-paid')}}</p>
            @endif
        </div>
    </div>

    {{-- Pending campaign posts by patients --}}
    <div class="bg-d-900 border border-[#e8edf5] rounded-2xl overflow-hidden fu">
        <div class="px-6 py-4 border-b border-[#e8edf5] flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#4285f4]/10 flex items-center justify-center"><i class="fas fa-inbox text-[#4285f4] text-sm"></i></div>
                <span class="text-sm font-bold text-d-100">Patient Posts Awaiting Review</span>
            </div>
            <span class="text-[10px] font-bold px-2 py-1 rounded-full {{ $pendingPosts->count() > 0 ? 'bg-[#4285f4]/15 text-[#4285f4]' : 'bg-green-500/15 text-green-400'}}">{{$pendingPosts->count()}} pending</span>
        </div>
        <div class="px-6 py-3">
            @if($pendingPosts->isEmpty())
                <p class="text-xs text-d-500 py-3 m-0"><i class="fas fa-check-circle text-green-400 mr-1"></i>{{__('No new submissions. Patient campaigns are all reviewed.')}}</p>
            @else
                @foreach($pendingPosts as $pc)
                    <div class="flex items-center justify-between gap-3 py-2 border-b border-[#eef2f8] last:border-0">
                        <div class="min-w-0">
                            <span class="text-xs font-semibold text-d-200 truncate block">{{\Illuminate\Support\Str::limit($pc->title, 42)}}</span>
                            <span class="text-[10px] text-d-600">by {{optional($pc->user)->username ?? 'user #'.$pc->user_id}}{{ $pc->fraud_score ? " • risk score {$pc->fraud_score}" : ''}}</span>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <form method="POST" action="{{route('admin.donation.approve')}}" onsubmit="return confirm('Approve and publish this campaign?')">
                                @csrf
                                <input type="hidden" name="id" value="{{$pc->id}}">
                                <button class="btn btn-success btn-sm px-2 py-1 text-[10px]" title="Approve"><i class="fas fa-check"></i></button>
                            </form>
                            <form method="POST" action="{{route('admin.donations.flag.fraud', $pc->id)}}" onsubmit="return confirm('Flag this campaign as FRAUD? It will be blocked from donations.')">
                                @csrf
                                <button class="btn btn-danger btn-sm px-2 py-1 text-[10px]" title="Flag as fraud"><i class="fas fa-flag"></i></button>
                            </form>
                        </div>
                    </div>
                @endforeach
                <div class="pt-2">
                    <a href="{{route('admin.donations.pending.all')}}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#4285f4] hover:underline"><i class="fas fa-external-link-alt text-[9px]"></i>{{__('Open review queue')}}</a>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-d-900 border border-[#e8edf5] rounded-2xl overflow-hidden fu">
        <div class="px-6 py-4 border-b border-[#e8edf5] flex items-center justify-between">
            <div class="flex items-center gap-2.5"><div class="w-8 h-8 rounded-lg bg-[#4285f4]/10 flex items-center justify-center"><i class="fas fa-chart-line text-[#4285f4] text-sm"></i></div><span class="text-sm font-bold text-d-100">Donation Flow &mdash; Last 14 Days</span></div>
            <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-green-500/15 text-green-400">{{amount_with_currency_symbol($totalRaised)}} total</span>
        </div>
        <div class="p-4"><canvas id="flowChart" height="118"></canvas></div>
    </div>
    <div class="bg-d-900 border border-[#e8edf5] rounded-2xl overflow-hidden fu">
        <div class="px-6 py-4 border-b border-[#e8edf5] flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-red-500/10 flex items-center justify-center"><i class="fas fa-shield-alt text-red-400 text-sm"></i></div><span class="text-sm font-bold text-d-100">Risk Distribution</span>
        </div>
        <div class="p-4"><canvas id="riskChart" height="150"></canvas></div>
    </div>
</div>

@if($topCampaigns->count())
<div class="bg-d-900 border border-[#e8edf5] rounded-2xl overflow-hidden mb-6 fu">
    <div class="px-6 py-4 border-b border-[#e8edf5] flex items-center justify-between">
        <div class="flex items-center gap-2.5"><div class="w-8 h-8 rounded-lg bg-green-500/10 flex items-center justify-center"><i class="fas fa-fire text-amber-400 text-sm"></i></div><span class="text-sm font-bold text-d-100">Top Performing Campaigns</span></div>
        <a href="{{route("admin.donations.all")}}" class="text-[11px] font-semibold text-[#4285f4] hover:text-[#2563eb] transition">All Campaigns <i class="fas fa-arrow-right text-[9px] ml-1"></i></a>
    </div>
    <div class="p-6 space-y-4">
        @foreach($topCampaigns as $tc)
        @php $pct = min(100, round(($tc->raised / max($tc->amount,1)) * 100)); @endphp
        <a href="{{route("admin.donations.edit",$tc->id)}}" class="block group">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-xs font-semibold text-d-200 group-hover:text-[#2563eb] transition truncate max-w-[70%]">{{$tc->title}}</span>
                <span class="text-[11px] text-d-500 shrink-0 ml-3"><span class="font-bold text-d-100">{{amount_with_currency_symbol($tc->raised)}}</span> of {{amount_with_currency_symbol($tc->amount)}}</span>
            </div>
                <div class="h-2 rounded-full bg-[#eef3fa] overflow-hidden">
                <div class="h-full rounded-full transition-all duration-700" style="width:{{$pct}}%;background:{{ $pct >= 75 ? 'linear-gradient(90deg,#22c55e,#86efac)' : ($pct >= 40 ? 'linear-gradient(90deg,#4285f4,#93b8f9)' : 'linear-gradient(90deg,#f59e0b,#fcd34d)') }}"></div>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif

@php $total = max($fraudStats["total_campaigns"], 1); @endphp
<div class="bg-d-900 border border-[#e8edf5] rounded-2xl overflow-hidden mb-6 fu">
    <div class="px-6 py-4 border-b border-[#e8edf5] flex items-center justify-between">
        <div class="flex items-center gap-2.5"><div class="w-8 h-8 rounded-lg bg-[#4285f4]/10 flex items-center justify-center"><i class="fas fa-shield-alt text-[#4285f4] text-sm"></i></div><span class="text-sm font-bold text-d-100">Fraud Detection Overview</span></div>
        <a href="{{route("admin.fraud.dashboard")}}" class="text-[11px] font-semibold text-[#4285f4] hover:text-[#2563eb] transition">Full Dashboard <i class="fas fa-arrow-right text-[9px] ml-1"></i></a>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            <div class="text-center p-4 rounded-xl bg-d-800 border border-[#e8edf5]">
                <div class="text-3xl font-extrabold text-[#4285f4]">{{$fraudStats["total_campaigns"]}}</div>
                <div class="text-[10px] text-d-500 font-semibold uppercase tracking-wider mt-1">Screened</div>
            </div>
            <div class="text-center p-4 rounded-xl bg-d-800 border border-[#e8edf5]">
                <div class="text-3xl font-extrabold text-green-400">{{$fraudStats["low_risk_count"]}}</div>
                <div class="text-[10px] text-d-500 font-semibold uppercase tracking-wider mt-1">Low Risk</div>
                <div class="h-1 rounded-full bg-[#f1f5fa] mt-3 overflow-hidden"><div class="h-full rounded-full bg-green-400 transition-all" style="width:{{round($fraudStats["low_risk_count"]/$total*100)}}%"></div></div>
            </div>
            <div class="text-center p-4 rounded-xl bg-d-800 border border-[#e8edf5]">
                <div class="text-3xl font-extrabold text-amber-400">{{$fraudStats["medium_risk_count"]}}</div>
                <div class="text-[10px] text-d-500 font-semibold uppercase tracking-wider mt-1">Medium Risk</div>
                <div class="h-1 rounded-full bg-[#f1f5fa] mt-3 overflow-hidden"><div class="h-full rounded-full bg-amber-400 transition-all" style="width:{{round($fraudStats["medium_risk_count"]/$total*100)}}%"></div></div>
            </div>
            <div class="text-center p-4 rounded-xl bg-d-800 border border-[#e8edf5]">
                <div class="text-3xl font-extrabold text-red-400">{{$fraudStats["high_risk_count"]}}</div>
                <div class="text-[10px] text-d-500 font-semibold uppercase tracking-wider mt-1">High Risk</div>
                <div class="h-1 rounded-full bg-[#f1f5fa] mt-3 overflow-hidden"><div class="h-full rounded-full bg-red-400 transition-all" style="width:{{round($fraudStats["high_risk_count"]/$total*100)}}%"></div></div>
            </div>
            <div class="text-center p-4 rounded-xl bg-d-800 border border-[#e8edf5]">
                <div class="text-3xl font-extrabold text-amber-400">{{$fraudStats["pending_review"]}}</div>
                <div class="text-[10px] text-d-500 font-semibold uppercase tracking-wider mt-1">Pending Review</div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-d-900 border border-[#e8edf5] rounded-2xl overflow-hidden fu">
        <div class="px-6 py-4 border-b border-[#e8edf5] flex items-center justify-between">
            <div class="flex items-center gap-2.5"><div class="w-8 h-8 rounded-lg bg-[#4285f4]/10 flex items-center justify-center"><i class="fas fa-receipt text-[#4285f4] text-sm"></i></div><span class="text-sm font-bold text-d-100">Recent Donations</span></div>
            <a href="{{route("admin.donations.all")}}" class="text-[11px] font-semibold text-[#4285f4] hover:text-[#2563eb] transition">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="border-b border-[#e8edf5]"><th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">ID</th><th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Amount</th><th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Gateway</th><th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Status</th><th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Date</th></tr></thead>
                <tbody>
                    @foreach($recentDonations as $d)
                    <tr class="border-b border-[#e8edf5] hover:bg-[#f2f6fc] transition">
                        <td class="px-6 py-3 text-xs text-d-400">#{{$d->id}}</td>
                        <td class="px-6 py-3 text-xs font-bold text-[#4285f4]">{{amount_with_currency_symbol($d->amount)}}</td>
                        <td class="px-6 py-3">@if($d->payment_gateway==="blockchain_demo")<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-[#4285f4]/10 text-[#4285f4] text-[10px] font-semibold"><i class="fas fa-link"></i> Blockchain</span>@else<span class="text-xs text-d-400">{{ucwords(str_replace("_"," ",$d->payment_gateway))}}</span>@endif</td>
                        <td class="px-6 py-3"><span class="px-2 py-0.5 rounded-md text-[10px] font-semibold {{$d->status=="complete"?"bg-green-500/10 text-green-400":"bg-amber-500/10 text-amber-400"}}">{{$d->status}}</span></td>
                        <td class="px-6 py-3 text-xs text-d-500">{{date_format($d->created_at,"d M Y")}}</td>
                    </tr>
                    @endforeach
                    @if($recentDonations->isEmpty())<tr><td colspan="5" class="px-6 py-8 text-center text-xs text-d-500">No donations yet</td></tr>@endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="bg-d-900 border border-[#e8edf5] rounded-2xl overflow-hidden fu">
        <div class="px-6 py-4 border-b border-[#e8edf5] flex items-center justify-between">
            <div class="flex items-center gap-2.5"><div class="w-8 h-8 rounded-lg bg-[#4285f4]/10 flex items-center justify-center"><i class="fas fa-link text-[#4285f4] text-sm"></i></div><span class="text-sm font-bold text-d-100">Blockchain Transactions</span></div>
            <a href="{{route("admin.blockchain.all")}}" class="text-[11px] font-semibold text-[#4285f4] hover:text-[#2563eb] transition">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="border-b border-[#e8edf5]"><th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Hash</th><th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Campaign</th><th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Amount</th><th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Status</th><th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Date</th></tr></thead>
                <tbody>
                    @forelse($recentTx as $tx)
                    <tr class="border-b border-[#e8edf5] hover:bg-[#f2f6fc] transition">
                        <td class="px-6 py-3"><code class="font-mono text-[11px] text-[#4285f4] bg-[#4285f4]/10 px-2 py-0.5 rounded">{{$tx->short_hash}}</code></td>
                        <td class="px-6 py-3 text-xs text-d-300">{{Str::limit(optional($tx->campaign)->title ?? "-",18)}}</td>
                        <td class="px-6 py-3 text-xs font-bold text-[#4285f4]">{{$tx->formatted_amount}}</td>
                        <td class="px-6 py-3"><span class="px-2 py-0.5 rounded-md bg-green-500/10 text-green-400 text-[10px] font-semibold">{{ucfirst($tx->status)}}</span></td>
                        <td class="px-6 py-3 text-xs text-d-500">{{optional($tx->created_at)->diffForHumans()}}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-xs text-d-500">No blockchain transactions yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section("script")
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function(){
    var grid = {color:'rgba(30,41,59,0.06)'};
    var ticks = {color:'#8a97ad', font:{family:'Inter',size:10}};

    var fc = document.getElementById('flowChart');
    if (fc) {
        new Chart(fc, {
            type:'line',
            data:{
                labels:@json($flowLabels),
                datasets:[{
                    label:'Donations (USD)',
                    data:@json($flowData),
                    borderColor:'#4285f4',
                    borderWidth:2.5,
                    tension:0.42,
                    fill:true,
                    pointRadius:0,
                    pointHoverRadius:5,
                    pointBackgroundColor:'#2563eb',
                    backgroundColor:(function(ctx){
                        var g = ctx.chart.ctx.createLinearGradient(0,0,0,260);
                        g.addColorStop(0,'rgba(66,133,244,0.22)');
                        g.addColorStop(1,'rgba(66,133,244,0)');
                        return g;
                    })
                }]
            },
            options:{
                responsive:true,
                maintainAspectRatio:false,
                plugins:{legend:{display:false},tooltip:{
                    backgroundColor:'#1d2635',borderColor:'transparent',borderWidth:1,
                    titleColor:'#ffffff',bodyColor:'#dbe4f2',padding:10,displayColors:false,
                    callbacks:{label:function(c){return '$'+Number(c.parsed.y).toLocaleString();}}
                }},
                scales:{
                    x:{grid:{display:false},ticks:ticks},
                    y:{grid:grid,ticks:Object.assign({callback:function(v){return '$'+(v>=1000?(v/1000)+'k':v);}},ticks),beginAtZero:true}
                }
            }
        });
    }

    var rc = document.getElementById('riskChart');
    if (rc) {
        new Chart(rc, {
            type:'doughnut',
            data:{
                labels:['Low Risk','Medium Risk','High Risk'],
                datasets:[{
                    data:[@json($fraudStats["low_risk_count"]),@json($fraudStats["medium_risk_count"]),@json($fraudStats["high_risk_count"])],
                    backgroundColor:['#22c55e','#f59e0b','#ef4444'],
                    borderColor:'#ffffff',
                    borderWidth:3,
                    hoverOffset:6
                }]
            },
            options:{
                responsive:true,
                maintainAspectRatio:false,
                cutout:'68%',
                plugins:{legend:{position:'bottom',labels:{color:'#5b6b83',boxWidth:8,boxHeight:8,padding:14,font:{family:'Inter',size:11}}},tooltip:{backgroundColor:'#1d2635',padding:10}}
            }
        });
    }
})();
</script>
@endsection

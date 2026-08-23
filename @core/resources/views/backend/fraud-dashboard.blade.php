@extends("backend.admin-master")
@section("title", __("Fraud Monitoring Dashboard"))
@section("content")
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-t-500 to-t-600 p-5 text-white sc fu">
        <i class="fas fa-shield-alt text-3xl opacity-80 mb-3"></i>
        <div class="text-3xl font-extrabold">{{$stats["total_campaigns"]}}</div>
        <div class="text-xs opacity-80 font-medium mt-1">Total Campaigns</div>
    </div>
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-red-500 to-red-600 p-5 text-white sc fu">
        <i class="fas fa-exclamation-triangle text-3xl opacity-80 mb-3"></i>
        <div class="text-3xl font-extrabold">{{$stats["flagged_campaigns"]}}</div>
        <div class="text-xs opacity-80 font-medium mt-1">Flagged Campaigns</div>
    </div>
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 p-5 text-white sc fu">
        <i class="fas fa-clock text-3xl opacity-80 mb-3"></i>
        <div class="text-3xl font-extrabold">{{$stats["pending_review"]}}</div>
        <div class="text-xs opacity-80 font-medium mt-1">Pending Review</div>
    </div>
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-green-500 to-green-600 p-5 text-white sc fu">
        <i class="fas fa-check text-3xl opacity-80 mb-3"></i>
        <div class="text-3xl font-extrabold">{{$stats["cleared_campaigns"]}}</div>
        <div class="text-xs opacity-80 font-medium mt-1">Cleared</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-d-900 border border-[#e8edf5] rounded-2xl overflow-hidden fu">
        <div class="px-6 py-4 border-b border-[#e8edf5]"><span class="text-sm font-bold text-d-100"><i class="fas fa-chart-bar text-t-400 mr-2"></i>Risk Level Distribution</span></div>
        <div class="p-6 grid grid-cols-3 gap-4">
            <div class="text-center p-6 rounded-xl bg-d-800 border border-[#e8edf5]">
                <div class="text-4xl font-extrabold text-green-400">{{$stats["low_risk_count"]}}</div>
                <div class="mt-2 inline-block px-3 py-1 rounded-lg bg-green-500/10 text-green-400 text-xs font-semibold">Low Risk</div>
                <div class="text-[11px] text-d-500 mt-2">Score 0-20</div>
            </div>
            <div class="text-center p-6 rounded-xl bg-d-800 border border-[#e8edf5]">
                <div class="text-4xl font-extrabold text-amber-400">{{$stats["medium_risk_count"]}}</div>
                <div class="mt-2 inline-block px-3 py-1 rounded-lg bg-amber-500/10 text-amber-400 text-xs font-semibold">Medium Risk</div>
                <div class="text-[11px] text-d-500 mt-2">Score 21-50</div>
            </div>
            <div class="text-center p-6 rounded-xl bg-d-800 border border-[#e8edf5]">
                <div class="text-4xl font-extrabold text-red-400">{{$stats["high_risk_count"]}}</div>
                <div class="mt-2 inline-block px-3 py-1 rounded-lg bg-red-500/10 text-red-400 text-xs font-semibold">High Risk</div>
                <div class="text-[11px] text-d-500 mt-2">Score 51-100</div>
            </div>
        </div>
    </div>
    <div class="bg-d-900 border border-[#e8edf5] rounded-2xl overflow-hidden fu">
        <div class="px-6 py-4 border-b border-[#e8edf5]"><span class="text-sm font-bold text-d-100"><i class="fas fa-tachometer-alt text-t-400 mr-2"></i>Avg Fraud Score</span></div>
        <div class="p-6 flex items-center justify-center">
            @php $avg = round($stats["avg_fraud_score"]); $color = $avg <= 20 ? "#22c55e" : ($avg <= 50 ? "#f59e0b" : "#ef4444"); @endphp
            <div class="relative">
                <svg width="140" height="140" viewBox="0 0 140 140"><circle cx="70" cy="70" r="60" fill="none" stroke="#eef3fa" stroke-width="10"/><circle cx="70" cy="70" r="60" fill="none" stroke="{{$color}}" stroke-width="10" stroke-dasharray="{{($avg/100)*377}} 377" stroke-linecap="round" style="transform:rotate(-90deg);transform-origin:center"/></svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center"><span class="text-4xl font-extrabold text-d-100">{{$avg}}</span><span class="text-xs text-d-500">/100</span></div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-d-900 border border-[#e8edf5] rounded-2xl overflow-hidden fu">
        <div class="px-6 py-4 border-b border-[#e8edf5] flex items-center justify-between"><span class="text-sm font-bold text-d-100"><i class="fas fa-flag text-red-400 mr-2"></i>Flagged Campaigns</span><a href="{{route("admin.fraud.reports")}}" class="text-[11px] font-semibold text-t-400 hover:text-t-300">View All</a></div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="border-b border-[#e8edf5]"><th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Campaign</th><th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Score</th><th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Risk</th></tr></thead>
                <tbody>
                    @forelse($flaggedCampaigns as $report)
                    <tr class="border-b border-[#e8edf5] hover:bg-[#f2f6fc]"><td class="px-6 py-3 text-xs text-d-300">{{optional($report->campaign)->title ?? "-"}}</td><td class="px-6 py-3"><span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-red-500/10 text-red-400">{{$report->fraud_score}}/100</span></td><td class="px-6 py-3">{!! $report->risk_badge !!}</td></tr>
                    @empty
                    <tr><td colspan="3" class="px-6 py-8 text-center text-xs text-d-500">No flagged campaigns</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="bg-d-900 border border-[#e8edf5] rounded-2xl overflow-hidden fu">
        <div class="px-6 py-4 border-b border-[#e8edf5] flex items-center justify-between"><span class="text-sm font-bold text-d-100"><i class="fas fa-file-alt text-t-400 mr-2"></i>Recent Fraud Reports</span><a href="{{route("admin.fraud.reports")}}" class="text-[11px] font-semibold text-t-400 hover:text-t-300">View All</a></div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="border-b border-[#e8edf5]"><th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Campaign</th><th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Score</th><th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Status</th><th class="px-6 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Date</th></tr></thead>
                <tbody>
                    @forelse($recentReports as $report)
                    <tr class="border-b border-[#e8edf5] hover:bg-[#f2f6fc]">
                        <td class="px-6 py-3"><a href="{{route("admin.fraud.view",$report->id)}}" class="text-xs text-t-400 hover:text-t-300">{{Str::limit(optional($report->campaign)->title ?? "-",25)}}</a></td>
                        <td class="px-6 py-3"><span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-t-500/10 text-t-400">{{$report->fraud_score}}</span></td>
                        <td class="px-6 py-3">
                            @php $sc = ["pending"=>"bg-amber-500/10 text-amber-400","reviewed"=>"bg-blue-500/10 text-blue-400","cleared"=>"bg-green-500/10 text-green-400","flagged"=>"bg-red-500/10 text-red-400"]; @endphp
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold {{$sc[$report->status] ?? "bg-d-700 text-d-400"}}">{{ucfirst($report->status)}}</span>
                        </td>
                        <td class="px-6 py-3 text-xs text-d-500">{{optional($report->created_at)->diffForHumans()}}</td>
                    </tr>
                        @empty
                    <tr><td colspan="4" class="px-6 py-8 text-center text-xs text-d-500">No fraud reports yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-d-900 border border-[#e8edf5] rounded-2xl overflow-hidden fu">
        <div class="px-6 py-4 border-b border-[#e8edf5]"><span class="text-sm font-bold text-d-100"><i class="fas fa-chart-simple text-t-400 mr-2"></i>Fraud Score Distribution</span></div>
        <div class="p-6 space-y-3">
            @php $maxBucket = max(1, max($buckets)); @endphp
            @foreach($buckets as $range => $count)
            <div class="flex items-center gap-3">
                <span class="text-[11px] text-d-400 w-14 font-semibold">{{ $range }}</span>
                <div class="flex-1 h-5 rounded-lg bg-[#f1f5fa] overflow-hidden">
                    @php $bc = $range === '0-20' ? '#22c55e' : ($range === '21-40' ? '#84cc16' : ($range === '41-60' ? '#f59e0b' : ($range === '61-80' ? '#f97316' : '#ef4444'))); @endphp
                    <div style="width: {{ round(($count / $maxBucket) * 100) }}%; background: linear-gradient(90deg, {{$bc}}, {{ $bc }}cc); height: 100%; border-radius: 8px;"></div>
                </div>
                <span class="text-xs font-bold text-d-200 w-6 text-right">{{ $count }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <div class="bg-d-900 border border-[#e8edf5] rounded-2xl overflow-hidden fu">
        <div class="px-6 py-4 border-b border-[#e8edf5]"><span class="text-sm font-bold text-d-100"><i class="fas fa-wave-square text-t-400 mr-2"></i>Flag Trend — Last 14 Days</span></div>
        <div class="p-6">
            <div class="flex items-end gap-[3px] h-28">
                @php $maxTrend = max(1, max($trendData)); @endphp
                @foreach($trendData as $i => $v)
                <div class="flex-1 flex flex-col items-center justify-end h-full group relative" title="{{ $trendLabels[$i] }}: {{ $v }}">
                    <div class="w-full rounded-t-sm transition-all" style="height: {{ max(4, round(($v / $maxTrend) * 100)) }}%; background: {{ $v > 0 ? 'linear-gradient(180deg,#f59e0b,#ef4444)' : '#e8edf5' }};"></div>
                </div>
                @endforeach
            </div>
            <div class="flex justify-between mt-2 text-[9.5px] text-d-500">
                <span>{{ $trendLabels[0] }}</span><span>{{ $trendLabels[7] }}</span><span>{{ $trendLabels[13] }}</span>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-d-900 border border-[#e8edf5] rounded-2xl overflow-hidden fu">
        <div class="px-6 py-4 border-b border-[#e8edf5]"><span class="text-sm font-bold text-d-100"><i class="fas fa-vault text-t-400 mr-2"></i>Escrow Pool (USD)</span></div>
        <div class="p-6 space-y-3">
            <div class="flex items-center justify-between p-3 rounded-xl bg-d-800 border border-[#e8edf5]"><span class="text-xs text-d-300 inline-flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-amber-400"></span>Held</span><b class="text-sm text-amber-400">${{ number_format($escrow['held'], 2) }}</b></div>
            <div class="flex items-center justify-between p-3 rounded-xl bg-d-800 border border-[#e8edf5]"><span class="text-xs text-d-300 inline-flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-green-400"></span>Released to beneficiaries</span><b class="text-sm text-green-400">${{ number_format($escrow['released'], 2) }}</b></div>
            <div class="flex items-center justify-between p-3 rounded-xl bg-d-800 border border-[#e8edf5]"><span class="text-xs text-d-300 inline-flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-blue-400"></span>Refunded to donors</span><b class="text-sm text-blue-400">${{ number_format($escrow['refunded'], 2) }}</b></div>
            <div class="flex items-center justify-between p-3 rounded-xl bg-red-500/[0.07] border border-red-500/20"><span class="text-xs text-red-300 inline-flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-red-400"></span>Disputed (frozen)</span><b class="text-sm text-red-400">${{ number_format($escrow['disputed'], 2) }}</b></div>
        </div>
    </div>

    <div class="bg-d-900 border border-[#e8edf5] rounded-2xl overflow-hidden fu">
        <div class="px-6 py-4 border-b border-[#e8edf5] flex items-center justify-between"><span class="text-sm font-bold text-d-100"><i class="fas fa-clone text-t-400 mr-2"></i>Duplicate Cluster Alerts</span>@if($paymentFlags)<span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-red-500/10 text-red-400">{{ $paymentFlags }} payment flags</span>@endif</div>
        <div class="p-4 space-y-2 max-h-[240px] overflow-y-auto">
            @forelse($clusters as $c)
            <a href="{{ route('admin.fraud.view', \App\FraudReport::where('campaign_id', $c['campaign_id'])->orderByDesc('id')->value('id') ?? 1) }}" class="block p-3 rounded-xl bg-d-800 border border-orange-500/20 hover:border-orange-500/40 transition">
                <div class="text-xs font-bold text-orange-300 truncate"><i class="fas fa-copy mr-1.5 opacity-70"></i>{{ Str::limit($c['title'], 34) }}</div>
                <div class="text-[10.5px] text-d-400 mt-1 leading-relaxed">{{ Str::limit($c['detail'], 90) }}</div>
            </a>
            @empty
            <div class="p-6 text-center text-xs text-d-500"><i class="fas fa-circle-check text-green-400 mr-1"></i>No duplicate clusters detected</div>
            @endforelse
        </div>
    </div>

    <div class="bg-d-900 border border-[#e8edf5] rounded-2xl overflow-hidden fu">
        <div class="px-6 py-4 border-b border-[#e8edf5] flex items-center justify-between"><span class="text-sm font-bold text-d-100"><i class="fas fa-clipboard-list text-t-400 mr-2"></i>Audit Trail — Recent Actions</span><a href="{{ route('admin.audit.logs') }}" class="text-[11px] font-semibold text-t-400 hover:text-t-300">View All</a></div>
        <div class="p-4 space-y-1.5 max-h-[240px] overflow-y-auto">
            @forelse($auditLogs as $log)
            <div class="flex items-start gap-2.5 p-2.5 rounded-lg hover:bg-[#f2f6fc]">
                <span class="mt-1 w-1.5 h-1.5 rounded-full shrink-0" style="background: {{ in_array($log->action, ['escrow_refund','wallet_blacklist','document_tamper_alert','milestone_reject']) ? '#ef4444' : '#22c55e' }};"></span>
                <div class="min-w-0">
                    <div class="text-[11px] text-d-200"><b class="text-d-100">{{ $log->user_type ?? 'system' }}</b> · <span class="font-mono text-[10px] text-t-400">{{ $log->action }}</span> · {{ $log->model }}#{{ $log->model_id }}</div>
                    <div class="text-[10px] text-d-500">{{ optional($log->created_at)->diffForHumans() }} · IP {{ $log->ip_address }}</div>
                </div>
            </div>
            @empty
            <div class="p-6 text-center text-xs text-d-500">No admin actions logged yet</div>
            @endforelse
        </div>
    </div>
</div>
@endsection

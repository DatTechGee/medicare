@extends("backend.admin-master")
@section("title", __("Campaign Approvals"))
@section("content")
@php
    $statusBadge = [
        'pending' => ['warning', 'fa-clock', 'Pending'],
        'draft'   => ['secondary', 'fa-file', 'Draft'],
        'flagged' => ['danger', 'fa-flag', 'Flagged'],
        'rejected'=> ['danger', 'fa-times', 'Rejected'],
        'publish' => ['success', 'fa-check', 'Published'],
    ];
    $vCount = $campaigns->count();
    $hCount = $campaigns->filter(fn($c) => $c->hospital_confirmed)->count();
    $flagCount = $campaigns->where('status', 'flagged')->count();
@endphp

<div class="mb-5 grid grid-cols-3 gap-4">
    <div class="rounded-xl p-4" style="background:rgba(34,197,94,.06);border:1px solid rgba(34,197,94,.18);">
        <div class="text-[11px] font-bold text-d-500 uppercase mb-1">{{ __('Campaigns Awaiting Approval') }}</div>
        <div class="text-3xl font-extrabold text-green-400">{{ $vCount }}</div>
        <div class="text-[10px] text-d-500">user-submitted campaigns not yet live</div>
    </div>
    <div class="rounded-xl p-4" style="background:rgba(139,92,246,.06);border:1px solid rgba(139,92,246,.18);">
        <div class="text-[11px] font-bold text-d-500 uppercase mb-1">{{ __('Hospital-Confirmed') }}</div>
        <div class="text-3xl font-extrabold text-violet-400">{{ $hCount }}</div>
        <div class="text-[10px] text-d-500">authenticity confirmed by treating hospital</div>
    </div>
    <div class="rounded-xl p-4" style="background:rgba(239,68,68,.06);border:1px solid rgba(239,68,68,.18);">
        <div class="text-[11px] font-bold text-d-500 uppercase mb-1">{{ __('Flagged as Fraud') }}</div>
        <div class="text-3xl font-extrabold text-red-400">{{ $flagCount }}</div>
        <div class="text-[10px] text-d-500">need manual fraud review</div>
    </div>
</div>

<div class="rounded-2xl overflow-hidden" style="background:#0f1523;border:1px solid rgba(232,237,245,.12);">
    <div class="px-6 py-4 border-b flex items-center justify-between flex-wrap gap-2" style="border-color:rgba(232,237,245,.08);">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(34,197,94,.14);"><i class="fas fa-file-circle-check text-sm" style="color:#4ade80;"></i></div>
            <span class="text-sm font-bold text-d-100">{{ __('Approvals Queue') }}</span>
        </div>
        <span class="text-[10px] font-bold px-2 py-1 rounded-full" style="background:rgba(34,197,94,.12);color:#4ade80;">{{ $vCount }} {{ __('waiting') }}</span>
    </div>

    <div class="p-5 space-y-3">
        @forelse($campaigns as $c)
            @php
                $sb = $statusBadge[$c->status] ?? ['secondary', 'fa-question', ucfirst($c->status)];
                $ml = ($c->last_report && $c->last_report->fraud_score !== null)
                    ? \App\Services\FraudMlService::predict($c, $c->last_report)
                    : null;
                $firstV = $c->verifications->first() ?? null;
            @endphp
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-3 px-4 py-3 rounded-xl" style="background:rgba(15,23,42,.55);border:1px solid rgba(148,163,184,.14);">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap mb-1.5">
                        <span class="text-[10px] font-bold text-d-400">#{{ $c->id }}</span>
                        <a href="{{ route('frontend.donations.single', $c->slug) }}" target="_blank" class="text-xs font-bold text-d-100 hover:text-[#4285f4]">{{ Str::limit($c->title, 60) }}</a>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9.5px] font-bold bg-{{ $sb[0] }}/15 text-{{ $sb[0] }}"><i class="fas {{ $sb[1] }} text-[8px]"></i>{{ $sb[2] }}</span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9.5px] font-bold bg-amber-500/15 text-amber-400"><i class="fas fa-clipboard-list text-[8px]"></i>{{ $c->verified_rows }}/{{ $c->total_rows }} checklist</span>
                        @if($c->hospital_confirmed)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9.5px] font-bold text-green-400" style="background:rgba(34,197,94,.12);"><i class="fas fa-hospital text-[8px]"></i>Hospital confirmed</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-3 text-[10px] text-d-500 flex-wrap">
                        <span><i class="fas fa-user mr-1"></i>{{ optional($c->user)->name ?? '-' }}</span>
                        <span><i class="fas fa-user-injured mr-1"></i>{{ $c->patient_name ?? '-' }}</span>
                        <span><i class="fas fa-hospital mr-1"></i>{{ $c->hospital_name ?: '—' }}</span>
                        <span><i class="fas fa-coins mr-1"></i>{{ amount_with_currency_symbol($c->amount) }}</span>
                        <span class="inline-flex items-center gap-1"><i class="fas fa-shield-halved mr-1"></i>Fraud: {{ $c->fraud_score ?? 0 }}/100</span>
                        @if($ml)
                            <span class="inline-flex items-center gap-1"><i class="fas fa-brain mr-1" style="color:#a78bfa;"></i><span style="color:#a78bfa;">ML {{ $ml['probability_pct'] }}% · {{ $ml['confidence'] }}% conf</span></span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0 flex-wrap">
                    <form action="{{ route('admin.donation.approve') }}" method="post" onsubmit="return confirm('Approve and publish this campaign?', 'All checks will be marked verified and the campaign goes live.');">
                        @csrf
                        <input type="hidden" name="id" value="{{ $c->id }}">
                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-[11px] font-bold text-white transition hover:opacity-90" style="background:linear-gradient(135deg,#16a34a,#22c55e);">
                            <i class="fas fa-check text-[10px]"></i>{{ __('Approve') }}
                        </button>
                    </form>
                    <form action="{{ route('admin.donations.flag.fraud', $c->id) }}" method="post" onsubmit="return confirm('Flag this campaign as fraud? It will be blocked from receiving donations.');">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-[11px] font-bold text-white transition hover:opacity-90" style="background:linear-gradient(135deg,#dc2626,#ef4444);">
                            <i class="fas fa-flag text-[10px]"></i>{{ __('Flag Fraud') }}
                        </button>
                    </form>
                    @if($c->last_report)
                        <a href="{{ route('admin.fraud.view', $c->last_report->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-[11px] font-bold text-white transition hover:opacity-90" style="background:linear-gradient(135deg,#7c3aed,#8b5cf6);">
                            <i class="fas fa-file-lines text-[10px]"></i>{{ __('Fraud Report') }}
                        </a>
                    @endif
                    @if($firstV)
                        <a href="{{ route('admin.verification.view', $firstV->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-[11px] font-bold transition" style="background:rgba(148,163,184,.1);border:1px solid rgba(148,163,184,.28);color:#e2e8f0;">
                            <i class="fas fa-clipboard-list text-[10px]"></i>{{ __('Checklist') }}
                        </a>
                    @endif
                    <a href="{{ route('admin.donations.edit', $c->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-[11px] font-bold transition" style="background:rgba(148,163,184,.1);border:1px solid rgba(148,163,184,.28);color:#e2e8f0;">
                        <i class="fas fa-edit text-[10px]"></i>{{ __('Edit') }}
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center py-8">
                <i class="fas fa-circle-check text-green-400 text-2xl mb-2"></i>
                <p class="text-xs text-d-500">{{__('No campaigns awaiting approval — everything is live or flagged.')}}</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
@extends("backend.admin-master")
@section("title", __("Campaign Verifications"))
@section("content")
@php
    $statusMap = ['pending' => 'warning', 'verified' => 'success', 'rejected' => 'danger'];
    $typeMap   = ['patient' => ['primary', 'fa-user-md'], 'hospital' => ['info', 'fa-hospital'], 'document' => ['warning', 'fa-file-medical-alt'], 'amount' => ['secondary', 'fa-dollar-sign']];
@endphp

@if(optional($pendingCampaigns)->count())
<div class="mb-5 rounded-xl overflow-hidden" style="border:1px solid rgba(251,191,36,.28); background:linear-gradient(135deg, rgba(251,191,36,.06), rgba(24,24,27,.9));">
    <div class="px-6 py-4 border-b flex items-center justify-between flex-wrap gap-2" style="border-color:rgba(251,191,36,.18);">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(251,191,36,.14);"><i class="fas fa-clock text-sm" style="color:#fbbf24;"></i></div>
            <span class="text-sm font-bold text-d-100">{{__('Pending Campaigns')}}</span>
            <span class="text-[10px] font-bold px-2 py-1 rounded-full text-amber-400" style="background:rgba(251,191,36,.14);">{{ $pendingCampaigns->count() }} {{__('awaiting review')}}</span>
        </div>
    </div>
    <div class="p-5 space-y-3">
        @forelse($pendingCampaigns as $p)
            @php
                $rowCount = $p->verification_rows ?? 0;
                $pStatus  = $p->verification_status ?? 'pending';
                $pBadge   = [
                    'pending'      => ['warning',  'fa-clock',    'Pending Review'],
                    'under_review' => ['info',     'fa-search',   'Under Review'],
                    'approved'     => ['success',  'fa-check',    'Verified'],
                    'rejected'     => ['danger',   'fa-times',    'Rejected'],
                ][$pStatus] ?? ['secondary', 'fa-question', ucfirst($pStatus)];
                $firstVerification = $p->verifications->first() ?? null;
                $reviewUrl = $firstVerification
                    ? route('admin.verification.view', $firstVerification->id)
                    : null;
            @endphp
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 px-4 py-3 rounded-xl" style="background:rgba(15,23,42,.55);border:1px solid rgba(251,191,36,.15);">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <span class="text-[10px] font-bold text-d-400">#{{ $p->id }}</span>
                        <span class="text-xs font-bold text-d-100">{{ Str::limit($p->title, 55) }}</span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9.5px] font-bold bg-{{ $pBadge[0] }}/15 text-{{ $pBadge[0] }}"><i class="fas {{ $pBadge[1] }} text-[8px]"></i>{{ $pBadge[2] }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-[10px] text-d-500 flex-wrap">
                        <span><i class="fas fa-user mr-1"></i>{{ optional($p->user)->name ?? '-' }}</span>
                        <span><i class="fas fa-coins mr-1"></i>{{ amount_with_currency_symbol($p->amount) }}</span>
                        <span><i class="fas fa-shield-halved mr-1"></i>{{ __('Fraud') }}: {{ $p->fraud_score ?? 0 }}/100</span>
                        <span><i class="fas fa-clipboard-list mr-1"></i>{{ $rowCount }} {{ __('checklist items') }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <form action="{{ route('admin.verification.sync', $p->id) }}" method="post">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-[11px] font-bold text-white transition hover:opacity-90" style="background:linear-gradient(135deg,#7c3aed,#8b5cf6);">
                            <i class="fas fa-sync text-[10px]"></i>{{ $rowCount > 0 ? __('Sync') : __('Create Checklist') }}
                        </button>
                    </form>
                    @if($reviewUrl)
                        <a href="{{ $reviewUrl }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-[11px] font-bold text-white transition hover:opacity-90" style="background:linear-gradient(135deg,#4338ca,#6366f1);">
                            <i class="fas fa-clipboard-list text-[10px]"></i>{{ __('Review') }}
                        </a>
                    @endif
                    <a href="{{ route('admin.donations.edit', $p->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-[11px] font-bold transition" style="background:rgba(148,163,184,.1);border:1px solid rgba(148,163,184,.28);color:#e2e8f0;">
                        <i class="fas fa-edit text-[10px]"></i>{{ __('Edit') }}
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center py-6">
                <i class="fas fa-check-circle text-green-400 text-2xl mb-2"></i>
                <p class="text-xs text-d-500">{{__('All campaigns are reviewed — nothing pending.')}}</p>
            </div>
        @endforelse
    </div>
</div>
@endif

<div class="bg-d-900 border border-[#e8edf5] rounded-2xl overflow-hidden">
    @php $filterTypes = [null => ['fa-list','All'], 'patient' => ['fa-user-md','Patient'], 'hospital' => ['fa-hospital','Hospital'], 'document' => ['fa-file-medical-alt','Document'], 'amount' => ['fa-dollar-sign','Amount']]; @endphp
    <div class="px-6 py-2 border-b border-[#e8edf5] flex items-center gap-2 flex-wrap">
        @foreach($filterTypes as $val => $meta)
            <a href="{{ $val ? '?type='.$val : route('admin.verifications.all') }}"
               class="px-3 py-1 rounded-lg text-[11px] font-bold transition {{ ($activeType ?? null) === $val ? 'bg-[#4285f4] text-white shadow-sm' : 'bg-d-800/60 text-d-400 hover:text-d-200 hover:bg-d-700/60' }}">
                <i class="fas {{ $meta[0] }} mr-1"></i>{{__($meta[1])}}
            </a>
        @endforeach
    </div>
    <div class="px-6 py-4 border-b border-[#e8edf5] flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-t-500/10 flex items-center justify-center">
                <i class="fas fa-check-double text-t-400 text-sm"></i>
            </div>
            <span class="text-sm font-bold text-d-100">
                @if(($activeType ?? null) === 'hospital') {{__('Hospital Verifications')}}
                @elseif(($activeType ?? null) === 'patient') {{__('Patient Verifications')}}
                @elseif(($activeType ?? null) === 'document') {{__('Document Verifications')}}
                @elseif(($activeType ?? null) === 'amount') {{__('Amount Verifications')}}
                @else {{__('Verification Checklist Items')}} @endif
            </span>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-lg bg-amber-500/10 text-amber-400 text-xs font-semibold"><i class="fas fa-clock mr-1"></i>{{$pendingCount}} {{__('Pending')}}</span>
            <span class="px-3 py-1 rounded-lg bg-green-500/10 text-green-400 text-xs font-semibold"><i class="fas fa-check mr-1"></i>{{$verifiedCount}} {{__('Verified')}}</span>
            <span class="px-3 py-1 rounded-lg bg-red-500/10 text-red-400 text-xs font-semibold"><i class="fas fa-times mr-1"></i>{{$rejectedCount}} {{__('Rejected')}}</span>
        </div>
    </div>

    <div class="overflow-x-auto p-4">
        @if($verifications->isEmpty())
            <div class="text-center py-12">
                <i class="fas fa-folder-open text-d-600 text-3xl mb-3"></i>
                <p class="text-sm text-d-500">{{__('No verification checklist items yet.')}}</p>
                <p class="text-[11px] text-d-500 mt-1">{{__('Click "Create Checklist" on a pending campaign above to generate the standard verification items.') }}</p>
            </div>
        @else
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-[#e8edf5]">
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-d-500 uppercase">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-d-500 uppercase">{{__('Campaign')}}</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-d-500 uppercase">{{__('Type')}}</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-d-500 uppercase">{{__('Status')}}</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-d-500 uppercase">{{__('Verified By')}}</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-d-500 uppercase">{{__('Date')}}</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-d-500 uppercase">{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($verifications as $v)
                    @php
                        $vColor    = $statusMap[$v->status] ?? 'secondary';
                        $tMeta     = $typeMap[$v->type] ?? ['secondary', 'fa-question'];
                        $campaignUrl = $v->campaign ? route('admin.donations.edit', optional($v->campaign)->id) : '#';
                        $campVStatus = optional($v->campaign)->verification_status ?? 'pending';
                        $campBadge   = ['pending' => ['warning', 'Pending'], 'approved' => ['success', 'Verified'], 'rejected' => ['danger', 'Rejected']][$campVStatus] ?? ['secondary', ucfirst($campVStatus)];
                    @endphp
                    <tr class="border-b border-[#e8edf5] last:border-0 hover:bg-d-800/50 transition">
                        <td class="px-4 py-3 text-xs text-d-300 font-medium">#{{$v->id}}</td>
                        <td class="px-4 py-3">
                            @if($v->campaign)
                                <a href="{{ $campaignUrl }}" class="text-xs text-d-100 font-semibold hover:text-[#4285f4] transition">{{$v->campaign->title}}</a>
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-{{ $campBadge[0] }}/15 text-{{ $campBadge[0] }} ml-1.5"><i class="fas {{ $campBadge[0] === 'success' ? 'fa-check-circle' : ($campBadge[0] === 'danger' ? 'fa-times-circle' : 'fa-clock') }} text-[7px]"></i>{{ $campBadge[1] }}</span>
                            @else
                                <span class="text-xs text-d-400">- {{__('Campaign deleted')}}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2.5 py-1 rounded-lg bg-{{$tMeta[0]}}/10 text-xs font-semibold" style="color:#5b6b83"><i class="fas {{$tMeta[1]}} mr-1"></i>{{ucfirst($v->type)}}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2.5 py-1 rounded-lg bg-{{$vColor}}/10 text-{{$vColor}} text-xs font-bold">{{ucfirst($v->status)}}</span>
                        </td>
                        <td class="px-4 py-3 text-xs text-d-300">{{$v->verified_by ?? '-'}}</td>
                        <td class="px-4 py-3 text-xs text-d-400">{{optional($v->created_at)->format('M j, Y H:i')}}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{route('admin.verification.view', $v->id)}}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-[#4285f4]/10 text-[#4285f4] text-[11px] font-semibold hover:bg-[#4285f4]/20 transition">
                                    <i class="fas fa-eye"></i>{{__('Review')}}
                                </a>
                                @if($v->status === 'pending')
                                    <form action="{{route('admin.verification.verify', $v->id)}}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" onclick="return confirm('{{__('Approve this verification?')}}')" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-green-500/15 border border-green-500/40 text-green-400 text-[11px] font-bold hover:bg-green-500 hover:text-white transition">
                                            <i class="fas fa-check"></i>{{__('Approve')}}
                                        </button>
                                    </form>
                                    <form action="{{route('admin.verification.reject', $v->id)}}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" onclick="return confirm('{{__('Reject this verification?')}}')" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-red-500/15 border border-red-500/40 text-red-400 text-[11px] font-bold hover:bg-red-500 hover:text-white transition">
                                            <i class="fas fa-times"></i>{{__('Reject')}}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endsection
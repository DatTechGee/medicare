@extends('frontend.user.dashboard.user-master')
@section('title', __('Hospital Verifications'))
@section('section')

<style>
    .hv-badge{display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:999px;font-size:11px;font-weight:700}
    .hv-card{transition:box-shadow .2s ease}
    .hv-card:hover{box-shadow:0 10px 30px rgba(13,148,136,.08)}
    .hv-steps{display:flex;align-items:center;gap:10px;flex-wrap:wrap;font-size:11.5px;color:#6b7280}
</style>

<div class="ud-panel">
    <div class="d-flex align-items-center justify-content-between flex-wrap mb-4">
        <div>
            <h4 class="mb-1 d-flex align-items-center gap-2"><i class="fas fa-hospital text-teal-600"></i> {{__('Hospital Verification')}}</h4>
            <p class="mb-0 text-muted" style="font-size:13px;">{{__('Confirm the authenticity of patient claims for campaigns that list your hospital.')}}
                <span class="hv-badge" style="background:#f0fdfa;color:#0d9488;"><i class="fas fa-building-columns"></i>{{ $hospitalName }}</span>
            </p>
        </div>
        <div class="hv-steps">
            <span><i class="fas fa-circle-user" style="color:#0d9488;"></i> Patient submits claim</span>
            <i class="fas fa-arrow-right text-muted"></i>
            <span><i class="fas fa-hospital-user" style="color:#0d9488;"></i> Hospital verifies authenticity</span>
            <i class="fas fa-arrow-right text-muted"></i>
            <span><i class="fas fa-shield-halved" style="color:#0d9488;"></i> Fraud gate → campaign published on-chain</span>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="hv-card border rounded-4 p-3 text-center h-100" style="background:#f8fafc;">
                <div class="fs-4 fw-bold text-dark">Total Assigned</div>
                <div class="fw-extrabold" style="font-size:30px;color:#0d9488;">{{ $stats['assigned'] }}</div>
                <div class="text-muted" style="font-size:11.5px;">{{__('Campaigns listing your hospital')}}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="hv-card border rounded-4 p-3 text-center h-100" style="background:#fff7ed;">
                <div class="fs-4 fw-bold text-dark">Awaiting Confirmation</div>
                <div class="fw-extrabold" style="font-size:30px;color:#f59e0b;">{{ $stats['pending'] }}</div>
                <div class="text-muted" style="font-size:11.5px;">{{__('Need your review')}}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="hv-card border rounded-4 p-3 text-center h-100" style="background:#f0fdf4;">
                <div class="fs-4 fw-bold text-dark">Confirmed / Rejected</div>
                <div class="fw-extrabold" style="font-size:30px;color:#16a34a;">{{ $stats['completed'] }}</div>
                <div class="text-muted" style="font-size:11.5px;">{{__('You have responded')}}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="hv-card border rounded-4 p-3 text-center h-100" style="background:#eff6ff;">
                <div class="fs-4 fw-bold text-dark">Fraud-Gate Awaiting Approval</div>
                <div class="fw-extrabold" style="font-size:30px;color:#2563eb;">{{ $stats['awaitingApproval'] }}</div>
                <div class="text-muted" style="font-size:11.5px;">{{__('Pending admin approval in queue')}}</div>
            </div>
        </div>
    </div>

    <div class="border rounded-4 overflow-hidden mb-4" style="background:#fff;">
        <div class="px-4 py-3 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="mb-0" style="font-weight:800;"><i class="fas fa-clipboard-check me-2" style="color:#f59e0b;"></i>{{__('Campaigns Awaiting Hospital Confirmation')}}</h5>
            <span class="hv-badge" style="background:#fff7ed;color:#b45309;">{{ $stats['pending'] }} pending</span>
        </div>
        @if($pendingCampaigns->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="fas fa-circle-check" style="font-size:34px;color:#86efac;"></i>
                <p class="mt-3 mb-0">{{__('Nothing awaiting your confirmation — all campaigns listing your hospital have been responded to.')}}</p>
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:13px;">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Campaign</th>
                        <th>Patient</th>
                        <th>Claim / Hospital</th>
                        <th>Goal</th>
                        <th>Status</th>
                        <th class="pe-4">Confirm Authenticity</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingCampaigns as $c)
                    <tr>
                        <td class="ps-4">
                            <a href="{{ route('frontend.donations.single', $c->slug) }}" target="_blank" class="fw-semibold text-dark text-decoration-none">{{ \Illuminate\Support\Str::limit($c->title, 32) }}</a>
                            <div class="text-muted" style="font-size:11px;">#{{ $c->id }} · {{ $c->created_at->diffForHumans() }}</div>
                        </td>
                        <td>{{ $c->patient_name ?? '-' }}</td>
                        <td>{{ $c->hospital_name ?? '-' }}</td>
                        <td>{{ amount_with_currency_symbol($c->amount) }}</td>
                        <td>
                            @if($c->status == 'pending')
                                <span class="hv-badge" style="background:#fff7ed;color:#b45309;"><i class="fas fa-hourglass-half"></i>Awaiting approval</span>
                            @else
                                <span class="hv-badge" style="background:#eff6ff;color:#2563eb;"><i class="fas fa-globe"></i>Live</span>
                            @endif
                        </td>
                        <td class="pe-4">
                            <div class="d-flex gap-2">
                                <form action="{{ route('user.hospital.verify', $c->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirm this patient claim as authentic?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-check me-1"></i>Confirm Authentic</button>
                                </form>
                                <form action="{{ route('user.hospital.reject', $c->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Mark this claim as NOT confirmed by your hospital?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-xmark me-1"></i>Not Confirmed</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <div class="border rounded-4 overflow-hidden" style="background:#fff;">
        <div class="px-4 py-3 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="mb-0" style="font-weight:800;"><i class="fas fa-clock-rotate-left me-2" style="color:#0d9488;"></i>{{__('Your Response History')}}</h5>
            <span class="hv-badge" style="background:#f0fdfa;color:#0d9488;">{{ $doneCampaigns->count() }} responded</span>
        </div>
        @if($doneCampaigns->isEmpty())
            <div class="text-center py-5 text-muted"><i class="fas fa-history" style="font-size:30px;"></i><p class="mt-2 mb-0">{{__('No responses recorded yet.')}}</p></div>
        @else
        <div class="table-responsive">
            <table class="table mb-0" style="font-size:13px;">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Campaign</th>
                        <th>Patient</th>
                        <th>Your Decision</th>
                        <th>Verified By</th>
                        <th class="pe-4">When</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($doneCampaigns as $c)
                    <tr>
                        <td class="ps-4 fw-semibold">{{ \Illuminate\Support\Str::limit($c->title, 32) }}</td>
                        <td>{{ $c->patient_name ?? '-' }}</td>
                        <td>
                            @if($c->hospital_vrow && $c->hospital_vrow->status == 'verified')
                                <span class="hv-badge" style="background:#f0fdf4;color:#16a34a;"><i class="fas fa-check-circle"></i>Confirmed authentic</span>
                            @else
                                <span class="hv-badge" style="background:#fef2f2;color:#dc2626;"><i class="fas fa-circle-exclamation"></i>Not confirmed</span>
                            @endif
                        </td>
                        <td>{{ $c->hospital_vrow->verified_by ?? '-' }}</td>
                        <td class="pe-4">{{ optional($c->hospital_vrow)->created_at?->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

@endsection
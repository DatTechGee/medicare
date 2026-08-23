@extends('backend.admin-master')

@section('style')
<style>
    .escrow-stat{background:#fff;border-radius:10px;padding:18px 20px;box-shadow:0 1px 4px rgba(0,0,0,.06);height:100%}
    .escrow-stat .lbl{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#8e9aaf;margin-bottom:4px}
    .escrow-stat .val{font-size:22px;font-weight:800;color:#2b3b4e}
    .escrow-stat.held .val{color:#0d9488}
    .escrow-stat.frozen .val{color:#e6a23c}
    .escrow-stat.released .val{color:#409eff}
.escrow-stat.income .val{color:#7c3aed}
.escrow-stat.income{border-left:3px solid #7c3aed}
    .escrow-badge{display:inline-block;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700}
    .escrow-badge.ok{background:#e7f8f3;color:#0d9488}
    .escrow-badge.warn{background:#fdf3e3;color:#b8860b}
    .escrow-badge.bad{background:#fdeaea;color:#d9534f}
    .escrow-wallet{font-family:SFMono-Regular,Consolas,monospace;font-size:11.5px}
    .disburse-form{display:flex;gap:6px;align-items:center;flex-wrap:wrap}
    .disburse-form input{width:110px;padding:5px 8px;border:1px solid #d7dde5;border-radius:7px;font-size:12.5px}
</style>
@endsection

@section('site-title')
    {{__('Escrow & Disbursements')}}
@endsection

@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-lg-12">
                <div class="margin-top-40"></div>
                <x-msg.error/>
                <x-msg.success/>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="escrow-stat held">
                    <div class="lbl">{{__('Held (available)')}}</div>
                    <div class="val">${{ number_format($totals['held'], 2) }}</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="escrow-stat frozen">
                    <div class="lbl">{{__('Frozen / disputed')}}</div>
                    <div class="val">${{ number_format($totals['frozen'], 2) }}</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="escrow-stat released">
                    <div class="lbl">{{__('Released to patients')}}</div>
                    <div class="val">${{ number_format($totals['released'], 2) }}</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="escrow-stat">
                    <div class="lbl">{{__('Refunded to donors')}}</div>
                    <div class="val">${{ number_format($totals['refunded'], 2) }}</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="escrow-stat income">
                    <div class="lbl">{{__('Platform income (2% fee)')}}</div>
                    <div class="val">${{ number_format($totals['platform_income'], 2) }}</div>
                </div>
            </div>

            <div class="col-lg-12 mt-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{__('Campaign Escrow Balances')}}</h4>
                        <p class="text-muted" style="font-size:12.5px">{{__('Donor payments are held in escrow. Release funds to the patient\'s verified wallet after manual review. High fraud scores or unverified wallets will block disbursement.')}}</p>

                        <div class="table-wrap table-responsive">
                            <table class="table table-default">
                                <thead>
                                <tr>
                                    <th>{{__('Campaign')}}</th>
                                    <th>{{__('Patient / Wallet')}}</th>
                                    <th>{{__('Fraud')}}</th>
                                    <th>{{__('Raised')}}</th>
                                    <th>{{__('Held')}}</th>
                                    <th>{{__('Frozen')}}</th>
                                    <th>{{__('Released')}}</th>
                                    <th style="min-width:230px">{{__('Disburse')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($campaigns as $cause)
                                    @php
                                        $s = $stats->get($cause->id);
                                        $owner = $owners->get($cause->user_id);
                                        $wallet = $cause->wallet_address ?: optional($owner)->wallet_address;
                                        $walletVerified = (bool) ($cause->wallet_verified || optional($owner)->wallet_verified);
                                        $score = (int) ($cause->fraud_score ?? 0);
                                        $available = (float) ($s->held_available ?? 0);
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>#{{ $cause->id }} {{ Str::limit($cause->title, 34) }}</strong><br>
                                            <small class="text-muted">{{ __('status') }}: {{ $cause->status }} · {{ __('verification') }}:
                                                <span class="escrow-badge {{ in_array($cause->verification_status, ['verified','approved']) ? 'ok' : ($cause->verification_status === 'rejected' ? 'bad' : 'warn') }}">
                                                    {{ $cause->verification_status ?? 'pending' }}
                                                </span>
                                            </small>
                                        </td>
                                        <td>
                                            <small>{{ optional($owner)->name ?? __('—') }}</small><br>
                                            <span class="escrow-wallet">{{ $wallet ? substr($wallet, 0, 10) . '…' . substr($wallet, -6) : __('no wallet') }}</span>
                                            <span class="escrow-badge {{ $walletVerified ? 'ok' : 'bad' }}">{{ $walletVerified ? __('verified') : __('unverified') }}</span>
                                        </td>
                                        <td>
                                            <span class="escrow-badge {{ $score >= 70 ? 'bad' : ($score >= 40 ? 'warn' : 'ok') }}">{{ $score }}/100</span>
                                        </td>
                                        <td>${{ number_format((float) $cause->raised, 2) }}</td>
                                        <td><strong>${{ number_format($available, 2) }}</strong></td>
                                        <td>${{ number_format((float) ($s->held_frozen ?? 0), 2) }}</td>
                                        <td>${{ number_format((float) ($s->released_total ?? 0), 2) }}</td>
                                        <td>
                                            @if($available > 0 && $cause->status === 'publish' && $wallet && $walletVerified && !($score >= 70 && !in_array($cause->verification_status, ['verified','approved'])))
                                                <form class="disburse-form" action="{{ route('admin.donations.escrow.disburse') }}" method="post" onsubmit="return confirm('{{ __('Disburse escrow funds to the patient wallet? This is recorded on-chain and audited.') }}')">
                                                    @csrf
                                                    <input type="hidden" name="campaign_id" value="{{ $cause->id }}">
                                                    <input type="number" name="amount" value="{{ number_format($available, 2, '.', '') }}" step="0.01" min="0.01" max="{{ $available }}">
                                                    <button type="submit" class="btn btn-success btn-sm">{{ __('Release') }}</button>
                                                </form>
                                            @else
                                                <span class="escrow-badge {{ $available <= 0 ? '' : 'bad' }}">{{ $available <= 0 ? __('nothing held') : __('blocked — see gates') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">{{__('No campaign has received donations yet.')}}</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 mt-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{__('Held Donations (refundable individually)')}}</h4>
                        <p class="text-muted" style="font-size:12.5px">{{__('Each donor payment is held separately. Refund sends the money back to the donor\'s wallet and removes it from the campaign balance.')}}</p>

                        <div class="table-wrap table-responsive">
                            <table class="table table-default">
                                <thead>
                                <tr>
                                    <th>{{__('#')}}</th>
                                    <th>{{__('Campaign')}}</th>
                                    <th>{{__('Donation ID')}}</th>
                                    <th>{{__('Donor wallet')}}</th>
                                    <th>{{__('Amount')}}</th>
                                    <th>{{__('Held since')}}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($heldDonations as $row)
                                    @php $rowCause = $campaigns->where('id', $row->campaign_id)->first(); @endphp
                                    <tr>
                                        <td>{{ $row->id }}</td>
                                        <td>
                                            {{ \Illuminate\Support\Str::limit(optional($rowCause)->title, 32) }}
                                        </td>
                                        <td>#{{ $row->cause_log_id }}</td>
                                        <td style="font-family:monospace;font-size:11.5px">{{ \Illuminate\Support\Str::limit($row->donor_wallet_address ?: '—', 18) }}</td>
                                        <td><strong>${{ number_format((float) $row->amount, 2) }}</strong></td>
                                        <td>{{ optional($row->created_at)->format('d M Y H:i') }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('admin.donations.escrow.refund') }}"
                                                  onsubmit="return confirm('Refund this donation back to the donor wallet? This cannot be undone.');">
                                                @csrf
                                                <input type="hidden" name="escrow_id" value="{{ $row->id }}">
                                                <button type="submit" class="btn btn-danger btn-sm">{{ __('Refund to donor') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">{{__('No donations are currently held in escrow.')}}</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

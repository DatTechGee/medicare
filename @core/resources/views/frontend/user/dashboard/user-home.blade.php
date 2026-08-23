@extends('frontend.user.dashboard.user-master')
@section('site-title')
{{ auth()->guard('web')->user()->isPatient() ? __('Patient Dashboard') : __('Donor Dashboard') }}
@endsection

@php $u = auth()->guard('web')->user(); $isP = $u->isPatient(); @endphp

@section('section')

@php
    $walletConnected = !empty($u->wallet_address);
    $walletVerified = (bool)($u->wallet_verified ?? false);
    $role = $u->role ?? 'donor';
@endphp

{{-- ===== HERO BANNER ===== --}}
<div class="ud-hero">
    <div class="ud-hero-glow"></div>
    <div style="position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
        <div>
            <p class="ud-hero-date"><i class="fas fa-calendar-day"></i> {{date('l, d F Y')}}</p>
            <h2 class="ud-hero-title">{{__('Welcome back,')}} {{explode(' ', $u->name)[0]}} <span style="color:#00D4AA;">&bull;</span> {{$isP ? __('Patient Control Center') : __('Donor Hub')}}</h2>
            <p class="ud-hero-sub">
                @if($isP)
                    {{__('Manage your medical campaigns, track received donations and request payouts')}}
                @else
                    {{__('Track your donations, discover verified campaigns and change lives on-chain')}}
                @endif
            </p>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            @if($isP)
                <a href="{{route('user.campaign.new')}}" class="ud-btn-primary"><i class="fas fa-plus"></i> {{__('New Campaign')}}</a>
                <a href="{{route('user.campaign.all')}}" class="ud-btn-ghost"><i class="fas fa-list"></i> {{__('My Campaigns')}}</a>
            @else
                <a href="{{route('frontend.donations')}}" class="ud-btn-primary"><i class="fas fa-bullhorn"></i> {{__('Browse Campaigns')}}</a>
                <a href="{{route('user.home.donations')}}" class="ud-btn-ghost"><i class="fas fa-receipt"></i> {{__('My Donations')}}</a>
            @endif
        </div>
    </div>

    {{-- hero mini stats --}}
    <div style="position:relative;z-index:1;display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:12px;margin-top:22px;">
        <div class="ud-chip"><p>{{$isP ? __('Raised') : __('Donated')}}</p><span>{{amount_with_currency_symbol($isP ? ($total_raised ?? 0) : \App\CauseLogs::where('user_id',$u->id)->where('status','complete')->sum('amount'))}}</span></div>
        <div class="ud-chip"><p>{{$isP ? __('Received') : __('Gifts Given')}}</p><span>{{$isP ? ($donations_received_count ?? 0) : ($donation ?? 0)}}</span></div>
        <div class="ud-chip"><p>{{$isP ? __('Campaigns') : __('Causes Backed')}}</p><span>{{$isP ? ($campaigns ?? 0) : \App\CauseLogs::where('user_id',$u->id)->distinct('cause_id')->count('cause_id')}}</span></div>
        <div class="ud-chip"><p>{{__('ETH Wallet')}}</p><span style="font-family:'JetBrains Mono',monospace;">{{number_format((float)($u->demo_eth_balance ?? 0),2)}} ETH</span></div>
    </div>
</div>

@if($isP)
{{-- ===== VERIFICATION STEPPER ===== --}}
<div class="ud-card" style="margin-bottom:20px;">
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:18px;">
        <i class="fas fa-shield-alt" style="color:#627EEA;font-size:15px;"></i>
        <h4 style="color:#E6E7F5;font-size:14px;font-weight:700;margin:0;">{{__('Account Verification Status')}}</h4>
    </div>
    <div style="display:flex;align-items:center;gap:0;flex-wrap:wrap;">
        <div style="display:flex;align-items:center;gap:10px;flex:1;min-width:160px;">
            <div class="ud-step" style="background:rgba(0,212,170,.15);border-color:#00D4AA;color:#00D4AA;"><i class="fas fa-check"></i></div>
            <div><p style="color:#00D4AA;font-size:12px;font-weight:700;margin:0;">{{__('Account Created')}}</p><p style="color:rgba(224,224,224,.35);font-size:11px;margin:2px 0 0;">{{__('Registration complete')}}</p></div>
        </div>
        <div class="ud-stepper-line" style="background:linear-gradient(90deg,{{ $walletConnected ? '#00D4AA' : '#2A2A4A' }},{{ $walletVerified ? '#00D4AA' : ($walletConnected ? '#FFB800' : '#2A2A4A') }});"></div>
        <div style="display:flex;align-items:center;gap:10px;flex:1;min-width:160px;">
            <div class="ud-step" style="background:{{ $walletConnected ? 'rgba(0,212,170,.15)' : 'rgba(42,42,74,.5)' }};border-color:{{ $walletConnected ? '#00D4AA' : '#2A2A4A' }};color:{{ $walletConnected ? '#00D4AA' : '#555' }};">@if($walletConnected)<i class="fas fa-check"></i>@else<i class="fas fa-link"></i>@endif</div>
            <div><p style="color:{{ $walletConnected ? '#00D4AA' : 'rgba(224,224,224,.4)' }};font-size:12px;font-weight:700;margin:0;">{{__('Wallet Connected')}}</p><p style="color:rgba(224,224,224,.35);font-size:11px;margin:2px 0 0;">{{ $walletConnected ? __('MetaMask linked') : __('Connect to continue') }}</p></div>
        </div>
        <div class="ud-stepper-line" style="background:linear-gradient(90deg,{{ $walletVerified ? '#00D4AA' : ($walletConnected ? '#FFB800' : '#2A2A4A') }},{{ $walletVerified ? '#00D4AA' : '#2A2A4A' }});"></div>
        <div style="display:flex;align-items:center;gap:10px;flex:1;min-width:160px;">
            <div class="ud-step" style="background:{{ $walletVerified ? 'rgba(0,212,170,.15)' : 'rgba(42,42,74,.5)' }};border-color:{{ $walletVerified ? '#00D4AA' : '#2A2A4A' }};color:{{ $walletVerified ? '#00D4AA' : '#555' }};">@if($walletVerified)<i class="fas fa-check"></i>@else<i class="fas fa-clock"></i>@endif</div>
            <div><p style="color:{{ $walletVerified ? '#00D4AA' : 'rgba(224,224,224,.4)' }};font-size:12px;font-weight:700;margin:0;">{{__('Admin Verified')}}</p><p style="color:rgba(224,224,224,.35);font-size:11px;margin:2px 0 0;">{{ $walletVerified ? __('Wallet approved by admin') : __('Pending admin review') }}</p></div>
        </div>
    </div>
    @if(!$walletConnected)
    <div class="ud-inline-note warn"><i class="fas fa-exclamation-triangle"></i><span>{{__('Connect your MetaMask wallet to start receiving donations. Your wallet will be verified by our admin team before funds are released.')}}</span></div>
    @elseif(!$walletVerified)
    <div class="ud-inline-note info"><i class="fas fa-info-circle"></i><span>{{__('Your wallet is under review. Once admin verifies it, your campaign donations will be released to you.')}}</span></div>
    @endif
</div>
@endif

{{-- ===== STAT CARDS ===== --}}
<div class="row">
    @if($isP)
    <div class="col-lg-3 col-md-6" style="margin-bottom:20px;">
        <div class="ud-card ud-stat-hover" style="display:flex;align-items:center;gap:16px;">
            <div class="ud-stat-icon" style="background:linear-gradient(135deg,rgba(98,126,234,.2),rgba(98,126,234,.05));color:#8fa3f5;"><i class="fas fa-file-medical"></i></div>
            <div>
                <p class="ud-stat-label">{{__('My Campaigns')}}</p>
                <p class="ud-stat-value">{{$campaigns}}</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6" style="margin-bottom:20px;">
        <div class="ud-card ud-stat-hover" style="display:flex;align-items:center;gap:16px;">
            <div class="ud-stat-icon" style="background:linear-gradient(135deg,rgba(255,107,107,.18),rgba(255,107,107,.05));color:#ff8b8b;"><i class="fas fa-hand-holding-heart"></i></div>
            <div>
                <p class="ud-stat-label">{{__('Donations Received')}}</p>
                <p class="ud-stat-value">{{$donations_received_count}}</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6" style="margin-bottom:20px;">
        <div class="ud-card ud-stat-hover" style="display:flex;align-items:center;gap:16px;">
            <div class="ud-stat-icon" style="background:linear-gradient(135deg,rgba(0,212,170,.2),rgba(0,212,170,.06));color:#00D4AA;"><i class="fas fa-arrow-trend-up"></i></div>
            <div>
                <p class="ud-stat-label">{{__('Total Raised')}}</p>
                <p class="ud-stat-value" style="color:#00D4AA;">{{amount_with_currency_symbol($total_raised ?? 0)}}</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6" style="margin-bottom:20px;">
        <div class="ud-card ud-stat-hover" style="display:flex;align-items:center;gap:16px;">
            <div class="ud-stat-icon" style="background:linear-gradient(135deg,rgba(255,184,0,.18),rgba(255,184,0,.05));color:#FFB800;"><i class="fas fa-vault"></i></div>
            <div>
                <p class="ud-stat-label">{{__('Remaining Balance')}}</p>
                <p class="ud-stat-value" style="color:#FFB800;font-size:20px;">{{amount_with_currency_symbol($remaining_balance ?? 0)}}</p>
            </div>
        </div>
    </div>
    @else
    <div class="col-lg-3 col-md-6" style="margin-bottom:20px;">
        <div class="ud-card ud-stat-hover" style="display:flex;align-items:center;gap:16px;">
            <div class="ud-stat-icon" style="background:linear-gradient(135deg,rgba(255,107,107,.18),rgba(255,107,107,.05));color:#ff8b8b;"><i class="fas fa-hand-holding-heart"></i></div>
            <div>
                <p class="ud-stat-label">{{__('Total Donations')}}</p>
                <p class="ud-stat-value">{{$donation}}</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6" style="margin-bottom:20px;">
        <div class="ud-card ud-stat-hover" style="display:flex;align-items:center;gap:16px;">
            <div class="ud-stat-icon" style="background:linear-gradient(135deg,rgba(0,212,170,.2),rgba(0,212,170,.06));color:#00D4AA;"><i class="fas fa-coins"></i></div>
            <div>
                <p class="ud-stat-label">{{__('Total Contributed')}}</p>
                <p class="ud-stat-value" style="color:#00D4AA;">{{amount_with_currency_symbol(\App\CauseLogs::where('user_id',$u->id)->where('status','complete')->sum('amount'))}}</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6" style="margin-bottom:20px;">
        <div class="ud-card ud-stat-hover" style="display:flex;align-items:center;gap:16px;">
            <div class="ud-stat-icon" style="background:linear-gradient(135deg,rgba(98,126,234,.2),rgba(98,126,234,.05));color:#8fa3f5;"><i class="fas fa-award"></i></div>
            <div>
                <p class="ud-stat-label">{{__('Reward Points')}}</p>
                <p class="ud-stat-value">{{$donation_reward > 0 ? $donation_reward : 0}}</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6" style="margin-bottom:20px;">
        <div class="ud-card ud-stat-hover" style="display:flex;align-items:center;gap:16px;">
            <div class="ud-stat-icon" style="background:linear-gradient(135deg,rgba(255,184,0,.18),rgba(255,184,0,.05));color:#FFB800;"><i class="fas fa-shield-halved"></i></div>
            <div>
                <p class="ud-stat-label">{{__('Account Role')}}</p>
                <p class="ud-stat-value" style="font-size:17px;">{{ucfirst($role)}}</p>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- ===== WALLET CARD ===== --}}
<div class="ud-card" style="margin-bottom:20px;background:linear-gradient(120deg,#16162A 55%,rgba(98,126,234,.08));border-color:rgba(98,126,234,.25);">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px;">
        <div style="display:flex;align-items:center;gap:16px;">
            <div class="ud-stat-icon" style="width:56px;height:56px;font-size:22px;background:linear-gradient(135deg,rgba(98,126,234,.25),rgba(0,212,170,.15));"><i class="fas fa-wallet"></i></div>
            <div>
                <p class="ud-stat-label">{{$isP ? __('Payout Wallet') : __('MetaMask Wallet')}}</p>
                <p style="color:#E0E0E0;font-size:14px;font-family:'JetBrains Mono',monospace;margin:0;" id="dashWalletDisplay">
                    @if($walletConnected) {{substr($u->wallet_address,0,10)}}...{{substr($u->wallet_address,-8)}} @else {{__('No wallet connected')}} @endif
                </p>
            </div>
            @if($isP)
                @if($walletVerified)
                    <span class="ud-pill ok"><i class="fas fa-check-circle"></i> {{__('Verified')}}</span>
                @elseif($walletConnected)
                    <span class="ud-pill pending"><i class="fas fa-clock"></i> {{__('Pending')}}</span>
                @endif
            @endif
        </div>
        <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
            <span id="walletMsg" style="display:none;color:#ff6b6b;font-size:12px;font-weight:600;"></span>
            <button type="button" class="ud-btn-primary" id="connectWalletBtn" onclick="connectWalletDash()">
                <i class="fas fa-link"></i> {{empty($u->wallet_address) ? __('Connect MetaMask') : __('Change Wallet')}}
            </button>
            @if($walletConnected)
            <form action="{{route('user.wallet.disconnect')}}" method="POST" onsubmit="return confirm('{{__('Disconnect your payout wallet?')}}')" style="margin:0;">
                @csrf
                <button type="submit" class="ud-btn-ghost" style="padding:10px 16px;"><i class="fas fa-unlink"></i> {{__('Disconnect')}}</button>
            </form>
            @endif
        </div>
    </div>
</div>

@if($isP)
{{-- ===== PAYOUT SUMMARY TILES ===== --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;margin-bottom:20px;">
    <div class="ud-tile" style="border-style:solid;border-color:rgba(0,212,170,.25);">
        <i class="fas fa-circle-check" style="color:#00D4AA;"></i>
        <div><p>{{__('Approved Payouts')}}</p><span>{{amount_with_currency_symbol($approved_withdraws ?? 0)}}</span></div>
    </div>
    <div class="ud-tile" style="border-style:solid;border-color:rgba(255,184,0,.25);">
        <i class="fas fa-hourglass-half" style="color:#FFB800;"></i>
        <div><p>{{__('Pending Payouts')}}</p><span>{{amount_with_currency_symbol($pending_withdraws ?? 0)}}</span></div>
    </div>
    <a href="{{route('user.campaign.new')}}" class="ud-tile" style="border-style:dashed;border-color:rgba(0,212,170,.35);">
        <i class="fas fa-plus-circle" style="color:#00D4AA;"></i>
        <div><p>{{__('Action')}}</p><span>{{__('Create Campaign')}}</span></div>
    </a>
    <a href="{{url('/blockchain/explorer')}}" class="ud-tile" style="border-style:dashed;border-color:rgba(98,126,234,.35);">
        <i class="fas fa-cubes" style="color:#627EEA;"></i>
        <div><p>{{__('Explore')}}</p><span>{{__('Blockchain Explorer')}}</span></div>
    </a>
</div>
@endif

{{-- ===== RECENT DONATIONS FEED ===== --}}
@if(!empty($recent_received) && count($recent_received) > 0)
<div class="ud-card" style="margin-bottom:20px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
        <h4 style="color:#E6E7F5;font-size:14px;font-weight:700;margin:0;display:flex;align-items:center;gap:8px;">
            <i class="fas fa-wave-square" style="color:#00D4AA;"></i>
            {{$isP ? __('Recent Donations Received') : __('Your Recent Donations')}}
        </h4>
        <a href="{{route('user.home.donations')}}" class="ud-link-more">{{$isP ? __('View all received') : __('View all donations')}} <i class="fas fa-arrow-right"></i></a>
    </div>
    <div style="display:flex;flex-direction:column;gap:10px;">
        @foreach($recent_received as $log)
        @php
            $donorName = !empty($log->anonymous) ? null : ($log->name ?: (optional($log->user)->name ?? null));
            $initial = $donorName ? strtoupper(substr(trim($donorName), 0, 1)) : '?';
            $palette = ['#627EEA','#00D4AA','#FFB800','#ff6b6b','#8fa3f5','#34d399'];
            $avatarColor = $palette[$log->id % count($palette)];
        @endphp
        <div class="ud-donation-row">
            <div class="ud-avatar" style="background:{{$avatarColor}}22;color:{{$avatarColor}};border:1.5px solid {{$avatarColor}}66;">{{$initial}}</div>
            <div style="flex:1;min-width:180px;">
                <p style="color:#fff;font-size:13.5px;font-weight:700;margin:0;">
                    @if($donorName) {{__('Anonymous') === $donorName ? $donorName : $donorName}} @else <span style="color:#a0a0b4;"><i class="fas fa-user-secret" style="margin-right:6px;"></i>{{__('Anonymous Donor')}}</span> @endif
                    <span style="color:rgba(224,224,224,.45);font-weight:500;font-size:12px;">{{$isP ? __('donated to') : __('supported')}}</span>
                    @if($log->cause)
                        <a href="{{route('frontend.donations.single', $log->cause->slug)}}" style="color:#8fa3f5;text-decoration:none;">{{\Illuminate\Support\Str::limit($log->cause->title, 40)}}</a>
                    @endif
                </p>
                <p style="color:rgba(224,224,224,.4);font-size:11px;margin:3px 0 0;">
                    <i class="far fa-clock"></i> {{$log->created_at->diffForHumans()}}
                    @if(!empty($log->blockchain_transaction_hash))
                        &nbsp;&bull;&nbsp;
                        <a href="{{route('blockchain.transaction.show',$log->blockchain_transaction_hash)}}" target="_blank" title="{{__('View on explorer')}}" style="color:#627EEA;text-decoration:none;font-family:'JetBrains Mono',monospace;">{{substr($log->blockchain_transaction_hash,0,10)}}&hellip;</a>
                    @endif
                </p>
            </div>
            <div style="text-align:right;">
                <p style="color:#00D4AA;font-size:15px;font-weight:800;margin:0;">+{{amount_with_currency_symbol($log->amount)}}</p>
                <span class="ud-pill ok" style="font-size:9px;padding:3px 9px;">{{__('Confirmed')}}</span>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

@if($isP)
{{-- ===== CAMPAIGN BALANCE BREAKDOWN WITH PROGRESS BARS ===== --}}
@if(!empty($campaign_balances) && count($campaign_balances) > 0)
<div class="ud-card" style="margin-bottom:20px;">
    <h4 style="color:#E6E7F5;font-size:14px;font-weight:700;margin:0 0 16px;display:flex;align-items:center;gap:8px;"><i class="fas fa-chart-simple" style="color:#627EEA;"></i> {{__('Campaign Balance Breakdown')}}</h4>
    <div style="display:flex;flex-direction:column;gap:16px;">
        @foreach($campaign_balances as $cb)
        @php $pct = min(100, round(($cb['raised'] / max($cb['amount'], 1)) * 100)); @endphp
        <div style="padding:14px 16px;background:rgba(13,13,26,.5);border:1px solid rgba(42,42,74,.7);border-radius:14px;">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;margin-bottom:10px;">
                <a href="{{route('frontend.donations.single', $cb['slug'])}}" style="color:#E6E7F5;font-weight:700;font-size:13.5px;text-decoration:none;">{{\Illuminate\Support\Str::limit($cb['title'], 45)}}</a>
                @if($cb['status'] == 'complete')
                    <span class="badge badge-success">{{__('Approved')}}</span>
                @elseif($cb['status'] == 'pending')
                    <span class="badge badge-warning">{{__('Pending Review')}}</span>
                @elseif($cb['status'] == 'flagged')
                    <span class="badge badge-danger">{{__('Flagged')}}</span>
                @else
                    <span class="badge badge-secondary">{{__($cb['status'])}}</span>
                @endif
            </div>
            <div style="height:9px;background:rgba(42,42,74,.8);border-radius:99px;overflow:hidden;margin-bottom:9px;">
                <div style="height:100%;width:{{$pct}}%;border-radius:99px;background:linear-gradient(90deg,#627EEA,#00D4AA);box-shadow:0 0 10px rgba(0,212,170,.4);transition:width .6s ease;"></div>
            </div>
            <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:6px;font-size:11.5px;">
                <span style="color:rgba(224,224,224,.5);">{{__('Goal')}} <strong style="color:#E0E0E0;">{{amount_with_currency_symbol($cb['amount'])}}</strong></span>
                <span style="color:rgba(224,224,224,.5);">{{__('Raised')}} <strong style="color:#00D4AA;">{{amount_with_currency_symbol($cb['raised'])}}</strong> ({{$pct}}%)</span>
                <span style="color:rgba(224,224,224,.5);">{{__('Withdrawn')}} <strong style="color:#8fa3f5;">{{amount_with_currency_symbol($cb['withdrawn'])}}</strong></span>
                <span style="color:rgba(224,224,224,.5);">{{__('Available')}} <strong style="color:#FFB800;">{{amount_with_currency_symbol(max(0,$cb['raised'] - $cb['withdrawn']))}}</strong></span>
            </div>
        </div>
        @endforeach
    </div>
    <div style="text-align:right;margin-top:14px;">
        <a href="{{route('user.campaign.log.withdraw')}}" class="ud-link-more">{{__('Manage withdrawals')}} <i class="fas fa-arrow-right"></i></a>
    </div>
</div>
@endif

@else
{{-- ===== DONOR TRUST BANNER + QUICK LINKS ===== --}}
<div class="ud-card" style="background:linear-gradient(135deg,rgba(98,126,234,.09),rgba(0,212,170,.06));border-color:rgba(98,126,234,.22);margin-bottom:20px;">
    <div style="display:flex;align-items:center;gap:18px;flex-wrap:wrap;">
        <div class="ud-stat-icon" style="width:56px;height:56px;font-size:22px;"><i class="fas fa-shield-halved"></i></div>
        <div style="flex:1;min-width:220px;">
            <p style="color:#fff;font-size:15px;font-weight:700;margin:0 0 4px;">{{__('Trusted Donations')}}</p>
            <p style="color:rgba(224,224,224,.5);font-size:12.5px;margin:0;">{{__('Every donation is verified, escrowed, and released only upon hospital-confirmed milestone proof.')}}</p>
        </div>
        <a href="{{route('frontend.donations')}}" class="ud-btn-primary"><i class="fas fa-arrow-right"></i> {{__('Donate Now')}}</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;margin-bottom:20px;">
    <a href="{{route('frontend.donations')}}" class="ud-quick"><i class="fas fa-bullhorn" style="color:#00D4AA;"></i><p>{{__('Browse Campaigns')}}</p></a>
    <a href="{{route('user.home.donations')}}" class="ud-quick"><i class="fas fa-receipt" style="color:#627EEA;"></i><p>{{__('My Donations')}}</p></a>
    <a href="{{url('/blockchain/explorer')}}" class="ud-quick"><i class="fas fa-cubes" style="color:#FFB800;"></i><p>{{__('Blockchain Explorer')}}</p></a>
    <a href="{{route('user.home.edit.profile')}}" class="ud-quick"><i class="fas fa-user-edit" style="color:#8fa3f5;"></i><p>{{__('Edit Profile')}}</p></a>
</div>
@endif

@endsection

@section('scripts')
@parent
<script>
function showWalletError(msg){
    var el = document.getElementById('walletMsg');
    if (el){ el.textContent = msg; el.style.display = 'inline'; }
}
function saveWallet(address){
    var btn = document.getElementById('connectWalletBtn');
    if (btn){ btn.disabled = true; btn.style.opacity = .65; }
    return fetch('{{route("api.blockchain.connect-wallet")}}', {
        method:'POST',
        headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':'{{csrf_token()}}'},
        body: JSON.stringify({wallet_address: address})
    }).then(function(r){ return r.json().catch(function(){ return {}; }); })
      .then(function(d){
        if (!d || !d.success){
            showWalletError((d && d.message) ? d.message : '{{__("Could not connect wallet. Please try again.")}}');
            if (btn){ btn.disabled = false; btn.style.opacity = 1; }
            return;
        }
        location.reload();
      })
      .catch(function(){
        showWalletError('{{__("Network error while connecting wallet.")}}');
        if (btn){ btn.disabled = false; btn.style.opacity = 1; }
      });
}
function connectWalletDash(){
    if (typeof MetaMaskSim === 'undefined'){ showWalletError('Wallet module not loaded — refresh the page.'); return; }
    MetaMaskSim.connect({
        onConnect: function(acc){
            saveWallet(acc.address);
        },
        onCancel: function(){}
    });
}
</script>
@endsection

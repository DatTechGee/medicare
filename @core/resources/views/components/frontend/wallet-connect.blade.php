@php
    $mfUser = auth()->guard('web')->user();
@endphp
@if($mfUser)
<script>
window.MF_USER_WALLET = "{{ strtolower($mfUser->wallet_address ?? '') }}";
window.MF_USER_BALANCE = "{{ $mfUser->demo_eth_balance ?? 0 }}";
window.MF_USER_ROLE = "{{ $mfUser->role ?? 'donor' }}";
window.MF_USER_VERIFIED = {{ ($mfUser->wallet_verified ?? false) ? 'true' : 'false' }};
window.MF_LOGIN_URL = "{{ route('user.login') }}";
</script>
@endif
<style>
.mf-wallet{position:relative}
.mf-wallet-btn{display:inline-flex;align-items:center;gap:7px;background:linear-gradient(135deg,#0d9488 0%,#0f766e 100%);color:#fff;padding:8px 16px;border-radius:8px;font-weight:600;font-size:12px;cursor:pointer;border:none;transition:all .25s ease;box-shadow:0 2px 8px rgba(13,148,136,0.25);letter-spacing:0.2px;white-space:nowrap;text-decoration:none}
.mf-wallet-btn:hover{box-shadow:0 4px 16px rgba(13,148,136,0.35);transform:translateY(-1px);color:#fff}
.mf-wallet-addr{display:inline-flex;align-items:center;gap:6px;background:rgba(13,148,136,0.08);border:1px solid rgba(13,148,136,0.15);border-radius:8px;padding:6px 10px;font-family:'JetBrains Mono',monospace;font-size:11px;color:#14b8a6;font-weight:500}
.mf-wallet-dot{width:6px;height:6px;border-radius:50%;background:#10b981;box-shadow:0 0 6px rgba(16,185,129,0.6);animation:mfPulse 2s ease infinite}
@keyframes mfPulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.5;transform:scale(.8)}}
.mf-role-badge{display:inline-flex;align-items:center;gap:4px;border-radius:6px;padding:3px 8px;font-size:10px;font-weight:700;letter-spacing:0.3px}
.mf-role-donor{background:rgba(20,184,166,0.1);color:#0d9488}
.mf-role-patient{background:rgba(139,92,246,0.1);color:#8b5cf6}
.mf-bal-chip{display:inline-flex;align-items:center;gap:4px;background:rgba(245,158,11,0.12);border:1px solid rgba(245,158,11,0.25);color:#b45309;border-radius:6px;padding:3px 8px;font-family:'JetBrains Mono',monospace;font-size:10px;font-weight:700}
.mf-wallet-disconnect{padding:6px 10px;border-radius:6px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.15);color:#ef4444;font-size:11px;cursor:pointer;transition:all .2s}
.mf-wallet-disconnect:hover{background:rgba(239,68,68,0.2);border-color:rgba(239,68,68,0.3)}
@media (max-width: 1280px){ .mf-net-badge,.mf-role-badge{display:none} .mf-wallet-btn{padding:7px 12px;font-size:11px} .mf-wallet-addr{padding:5px 8px;font-size:10px} }
@media (max-width: 991px){ .mf-bal-chip{display:none} }

/* connecting overlay */
.mf-connecting{position:fixed;inset:0;z-index:9999;background:rgba(2,6,23,0.72);backdrop-filter:blur(6px);display:none;align-items:center;justify-content:center}
.mf-connecting.open{display:flex}
.mf-connecting-box{background:#0f172a;border:1px solid rgba(20,184,166,0.25);border-radius:18px;padding:32px 40px;text-align:center;max-width:340px;width:90%}
.mf-ring{width:64px;height:64px;margin:0 auto 18px;border-radius:50%;border:3px solid rgba(20,184,166,0.15);border-top-color:#14b8a6;animation:mfSpin .8s linear infinite;display:flex;align-items:center;justify-content:center}
@keyframes mfSpin{to{transform:rotate(360deg)}}
.mf-connecting-box h4{color:#f1f5f9;font-size:16px;font-weight:700;margin:0 0 6px}
.mf-connecting-box p{color:#64748b;font-size:12px;margin:0;line-height:1.5}

/* Toast */
.mf-wt{position:fixed;bottom:20px;right:20px;background:#1e293b;border:1px solid rgba(20,184,166,0.2);border-radius:10px;padding:12px 16px;display:flex;align-items:center;gap:8px;box-shadow:0 8px 30px rgba(0,0,0,0.3);z-index:9998;transform:translateY(120%);transition:transform .3s ease;max-width:360px}
.mf-wt.show{transform:translateY(0)}
.mf-wt i{color:#10b981;font-size:15px;flex-shrink:0}
.mf-wt span{color:#e2e8f0;font-size:12px}
</style>

<div class="mf-wallet" id="mfWallet">
    <div id="mfDisconnected" @if($mfUser) style="display:none" @endif>
        <a href="{{ route('user.login') }}" class="mf-wallet-btn">
            <i class="fas fa-right-to-bracket"></i>
            <span>{{ __('Login') }}</span>
        </a>
    </div>

    @if($mfUser)
    <div id="mfConnected" style="display:none">
        <div style="display:flex;align-items:center;gap:6px;flex-wrap:nowrap">
            <span class="mf-role-badge {{ ($mfUser->role ?? 'donor') === 'patient' ? 'mf-role-patient' : 'mf-role-donor' }}">
                <i class="fas fa-{{ ($mfUser->role ?? 'donor') === 'patient' ? 'user-injured' : 'hand-holding-heart' }}"></i>
                {{ ucfirst($mfUser->role ?? 'Donor') }}
            </span>
            <div class="mf-wallet-addr">
                <span class="mf-wallet-dot"></span>
                <span id="mfAddrShort">0x0000...0000</span>
            </div>
            @if(($mfUser->role ?? '') === 'patient')
            <div id="mfVerifyBadge" style="display:inline-flex;align-items:center;gap:4px;border-radius:6px;padding:3px 8px;font-size:10px;font-weight:700;letter-spacing:0.3px;background:{{ $mfUser->wallet_verified ? 'rgba(16,185,129,.12)' : 'rgba(251,191,36,.12)' }};color:{{ $mfUser->wallet_verified ? '#10b981' : '#fbbf24' }};border:1px solid {{ $mfUser->wallet_verified ? 'rgba(16,185,129,.25)' : 'rgba(251,191,36,.25)' }}">
                <i class="fas fa-{{ $mfUser->wallet_verified ? 'shield-check' : 'clock' }}"></i>
                <span>{{ $mfUser->wallet_verified ? __('Verified') : __('Pending') }}</span>
            </div>
            @endif
            <div class="mf-bal-chip mf-net-badge"><i class="fab fa-ethereum" style="font-size:9px"></i> <span id="mfBalShort">0.00</span></div>
            <button class="mf-wallet-disconnect" onclick="mfDisconnect()" title="{{__('Disconnect')}}">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    </div>
    @endif
</div>

{{-- Authorizing overlay — replaces the old "pick an account" popup --}}
<div class="mf-connecting" id="mfConnecting">
    <div class="mf-connecting-box">
        <div class="mf-ring"><i class="fas fa-wallet" style="color:#14b8a6;font-size:20px"></i></div>
        <h4>{{ __('Connecting Your Account') }}</h4>
        <p>{{ __('Verifying your MediFund account and wallet...') }}</p>
    </div>
</div>

<!-- Toast -->
<div class="mf-wt" id="mfToast">
    <i class="fas fa-circle-check"></i>
    <span id="mfToastMsg"></span>
</div>

@if($mfUser)
<script>
(function(){
    function mfToast(m){
        var t=document.getElementById('mfToast');
        document.getElementById('mfToastMsg').textContent=m;
        t.classList.add('show');
        setTimeout(function(){t.classList.remove('show')},3500);
    }

    function mfSetConnected(addr){
        document.getElementById('mfDisconnected').style.display='none';
        var c=document.getElementById('mfConnected');
        if(c){
            c.style.display='block';
            document.getElementById('mfAddrShort').textContent=addr.substring(0,6)+'...'+addr.substring(38);
            var b=document.getElementById('mfBalShort');
            if(b) b.textContent=(parseFloat(window.MF_USER_BALANCE||0)).toFixed(2)+' ETH';
        }
        if(typeof window.onWalletConnected==='function') window.onWalletConnected(addr);
    }

    /* No account picker: connect silently binds the account generated at registration */
    function mfConnectOwn(){
        var ov=document.getElementById('mfConnecting'); ov.classList.add('open');
        fetch('{{route("api.blockchain.connect-wallet")}}',{
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{csrf_token()}}'},
            body:JSON.stringify({wallet_address:window.MF_USER_WALLET})
        }).then(function(r){return r.json()}).then(function(d){
            setTimeout(function(){
                ov.classList.remove('open');
                mfSetConnected(d.success&&d.wallet_address?d.wallet_address:window.MF_USER_WALLET);
                if(d.balance) window.MF_USER_BALANCE=parseFloat(String(d.balance).replace(' ETH',''));
                var b=document.getElementById('mfBalShort');
                if(b&&d.balance) b.textContent=d.balance;
                mfToast('{{__("Account connected — funded & ready")}}');
            },900);
        }).catch(function(){
            ov.classList.remove('open');
            mfSetConnected(window.MF_USER_WALLET);
        });
    }
    window.mfConnectOwn=mfConnectOwn;

    window.mfDisconnect=function(){
        fetch('{{route("api.blockchain.disconnect-wallet")}}',{
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{csrf_token()}}'}
        }).catch(function(){});
        document.getElementById('mfDisconnected').style.display='block';
        document.getElementById('mfConnected').style.display='none';
        mfToast('{{__("Wallet disconnected")}}');
    };

    window.mfGetWallet=function(){return window.MF_USER_WALLET||null};

    document.addEventListener('DOMContentLoaded',function(){
        fetch('{{route("api.blockchain.wallet-status")}}').then(function(r){return r.json()}).then(function(d){
            if(d.connected&&d.wallet_address){ mfSetConnected(d.wallet_address); }
        }).catch(function(){});
    });
})();
</script>
@endif

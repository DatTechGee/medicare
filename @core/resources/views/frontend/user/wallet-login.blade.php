@extends('frontend.frontend-page-master')
@section('page-title')
    {{__('Wallet Login')}}
@endsection
@section('content')
<style>
.wl-wrap{min-height:80vh;display:flex;align-items:center;justify-content:center;padding:60px 20px;background:linear-gradient(160deg,#0a0a1a 0%,#0d1117 50%,#111128 100%)}
.wl-card{background:#111128;border:1px solid #1e1e3a;border-radius:20px;padding:44px 40px;width:440px;max-width:100%;box-shadow:0 25px 60px rgba(0,0,0,0.5);position:relative;overflow:hidden}
.wl-card::before{content:'';position:absolute;top:-60%;left:-30%;width:70%;height:120%;background:radial-gradient(circle,rgba(98,126,234,0.08) 0%,transparent 70%);pointer-events:none}
.wl-logo{width:64px;height:64px;border-radius:18px;background:linear-gradient(135deg,#0d9488,#14b8a6);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;color:#fff;font-size:26px;box-shadow:0 8px 24px rgba(13,148,136,0.35)}
.wl-title{text-align:center;font-size:24px;font-weight:800;color:#f1f5f9;margin-bottom:6px}
.wl-sub{text-align:center;font-size:13px;color:#8b8ba3;margin-bottom:32px;line-height:1.6}
.wl-btn{display:flex;align-items:center;justify-content:center;gap:12px;width:100%;padding:15px;border-radius:12px;font-weight:700;font-size:14px;cursor:pointer;border:none;transition:all .25s;margin-bottom:12px}
.wl-btn-mm{background:linear-gradient(135deg,#ff9800,#f57c00);color:#fff;box-shadow:0 4px 16px rgba(255,152,0,0.3)}
.wl-btn-mm:hover{box-shadow:0 8px 28px rgba(255,152,0,0.45);transform:translateY(-2px)}
.wl-btn-demo{background:rgba(255,255,255,0.04);border:1px solid #2a2a4a;color:#b8b8d0}
.wl-btn-demo:hover{border-color:#14b8a6;color:#14b8a6;background:rgba(20,184,166,0.05)}
.wl-btn:disabled{opacity:.55;cursor:not-allowed;transform:none!important}
.wl-status{display:none;text-align:center;padding:14px;border-radius:10px;background:rgba(20,184,166,0.06);border:1px solid rgba(20,184,166,0.15);color:#14b8a6;font-size:13px;margin-top:16px}
.wl-status.err{background:rgba(255,107,107,0.06);border-color:rgba(255,107,107,0.2);color:#ff6b6b}
.wl-steps{margin-top:28px;padding-top:24px;border-top:1px solid #1e1e3a}
.wl-step{display:flex;align-items:flex-start;gap:12px;margin-bottom:14px}
.wl-step .n{width:22px;height:22px;border-radius:50%;background:rgba(98,126,234,0.12);color:#627EEA;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px}
.wl-step p{font-size:12px;color:#8b8ba3;line-height:1.5;margin:0}
.wl-foot{text-align:center;margin-top:24px;font-size:12px;color:#565672}
.wl-foot a{color:#14b8a6;text-decoration:none}
.wl-badge{position:absolute;top:18px;right:18px;display:inline-flex;align-items:center;gap:5px;background:rgba(139,92,246,0.08);border:1px solid rgba(139,92,246,0.15);border-radius:6px;padding:4px 10px;font-size:10px;font-weight:600;color:#8b5cf6}
</style>

<section class="wl-wrap">
    <div class="wl-card">
        <span class="wl-badge"><i class="fas fa-shield-halved"></i> Demo Network</span>
        <div class="wl-logo"><i class="fas fa-heartbeat"></i></div>
        <h2 class="wl-title">{{__('Patient Wallet Login')}}</h2>
        <p class="wl-sub">{{__('Connect your MetaMask wallet to access your patient dashboard, campaigns and donations. No password needed.')}}</p>

        <button class="wl-btn wl-btn-mm" id="mmLoginBtn" onclick="walletLogin()">
            <i class="fab fa-ethereum" style="font-size:18px"></i>
            <span id="mmBtnText">{{__('Login with MetaMask')}}</span>
        </button>

        <button class="wl-btn wl-btn-demo" id="demoLoginBtn" onclick="demoWalletLogin()">
            <i class="fas fa-microchip"></i>
            {{__('Use Demo Wallet')}}
        </button>

        <div class="wl-status" id="wlStatus"></div>

        <div class="wl-steps">
            <div class="wl-step"><span class="n">1</span><p>{{__('Click login — MetaMask opens and asks you to sign a secure message.')}}</p></div>
            <div class="wl-step"><span class="n">2</span><p>{{__('We verify your wallet address on the blockchain network.')}}</p></div>
            <div class="wl-step"><span class="n">3</span><p>{{__('New here? Your patient account is created automatically.')}}</p></div>
        </div>

        <div class="wl-foot">
            {{__('Prefer email?')}} <a href="{{route('user.login')}}">{{__('Login with username')}}</a>
        </div>
    </div>
</section>
@endsection
@section('scripts')
<x-metamask-sim/>
<script>
function setStatus(msg,isErr){
    var el=document.getElementById('wlStatus');
    el.style.display='block';
    el.className='wl-status'+(isErr?' err':'');
    el.textContent=msg;
}
function setBusy(busy){
    document.getElementById('mmLoginBtn').disabled=busy;
    document.getElementById('demoLoginBtn').disabled=busy;
    document.getElementById('mmBtnText').textContent=busy?'{{__("Waiting for signature...")}}':'{{__("Login with MetaMask")}}';
}

function authenticate(address,signature){
    fetch('{{route("api.blockchain.auth.verify")}}',{
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{csrf_token()}}'},
        body:JSON.stringify({address:address,signature:signature})
    })
    .then(function(r){return r.json().then(function(d){return {ok:r.ok,data:d}})})
    .then(function(res){
        if(res.ok&&res.data.success){
            setStatus('{{__("Welcome")}} '+res.data.user.name+' — {{__("redirecting...")}}',false);
            setTimeout(function(){window.location.href=res.data.redirect},900);
        }else{
            setBusy(false);
            setStatus(res.data.message||'{{__("Authentication failed")}}',true);
        }
    })
    .catch(function(){
        setBusy(false);
        setStatus('{{__("Network error. Please try again.")}}',true);
    });
}

function requestNonce(address,cb){
    fetch('{{route("api.blockchain.auth.nonce")}}',{
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{csrf_token()}}'},
        body:JSON.stringify({address:address})
    })
    .then(function(r){return r.json()})
    .then(cb)
    .catch(function(){
        setBusy(false);
        setStatus('{{__("Could not reach server. Try again.")}}',true);
    });
}

function walletLogin(){
    setBusy(true);
    if (typeof window.ethereum!=='undefined'){
        window.ethereum.request({method:'eth_requestAccounts'}).then(function(accounts){
            var addr=accounts[0];
            requestNonce(addr,function(data){
                if(!data.success){setBusy(false);setStatus(data.message,true);return;}
                window.ethereum.request({
                    method:'personal_sign',
                    params:[data.message,addr]
                }).then(function(signature){
                    authenticate(addr.toLowerCase(),signature);
                }).catch(function(){
                    setBusy(false);
                    setStatus('{{__("Signature rejected in MetaMask.")}}',true);
                });
            });
        }).catch(function(){
            setBusy(false);
            setStatus('{{__("Wallet connection rejected.")}}',true);
        });
        return;
    }

    /* no extension — full MetaMask simulation */
    if (typeof MetaMaskSim==='undefined'){
        setBusy(false);
        setStatus('{{__("Wallet module not loaded. Please refresh the page.")}}',true);
        return;
    }
    MetaMaskSim.connect({
        onCancel: function(){ setBusy(false); },
        onConnect: function(acc){
            var addr = acc.address;
            requestNonce(addr,function(data){
                if(!data.success){setBusy(false);setStatus(data.message,true);return;}
                MetaMaskSim.signMessage({
                    address: addr,
                    message: data.message,
                    onSign: function(signature){
                        authenticate(addr.toLowerCase(),signature);
                    },
                    onReject: function(){
                        setBusy(false);
                        setStatus('{{__("Signature rejected in MetaMask.")}}',true);
                    }
                });
            });
        }
    });
}

function demoWalletLogin(){
    setBusy(true);
    var h='0123456789abcdef',addr='0x';
    for(var i=0;i<40;i++) addr+=h[Math.floor(Math.random()*16)];
    requestNonce(addr,function(data){
        if(!data.success){setBusy(false);setStatus(data.message,true);return;}
        var sig='0x'+data.nonce.toLowerCase();
        while(sig.length<132) sig+='a';
        sig=sig.substring(0,130)+'1b';
        setTimeout(function(){authenticate(addr,sig)},600);
    });
}
</script>
@endsection

@once
{{--
    Realistic MetaMask Wallet Simulation v2
    Usage: <x-metamask-sim/>
    API:
      MetaMaskSim.connect({ onConnect(acc), onCancel() })
      MetaMaskSim.confirmTx({ amount, to, onConfirm(hash), onReject() })
      MetaMaskSim.setBalance(address, newBalance)
--}}
<div id="mmsim-root"></div>
<style>
#mmsim-root{display:contents}
#mmsim-overlay{position:fixed;top:0;left:0;right:0;bottom:0;margin:0;padding:16px;z-index:999999;display:none;align-items:center;justify-content:center;background:rgba(15,17,21,.62);backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Inter',Helvetica,Arial,sans-serif;animation:mmsimFade .18s ease}
#mmsim-overlay.mmsim-open{display:flex}
@keyframes mmsimFade{from{opacity:0}to{opacity:1}}
@keyframes mmsimSheet{from{transform:translateY(34px) scale(.97);opacity:0}to{transform:translateY(0) scale(1);opacity:1}}
.mmsim-popup{width:372px;max-width:calc(100vw - 32px);max-height:calc(100vh - 40px);overflow-y:auto;background:#FFFFFF;border:1px solid rgba(0,0,0,.06);border-radius:22px;box-shadow:0 1px 2px rgba(0,0,0,.08),0 32px 80px rgba(10,12,18,.5);color:#24272E;animation:mmsimSheet .3s cubic-bezier(.32,1.25,.55,1)}
.mmsim-popup::-webkit-scrollbar{width:6px}.mmsim-popup::-webkit-scrollbar-thumb{background:#DDE2E8;border-radius:99px}
.mmsim-step{animation:mmsimStepIn .22s ease}
@keyframes mmsimStepIn{from{opacity:.35;transform:translateX(10px)}to{opacity:1;transform:translateX(0)}}

.mmsim-header{display:flex;align-items:center;justify-content:space-between;padding:13px 16px;border-bottom:1px solid #E9EDF2;background:#FCFDFE;border-radius:22px 22px 0 0}
.mmsim-brand{display:flex;align-items:center;gap:9px;font-size:15px;font-weight:700;color:#24272E;letter-spacing:-.01em}
.mmsim-net-pill{font-size:11px;font-weight:700;color:#0376C9;background:#EAF6FE;border:1px solid #CFEAFB;border-radius:999px;padding:4px 11px;display:flex;align-items:center;gap:6px;letter-spacing:.01em}
.mmsim-net-dot{width:7px;height:7px;border-radius:50%;background:#19b271;box-shadow:0 0 5px rgba(25,178,113,.65);animation:mmsimDotPulse 1.8s ease infinite}
@keyframes mmsimDotPulse{0%,100%{transform:scale(1)}50%{transform:scale(1.28)}}
.mmsim-close{background:none;border:none;font-size:19px;line-height:1;color:#98A1AB;cursor:pointer;padding:3px 8px;border-radius:8px;transition:all .14s}
.mmsim-close:hover{background:#EFF2F6;color:#24272E}

.mmsim-body{padding:26px 26px 10px;text-align:center}
.mmsim-title{font-size:22px;font-weight:800;margin:10px 0 5px;color:#14171A;letter-spacing:-.02em}
.mmsim-sub{font-size:13.5px;color:#69727C;line-height:1.55}
.mmsim-site-row{display:flex;align-items:center;justify-content:center;gap:14px;margin:20px 0 4px}
.mmsim-site-avatar{width:40px;height:40px;border-radius:13px;background:linear-gradient(135deg,#627EEA,#00D4AA);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:16px;box-shadow:0 4px 14px rgba(98,126,234,.35)}
.mmsim-link-line{width:26px;height:2px;border-radius:2px;background:linear-gradient(90deg,#D6DBE2,#B8C0CB)}
.mmsim-hostname{font-weight:700;font-size:13.5px;color:#0376C9}
.mmsim-permission{display:flex;align-items:flex-start;gap:11px;background:#F6F8FA;border:1px solid #EBEEF2;border-radius:14px;padding:12px 14px;margin:16px 0 4px;text-align:left}
.mmsim-permission i{color:#0376C9;font-size:14px;margin-top:2px}
.mmsim-permission span{font-size:12.5px;color:#535A61;line-height:1.5}

.mmsim-account-box{border:1.5px solid #DADFE5;border-radius:16px;padding:13px 15px;margin:18px 0 4px;display:flex;align-items:center;gap:12px;cursor:pointer;transition:border-color .15s, box-shadow .15s, background .15s;position:relative;text-align:left;background:#fff}
.mmsim-account-box:hover{border-color:#0376C9;box-shadow:0 3px 14px rgba(3,118,201,.12)}
.mmsim-identicon{width:34px;height:34px;border-radius:50%;flex-shrink:0;box-shadow:inset 0 0 0 1px rgba(0,0,0,.06)}
.mmsim-acc-name{font-weight:700;font-size:14px;color:#14171A;letter-spacing:-.01em}
.mmsim-acc-addr{font-size:12px;color:#69727C;font-family:'SFMono-Regular','JetBrains Mono',Consolas,monospace;margin-top:1px}
.mmsim-acc-bal{margin-left:auto;text-align:right}
.mmsim-bal-val{font-size:13.5px;font-weight:800;color:#14171A;font-variant-numeric:tabular-nums}
.mmsim-bal-cur{font-size:11px;color:#8B959E;font-variant-numeric:tabular-nums;margin-top:1px}
.mmsim-caret{color:#98A1AB;font-size:11px;margin-left:9px;transition:transform .18s}
.mmsim-acct-menu{position:absolute;top:calc(100% + 7px);left:0;right:0;background:#fff;border:1px solid #E3E7EC;border-radius:16px;box-shadow:0 4px 10px rgba(16,24,40,.06),0 20px 48px rgba(16,24,40,.18);z-index:5;overflow:hidden;text-align:left;animation:mmsimMenuIn .16s cubic-bezier(.3,1.2,.6,1);transform-origin:top center}
@keyframes mmsimMenuIn{from{opacity:0;transform:translateY(-6px) scale(.985)}to{opacity:1;transform:translateY(0) scale(1)}}
.mmsim-acct-menu-label{padding:10px 14px 4px;font-size:10.5px;font-weight:800;color:#98A1AB;text-transform:uppercase;letter-spacing:.07em}
.mmsim-acct-item{display:flex;align-items:center;gap:11px;padding:11px 14px;cursor:pointer;font-size:13.5px;transition:background .12s}
.mmsim-acct-item:hover{background:#F5F8FB}
.mmsim-acct-item img{width:28px;height:28px;border-radius:50%;box-shadow:inset 0 0 0 1px rgba(0,0,0,.06)}
.mmsim-acct-item .chk{margin-left:auto;color:#0376C9;font-weight:800;font-size:12px}
.mmsim-switch-hint{font-size:11.5px;color:#8B959E;margin-top:10px}

.mmsim-footer{display:flex;gap:12px;padding:18px 26px 24px}
.mmsim-btn{flex:1;padding:14px 0;border-radius:13px;font-size:14.5px;font-weight:700;cursor:pointer;border:none;transition:all .15s;letter-spacing:.005em;font-family:inherit}
.mmsim-btn-cancel{background:#fff;color:#0376C9;border:1.5px solid #CBE5F8}
.mmsim-btn-cancel:hover{background:#F2F9FE;border-color:#9FD4F5}
.mmsim-btn-primary{background:#0376C9;color:#fff;box-shadow:0 1px 2px rgba(3,118,201,.4),0 6px 18px rgba(3,118,201,.28)}
.mmsim-btn-primary:hover{background:#0268B0;transform:translateY(-1px);box-shadow:0 2px 4px rgba(3,118,201,.4),0 10px 24px rgba(3,118,201,.36)}
.mmsim-btn-primary:active{transform:translateY(0) scale(.99)}
.mmsim-btn-primary:disabled{opacity:.45;cursor:not-allowed;box-shadow:none;transform:none}
.mmsim-spinner{width:46px;height:46px;border:3px solid #E5ECF5;border-top-color:#0376C9;border-radius:50%;margin:26px auto 18px;animation:mmsimSpin .75s linear infinite}
@keyframes mmsimSpin{to{transform:rotate(360deg)}}
.mmsim-tx-amount{text-align:center;font-size:38px;font-weight:800;color:#D73847;margin:8px 0 3px;letter-spacing:-.02em;font-variant-numeric:tabular-nums}
.mmsim-tx-usd{text-align:center;font-size:13px;color:#69727C;margin-bottom:18px;font-variant-numeric:tabular-nums}
.mmsim-tx-card{background:#F6F8FA;border:1px solid #EBEEF2;border-radius:14px;padding:4px 15px;margin-bottom:14px}
.mmsim-tx-row{display:flex;align-items:center;justify-content:space-between;padding:11px 0;border-bottom:1px solid #ECF0F4;font-size:13px}
.mmsim-tx-row:last-child{border-bottom:none}
.mmsim-tx-row .lbl{color:#69727C}
.mmsim-tx-row .val{font-weight:600;color:#14171A;display:flex;align-items:center;gap:7px;font-variant-numeric:tabular-nums}
.mmsim-tx-total{padding:13px 0;font-size:14px;border-top:1.5px solid #E3E7EC;margin-top:2px}
.mmsim-tx-total .val{font-weight:800}
.mmsim-gas-badge{background:#EAF6FE;border-radius:8px;padding:3px 9px;font-size:11px;color:#0376C9;font-weight:700;font-variant-numeric:tabular-nums}
.mmsim-insufficient{background:#FDF0F1;border:1px solid #F5C6CB;border-radius:12px;padding:11px 14px;margin:12px 0 2px;font-size:12.5px;color:#B02A37;text-align:left;display:flex;gap:9px;align-items:flex-start}
.mmsim-success-check{width:64px;height:64px;border-radius:50%;background:#E7FAF4;display:flex;align-items:center;justify-content:center;margin:18px auto 12px;animation:mmsimPopIn .4s cubic-bezier(.3,1.6,.6,1)}
@keyframes mmsimPopIn{from{transform:scale(.4);opacity:0}to{transform:scale(1);opacity:1}}
.mmsim-success-check svg{width:32px;height:32px}
.mmsim-unlock-body{padding:34px 30px 8px;text-align:center}
.mmsim-pass-wrap{position:relative;margin:22px 0 4px}
.mmsim-pass{width:100%;padding:14px 46px 14px 16px;font-size:14px;border:1.5px solid #DADFE5;border-radius:12px;outline:none;background:#fff;color:#24272E;font-family:inherit;transition:border-color .15s,box-shadow .15s;box-sizing:border-box}
.mmsim-pass:focus{border-color:#0376C9;box-shadow:0 0 0 3px rgba(3,118,201,.13)}
.mmsim-pass.err{border-color:#D73847;box-shadow:0 0 0 3px rgba(215,56,71,.1)}
.mmsim-eye{position:absolute;right:6px;top:50%;transform:translateY(-50%);background:none;border:none;color:#98A1AB;padding:8px;cursor:pointer;font-size:13px}
.mmsim-err-txt{color:#D73847;font-size:11.5px;text-align:left;margin-top:8px;display:flex;gap:5px;align-items:center}
.mmsim-hint{font-size:11px;color:#98A1AB;text-align:left;margin-top:10px;display:flex;gap:6px;align-items:center}
.mmsim-me-badge{display:inline-flex;align-items:center;gap:4px;background:#EAF6FE;border:1px solid #CFEAFB;color:#0376C9;font-size:9px;font-weight:800;border-radius:99px;padding:2.5px 8px;text-transform:uppercase;letter-spacing:.05em;margin-left:6px;vertical-align:1px}
.mmsim-unlock-note{display:flex;align-items:flex-start;gap:9px;background:#F6F8FA;border:1px solid #EBEEF2;border-radius:12px;padding:10px 13px;font-size:11.5px;color:#69727C;text-align:left;margin-top:14px}

</style>

<script>
(function(){
"use strict";

/* ---------- deterministic blockies-style identicon ---------- */
function mmsimIdenticon(address, size){
    size = size || 30;
    var seed = String(address||'0x0').toLowerCase().replace(/^0x/,'');
    var colors = ['#627EEA','#F6851B','#00D4AA','#E2761B','#8247E5','#0376C9','#D73847','#FFB800'];
    var hash = 0;
    for (var i=0;i<seed.length;i++){ hash = seed.charCodeAt(i) + ((hash<<5)-hash); }
    var c = document.createElement('canvas'); c.width=size; c.height=size;
    var ctx = c.getContext('2d');
    ctx.fillStyle = '#F2F4F6'; ctx.fillRect(0,0,size,size);
    var cell = size/5;
    for (var col=0; col<3; col++){
        for (var row=0; row<5; row++){
            hash = (hash*9301+49297)%233280;
            if (hash/233280 > .48){
                ctx.fillStyle = colors[Math.abs(hash)%colors.length];
                ctx.fillRect(col*cell,row*cell,cell+.5,cell+.5);
                ctx.fillRect((4-col)*cell,row*cell,cell+.5,cell+.5);
            }
        }
    }
    return c.toDataURL();
}

function randHex(n){var s='';var ch='0123456789abcdef';for(var i=0;i<n;i++)s+=ch[Math.floor(Math.random()*16)];return s;}
function short(a){a=String(a);return a.slice(0,6)+'...'+a.slice(-4);}
function esc(s){return String(s).replace(/[&<>"']/g,function(m){return{'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m];});}

/* ---------- persistent demo accounts ---------- */
function getAccounts(){
    var ownAcc = null;
    try{
        if (window.MF_USER_WALLET) {
            ownAcc = {
                name:'My Wallet',
                address:String(window.MF_USER_WALLET).toLowerCase(),
                balance:(window.MF_USER_BALANCE!==undefined && parseFloat(window.MF_USER_BALANCE)>0)
                    ? parseFloat(window.MF_USER_BALANCE)
                    : +(Math.random()*6+8).toFixed(4),
                locked:true
            };
        }
    }catch(e){}
    try{
        var raw = localStorage.getItem('mmsim_accounts');
        if(raw){
            var accs = JSON.parse(raw);
            /* keep the signed-in user's wallet pinned as Account 1 */
            accs = accs.filter(function(a){return !a.locked;});
            /* demo faucet: keep wallets funded */
            var topped = false;
            for (var i=0;i<accs.length;i++){
                if (!(parseFloat(accs[i].balance) >= 5)){ accs[i].balance = +(Math.random()*8+8).toFixed(4); topped = true; }
            }
            if (ownAcc) accs.unshift(ownAcc);
            if (topped || !raw.match('"locked"')) saveAccounts(accs);
            return accs;
        }
    }catch(e){}
    var accounts = [];
    if (ownAcc) accounts.push(ownAcc);
    accounts.push({name:'Account '+(accounts.length+1), address:'0x'+randHex(40), balance:+(Math.random()*6+10).toFixed(4)});
    accounts.push({name:'Account '+(accounts.length+1), address:'0x'+randHex(40), balance:+(Math.random()*4+5).toFixed(4)});
    localStorage.setItem('mmsim_accounts', JSON.stringify(accounts));
    return accounts;
}
function saveAccounts(a){ localStorage.setItem('mmsim_accounts', JSON.stringify(a)); }
function getSelected(){ return parseInt(localStorage.getItem('mmsim_selected')||'0',10)||0; }
function setSelected(i){ localStorage.setItem('mmsim_selected', String(i)); }

/* ---------- unlock state (per page load, like a real vault) ---------- */
var mmsimUnlocked = false;

function ensureUnlocked(render){
    if (mmsimUnlocked) { render(); return; }
    popup(
        headerHtml()+
        '<div class="mmsim-body mmsim-step mmsim-unlock-body">'+
            '<div style="text-align:center">'+FOX_SVG.replace('width="30" height="28"','width="54" height="50"')+'</div>'+
            '<div class="mmsim-title">Welcome Back!</div>'+
            '<div class="mmsim-sub">Enter your password to unlock your MetaMask wallet</div>'+
            '<div class="mmsim-pass-wrap">'+
                '<input type="password" class="mmsim-pass" id="mmsim-pass" placeholder="Password" autocomplete="off">'+
                '<button type="button" class="mmsim-eye" onclick="var i=document.getElementById(\'mmsim-pass\');i.type=(i.type===\'password\')?\'text\':\'password\';this.innerHTML=\'<i class=&quot;fas fa-\'+(i.type===\'text\'?\'eye-slash\':\'eye\')+\'&quot;&gt;</i>\'"><i class="fas fa-eye"></i></button>'+
            '</div>'+
            '<div id="mmsim-pass-err"></div>'+
            '<div class="mmsim-hint"><i class="fas fa-lock"></i> Demo vault &mdash; any password of 4+ characters unlocks</div>'+
        '</div>'+
        '<div class="mmsim-footer"><button type="button" class="mmsim-btn mmsim-btn-primary" id="mmsim-unlock-go">Unlock</button></div>'
    );
    var go = function(){
        var inp = document.getElementById('mmsim-pass');
        var errEl = document.getElementById('mmsim-pass-err');
        if (!inp.value || inp.value.length < 4){
            inp.classList.add('err');
            errEl.innerHTML = '<div class="mmsim-err-txt"><i class="fas fa-circle-exclamation"></i> Password must be at least 4 characters</div>';
            return;
        }
        var btn = document.getElementById('mmsim-unlock-go');
        btn.disabled = true;
        btn.innerHTML = '<span style="display:inline-block;width:15px;height:15px;border:2px solid rgba(255,255,255,.35);border-top-color:#fff;border-radius:50%;vertical-align:-2px;margin-right:6px;animation:mmsimSpin .7s linear infinite"></span>Unlocking vault...';
        inp.disabled = true;
        setTimeout(function(){
            mmsimUnlocked = true;
            render();
        }, 1000);
    };
    document.getElementById('mmsim-unlock-go').addEventListener('click', go);
    document.getElementById('mmsim-pass').addEventListener('keydown', function(e){ if(e.key==='Enter') go(); });
}

var FOX_SVG = '<svg width="30" height="28" viewBox="0 0 318 346" xmlns="http://www.w3.org/2000/svg"><polygon fill="#E2761B" points="305.2,29.6 176.4,126.8 200.4,69.6"/><polygon fill="#E4761B" points="13.6,29.6 141.2,127.6 118.4,69.6"/><polygon fill="#D7C1B3" points="265.2,235.6 229.6,290 306,311.6 328.4,236.8"/><polygon fill="#D7C1B3" points="53.2,236.8 74.8,311.6 151.2,290 116.4,235.6"/><polygon fill="#CD6116" points="155.6,168.4 133.2,202.8 212.4,199.2 188.4,163.6"/><polygon fill="#E2761B" points="59.6,60.4 117.6,88.4 112.4,42.8"/><polygon fill="#F6851B" points="222,60.4 169.2,43.6 164.4,89.2"/><polygon fill="#763D16" points="151.2,290 147.6,251.6 138.8,258.8 84,310"/><polygon fill="#763D16" points="230.4,310 175.6,258.8 166.8,251.6 163.6,290"/><polygon fill="#F6851B" points="151.2,290 155.6,255.6 152.4,246.4 129.2,264.4 129.6,268.8"/><polygon fill="#F6851B" points="230.4,310 230,268.8 230.4,264.4 206.8,246.4 203.6,255.6"/><polygon fill="#161616" points="130,202.4 106.8,195.6 123.2,187.6"/><polygon fill="#161616" points="151.6,202.4 174.8,195.6 158.4,187.6"/><polygon fill="#F6851B" points="129.6,268.8 129.2,246.4 114.8,250.4"/><polygon fill="#F6851B" points="152,268.8 152.4,246.4 166.8,250.4"/><polygon fill="#763D16" points="74.8,311.6 79.2,290 75.6,286.8"/><polygon fill="#763D16" points="207.2,311.6 206.4,286.8 202.8,290"/><polygon fill="#CD6116" points="79.2,290 74.8,311.6 98.8,290.4 98.4,286"/><polygon fill="#CD6116" points="202.8,290 206.4,311.6 183.2,290.4 183.6,286"/><polygon fill="#E2761B" points="98.4,264.4 92,283.6 99.2,286.4 98.8,290.4 122.8,270"/><polygon fill="#E2761B" points="183.6,264.4 190,283.6 182.8,286.4 183.2,290.4 159.2,270"/><polygon fill="#233447" points="106.8,195.6 113.6,208.8 90.8,201.6"/><polygon fill="#233447" points="174.8,195.6 168,208.8 190.8,201.6"/><polygon fill="#CD6116" points="53.2,236.8 66.4,172.4 41.6,173.2"/><polygon fill="#CD6116" points="215.2,172.4 228.4,236.8 240,173.2"/><polygon fill="#F6851B" points="41.6,173.2 53.2,236.8 52.4,191.2"/><polygon fill="#F6851B" points="240,173.2 229.2,236.8 229.6,191.2"/><polygon fill="#F6851B" points="112.4,42.8 117.6,88.4 118.8,110.8"/><polygon fill="#F6851B" points="169.2,42.8 164.4,89.2 162.8,110.8"/><polygon fill="#E2761B" points="118.8,110.8 112.4,160.4 132.4,171.2 133.6,140.4 131.6,105.2"/><polygon fill="#E2761B" points="162.8,110.8 150,104.8 148,140.4 149.2,171.2 169.2,160.4"/><polygon fill="#D7C1B3" points="133.6,140.4 132.4,171.2 134,184 132.8,200.8 148.8,213.6 165.2,200.8 164,184 165.6,171.2 164.4,140.4 162.8,110.8 150,104.8"/><polygon fill="#233447" points="134,184 132.8,200.8 134.4,202 147.2,211.6 149.2,211.6 162.4,202 164,200.8 162.8,184"/><polygon fill="#CD6116" points="134.4,202 133.2,199.6 121.2,192.8 120.4,194.4 133.6,202.8"/><polygon fill="#CD6116" points="148.8,213.6 147.2,211.6 134.4,202 133.6,202.8 148.4,214"/><polygon fill="#CD6116" points="147.2,211.6 148.8,213.6 161.2,202.8 160.4,194.4 158.8,192.8"/><polygon fill="#CD6116" points="160.4,194.4 161.2,202.8 174.4,195.6 174,194.4 162.4,202"/><polygon fill="#F6851B" points="112.4,160.4 108.8,196 113.6,208.8 111.6,178.4"/><polygon fill="#F6851B" points="169.2,160.4 170,178.4 168,208.8 172.8,196"/><polygon fill="#D7C1B3" points="132.4,171.2 112.4,160.4 111.6,178.4 133.2,185.6"/><polygon fill="#D7C1B3" points="149.2,171.2 150.4,185.6 172,178.4 169.2,160.4"/><polygon fill="#233447" points="90.8,201.6 113.6,208.8 111.6,178.4"/><polygon fill="#233447" points="168,208.8 190.8,201.6 152,178.4"/><polygon fill="#F6851B" points="41.6,173.2 52.4,191.2 51.6,167.6"/><polygon fill="#F6851B" points="229.6,191.2 240,173.2 230,167.6"/><polygon fill="#E2761B" points="51.6,167.6 52.4,191.2 66.4,172.4"/><polygon fill="#E2761B" points="229.2,191.2 230,167.6 215.2,172.4"/><polygon fill="#763D16" points="66.4,172.4 52.4,191.2 90.8,201.6"/><polygon fill="#763D16" points="190.8,201.6 229.2,191.2 215.2,172.4"/><polygon fill="#763D16" points="112.4,160.4 90.8,201.6 111.6,178.4"/><polygon fill="#763D16" points="151.2,160.4 152,178.4 174,160.4"/><polygon fill="#C0AD9E" points="75.6,286.8 71.6,317.2 73.6,320 98.8,300.4 99.2,286"/><polygon fill="#C0AD9E" points="183.2,286 183.6,300.4 208.8,320 210.8,317.2 206.8,286.8"/><polygon fill="#C0AD9E" points="99.2,286 98.8,300.4 122.8,281.6 122.8,270"/><polygon fill="#C0AD9E" points="183.2,286 159.6,270 160,281.6 183.6,300.4"/></svg>';

function popup(html){
    /* portal to <body> so ancestor transforms can never break fixed centering */
    var ov = document.getElementById('mmsim-overlay');
    if (!ov || !ov.parentNode || ov.parentNode !== document.body){
        if (ov) ov.parentNode.removeChild(ov);
        ov = document.createElement('div');
        ov.id = 'mmsim-overlay';
        document.body.appendChild(ov);
    }
    ov.className = 'mmsim-open';
    ov.innerHTML = '<div class="mmsim-popup">'+html+'</div>';
    var bodyEl = ov.querySelector('.mmsim-body');
    if (bodyEl) { bodyEl.classList.add('mmsim-step'); }
    ov.onclick = function(e){ if(e.target === ov && window.__mmsimOnDismiss){ window.__mmsimOnDismiss(); } };
}

/* ---------- REAL METAMASK BRIDGE (transfer mode = real) ---------- */
function mfRealProvider(){
    return (window.MF_CHAIN && window.MF_CHAIN.mode === 'real' && window.ethereum && typeof window.ethereum.request === 'function') ? window.ethereum : null;
}
function mfNetLabel(){
    if (!window.MF_CHAIN || window.MF_CHAIN.mode !== 'real') return 'Demo Network';
    return window.MF_CHAIN.networkName || 'Live Network';
}
function mfToWeiHex(amount){
    var s = String(amount).trim();
    if (window.ethers && window.ethers.utils && window.ethers.utils.parseEther){
        try { return '0x'+window.ethers.BigNumber.from(window.ethers.utils.parseEther(s)).toString(); } catch(e){}
    }
    var parts = s.replace(/-/,'').split('.');
    var intPart = parts[0] || '0';
    var frac = (parts[1] || '').slice(0,18);
    while (frac.length < 18) frac += '0';
    var wei = BigInt(intPart) * BigInt('1000000000000000000') + BigInt(frac || '0');
    return '0x'+wei.toString(16);
}
function mfHexUtf8(str){
    var hex = '0x';
    for (var i=0;i<str.length;i++){ hex += str.charCodeAt(i).toString(16).padStart(2,'0'); }
    return hex;
}
function mfErrText(err){
    if (!err) return 'Unknown wallet error';
    if (err.code === 4001 || /reject/i.test(err.message||'')) return 'Request rejected in MetaMask';
    return err.message || 'Wallet error';
}

function headerHtml(){
    return '<div class="mmsim-header">'+
        '<div class="mmsim-brand">'+FOX_SVG+' MetaMask</div>'+
        '<span style="display:flex;align-items:center;gap:10px">'+
            '<span class="mmsim-net-pill"><span class="mmsim-net-dot"></span>'+esc(mfNetLabel())+'</span>'+
            '<button type="button" class="mmsim-close" onclick="MetaMaskSim.dismiss()">&times;</button>'+
        '</span></div>';
}

function accountRowHtml(acc, withMenu){
    var meBadge = (withMenu) ? '<span class="mmsim-me-badge"><i class="fas fa-user-check" style="font-size:8px"></i> Your Account</span>' : '';
    return '<div class="mmsim-account-box" id="mmsim-acc-box" '+(withMenu?'onclick="MetaMaskSim.toggleAccounts()"':'')+'>'+
        '<img class="mmsim-identicon" src="'+mmsimIdenticon(acc.address)+'"/>'+
        '<div><div class="mmsim-acc-name">'+esc(acc.name)+meBadge+'</div>'+
        '<div class="mmsim-acc-addr">'+short(acc.address)+'</div></div>'+
        '<div class="mmsim-acc-bal"><div class="mmsim-bal-val" id="mmsim-bal-val">'+acc.balance+' ETH</div><div class="mmsim-bal-cur">~$'+(acc.balance*3450).toFixed(2)+'</div></div>'+
        (withMenu?'<i class="fas fa-chevron-down mmsim-caret"></i>':'')+
        '</div>';
}

window.MetaMaskSim = {

    dismiss: function(){
        var ov = document.getElementById('mmsim-overlay');
        if (ov){ ov.className=''; ov.innerHTML=''; }
        window.__mmsimDismissed = true;
        if(window.__mmsimOnDismiss){ var cb = window.__mmsimOnDismiss; window.__mmsimOnDismiss=null; cb(); }
    },

    setBalance: function(address, balance){
        var accounts = getAccounts();
        for (var i=0;i<accounts.length;i++){
            if (accounts[i].address.toLowerCase() === String(address).toLowerCase()){
                accounts[i].balance = +parseFloat(balance).toFixed(4);
                saveAccounts(accounts);
                return true;
            }
        }
        return false;
    },

    toggleAccounts: function(){
        var existing = document.getElementById('mmsim-acct-menu');
        if (existing){ existing.remove(); return; }
        var accounts = getAccounts();
        var sel = getSelected();
        var html = '<div class="mmsim-acct-menu" id="mmsim-acct-menu"><div class="mmsim-acct-menu-label">My Accounts</div>';
        for (var i=0;i<accounts.length;i++){
            html += '<div class="mmsim-acct-item" onclick="event.stopPropagation();MetaMaskSim.pickAccount('+i+')">'+
                '<img src="'+mmsimIdenticon(accounts[i].address)+'"/>'+
                '<div><div style="font-weight:600">'+esc(accounts[i].name)+'</div>'+
                '<div style="font-size:11px;color:#6A737D;font-family:monospace">'+short(accounts[i].address)+' &bull; '+accounts[i].balance+' ETH</div></div>'+
                (i===sel?'<span class="chk">&#10003;</span>':'')+
                '</div>';
        }
        html += '</div>';
        document.getElementById('mmsim-acc-box').appendChild(html ? document.createRange().createContextualFragment(html) : null);
    },

    pickAccount: function(i){
        setSelected(i);
        var box = document.getElementById('mmsim-acc-box');
        if (box){
            var acc = getAccounts()[i];
            box.outerHTML = accountRowHtml(acc, true);
        }
    },

    /* ---------- CONNECT FLOW ---------- */
    connect: function(opts){
        var self = this;
        opts = opts || {};

        /* REAL MODE: hand off to the actual MetaMask extension */
        if (mfRealProvider()) {
            var eth = mfRealProvider();
            popup(
                headerHtml()+
                '<div class="mmsim-body" style="text-align:center">'+
                    '<div style="text-align:center">'+FOX_SVG.replace('width="30" height="28"','width="46" height="43"')+'</div>'+
                    '<div class="mmsim-title">Connect MetaMask</div>'+
                    '<div class="mmsim-sub"><i class="fas fa-spinner fa-spin"></i> Approve the connection request in your MetaMask extension</div>'+
                '</div>'+
                '<div class="mmsim-footer"><button type="button" class="mmsim-btn mmsim-btn-cancel" onclick="MetaMaskSim.dismiss()">Cancel</button></div>'
            );
            window.__mmsimOnDismiss = opts.onCancel || null;
            window.__mmsimDismissed = false;
            eth.request({method:'eth_requestAccounts'}).then(function(accts){
                if (!accts || !accts.length) throw {code:4001};
                var addr = accts[0];
                window.__mfRealAccount = addr;
                return eth.request({method:'eth_getBalance', params:[addr,'latest']}).then(function(balHex){
                    var bal = balHex ? Math.round((parseInt(balHex,16)/1e18)*10000)/10000 : 0;
                    window.__mfRealBalance = bal;
                    window.__mmsimOnDismiss = null;
                    self.dismiss();
                    if (opts.onConnect) opts.onConnect({address:String(addr).toLowerCase(), name:'My MetaMask Wallet', balance:bal});
                });
            }).catch(function(err){
                var cb = window.__mmsimOnDismiss; window.__mmsimOnDismiss = null;
                self.dismiss();
                if (cb) cb(mfErrText(err));
            });
            return;
        }

        /* MediFund mode: each login owns ONE auto-generated account — no account picking.
           If the page provides MF_USER_WALLET (logged-in user), bind it directly. */
        if (window.MF_USER_WALLET && /^0x[0-9a-fA-F]{40}$/.test(window.MF_USER_WALLET)) {
            popup(
                headerHtml()+
                '<div class="mmsim-body">'+
                    '<div style="text-align:center">'+FOX_SVG.replace('width="30" height="28"','width="46" height="43"')+'</div>'+
                    '<div class="mmsim-title">Connect Your Account</div>'+
                    '<div class="mmsim-site-row">'+
                        '<div class="mmsim-site-avatar">M</div>'+
                        '<span style="font-size:13px;color:#6A737D">Authorized for</span>'+
                        '<span class="mmsim-hostname">'+esc(window.location.hostname||'this site')+'</span>'+
                    '</div>'+
                    '<div class="mmsim-permission"><i class="fas fa-user-check"></i><span>This account was auto-generated for your username &amp; password at registration</span></div>'+
                    accountRowHtml({address:window.MF_USER_WALLET,name:(window.MF_USER_ROLE==='patient'?'My Patient Account':'My Donor Account'),balance:parseFloat(window.MF_USER_BALANCE||0)}, true)+
                    '<div class="mmsim-switch-hint"><i class="fas fa-lock"></i> One account per MediFund login — no switching needed</div>'+
                '</div>'+
                '<div class="mmsim-footer">'+
                    '<button type="button" class="mmsim-btn mmsim-btn-cancel" onclick="MetaMaskSim.dismiss()">Cancel</button>'+
                    '<button type="button" class="mmsim-btn mmsim-btn-primary" id="mmsim-next">Connect</button>'+
                '</div>'
            );
            document.getElementById('mmsim-next').addEventListener('click', function(){
                var btn=this;
                btn.disabled=true;
                btn.innerHTML='<span style="display:inline-block;width:15px;height:15px;border:2px solid rgba(255,255,255,.35);border-top-color:#fff;border-radius:50%;vertical-align:-2px;margin-right:6px;animation:mmsimSpin .7s linear infinite"></span>Connecting...';
                setTimeout(function(){
                    self.dismiss();
                    if(opts.onConnect) opts.onConnect({address:window.MF_USER_WALLET.toLowerCase(),name:'My MediFund Account',balance:parseFloat(window.MF_USER_BALANCE||0)});
                },1000);
            });
            return;
        }

        /* Guest fallback: send to username/password login instead of showing accounts */
        if (!window.MF_USER_WALLET) {
            popup(
                headerHtml()+
                '<div class="mmsim-body" style="text-align:center">'+
                    '<div style="text-align:center">'+FOX_SVG.replace('width="30" height="28"','width="46" height="43"')+'</div>'+
                    '<div class="mmsim-title">Login Required</div>'+
                    '<p style="font-size:13px;color:#6A737D;line-height:1.6;margin:0 0 16px">Blockchain accounts are generated when you log in with your <strong>username &amp; password</strong>. Donor accounts are pre-funded; patients receive payouts after admin verification.</p>'+
                '</div>'+
                '<div class="mmsim-footer">'+
                    '<button type="button" class="mmsim-btn mmsim-btn-cancel" onclick="MetaMaskSim.dismiss()">Cancel</button>'+
                    '<a href="'+(window.MF_LOGIN_URL||'/login')+'" class="mmsim-btn mmsim-btn-primary" style="text-decoration:none;text-align:center">Login / Register</a>'+
                '</div>'
            );
            return;
        }

        ensureUnlocked(function(){ self._renderConnect(opts); });
    },

    _renderConnect: function(opts){
        opts = opts || {};
        var accounts = getAccounts();
        var sel = getSelected();
        var host = window.location.hostname || 'this site';
        var self = this;

        popup(
            headerHtml()+
            '<div class="mmsim-body">'+
                '<div style="text-align:center">'+FOX_SVG.replace('width="30" height="28"','width="46" height="43"')+'</div>'+
                '<div class="mmsim-title">Connect With MetaMask</div>'+
                '<div class="mmsim-site-row">'+
                    '<div class="mmsim-site-avatar">'+esc(host.charAt(0).toUpperCase())+'</div>'+
                    '<span style="font-size:13px;color:#6A737D">Connect with</span>'+
                    '<span class="mmsim-hostname">'+esc(host)+'</span>'+
                '</div>'+
                '<div class="mmsim-permission"><i class="fas fa-eye"></i><span>See address, account balance, activity and suggest transactions to approve</span></div>'+
                '<div class="mmsim-permission"><i class="fas fa-bell"></i><span>Receive notifications about your wallet activity</span></div>'+
                accountRowHtml(accounts[sel], true)+
                '<div class="mmsim-unlock-note"><i class="fas fa-wallet" style="color:#0376C9;margin-top:2px"></i><span><strong>'+esc(accounts[sel].name)+'</strong> holds <strong>'+accounts[sel].balance+' ETH &nbsp;(~$'+(accounts[sel].balance*3450).toFixed(2)+')</strong>. Click the box above to pick a different account.</span></div>'+
                '<div class="mmsim-switch-hint"><i class="fas fa-info-circle"></i> Only connect with sites you trust</div>'+
            '</div>'+
            '<div class="mmsim-footer">'+
                '<button type="button" class="mmsim-btn mmsim-btn-cancel" onclick="MetaMaskSim.dismiss()">Cancel</button>'+
                '<button type="button" class="mmsim-btn mmsim-btn-primary" id="mmsim-next">Next</button>'+
            '</div>'
        );

        window.__mmsimOnDismiss = opts.onCancel || null;
        window.__mmsimDismissed = false;

        document.getElementById('mmsim-next').addEventListener('click', function(){
            var btn = this;
            btn.disabled = true;
            btn.innerHTML = '<span style="display:inline-block;width:15px;height:15px;border:2px solid rgba(255,255,255,.35);border-top-color:#fff;border-radius:50%;vertical-align:-2px;margin-right:6px;animation:mmsimSpin .7s linear infinite"></span>Connecting...';
            setTimeout(function(){
                var acc = getAccounts()[getSelected()];
                self.dismiss();
                if(opts.onConnect) opts.onConnect({address: acc.address.toLowerCase(), name: acc.name, balance: acc.balance});
            }, 1400);
        });
    },

    /* ---------- SIGNATURE REQUEST FLOW (personal_sign) ---------- */
    signMessage: function(opts){
        var self = this;
        opts = opts || {};

        /* REAL MODE: sign with the actual extension */
        var realEth = mfRealProvider();
        if (realEth) {
            var doSign = function(addr){
                realEth.request({method:'personal_sign', params:[mfHexUtf8(String(opts.message||'Sign this message to prove you own this wallet.')), addr]})
                .then(function(sig){ if (opts.onSign) opts.onSign(sig); })
                .catch(function(err){ if (opts.onReject) opts.onReject(mfErrText(err)); });
            };
            var addr = opts.address || window.__mfRealAccount;
            if (addr) { doSign(addr); }
            else {
                realEth.request({method:'eth_requestAccounts'})
                .then(function(a){ doSign(a[0]); })
                .catch(function(err){ if (opts.onReject) opts.onReject(mfErrText(err)); });
            }
            return;
        }

        ensureUnlocked(function(){ self._renderSign(opts); });
    },

    _renderSign: function(opts){
        var accounts = getAccounts();
        var acc = accounts[getSelected()];
        if (opts.address) {
            for (var i=0;i<accounts.length;i++){
                if (accounts[i].address.toLowerCase() === String(opts.address).toLowerCase()){ acc = accounts[i]; break; }
            }
        }
        var host = window.location.hostname || 'this site';
        var msg = String(opts.message || 'Sign this message to prove you own this wallet.');
        var self = this;

        popup(
            headerHtml()+
            '<div class="mmsim-body" style="text-align:left;padding-bottom:0">'+
                '<div class="mmsim-title" style="font-size:19px;text-align:left;margin-top:0">Signature Request</div>'+
                '<div class="mmsim-sub" style="text-align:left"><i class="fas fa-shield-alt" style="color:#0376C9"></i> Only sign messages you understand</div>'+
                '<div class="mmsim-tx-card" style="margin-top:16px">'+
                    '<div class="mmsim-tx-row"><span class="lbl">Origin</span><span class="val">'+esc(host)+'</span></div>'+
                    '<div class="mmsim-tx-row"><span class="lbl">Account</span><span class="val"><img class="mmsim-identicon" style="width:20px;height:20px" src="'+mmsimIdenticon(acc.address)+'"/> '+short(acc.address)+'</span></div>'+
                '</div>'+
                '<div style="background:#F6F8FA;border:1px solid #EBEEF2;border-radius:14px;padding:14px 16px;font-family:SFMono-Regular,Consolas,monospace;font-size:11.5px;line-height:1.7;color:#3B424A;white-space:pre-wrap;word-break:break-word;max-height:170px;overflow-y:auto">'+esc(msg)+'</div>'+
            '</div>'+
            '<div class="mmsim-footer">'+
                '<button type="button" class="mmsim-btn mmsim-btn-cancel" id="mmsim-sign-cancel">Cancel</button>'+
                '<button type="button" class="mmsim-btn mmsim-btn-primary" id="mmsim-sign-ok">Sign</button>'+
            '</div>'
        );

        window.__mmsimOnDismiss = opts.onReject || null;
        window.__mmsimDismissed = false;

        document.getElementById('mmsim-sign-cancel').addEventListener('click', function(){ self.dismiss(); });
        document.getElementById('mmsim-sign-ok').addEventListener('click', function(){
            var btn = this;
            btn.disabled = true;
            btn.innerHTML = '<span style="display:inline-block;width:15px;height:15px;border:2px solid rgba(255,255,255,.35);border-top-color:#fff;border-radius:50%;vertical-align:-2px;margin-right:6px;animation:mmsimSpin .7s linear infinite"></span>Signing...';
            setTimeout(function(){
                /* 65-byte ECDSA-style signature (r || s || v), format-valid for demo auth */
                var sig = '0x'+randHex(64)+randHex(64)+(Math.random()<.5?'1b':'1c');
                window.__mmsimOnDismiss = null;
                self.dismiss();
                if(opts.onSign) opts.onSign(sig);
            }, 1100);
        });
    },

    /* ---------- TRANSACTION CONFIRM FLOW ---------- */
    confirmTx: function(opts){
        var self = this;
        opts = opts || {};

        /* REAL MODE: MetaMask renders its own confirmation window; we just send */
        var realEth = mfRealProvider();
        if (realEth) {
            popup(
                headerHtml()+
                '<div class="mmsim-body" style="text-align:center">'+
                    '<div style="text-align:center">'+FOX_SVG.replace('width="30" height="28"','width="46" height="43"')+'</div>'+
                    '<div class="mmsim-title">Confirm in MetaMask</div>'+
                    '<div class="mmsim-sub"><i class="fas fa-spinner fa-spin"></i> Review and approve the transaction in your MetaMask extension</div>'+
                '</div>'+
                '<div class="mmsim-footer"><button type="button" class="mmsim-btn mmsim-btn-cancel" onclick="MetaMaskSim.rejectTx()">Cancel</button></div>'
            );
            window.__mmsimOnDismiss = opts.onReject || null;
            window.__mmsimDismissed = false;
            var send = function(from){
                realEth.request({method:'eth_sendTransaction', params:[{
                    from: from,
                    to: String(opts.to||'0x0000000000000000000000000000000000000000'),
                    value: mfToWeiHex(opts.amount||'0')
                }]}).then(function(hash){
                    var tries = 0;
                    var finish = function(ok){
                        var ov = document.getElementById('mmsim-overlay');
                        if (ov && ov.querySelector('.mmsim-popup')) ov.querySelector('.mmsim-popup').innerHTML =
                            '<div class="mmsim-header"><div class="mmsim-brand">'+FOX_SVG+' MetaMask</div><span class="mmsim-net-pill"><span class="mmsim-net-dot"></span>'+esc(mfNetLabel())+'</span></div>'+
                            '<div class="mmsim-body" style="padding-bottom:20px">'+
                                '<div class="mmsim-success-check"><svg viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="'+(ok?'#06BE7F':'#D14343')+'" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div>'+
                                '<div class="mmsim-title" style="margin-top:0">'+(ok?'Transaction Submitted':'Transaction Failed')+'</div>'+
                                '<div class="mmsim-sub">'+(ok?('Confirmed on '+esc(mfNetLabel())):'The transaction was rejected or reverted on-chain')+'</div>'+
                                '<div style="font-family:monospace;font-size:11px;color:#0376C9;background:#F5F6FC;border-radius:8px;padding:8px 10px;margin-top:14px;word-break:break-all">'+String(hash).slice(0,26)+'...</div>'+
                                '<div style="font-size:11.5px;color:#8B959E;margin-top:10px"><i class="fas fa-spinner fa-spin"></i> Redirecting...</div>'+
                            '</div>';
                        window.__mmsimOnDismiss = null;
                        setTimeout(function(){
                            self.dismiss();
                            if (ok && opts.onConfirm) opts.onConfirm(hash);
                        }, 1800);
                    };
                    var poll = function(){
                        realEth.request({method:'eth_getTransactionReceipt', params:[hash]}).then(function(rcpt){
                            if (!rcpt){ if (++tries > 80){ finish(true); } else { setTimeout(poll, 1500); } return; }
                            finish(rcpt.status === '0x1' || rcpt.status === '0x01');
                        }).catch(function(){ finish(true); });
                    };
                    poll();
                }).catch(function(err){
                    var cb = window.__mmsimOnDismiss; window.__mmsimOnDismiss = null;
                    self.dismiss();
                    if (cb) cb(mfErrText(err));
                });
            };
            var from = opts.from || window.__mfRealAccount;
            if (from) { send(from); }
            else {
                realEth.request({method:'eth_requestAccounts'})
                .then(function(a){ send(a[0]); })
                .catch(function(err){
                    var cb = window.__mmsimOnDismiss; window.__mmsimOnDismiss = null;
                    self.dismiss();
                    if (cb) cb(mfErrText(err));
                });
            }
            return;
        }

        ensureUnlocked(function(){ self._renderConfirmTx(opts); });
    },

    _renderConfirmTx: function(opts){
        var accounts = getAccounts();
        var acc = accounts[getSelected()];
        /* MediFund mode: spend from the logged-in user's own generated account */
        if (window.MF_USER_WALLET) {
            acc = {address: window.MF_USER_WALLET, name: 'My MediFund Account', balance: parseFloat(window.MF_USER_BALANCE||0)};
        }
        var amount = parseFloat(opts.amount||'0');
        var gas = 0.0021;
        var total = amount + gas;
        var usd = (amount*3450).toFixed(2);
        var to = opts.to || '0x0000000000000000000000000000000000000000';
        var insufficient = total > parseFloat(acc.balance);
        var self = this;

        popup(
            headerHtml()+
            '<div class="mmsim-body" style="padding-bottom:0">'+
                '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">'+
                    '<span style="font-size:15px;font-weight:700">Review</span>'+
                    '<span class="mmsim-gas-badge"><i class="fas fa-fire" style="font-size:10px"></i> Market</span>'+
                '</div>'+
                '<div class="mmsim-tx-amount">-'+amount.toFixed(3)+' ETH</div>'+
                '<div class="mmsim-tx-usd">$'+usd+' USD &bull; Demo Network</div>'+
                '<div class="mmsim-tx-card">'+
                '<div class="mmsim-tx-row"><span class="lbl">From</span><span class="val"><img class="mmsim-identicon" style="width:20px;height:20px" src="'+mmsimIdenticon(acc.address)+'"/> '+short(acc.address)+'</span></div>'+
                '<div class="mmsim-tx-row"><span class="lbl">To</span><span class="val"><img class="mmsim-identicon" style="width:20px;height:20px" src="'+mmsimIdenticon(to)+'"/> '+short(to)+'</span></div>'+
                '<div class="mmsim-tx-row"><span class="lbl">Estimated gas fee</span><span class="val">'+gas.toFixed(4)+' ETH <small style="color:#6A737D;font-weight:400">( $'+(gas*3450).toFixed(2)+' )</small></span></div>'+
                '</div>'+
                '<div class="mmsim-tx-card" style="background:#fff;border-color:#E3E7EC">'+
                '<div class="mmsim-tx-row mmsim-tx-total"><span class="lbl" style="font-weight:700;color:#24272E">Total</span><span class="val" style="font-weight:800">'+total.toFixed(4)+' ETH</span></div>'+
                '</div>'+
                (insufficient
                    ? '<div class="mmsim-insufficient"><i class="fas fa-exclamation-triangle" style="margin-top:2px"></i><span><strong>Insufficient funds.</strong> You need '+total.toFixed(4)+' ETH but only have '+parseFloat(acc.balance).toFixed(4)+' ETH in '+esc(acc.name)+'.</span></div>'
                    : '<div style="font-size:11.5px;color:#8B959E;padding:8px 0 4px"><i class="fas fa-info-circle"></i> Simulated transaction on demo network &mdash; no real funds move</div>')+
            '</div>'+
            '<div class="mmsim-footer">'+
                '<button type="button" class="mmsim-btn mmsim-btn-cancel" onclick="MetaMaskSim.rejectTx()">Reject</button>'+
                '<button type="button" class="mmsim-btn mmsim-btn-primary" id="mmsim-confirm" '+(insufficient?'disabled':'')+'>Confirm</button>'+
            '</div>'
        );

        window.__mmsimOnDismiss = opts.onReject || null;
        window.__mmsimDismissed = false;

        document.getElementById('mmsim-confirm').addEventListener('click', function(){
            var btn = this;
            btn.disabled = true;
            btn.innerHTML = '<span style="display:inline-block;width:15px;height:15px;border:2px solid rgba(255,255,255,.35);border-top-color:#fff;border-radius:50%;vertical-align:-2px;margin-right:6px;animation:mmsimSpin .7s linear infinite"></span>Processing...';
            setTimeout(function(){
                var hash = '0x'+randHex(64);

                /* deduct from demo balance (persisted) */
                var accts = getAccounts();
                var cur = accts[getSelected()];
                cur.balance = Math.max(0, +(cur.balance - total).toFixed(4));
                saveAccounts(accts);

                var ov = document.getElementById('mmsim-overlay');
                if (ov) ov.querySelector('.mmsim-popup').innerHTML =
                    '<div class="mmsim-header"><div class="mmsim-brand">'+FOX_SVG+' MetaMask</div><span class="mmsim-net-pill"><span class="mmsim-net-dot"></span>Demo Network</span></div>'+
                    '<div class="mmsim-body" style="padding-bottom:20px">'+
                        '<div class="mmsim-success-check"><svg viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="#06BE7F" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div>'+
                        '<div class="mmsim-title" style="margin-top:0">Transaction Submitted</div>'+
                        '<div class="mmsim-sub">Your transaction is being confirmed on the demo blockchain</div>'+
                        '<div style="font-family:monospace;font-size:11px;color:#0376C9;background:#F5F6FC;border-radius:8px;padding:8px 10px;margin-top:14px;word-break:break-all">'+hash.slice(0,20)+'...</div>'+
                        '<div style="font-size:11.5px;color:#8B959E;margin-top:10px"><i class="fas fa-spinner fa-spin"></i> Redirecting...</div>'+
                    '</div>';

                /* auto-continue after a beat â€” no extra click needed */
                window.__mmsimOnDismiss = null;
                setTimeout(function(){
                    self.dismiss();
                    if(opts.onConfirm) opts.onConfirm(hash);
                }, 1600);
            }, 1800);
        });
    },

    rejectTx: function(){
        this.dismiss();
    }
};

/* ESC closes the popup like the real extension */
document.addEventListener('keydown', function(e){
    if (e.key === 'Escape' && document.getElementById('mmsim-overlay')) { MetaMaskSim.dismiss(); }
});
})();
</script>
@endonce

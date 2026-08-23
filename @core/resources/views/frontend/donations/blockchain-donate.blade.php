@extends('frontend.frontend-page-master')
@section('site-title')
    {{__('Blockchain Donation')}} - {{__('Demo Ethereum Network')}}
@endsection

@section('style')
<style>
/* ============ MediFund Blockchain Donate — fully self-contained (bf- prefix) ============ */
.bf-page, .bf-page *, .bf-page *::before, .bf-page *::after { box-sizing: border-box; }
.bf-page {
    --bf-primary:#627EEA; --bf-accent:#00D4AA; --bf-danger:#ff5c7a;
    --bf-bg:#0B0E1A; --bf-card:#131728; --bf-card2:#1A2036; --bf-border:rgba(255,255,255,.08);
    --bf-text:#EAECF5; --bf-muted:rgba(234,236,245,.55); --bf-faint:rgba(234,236,245,.35);
    min-height:100vh;
    background:
        radial-gradient(900px 500px at 15% -10%, rgba(98,126,234,.16), transparent 60%),
        radial-gradient(800px 460px at 90% 110%, rgba(0,212,170,.12), transparent 60%),
        var(--bf-bg);
    color:var(--bf-text);
    font-family:'Inter','Segoe UI',system-ui,-apple-system,sans-serif;
    padding:56px 20px 80px;
    position:relative;
}
.bf-wrap{max-width:960px;margin:0 auto;position:relative;z-index:1}

/* header */
.bf-top{display:flex;align-items:center;justify-content:center;gap:10px;margin-bottom:26px}
.bf-netpill{display:inline-flex;align-items:center;gap:8px;background:rgba(0,212,170,.08);border:1px solid rgba(0,212,170,.25);color:var(--bf-accent);font-size:12px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;border-radius:999px;padding:7px 16px}
.bf-dot{width:8px;height:8px;border-radius:50%;background:var(--bf-accent);box-shadow:0 0 0 0 rgba(0,212,170,.5);animation:bfPulse 2s infinite}
@keyframes bfPulse{0%{box-shadow:0 0 0 0 rgba(0,212,170,.45)}70%{box-shadow:0 0 0 9px rgba(0,212,170,0)}100%{box-shadow:0 0 0 0 rgba(0,212,170,0)}}

.bf-demo{display:flex;align-items:center;justify-content:center;gap:8px;background:rgba(255,184,0,.07);border:1px dashed rgba(255,184,0,.35);color:#FFD166;font-size:12.5px;font-weight:600;border-radius:12px;padding:10px 16px;margin-bottom:22px;text-align:center}

/* layout */
.bf-grid{display:grid;grid-template-columns:1fr;gap:20px}
@media(min-width:900px){.bf-grid{grid-template-columns:1.05fr .95fr;align-items:start}}
.bf-card{background:linear-gradient(180deg,var(--bf-card),rgba(19,23,40,.75));border:1px solid var(--bf-border);border-radius:20px;padding:30px 28px;box-shadow:0 18px 50px rgba(0,0,0,.35)}
.bf-card h1{font-size:24px;font-weight:800;color:#fff;margin:0 0 6px;letter-spacing:-.02em}
.bf-sub{font-size:13.5px;color:var(--bf-muted);margin:0 0 22px;line-height:1.55}

/* campaign summary */
.bf-camp{background:var(--bf-card2);border:1px solid var(--bf-border);border-radius:16px;padding:18px 20px;margin-bottom:22px}
.bf-camp-title{display:flex;align-items:center;gap:10px;margin-bottom:14px}
.bf-camp-ico{width:38px;height:38px;border-radius:11px;background:linear-gradient(135deg,var(--bf-primary),#8A9FF5);display:flex;align-items:center;justify-content:center;color:#fff;font-size:15px;flex-shrink:0}
.bf-camp-name{font-weight:700;font-size:14.5px;color:#fff;line-height:1.35}
.bf-row{display:flex;justify-content:space-between;align-items:center;padding:7px 0;font-size:13px}
.bf-row .k{color:var(--bf-muted)}
.bf-row .v{font-weight:700;color:#fff}
.bf-row .v small{color:var(--bf-faint);font-weight:500;margin-left:6px}
.bf-bar{height:8px;background:rgba(255,255,255,.07);border-radius:99px;overflow:hidden;margin-top:10px}
.bf-bar i{display:block;height:100%;border-radius:99px;background:linear-gradient(90deg,var(--bf-primary),var(--bf-accent))}
.bf-pct{text-align:right;font-size:12px;color:var(--bf-accent);font-weight:700;margin-top:7px}

/* form */
.bf-label{display:block;font-size:12.5px;font-weight:700;color:var(--bf-muted);text-transform:uppercase;letter-spacing:.05em;margin:0 0 9px}
.bf-field{margin-bottom:20px}
.bf-inputwrap{position:relative}
.bf-input{width:100%;background:var(--bf-card2);border:1px solid var(--bf-border);border-radius:13px;color:#fff;font-size:16px;font-weight:600;padding:15px 58px 15px 42px;outline:none;transition:border-color .15s, box-shadow .15s;-moz-appearance:textfield}
.bf-input::-webkit-outer-spin-button,.bf-input::-webkit-inner-spin-button{-webkit-appearance:none;margin:0}
.bf-input:focus{border-color:var(--bf-primary);box-shadow:0 0 0 3px rgba(98,126,234,.18)}
.bf-input::placeholder{color:var(--bf-faint);font-weight:500}
.bf-cur{position:absolute;left:16px;top:50%;transform:translateY(-50%);color:var(--bf-faint);font-weight:800;font-size:15px}
.bf-suffix{position:absolute;right:16px;top:50%;transform:translateY(-50%);color:var(--bf-faint);font-size:12px;font-weight:800;letter-spacing:.04em}
.bf-conv{display:flex;align-items:center;justify-content:space-between;margin-top:9px;padding:0 3px}
.bf-eth{color:var(--bf-accent);font-size:13.5px;font-weight:800}
.bf-rate{color:var(--bf-faint);font-size:11.5px}
.bf-err{color:var(--bf-danger);font-size:12px;display:block;margin-top:6px}
.bf-chips{display:grid;grid-template-columns:repeat(4,1fr);gap:9px;margin-top:12px}
.bf-chip{background:var(--bf-card2);border:1px solid var(--bf-border);color:var(--bf-muted);font-size:13px;font-weight:700;border-radius:11px;padding:10px 0;cursor:pointer;transition:all .15s;text-align:center}
.bf-chip:hover{border-color:var(--bf-primary);color:#fff}
.bf-chip.active{background:rgba(98,126,234,.15);border-color:var(--bf-primary);color:#fff}

/* identity fields */
.bf-input--plain{padding-left:16px;padding-right:16px;font-size:14.5px;font-weight:500}

/* anonymous toggle */
.bf-anon{display:flex;align-items:center;gap:12px;background:rgba(0,212,170,.05);border:1px solid rgba(0,212,170,.18);border-radius:13px;padding:14px 16px;margin-bottom:20px;cursor:pointer}
.bf-anon input[type=checkbox]{width:18px;height:18px;accent-color:var(--bf-accent);cursor:pointer;flex-shrink:0}
.bf-anon .t{font-size:13.5px;font-weight:700;color:var(--bf-text)}
.bf-anon .d{font-size:11.5px;color:var(--bf-faint);margin-left:auto;text-align:right}

/* recipient wallet */
.bf-recv{display:flex;align-items:center;gap:11px;background:var(--bf-card2);border:1px solid var(--bf-border);border-radius:13px;padding:13px 16px}
.bf-recv i{color:var(--bf-accent)}
.bf-recv span{font-family:'SFMono-Regular',Consolas,monospace;font-size:12px;color:var(--bf-text);word-break:break-all}

/* wallet connect area */
.bf-wallet{border:1.5px dashed rgba(98,126,234,.45);border-radius:16px;padding:26px 20px;text-align:center;cursor:pointer;transition:all .2s;background:rgba(98,126,234,.04)}
.bf-wallet:hover{border-color:var(--bf-primary);background:rgba(98,126,234,.09)}
.bf-wallet.connected{border:1px solid rgba(0,212,170,.4);background:rgba(0,212,170,.05);cursor:default}
.bf-wallet-ico{width:52px;height:52px;border-radius:15px;background:linear-gradient(135deg,#627EEA,#8A9FF5);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;color:#fff;font-size:20px;box-shadow:0 8px 22px rgba(98,126,234,.35)}
.bf-wallet h4{color:#fff;font-size:15.5px;font-weight:800;margin:0 0 5px}
.bf-wallet p{color:var(--bf-faint);font-size:12.5px;margin:0}
.bf-wallet .mm-tag{display:inline-flex;align-items:center;gap:6px;background:#fff;color:#24272E;font-size:11px;font-weight:800;border-radius:8px;padding:4px 10px;margin-top:12px}
.bf-wconn{display:flex;align-items:center;gap:14px;text-align:left}
.bf-wconn canvas{border-radius:50%;border:3px solid rgba(0,212,170,.4);flex-shrink:0}
.bf-waddr{display:flex;align-items:center;gap:8px;background:rgba(255,255,255,.05);border:1px solid var(--bf-border);border-radius:10px;padding:8px 12px;margin:7px 0}
.bf-waddr span{flex:1;font-family:monospace;font-size:12px;color:var(--bf-text);word-break:break-all}
.bf-copybtn{background:rgba(98,126,234,.15);border:none;color:#AAB6F8;font-size:11px;font-weight:700;border-radius:8px;padding:5px 10px;cursor:pointer}
.bf-copybtn:hover{background:rgba(98,126,234,.3)}

/* fee note */
.bf-fee{display:flex;gap:10px;align-items:flex-start;background:var(--bf-card2);border:1px solid var(--bf-border);border-radius:13px;padding:13px 16px;font-size:12.5px;color:var(--bf-muted);margin-bottom:22px}
.bf-fee i{color:var(--bf-primary);margin-top:2px}
.bf-fee strong{color:var(--bf-text)}

/* submit */
.bf-submit{width:100%;border:none;border-radius:14px;padding:17px 0;font-size:15.5px;font-weight:800;color:#fff;cursor:pointer;background:linear-gradient(135deg,var(--bf-primary) 0%,#4B6CEB 55%,var(--bf-accent) 130%);box-shadow:0 10px 28px rgba(75,108,235,.35);transition:all .2s;display:flex;align-items:center;justify-content:center;gap:10px}
.bf-submit:hover:not(:disabled){transform:translateY(-1px);box-shadow:0 14px 34px rgba(75,108,235,.45)}
.bf-submit:disabled{opacity:.45;cursor:not-allowed;box-shadow:none}

/* side info card */
.bf-info-item{display:flex;gap:13px;padding:13px 0;border-bottom:1px solid var(--bf-border)}
.bf-info-item:last-child{border-bottom:none}
.bf-info-ico{width:36px;height:36px;border-radius:10px;background:rgba(98,126,234,.12);display:flex;align-items:center;justify-content:center;color:#8A9FF5;font-size:14px;flex-shrink:0}
.bf-info-item h5{font-size:13px;font-weight:700;color:#fff;margin:0 0 3px}
.bf-info-item p{font-size:12px;color:var(--bf-faint);margin:0;line-height:1.5}

.bf-foot{text-align:center;color:var(--bf-faint);font-size:11.5px;margin-top:26px}
</style>
@endsection

@section('content')
<div class="bf-page">
    <div class="bf-wrap">
        <div class="bf-top">
            <span class="bf-netpill"><span class="bf-dot"></span> {{__('Demo Ethereum Network')}}</span>
        </div>

        <div class="bf-demo">
            <i class="fas fa-flask"></i>
            {{__('DEMO BLOCKCHAIN TRANSACTION')}} &mdash; {{__('No real cryptocurrency is used')}}
        </div>

        <div class="bf-grid">
            <!-- LEFT: donation form -->
            <div class="bf-card">
                <h1>{{__('Donate with Crypto')}}</h1>
                <p class="bf-sub">{{__('Secure, transparent, and trackable on the blockchain')}}.</p>

                <div class="bf-camp">
                    <div class="bf-camp-title">
                        <div class="bf-camp-ico"><i class="fas fa-heartbeat"></i></div>
                        <div class="bf-camp-name">{{Str::limit($donation->title, 60)}}</div>
                    </div>
                    <div class="bf-row"><span class="k">{{__('Raised')}}</span><span class="v" style="color:var(--bf-accent)">{{amount_with_currency_symbol($donation->raised)}}<small>&asymp; {{number_format(($donation->raised ?? 0)/3450, 4)}} ETH</small></span></div>
                    <div class="bf-row"><span class="k">{{__('Goal')}}</span><span class="v">{{amount_with_currency_symbol($donation->amount)}}<small>&asymp; {{number_format(($donation->amount ?? 0)/3450, 4)}} ETH</small></span></div>
                    @php $progress = $donation->amount > 0 ? round(($donation->raised / $donation->amount) * 100) : 0; @endphp
                    <div class="bf-bar"><i style="width:{{min($progress,100)}}%"></i></div>
                    <div class="bf-pct">{{$progress}}% {{__('funded')}}</div>
                </div>

                {{-- Live smart-contract state: Algorithm 1 fraud gate straight from MediFundCampaign --}}
                <div class="bf-camp" id="chainStateCard" style="display:none">
                    <div class="bf-camp-title">
                        <div class="bf-camp-ico" style="background:linear-gradient(135deg,#00D4AA,#4B6CEB);"><i class="fas fa-cubes"></i></div>
                        <div class="bf-camp-name">{{__('On-Chain Campaign State')}}
                            <span style="font-size:9px;color:#00D4AA;border:1px solid rgba(0,212,170,.4);border-radius:99px;padding:2px 8px;margin-left:6px;">{{__('LIVE')}}</span>
                        </div>
                    </div>
                    <div class="bf-row"><span class="k">{{__('Contract Status')}}</span><span class="v" id="csStatus">&mdash;</span></div>
                    <div class="bf-row"><span class="k">{{__('Fraud Gate')}}</span><span class="v" id="csGate">&mdash;</span></div>
                    <div class="bf-row" style="padding-top:10px;">
                        <div style="flex:1;">
                            <div style="height:6px;background:rgba(255,255,255,.07);border-radius:99px;overflow:hidden;">
                                <div id="csScoreBar" style="height:100%;width:0%;border-radius:99px;background:linear-gradient(90deg,#00D4AA 0%,#FFB800 50%,#ff5c7a 100%);transition:width .8s cubic-bezier(.25,1,.5,1);"></div>
                            </div>
                            <div style="display:flex;justify-content:space-between;font-size:9.5px;color:var(--bf-faint);margin-top:5px;text-transform:uppercase;letter-spacing:.05em;font-weight:700;">
                                <span>{{__('Fraud score')}} <b id="csScoreNum" style="color:var(--bf-text)">0</b>/100</span>
                                <span>{{__('block ≥50 = blocked')}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="bf-row"><span class="k">{{__('Hospital Verified')}}</span><span class="v" id="csHospital">&mdash;</span></div>
                    <div class="bf-row"><span class="k">{{__('Patient Verified')}}</span><span class="v" id="csPatient">&mdash;</span></div>
                    <div class="bf-row"><span class="k">{{__('Escrow Held (this campaign)')}}</span><span class="v" style="color:var(--bf-accent)" id="csEscrow">&mdash;</span></div>
                </div>

                <form action="{{route('blockchain.donate.process')}}" method="POST" id="blockchainDonateForm" novalidate>
                    @csrf
                    <input type="hidden" name="campaign_id" value="{{$donation->id}}">
                    <input type="hidden" name="eth_amount" id="ethAmountInput" value="">

                    <div class="bf-field" id="donorIdentityFields">
                        <label class="bf-label">{{__('Your Name')}}</label>
                        <input type="text" name="donor_name" class="bf-input bf-input--plain" placeholder="{{__('Enter your name')}}" value="{{auth()->check() ? auth()->user()->name : ''}}">
                        @error('donor_name') <small class="bf-err">{{$message}}</small> @enderror

                        <label class="bf-label" style="margin-top:16px;">{{__('Email Address')}}</label>
                        <input type="email" name="donor_email" class="bf-input bf-input--plain" placeholder="{{__('your@email.com')}}" value="{{auth()->check() ? auth()->user()->email : ''}}">
                        @error('donor_email') <small class="bf-err">{{$message}}</small> @enderror
                    </div>

                    <label class="bf-anon" for="anonCheck">
                        <input type="checkbox" name="anonymous" value="1" id="anonCheck">
                        <span class="t"><i class="fas fa-user-secret" style="color:var(--bf-accent);margin-right:6px;"></i>{{__('Donate Anonymously')}}</span>
                        <span class="d">{{__('Your identity stays private on-chain')}}</span>
                    </label>

                    <div class="bf-field">
                        <label class="bf-label">{{__('Donation Amount (USD)')}}</label>
                        <div class="bf-inputwrap">
                            <span class="bf-cur">$</span>
                            <input type="number" name="amount" class="bf-input" step="1" min="1" placeholder="100" id="donationAmount" required oninput="updateUsd()">
                            <span class="bf-suffix">USD</span>
                        </div>
                        <div class="bf-conv">
                            <span class="bf-eth" id="usdPreview">&asymp; 0.000000 ETH</span>
                            <span class="bf-rate">1 ETH = $3,450</span>
                        </div>
                        @error('amount') <small class="bf-err">{{$message}}</small> @enderror
                        <div class="bf-chips">
                            <button type="button" class="bf-chip" onclick="setAmount(10)">$10</button>
                            <button type="button" class="bf-chip" onclick="setAmount(50)">$50</button>
                            <button type="button" class="bf-chip" onclick="setAmount(100)">$100</button>
                            <button type="button" class="bf-chip" onclick="setAmount(500)">$500</button>
                        </div>
                    </div>

                    <div class="bf-field">
                        <label class="bf-label">{{__('Funds Go To (Receiving Wallet)')}}</label>
                        <div class="bf-recv">
                            <i class="fas fa-hospital"></i>
                            <span>{{strtolower($donation->wallet_address ?? $demoWallet)}}</span>
                        </div>
                    </div>

                    <div class="bf-field">
                        <label class="bf-label">{{__('Your Wallet Address')}}</label>
                        <div class="bf-wallet" id="walletConnectArea" onclick="connectDonorWallet()">
                            <div id="walletConnectContent">
                                <div class="bf-wallet-ico"><i class="fas fa-wallet"></i></div>
                                <h4>{{__('Connect MetaMask')}}</h4>
                                <p>{{__('Click to connect your wallet securely')}}</p>
                                <span class="mm-tag"><svg width="14" height="13" viewBox="0 0 318 346" xmlns="http://www.w3.org/2000/svg"><polygon fill="#E2761B" points="305.2,29.6 176.4,126.8 200.4,69.6"/><polygon fill="#E4761B" points="13.6,29.6 141.2,127.6 118.4,69.6"/><polygon fill="#D7C1B3" points="265.2,235.6 229.6,290 306,311.6 328.4,236.8"/><polygon fill="#D7C1B3" points="53.2,236.8 74.8,311.6 151.2,290 116.4,235.6"/><polygon fill="#CD6116" points="155.6,168.4 133.2,202.8 212.4,199.2 188.4,163.6"/><polygon fill="#763D16" points="151.2,290 147.6,251.6 84,310"/><polygon fill="#763D16" points="230.4,310 175.6,258.8 163.6,290"/><polygon fill="#F6851B" points="151.2,290 155.6,255.6 129.2,264.4"/><polygon fill="#F6851B" points="230.4,310 230,268.8 206.8,246.4"/><polygon fill="#161616" points="130,202.4 106.8,195.6 123.2,187.6"/><polygon fill="#161616" points="151.6,202.4 174.8,195.6 158.4,187.6"/><polygon fill="#CD6116" points="53.2,236.8 66.4,172.4 41.6,173.2"/><polygon fill="#CD6116" points="215.2,172.4 228.4,236.8 240,173.2"/><polygon fill="#F6851B" points="41.6,173.2 53.2,236.8 51.6,264.4 13.6,208.4"/><polygon fill="#F6851B" points="240,173.2 251.6,236.8 250,264.4 288,208.4"/><polygon fill="#E2761B" points="129.6,268.8 114.8,250.4 129.2,246.4"/><polygon fill="#E2761B" points="152,268.8 166.8,250.4 152.4,246.4"/></svg> MetaMask</span>
                            </div>
                        </div>
                        <input type="hidden" name="wallet_address" id="walletAddressInput" value="{{$userWallet ?? ''}}">
                        <input type="hidden" name="confirmed_amount_eth" id="confirmedAmountEthInput" value="">
                        <input type="hidden" name="tx_hash_preview" id="txHashPreviewInput" value="">
                        @error('wallet_address') <small class="bf-err">{{$message}}</small> @enderror
                    </div>

                    <div class="bf-fee">
                        <i class="fas fa-info-circle"></i>
                        <div><strong>{{__('Network Fee:')}} ~0.002 ETH</strong> &bull; {{__('Demo Mode: No actual fees charged')}}</div>
                    </div>

                    <button type="submit" class="bf-submit" id="submitBtn" disabled>
                        <i class="fas fa-lock"></i>
                        {{__('Connect Wallet to Donate')}}
                    </button>
                </form>
            </div>

            <!-- RIGHT: trust info -->
            <div class="bf-card">
                <h1 style="font-size:18px;">{{__('How it works')}}</h1>
                <p class="bf-sub" style="margin-bottom:8px;">{{__('Your donation is recorded as a transparent on-chain transaction')}}.</p>

                <div class="bf-info-item">
                    <div class="bf-info-ico"><i class="fas fa-wallet"></i></div>
                    <div><h5>1. {{__('Connect your wallet')}}</h5><p>{{__('Link MetaMask and choose the amount in USD — we convert it to ETH automatically at a fixed demo rate.')}}</p></div>
                </div>
                <div class="bf-info-item">
                    <div class="bf-info-ico"><i class="fas fa-paper-plane"></i></div>
                    <div><h5>2. {{__('Confirm the transaction')}}</h5><p>{{__('MetaMask shows the exact ETH amount, gas fee and receiving address before you approve.')}}</p></div>
                </div>
                <div class="bf-info-item">
                    <div class="bf-info-ico"><i class="fas fa-cubes"></i></div>
                    <div><h5>3. {{__('On-chain receipt')}}</h5><p>{{__('You get a transaction hash anyone can verify — donations are fully traceable end-to-end.')}}</p></div>
                </div>
                <div class="bf-info-item">
                    <div class="bf-info-ico"><i class="fas fa-shield-alt"></i></div>
                    <div><h5>{{__('Fraud protected')}}</h5><p>{{__('Every campaign passes automated fraud screening before it can receive funds.')}}</p></div>
                </div>

                <div class="bf-fee" style="margin:18px 0 0;">
                    <i class="fas fa-flask"></i>
                    <div>{{__('This is a course-project simulation running on a demo network. No real funds move.')}}</div>
                </div>
            </div>
        </div>

        <div class="bf-foot">
            <i class="fas fa-shield-alt"></i> {{__('Secured by Demo Blockchain Technology')}} &bull; {{__('256-bit Encryption')}}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('assets/blockchain/ethers.min.js')}}"></script>
<script src="{{asset('assets/blockchain/medi-fund-web3.js')}}?v={{filemtime(public_path('assets/blockchain/medi-fund-web3.js'))}}"></script>
<x-metamask-sim/>
<script>
let walletConnected = false;
let connectedAccount = null;
let realWalletMode = false;   /* true = real MetaMask talking to deployed contracts */
const ETH_USD_RATE = 3450;
const CAMPAIGN_CHAIN_ID = {{$donation->id}};

function usdToEth(usd) { return usd / ETH_USD_RATE; }

function setAmount(amount) {
    document.getElementById('donationAmount').value = amount;
    document.querySelectorAll('.bf-chip').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    updateUsd();
}

function updateUsd() {
    var usd = parseFloat(document.getElementById('donationAmount').value || '0');
    var eth = usdToEth(usd);
    var el = document.getElementById('usdPreview');
    if (el) el.innerHTML = '&asymp; ' + eth.toFixed(6) + ' ETH';
    var hidden = document.getElementById('ethAmountInput');
    if (hidden) hidden.value = eth.toFixed(6);
}

function renderConnectedWallet(address, label) {
    const area = document.getElementById('walletConnectArea');
    const content = document.getElementById('walletConnectContent');
    const addressInput = document.getElementById('walletAddressInput');
    const submitBtn = document.getElementById('submitBtn');

    addressInput.value = address;
    walletConnected = true;
    connectedAccount = {address: address, label: label};
    area.classList.add('connected');
    content.innerHTML = `
        <div class="bf-wconn">
            <canvas id="walletIdenticon" width="52" height="52"></canvas>
            <div style="flex:1;">
                <h4 style="color:var(--bf-accent);">{{__('Wallet Connected')}} <i class="fas fa-check-circle"></i></h4>
                <div class="bf-waddr">
                    <span>${address}</span>
                    <button class="bf-copybtn" onclick="copyAddress(event)"><i class="fas fa-copy"></i> {{__('Copy')}}</button>
                </div>
                <p style="color:var(--bf-faint);font-size:12px;margin:0;" id="walletMetaLine">
                    ${label} &bull; {{__('Demo Ethereum Network')}}
                </p>
            </div>
        </div>
    `;
    drawIdenticon(address);
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> {{__("Donate on Blockchain")}}';
}

function drawIdenticon(address) {
    var canvas = document.getElementById('walletIdenticon');
    if (!canvas) return;
    var seed = String(address).toLowerCase().replace(/^0x/, '');
    var colors = ['#627EEA','#F6851B','#00D4AA','#E2761B','#8247E5','#0376C9','#D73847','#FFB800'];
    var hash = 0;
    for (var i = 0; i < seed.length; i++) { hash = seed.charCodeAt(i) + ((hash << 5) - hash); }
    var ctx = canvas.getContext('2d');
    ctx.fillStyle = '#12141D'; ctx.fillRect(0, 0, 52, 52);
    var cell = 52 / 5;
    for (var col = 0; col < 3; col++) {
        for (var row = 0; row < 5; row++) {
            hash = (hash * 9301 + 49297) % 233280;
            if (hash / 233280 > .48) {
                ctx.fillStyle = colors[Math.abs(hash) % colors.length];
                ctx.fillRect(col * cell, row * cell, cell + .5, cell + .5);
                ctx.fillRect((4 - col) * cell, row * cell, cell + .5, cell + .5);
            }
        }
    }
}

function connectDonorWallet() {
    if (walletConnected) return;

    @if(!empty($userWallet))
    renderConnectedWallet('{{strtolower($userWallet)}}', '{{__("Your Linked Wallet")}}');
    return;
    @endif

    /* REAL WALLET PATH: injected provider talks directly to the deployed contracts */
    if (typeof MediFundWeb3 !== 'undefined' && MediFundWeb3.hasInjectedProvider()) {
        MediFundWeb3.connectWallet().then(function(acc) {
            realWalletMode = true;
            renderConnectedWallet(acc.address, 'MetaMask &bull; {{__("Live Network")}} #' + acc.chainId);
            const meta = document.getElementById('walletMetaLine');
            if (meta) {
                window.ethereum.request({ method: 'eth_getBalance', params: [acc.address, 'latest'] })
                    .then(function(weiHex) {
                        if (typeof ethers !== 'undefined') {
                            meta.innerHTML = 'MetaMask &bull; ' + parseFloat(ethers.formatEther(weiHex)).toFixed(4) + ' ETH available';
                        }
                    }).catch(function() {});
            }
        }).catch(function(err) {
            if (err && err.message === 'NO_WALLET') { fallbackSimConnect(); }
        });
        return;
    }

    /* SIMULATED FALLBACK: no wallet installed */
    fallbackSimConnect();
}

function fallbackSimConnect() {
    MetaMaskSim.connect({
        onConnect: function(acc) {
            connectedAccount = acc;
            renderConnectedWallet(acc.address, acc.name + ' &bull; MetaMask');
            var meta = document.getElementById('walletMetaLine');
            if (meta && acc.balance !== undefined) {
                meta.innerHTML = acc.name + ' &bull; ' + parseFloat(acc.balance).toFixed(4) + ' ETH available';
            }
        },
        onCancel: function() {}
    });
}

/* ---------- live on-chain state (fraud gate + escrow) ---------- */
(async function loadChainState() {
    try {
        if (typeof MediFundWeb3 === 'undefined' || !(await MediFundWeb3.ready())) return;
        const trust = await MediFundWeb3.readCampaignTrust(CAMPAIGN_CHAIN_ID);
        if (!trust || !trust.exists) return;
        const card = document.getElementById('chainStateCard');
        card.style.display = '';

        const st = document.getElementById('csStatus');
        st.textContent = trust.statusLabel;
        st.style.color = (trust.status === 4) ? '#00D4AA' : (trust.status === 3 ? '#ff5c7a' : '#FFD166');

        const gate = document.getElementById('csGate');
        gate.textContent = trust.isDonatable ? '{{__("OPEN — donations accepted")}}' : '{{__("CLOSED — contract rejects donations")}}';
        gate.style.color = trust.isDonatable ? '#00D4AA' : '#ff5c7a';

        document.getElementById('csScoreNum').textContent = trust.fraudScore;
        document.getElementById('csScoreBar').style.width = Math.min(trust.fraudScore, 100) + '%';

        const mark = function(el, ok) {
            el.innerHTML = ok ? '<i class="fas fa-circle-check" style="color:#00D4AA"></i> {{__("Verified")}}'
                              : '<i class="fas fa-hourglass-half" style="color:#FFB800"></i> {{__("Pending")}}';
        };
        mark(document.getElementById('csHospital'), trust.hospitalVerified);
        mark(document.getElementById('csPatient'), trust.patientVerified);

        const escrow = await MediFundWeb3.escrowBalance(CAMPAIGN_CHAIN_ID);
        if (escrow !== null) {
            document.getElementById('csEscrow').textContent = parseFloat(MediFundWeb3.formatEther(escrow)).toFixed(6) + ' ETH held';
        }

        /* banner reflects the active mode */
        if (MediFundWeb3.hasInjectedProvider()) {
            const demo = document.querySelector('.bf-demo');
            if (demo) {
                demo.style.borderColor = 'rgba(0,212,170,.5)';
                demo.style.background = 'rgba(0,212,170,.06)';
                demo.innerHTML = '<i class="fas fa-link"></i> {{__("LIVE SMART CONTRACT MODE")}} — {{__('your wallet will call the deployed MediFund contracts')}}';
                demo.style.color = '#00D4AA';
            }
        }
    } catch (e) { console.warn('chain state unavailable', e); }
})();

function copyAddress(e) {
    e.stopPropagation();
    const address = document.getElementById('walletAddressInput').value;
    navigator.clipboard.writeText(address);
    const btn = e.target.closest('.bf-copybtn');
    btn.innerHTML = '<i class="fas fa-check"></i> {{__("Copied!")}}';
    setTimeout(() => {
        btn.innerHTML = '<i class="fas fa-copy"></i> {{__("Copy")}}';
    }, 2000);
}

document.getElementById('anonCheck').addEventListener('change', function() {
    document.getElementById('donorIdentityFields').style.display = this.checked ? 'none' : 'block';
});

document.getElementById('blockchainDonateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    if (!walletConnected) {
        alert('{{__("Please connect your wallet first")}}');
        return;
    }
    const form = this;
    const usd = parseFloat(document.getElementById('donationAmount').value || '0');
    const ethAmount = usdToEth(usd);
    const to = '{{strtolower($donation->wallet_address ?? $demoWallet)}}';

    if (usd <= 0) {
        alert('{{__("Please enter a donation amount")}}');
        return;
    }

    /* guard: demo balance check before opening MetaMask (amount + gas in ETH) */
    if (connectedAccount && connectedAccount.balance !== undefined && (ethAmount + 0.0021) > parseFloat(connectedAccount.balance)) {
        alert('{{__("Insufficient ETH balance in your wallet for this donation. Try a smaller amount.")}}');
        return;
    }

    /* ---------- REAL ON-CHAIN DONATION via MediFundDonation.donate() ---------- */
    if (realWalletMode) {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{__("Confirm in MetaMask...")}}';
        MediFundWeb3.donate(CAMPAIGN_CHAIN_ID, ethAmount.toFixed(12)).then(function(receipt) {
            document.getElementById('confirmedAmountEthInput').value = ethAmount.toFixed(6);
            document.getElementById('txHashPreviewInput').value = receipt.hash || '';
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{__("Recording donation...")}}';
            form.submit();
        }).catch(function(err) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane"></i> {{__("Donate on Blockchain")}}';
            const msg = (err && (err.reason || err.shortMessage || err.message)) || '{{__("Transaction failed")}}';
            alert('{{__("On-chain donation rejected:")}} ' + msg);
        });
        return;
    }

    MetaMaskSim.confirmTx({
        amount: ethAmount,
        to: to,
        onConfirm: function(hash) {
            /* payment-integrity tripwire data: what MetaMask actually confirmed */
            document.getElementById('confirmedAmountEthInput').value = ethAmount.toFixed(6);
            document.getElementById('txHashPreviewInput').value = hash || '';
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{__("Broadcasting Transaction...")}}';
            form.submit();
        },
        onReject: function() {}
    });
});

document.addEventListener('DOMContentLoaded', updateUsd);
</script>
@endsection

@extends('frontend.user.dashboard.user-master')
@section('site-title')
    {{__('Create New Campaign')}}
@endsection
@section('style')
@parent
    <x-media.css/>
    <link rel="stylesheet" href="{{asset('assets/backend/css/summernote-bs4.css')}}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/select2.min.css')}}">
    <style>
        .ncf-hero{position:relative;overflow:hidden;border-radius:20px;padding:26px 28px;margin-bottom:22px;background:linear-gradient(120deg,#1D1D3F 0%,#16162A 55%,#101028 100%);border:1px solid rgba(98,126,234,.25);box-shadow:0 10px 34px rgba(0,0,0,.4)}
        .ncf-hero::before{content:'';position:absolute;inset:0;background:radial-gradient(420px 190px at 88% 12%,rgba(98,126,234,.16),transparent 62%),radial-gradient(360px 170px at 6% 92%,rgba(0,212,170,.13),transparent 60%);pointer-events:none}
        .ncf-card{background:#14142A;border:1px solid #26264A;border-radius:20px;padding:26px 28px;margin-bottom:22px;position:relative;overflow:hidden}
        .ncf-card::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(98,126,234,.45),rgba(0,212,170,.35),transparent)}
        .ncf-head{display:flex;align-items:center;gap:14px;margin-bottom:22px}
        .ncf-head-icon{width:44px;height:44px;border-radius:13px;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0;background:linear-gradient(135deg,rgba(98,126,234,.16),rgba(0,212,170,.12));border:1px solid rgba(98,126,234,.35);color:#8FA3FF}
        .ncf-head h4{margin:0;font-size:16.5px;font-weight:800;color:#F2F2FA;letter-spacing:-.01em}
        .ncf-head p{margin:2px 0 0;font-size:12px;color:rgba(224,224,255,.45)}
        .ncf label{display:block;font-size:10.5px;font-weight:800;text-transform:uppercase;letter-spacing:.09em;color:rgba(224,224,255,.5);margin-bottom:8px}
        .ncf .form-control{background:#101024;border:1px solid #2A2A4A;border-radius:12px;color:#EAEBF7;padding:13px 16px;font-size:13.5px;height:auto;box-shadow:none;width:100%;transition:border-color .18s,box-shadow .18s;color-scheme:dark}
        .ncf .form-control::placeholder{color:rgba(200,205,235,.28)}
        .ncf .form-control:focus{outline:none;border-color:#627EEA;background:#131330;box-shadow:0 0 0 3px rgba(98,126,234,.16);color:#fff}
        .ncf textarea.form-control{min-height:110px;resize:vertical;line-height:1.65}
        .ncf select.form-control{appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='7'%3E%3Cpath d='M1 1l4.5 4L10 1' stroke='%238B93B8' stroke-width='2' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 16px center;padding-right:40px;cursor:pointer}
        .ncf-row{display:grid;grid-template-columns:1fr 1fr;gap:22px;align-items:start}
        .ncf-hint{font-size:11.5px;color:rgba(200,205,235,.38);margin-top:7px;display:flex;align-items:center;gap:6px}
        .ncf-hint i{font-size:10px}

        /* ===== AMOUNT FIELD ===== */
        .amt-box{position:relative}
        .amt-box .cur{position:absolute;left:20px;top:26px;transform:translateY(-50%);font-weight:800;color:#00D4AA;font-size:24px;pointer-events:none}
        .amt-box input{padding:22px 16px 22px 48px !important;font-weight:800 !important;font-size:26px !important;letter-spacing:.01em}
        .amt-chips{display:flex;gap:9px;flex-wrap:wrap;margin-top:13px}
        .amt-chip{background:#101024;border:1px solid #2E2E5A;border-radius:99px;color:#8FA3FF;font-size:12px;font-weight:800;padding:8px 17px;cursor:pointer;transition:all .16s}
        .amt-chip:hover{border-color:#627EEA;color:#fff;background:rgba(98,126,234,.15)}
        .amt-chip.active{background:linear-gradient(135deg,#627EEA,#00D4AA);border-color:transparent;color:#fff;box-shadow:0 4px 16px rgba(0,212,170,.3)}
        .amt-eth{margin-top:13px;display:flex;align-items:center;gap:10px;background:rgba(0,212,170,.06);border:1px solid rgba(0,212,170,.25);border-radius:12px;padding:11px 15px}
        .amt-eth i{color:#00D4AA;font-size:14px}
        .amt-eth .val{font-family:'JetBrains Mono',Consolas,monospace;color:#00D4AA;font-weight:800;font-size:14px}
        .amt-eth .lbl{color:rgba(200,205,235,.45);font-size:11.5px}

        /* ===== WALLET ===== */
        .ncf-wallet{border-radius:20px;padding:1.5px;background:linear-gradient(135deg,#627EEA,#00D4AA);box-shadow:0 10px 36px rgba(98,126,234,.16);height:100%}
        .ncf-wallet-inner{background:#111128;border-radius:19px;padding:24px 26px;height:100%;display:flex;flex-direction:column}
        .ncf-wallet .eth-chip{display:inline-flex;align-self:flex-start;align-items:center;gap:7px;background:rgba(98,126,234,.12);border:1px solid rgba(98,126,234,.3);color:#8FA3FF;font-size:10.5px;font-weight:800;letter-spacing:.07em;text-transform:uppercase;border-radius:999px;padding:4px 12px;margin-bottom:16px}
        .ncf-wallet .eth-dot{width:7px;height:7px;border-radius:50%;background:#627EEA;box-shadow:0 0 8px #627EEA}
        .ncf-wallet .form-control{font-family:'JetBrains Mono',Consolas,monospace;font-size:12.5px !important;letter-spacing:.02em}
        .wallet-ok{display:none;margin-top:12px;align-items:center;gap:9px;background:rgba(0,212,170,.08);border:1px solid rgba(0,212,170,.3);color:#00D4AA;border-radius:12px;padding:10px 15px;font-size:12.5px;font-weight:700}
        .wallet-ok.show{display:flex}
        .ncf-connect-btn{width:100%;margin-top:auto;display:flex;align-items:center;justify-content:center;gap:10px;padding:13px;border:none;border-radius:12px;font-size:13px;font-weight:800;color:#fff;cursor:pointer;background:linear-gradient(135deg,#627EEA,#4A5FD6);transition:all .2s;margin-top:14px}
        .ncf-connect-btn:hover{filter:brightness(1.1);box-shadow:0 8px 24px rgba(98,126,234,.35);transform:translateY(-1px)}

        /* uploads */
        .ncf-uploads{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px}
        .ncf-upload{background:#101024;border:2px dashed #2E2E5A;border-radius:16px;padding:22px 18px;text-align:center;transition:border-color .2s,background .2s}
        .ncf-upload:hover{border-color:rgba(98,126,234,.55);background:#121230}
        .ncf-upload .up-icon{width:46px;height:46px;margin:0 auto 12px;border-radius:13px;background:linear-gradient(135deg,rgba(98,126,234,.15),rgba(0,212,170,.1));border:1px solid rgba(98,126,234,.3);display:flex;align-items:center;justify-content:center;font-size:17px;color:#8FA3FF}
        .ncf-upload h6{font-size:13px;font-weight:800;color:#E6E7F5;margin:0 0 4px}
        .ncf-upload p{font-size:11px;color:rgba(200,205,235,.38);margin:0 0 14px}
        .ncf-upload .img-wrap img{max-width:100%;border-radius:10px;margin-bottom:10px}
        .ncf-upload .media_upload_form_btn{display:inline-flex;align-items:center;gap:8px;background:rgba(98,126,234,.14);border:1px solid rgba(98,126,234,.4);color:#AAB6FF;font-size:12px;font-weight:700;border-radius:10px;padding:9px 18px;cursor:pointer;transition:all .18s}
        .ncf-upload .media_upload_form_btn:hover{background:rgba(98,126,234,.26);color:#fff}

        /* submit */
        .ncf-submit-bar{display:flex;align-items:center;justify-content:space-between;gap:16px;background:#14142A;border:1px solid #26264A;border-radius:20px;padding:20px 26px;flex-wrap:wrap}
        .ncf-submit-bar .sb-note{font-size:12px;color:rgba(200,205,235,.45);display:flex;align-items:center;gap:9px}
        .ncf-submit-bar .sb-note i{color:#00D4AA}
        #submit{display:inline-flex;align-items:center;gap:10px;padding:14px 34px;border:none;border-radius:13px;font-size:13.5px;font-weight:800;color:#fff;cursor:pointer;background:linear-gradient(135deg,#627EEA,#00D4AA);letter-spacing:.01em;transition:all .2s}
        #submit:hover{filter:brightness(1.08);box-shadow:0 10px 30px rgba(0,212,170,.28);transform:translateY(-1px)}
        .ud-section-header h3{display:flex;align-items:center;gap:11px}

        /* steps */
        .ncf-steps{display:flex;align-items:center;flex-wrap:wrap;gap:6px;margin-top:18px;position:relative;z-index:1}
        .ncf-step{display:flex;align-items:center;flex:1;min-width:180px}
        .ncf-step .si{width:36px;height:36px;border-radius:50%;background:rgba(98,126,234,.14);border:1.5px solid rgba(98,126,234,.5);color:#8FA3FF;display:flex;align-items:center;justify-content:center;font-size:12.5px;font-weight:800;flex-shrink:0}
        .ncf-step .stx{margin-left:11px;min-width:0}
        .ncf-step .st{font-size:12.5px;font-weight:800;color:#D9DCF0;display:block}
        .ncf-step .sd{font-size:10.5px;color:rgba(200,205,235,.35);display:block}
        .ncf-step::after{content:'';flex:1;height:1.5px;background:repeating-linear-gradient(90deg,#2E2E5A 0 6px,transparent 6px 12px);margin:0 16px}
        .ncf-step:last-child::after{display:none}
        @media(max-width:768px){.ncf-row{grid-template-columns:1fr}.ncf-step::after{display:none}}
        .btn.slug_update_button{background:#00D4AA;border-color:#00D4AA;color:#06281F !important}
        .note-editor{border-radius:14px !important;border:1px solid #2A2A4A !important;background:#101024 !important;overflow:hidden}
        .note-toolbar{background:#181836 !important;border-bottom:1px solid #2A2A4A !important;border-top:none !important}
        .note-toolbar .note-btn{background:transparent;border-color:transparent;color:#B9BEDF;border-radius:8px}
        .note-editable{background:#0D0D1E !important;color:#EAEBF7 !important;font-size:14.5px;line-height:1.75;padding:18px 20px !important}
        .note-statusbar{background:#181836 !important;border-top:1px solid #2A2A4A !important}
    </style>
@endsection
@section('section')
@php $ethPerUsd = (float) medifund_usd_to_eth(1); @endphp
<div class="ncf">

    {{-- ===== HERO + STEPS ===== --}}
    <div class="ncf-hero">
        <div style="position:relative;z-index:1;display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:14px;">
            <div>
                <p style="color:#8fa3f5;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1.2px;margin:0 0 7px;"><i class="fas fa-bullhorn" style="margin-right:7px;"></i>{{__('New Fundraiser')}}</p>
                <h2 style="color:#fff;font-size:21px;font-weight:800;margin:0;">{{__('Launch Your Medical Campaign')}}</h2>
                <p style="color:rgba(224,224,224,.45);font-size:12.5px;margin:6px 0 0;">{{__('Tell your story, set a goal and receive donations straight to your MetaMask wallet')}}</p>
            </div>
            <a href="{{route('user.campaign.all')}}" class="ud-btn-ghost"><i class="fas fa-list"></i> {{__('My Campaigns')}}</a>
        </div>
        <div class="ncf-steps">
            <div class="ncf-step"><span class="si">1</span><span class="stx"><span class="st">{{__('Campaign Details')}}</span><span class="sd">{{__('Story, goal & images')}}</span></span></div>
            <div class="ncf-step"><span class="si">2</span><span class="stx"><span class="st">{{__('Connect Wallet')}}</span><span class="sd">{{__('Where donations arrive')}}</span></span></div>
            <div class="ncf-step"><span class="si"><i class="fas fa-shield-alt"></i></span><span class="stx"><span class="st">{{__('Admin Review')}}</span><span class="sd">{{__('Fraud check & approval')}}</span></span></div>
        </div>
    </div>

    @php
        $fraudReport = session('fraud_report');
        $frRiskColors = ['low' => '#00D4AA', 'medium' => '#FFB800', 'high' => '#ff5c7a'];
    @endphp
    @if($fraudReport)
    <div class="ncf-card fr-report">
        <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:18px;">
            <div class="fr-score-ring" data-score="{{ $fraudReport['score'] }}">
                <svg viewBox="0 0 80 80" width="84" height="84">
                    <circle cx="40" cy="40" r="34" fill="none" stroke="rgba(255,255,255,.07)" stroke-width="8"/>
                    <circle class="fr-ring-fill" cx="40" cy="40" r="34" fill="none"
                            stroke="{{ $frRiskColors[$fraudReport['risk_level']] ?? '#FFB800' }}" stroke-width="8"
                            stroke-linecap="round" transform="rotate(-90 40 40)"
                            stroke-dasharray="{{ number_format(2 * pi() * 34, 2) }}"
                            stroke-dashoffset="{{ number_format(2 * pi() * 34, 2) }}"/>
                </svg>
                <div class="fr-score-num">{{ $fraudReport['score'] }}<small>/100</small></div>
            </div>
            <div style="flex:1;min-width:220px;">
                <h4 style="color:#fff;font-size:16.5px;font-weight:800;margin:0 0 6px;">{{__('Fraud Screening Report')}} Ã¢â‚¬â€ <span style="color:{{ $frRiskColors[$fraudReport['risk_level']] ?? '#FFB800' }};">{{ ucfirst($fraudReport['risk_level']) }} risk</span></h4>
                <p style="color:#9AA3BF;font-size:13px;margin:0;line-height:1.6;">
                    @if($fraudReport['draft'])
                        {{__('Your campaign was saved as a draft because the automated fraud engine flagged serious issues.')}}
                    @else
                        {{__('Your campaign is queued for admin review. Failed checks below raised the risk score.')}}
                    @endif
                </p>
            </div>
        </div>
        <div class="fr-checks">
            @foreach($fraudReport['evidence'] as $check)
            <div class="fr-check {{ $check['pass'] ? 'ok' : 'fail' }}">
                <i class="fas fa-{{ $check['pass'] ? 'circle-check' : 'circle-xmark' }}"></i>
                <span class="fc-label">{{ ucwords(str_replace('_',' ', $check['check'])) }}</span>
                <span class="fc-detail">{{ $check['detail'] }}</span>
                <span class="fc-points">{{ $check['pass'] ? '+0' : '+' . $check['points'] }}</span>
            </div>
            @endforeach
        </div>
        <style>
            .fr-score-ring{position:relative;width:84px;height:84px;flex-shrink:0}
            .fr-score-num{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:19px}
            .fr-score-num small{font-size:10px;color:#9AA3BF;font-weight:700;margin-left:1px}
            .fr-checks{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:8px}
            .fr-check{display:flex;align-items:center;gap:9px;background:rgba(255,255,255,.03);border:1px solid rgba(35,42,71,.8);border-radius:10px;padding:9px 12px;font-size:12px}
            .fr-check.ok i{color:#00D4AA}.fr-check.fail i{color:#ff5c7a}
            .fr-check.fail{border-color:rgba(255,92,122,.28)}
            .fc-label{font-weight:800;color:#EAECF5;white-space:nowrap}
            .fc-detail{color:#6B7594;font-size:11px;flex:1}
            .fc-points{font-weight:800;font-size:11px;color:#ff5c7a;background:rgba(255,92,122,.1);border-radius:7px;padding:2px 7px}
        </style>
    </div>
    @endif

    <form action="{{route('user.campaign.new')}}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="ncf-body">

        {{-- ===== STORY ===== --}}
        <div class="ncf-card">
            <div class="ncf-head">
                <div class="ncf-head-icon"><i class="fas fa-feather-alt"></i></div>
                <div><h4>{{__('Campaign Story')}}</h4><p>{{__('Tell donors who you are helping and why it matters')}}</p></div>
            </div>

            <div class="form-group">
                <label for="title">{{__('Campaign Title')}}</label>
                <input type="text" class="form-control" id="title" name="title" value="{{old('title')}}" placeholder="{{__('e.g. Emergency Open-Heart Surgery for My Father')}}">
                <div id="slugPreviewWrap" style="display:none;" class="ncf-hint"><i class="fas fa-link"></i><span>{{__('Live at')}} <code id="slugPreview" style="color:#8fa3f5;"></code></span></div>
            </div>

            <div class="form-group permalink_label" style="display:none;">
                <label>{{__('Permalink')}}
                    <span class="ncf-permalink" style="display:inline-flex;margin-left:8px;vertical-align:middle;">
                        <span id="slug_show" class="url display-inline"></span>
                        <span id="slug_edit" class="display-inline">
                            <button class="btn slug_edit_button ml-1 px-2 py-1" type="button"> <i class="fas fa-pen"></i> </button>
                            <input type="text" name="slug" class="form-control blog_slug mt-0" style="display: none">
                            <button class="btn slug_update_button px-2 py-1" style="display: none" type="button">{{__('Save')}}</button>
                        </span>
                    </span>
                </label>
            </div>

            <div class="form-group">
                <label>{{__('Full Story')}}</label>
                <input type="hidden" name="cause_content">
                <div class="summernote"></div>
            </div>

            <div class="ncf-row">
                <div class="form-group">
                    <label for="patient_name">{{__('Patient Name')}}</label>
                    <input type="text" class="form-control" id="patient_name" name="patient_name" value="{{old('patient_name')}}" placeholder="{{__('Who is receiving the treatment?')}}">
                    <small class="ncf-hint"><i class="fas fa-user-injured"></i>{{__('Verified privately by the admin review team')}}</small>
                </div>
                <div class="form-group">
                    <label for="hospital_name">{{__('Treating Hospital / Clinic')}}</label>
                    <input type="text" class="form-control" id="hospital_name" name="hospital_name" value="{{old('hospital_name')}}" placeholder="{{__('e.g. Square Hospital Ltd')}}">
                    <small class="ncf-hint"><i class="fas fa-hospital"></i>{{__('Where the treatment will take place')}}</small>
                </div>
            </div>

            <div class="form-group" style="margin-bottom:0">
                <label for="excerpt">{{__('Short Summary')}}</label>
                <textarea class="form-control" name="excerpt" rows="3" placeholder="{{__('One or two sentences shown on campaign cards')}}"></textarea>
            </div>
        </div>

        {{-- ===== GOAL | WALLET ===== --}}
        <div class="ncf-row">
            <div class="ncf-card" style="margin-bottom:22px">
                <div class="ncf-head">
                    <div class="ncf-head-icon"><i class="fas fa-bullseye"></i></div>
                    <div><h4>{{__('Funding Goal')}}</h4><p>{{__('Set your target in USD Ã¢â‚¬â€ we convert to ETH automatically')}}</p></div>
                </div>
                <div class="form-group">
                    <label for="amount">{{__('Target Amount (USD)')}}</label>
                    <div class="amt-box">
                        <span class="cur">$</span>
                        <input type="number" class="form-control" id="amount" name="amount" placeholder="5,000" value="{{old('amount')}}" min="1" step="0.01">
                    </div>
                    <div class="amt-chips">
                        <button type="button" class="amt-chip" data-amt="500">$500</button>
                        <button type="button" class="amt-chip" data-amt="1000">$1,000</button>
                        <button type="button" class="amt-chip" data-amt="2500">$2,500</button>
                        <button type="button" class="amt-chip" data-amt="5000">$5,000</button>
                        <button type="button" class="amt-chip" data-amt="10000">$10,000</button>
                    </div>
                    <div class="amt-eth" id="ethPreview" style="display:none;">
                        <i class="fab fa-ethereum"></i>
                        <span class="val" id="ethValue">0 ETH</span>
                        <span class="lbl">Ã¢â€°Ë† {{__('converted at')}} 1 ETH = ${{number_format($ethPerUsd > 0 ? 1/$ethPerUsd : 0, 0)}}</span>
                    </div>
                </div>
                <div class="ncf-row">
                    <div class="form-group">
                        <label for="categories_id">{{__('Category')}}</label>
                        <select name="categories_id" class="form-control">
                            <option value="">{{__('Select Category')}}</option>
                            @foreach($all_category as $cat)
                                <option value="{{$cat->id}}">{{$cat->title}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label for="date">{{__('Deadline')}}</label>
                        <input type="date" class="form-control" name="deadline" value="{{old('deadline')}}">
                    </div>
                </div>
            </div>

            <div class="ncf-wallet" style="margin-bottom:22px">
                <div class="ncf-wallet-inner">
                    <span class="eth-chip"><span class="eth-dot"></span>Ethereum Receiving Wallet</span>
                    <div class="ncf-head" style="margin-bottom:16px;padding:0">
                        <div class="ncf-head-icon" style="background:linear-gradient(135deg,#627EEA,#8FA3FF);border:none;color:#fff"><i class="fab fa-ethereum"></i></div>
                        <div><h4>{{__('Where donations go')}}</h4><p>{{__('Verified by our admin team before launch')}}</p></div>
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label for="wallet_address">{{__('Wallet Address (ETH)')}}</label>
                        <input type="text" class="form-control" id="wallet_address" name="wallet_address" value="{{old('wallet_address', auth()->user()->wallet_address)}}" placeholder="0x71C7...">
                        <div class="wallet-ok" id="walletOkChip"><i class="fas fa-check-circle"></i> {{__('Wallet connected via MetaMask')}}</div>
                        @error('wallet_address') <small class="text-danger">{{$message}}</small> @enderror
                    </div>
                    <button type="button" class="ncf-connect-btn" onclick="connectCampaignWallet()"><i class="fab fa-meta"></i> {{__('Connect with MetaMask')}}</button>
                </div>
            </div>
        </div>

        {{-- ===== MEDIA ===== --}}
        <div class="ncf-card">
            <div class="ncf-head">
                <div class="ncf-head-icon"><i class="fas fa-images"></i></div>
                <div><h4>{{__('Media & Documents')}}</h4><p>{{__('A strong cover photo builds donor trust')}}</p></div>
            </div>
            <div class="ncf-uploads">
                <div class="ncf-upload">
                    <div class="up-icon"><i class="fas fa-camera"></i></div>
                    <h6>{{__('Cover Image')}}</h6>
                    <p>{{__('Recommended 1920x1280')}}</p>
                    <div class="media-upload-btn-wrapper">
                        <div class="img-wrap"></div>
                        <input type="hidden" name="image">
                        <button type="button" class="media_upload_form_btn" data-btntitle="{{__('Select Image')}}" data-modaltitle="{{__('Upload Image')}}" data-toggle="modal" data-target="#media_upload_modal">
                            <i class="fas fa-plus"></i> {{__('Choose Image')}}
                        </button>
                    </div>
                </div>
                <div class="ncf-upload">
                    <div class="up-icon"><i class="fas fa-layer-group"></i></div>
                    <h6>{{__('Image Gallery')}}</h6>
                    <p>{{__('Add more photos of the treatment')}}</p>
                    <div class="media-upload-btn-wrapper">
                        <div class="img-wrap"></div>
                        <input type="hidden" name="image_gallery">
                        <button type="button" class="media_upload_form_btn" data-mulitple="true" data-btntitle="{{__('Select Image')}}" data-modaltitle="{{__('Upload Image')}}" data-toggle="modal" data-target="#media_upload_modal">
                            <i class="fas fa-plus"></i> {{__('Add Photos')}}
                        </button>
                    </div>
                </div>
                <div class="ncf-upload">
                    <div class="up-icon" style="background:linear-gradient(135deg,rgba(0,212,170,.14),rgba(0,212,170,.06));border-color:rgba(0,212,170,.3);color:#00D4AA"><i class="fas fa-file-medical"></i></div>
                    <h6>{{__('Medical Documents')}}</h6>
                    <p>{{__('Reports & proofs Ã¢â‚¬â€ verified privately by admin')}}</p>
                    <div class="media-upload-btn-wrapper">
                        <div class="img-wrap"></div>
                        <input type="hidden" name="medical_document">
                        <button type="button" class="media_upload_form_btn" data-mulitple="true" data-btntitle="{{__('Select Image')}}" data-modaltitle="{{__('Upload Image')}}" data-toggle="modal" data-target="#media_upload_modal">
                            <i class="fas fa-plus"></i> {{__('Upload Documents')}}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== SUBMIT ===== --}}
        <div class="ncf-submit-bar">
            <span class="sb-note"><i class="fas fa-shield-alt"></i>{{__('Your campaign goes live after admin review & wallet verification')}}</span>
            <button id="submit" type="submit">{{__('Publish Campaign')}} <i class="fas fa-arrow-right"></i></button>
        </div>

    </div>
    </form>

    <script>
        function connectCampaignWallet(){
            if (typeof MetaMaskSim === 'undefined'){ alert('{{__("Wallet module not loaded - refresh the page.")}}'); return; }
            MetaMaskSim.connect({
                onConnect: function(acc){
                    document.getElementById('wallet_address').value = acc.address;
                    var chip = document.getElementById('walletOkChip');
                    if (chip){ chip.classList.add('show'); }
                }
            });
        }
        (function(){
            var amt = document.getElementById('amount');
            var prev = document.getElementById('ethPreview');
            var val = document.getElementById('ethValue');
            var ETH_PER_USD = {{ $ethPerUsd }};
            function fmt(n){ return n.toLocaleString(undefined,{maximumFractionDigits:6}); }
            function update(){
                var v = parseFloat(amt.value);
                document.querySelectorAll('.amt-chip').forEach(function(ch){
                    ch.classList.toggle('active', parseFloat(ch.dataset.amt) === v);
                });
                if (!isNaN(v) && v > 0 && ETH_PER_USD > 0){
                    val.textContent = fmt(v * ETH_PER_USD) + ' ETH';
                    prev.style.display = 'flex';
                } else { prev.style.display = 'none'; }
            }
            amt.addEventListener('input', update);
            document.querySelectorAll('.amt-chip').forEach(function(ch){
                ch.addEventListener('click', function(){ amt.value = ch.dataset.amt; update(); });
            });
            update();
        })();
    </script>

    <x-media.markup :userUpload="true" :imageUploadRoute="route('user.upload.media.file')"></x-media.markup>
</div>
@endsection
@section('scripts')
  <script src="{{asset('assets/backend/js/summernote-bs4.js')}}"></script>
  <script src="{{asset('assets/backend/js/bootstrap-tagsinput.js')}}"></script>
  <script src="{{asset('assets/backend/js/select2.min.js')}}"></script>
    <script>
        (function($){
            "use strict";
            $(document).ready(function () {

                function converToSlug(slug){
                    let finalSlug = slug.replace(/[^a-zA-Z0-9]/g, ' ');
                    finalSlug = slug.replace(/  +/g, ' ');
                    finalSlug = slug.replace(/\s/g, '-').toLowerCase().replace(/[^\w-]+/g, '-');
                    return finalSlug;
                }

                //Permalink Code
                $('.permalink_label').hide();
                $(document).on('keyup', '#title', function (e) {
                    var slug = converToSlug($(this).val());
                    var url = `{{url('/donation/')}}/` + slug;
                    $('.permalink_label').show();
                    $('#slug_show').text(url);
                    $('#slugPreviewWrap').show();
                    $('#slugPreview').text(url);
                    $('.blog_slug').val(slug);
                });

                //Slug Edit Code
                $(document).on('click', '.slug_edit_button', function (e) {
                    e.preventDefault();
                    $('.blog_slug').show();
                    $(this).hide();
                    $('.slug_update_button').show();
                });

                //Slug Update Code
                $(document).on('click', '.slug_update_button', function (e) {
                    e.preventDefault();
                    $(this).hide();
                    $('.slug_edit_button').show();
                    var update_input = $('.blog_slug').val();
                    var slug = converToSlug(update_input);
                    var url = `{{url('/donation/')}}/` + slug;
                    $('#slug_show').text(url);
                    $('#slugPreview').text(url);
                    $('.blog_slug').hide();
                });

                $(document).on('click','.mobile_nav',function(e){
                  e.preventDefault();
                   $(this).parent().toggleClass('show');
                });

                <x-btn.submit/>

                $('.summernote').summernote({
                    height: 320,
                    callbacks: {
                        onChange: function(contents, $editable) {
                            $(this).prev('input').val(contents);
                        }
                    }
                });
            });
        })(jQuery);
    </script>

    <x-media.js
        :deleteRoute="route('user.upload.media.file.delete')"
        :imgAltChangeRoute="route('user.upload.media.file.alt.change')"
        :allImageLoadRoute="route('user.upload.media.file.all')">
    </x-media.js>

  <script>
      $(function (){
          let data;
          data = $('.data').children();
          data[data.length-1].remove();
      });
  </script>
@endsection

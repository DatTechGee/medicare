@extends('frontend.user.dashboard.user-master')
@section('site-title')
    {{__('Create New Campaign')}}
@endsection
@section('style')
@parent
    <link rel="stylesheet" href="{{asset('assets/backend/css/summernote-bs4.css')}}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/select2.min.css')}}">
    <style>
        /* ============ MEDIFUND CREATE CAMPAIGN — v2 ============ */
        .ncv{color:#E0E0E0}
        .ncv-hero{position:relative;overflow:hidden;border-radius:20px;padding:26px 28px;margin-bottom:22px;
            background:linear-gradient(120deg,#1D1D3F 0%,#16162A 55%,#101028 100%);
            border:1px solid rgba(98,126,234,.25);box-shadow:0 10px 34px rgba(0,0,0,.4)}
        .ncv-hero::before{content:'';position:absolute;inset:0;pointer-events:none;
            background:radial-gradient(420px 190px at 88% 12%,rgba(98,126,234,.16),transparent 62%),radial-gradient(360px 170px at 6% 92%,rgba(0,212,170,.13),transparent 60%)}
        .ncv-hero-inner{position:relative;z-index:1;display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:14px}
        .ncv-hero .eyebrow{color:#8fa3f5;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1.2px;margin:0 0 7px}
        .ncv-hero h2{color:#fff;font-size:21px;font-weight:800;margin:0}
        .ncv-hero p.sub{color:rgba(224,224,224,.45);font-size:12.5px;margin:6px 0 0}

        .ncv-steps{display:flex;align-items:center;flex-wrap:wrap;gap:6px;margin-top:18px;position:relative;z-index:1}
        .ncv-step{display:flex;align-items:center;flex:1;min-width:180px}
        .ncv-step .si{width:36px;height:36px;border-radius:50%;background:rgba(98,126,234,.14);border:1.5px solid rgba(98,126,234,.5);color:#8FA3FF;display:flex;align-items:center;justify-content:center;font-size:12.5px;font-weight:800;flex-shrink:0}
        .ncv-step .stx{margin-left:11px;min-width:0}
        .ncv-step .st{font-size:12.5px;font-weight:800;color:#D9DCF0;display:block}
        .ncv-step .sd{font-size:10.5px;color:rgba(200,205,235,.35);display:block}
        .ncv-step::after{content:'';flex:1;height:1.5px;background:repeating-linear-gradient(90deg,#2E2E5A 0 6px,transparent 6px 12px);margin:0 16px}
        .ncv-step:last-child::after{display:none}

        .ncv-card{background:#14142A;border:1px solid #26264A;border-radius:18px;padding:24px 26px;margin-bottom:22px}
        .ncv-head{display:flex;align-items:center;gap:13px;margin-bottom:20px}
        .ncv-head-icon{width:42px;height:42px;border-radius:13px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;background:linear-gradient(135deg,rgba(98,126,234,.16),rgba(0,212,170,.12));border:1px solid rgba(98,126,234,.35);color:#8FA3FF}
        .ncv-head h4{margin:0;font-size:15.5px;font-weight:800;color:#F2F2FA}
        .ncv-head p{margin:2px 0 0;font-size:12px;color:rgba(224,224,255,.45)}

        .ncv .form-group{margin-bottom:18px}
        .ncv label{display:block;font-size:10.5px;font-weight:800;text-transform:uppercase;letter-spacing:.09em;color:rgba(224,224,255,.5);margin-bottom:8px}
        .ncv .form-control{background:#101024;border:1px solid #2A2A4A;border-radius:12px;color:#EAEBF7;padding:13px 16px;font-size:13.5px;height:auto;box-shadow:none;width:100%;transition:border-color .18s,box-shadow .18s;color-scheme:dark}
        .ncv .form-control::placeholder{color:rgba(200,205,235,.28)}
        .ncv .form-control:focus{outline:none;border-color:#627EEA;background:#131330;box-shadow:0 0 0 3px rgba(98,126,234,.16);color:#fff}
        .ncv textarea.form-control{min-height:110px;resize:vertical;line-height:1.65}
        .ncv select.form-control{appearance:auto;-webkit-appearance:auto;color-scheme:auto;cursor:pointer}
        .ncv-hint{font-size:11.5px;color:rgba(200,205,235,.38);margin-top:7px;display:flex;align-items:center;gap:6px}
        .ncv-hint i{font-size:10px}
        .ncv-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px}
        .ncv-grid.left{grid-template-columns:minmax(0,1fr) 360px}

        /* layout columns */
        .ncv-layout{display:grid;grid-template-columns:minmax(0,1fr) 370px;gap:22px;align-items:start}
        .ncv-main,.ncv-side{min-width:0}

        /* amount */
        .amt-box{position:relative}
        .amt-box .cur{position:absolute;left:20px;top:26px;transform:translateY(-50%);font-weight:800;color:#00D4AA;font-size:24px;pointer-events:none}
        .amt-box input{padding:22px 16px 22px 48px !important;font-weight:800 !important;font-size:26px !important;letter-spacing:.01em}
        .amt-chips{display:flex;gap:9px;flex-wrap:wrap;margin-top:13px}
        .amt-chip{background:#101024;border:1px solid #2E2E5A;border-radius:99px;color:#8FA3FF;font-size:12px;font-weight:800;padding:8px 17px;cursor:pointer;transition:all .16s}
        .amt-chip:hover{border-color:#627EEA;color:#fff;background:rgba(98,126,234,.15)}
        .amt-chip.active{background:linear-gradient(135deg,#627EEA,#00D4AA);border-color:transparent;color:#fff;box-shadow:0 4px 16px rgba(0,212,170,.3)}
        .amt-eth{margin-top:13px;display:none;align-items:center;gap:10px;background:rgba(0,212,170,.06);border:1px solid rgba(0,212,170,.25);border-radius:12px;padding:11px 15px}
        .amt-eth i{color:#00D4AA;font-size:14px}
        .amt-eth .val{font-family:'JetBrains Mono',Consolas,monospace;color:#00D4AA;font-weight:800;font-size:14px}
        .amt-eth .lbl{color:rgba(200,205,235,.45);font-size:11.5px}

        /* wallet */
        .ncv-wallet{border-radius:18px;padding:1.5px;background:linear-gradient(135deg,#627EEA,#00D4AA);box-shadow:0 10px 36px rgba(98,126,234,.16)}
        .ncv-wallet-inner{background:#111128;border-radius:16.5px;padding:22px 24px;height:100%}
        .wallet-ok{display:none;margin-top:12px;align-items:center;gap:9px;background:rgba(0,212,170,.08);border:1px solid rgba(0,212,170,.3);color:#00D4AA;border-radius:12px;padding:10px 15px;font-size:12.5px;font-weight:700}
        .wallet-ok.show{display:flex}
        .ncv-connect-btn{width:100%;display:flex;align-items:center;justify-content:center;gap:10px;padding:13px;border:none;border-radius:12px;font-size:13px;font-weight:800;color:#fff;cursor:pointer;background:linear-gradient(135deg,#627EEA,#4A5FD6);transition:all .2s;margin-top:14px}
        .ncv-connect-btn:hover{filter:brightness(1.1);box-shadow:0 8px 24px rgba(98,126,234,.35);transform:translateY(-1px)}

        /* gifts */
        .gift_toggle{display:flex;flex-direction:column;gap:8px}
        .switch{position:relative;display:inline-block;width:44px;height:24px;flex-shrink:0}
        .switch input{display:none}
        .switch .slider{position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;background:#2A2A4A;border-radius:99px;transition:.2s}
        .switch .slider::before{content:'';position:absolute;height:18px;width:18px;left:3px;top:3px;background:#fff;border-radius:50%;transition:.2s}
        .switch input:checked+.slider{background:linear-gradient(135deg,#627EEA,#00D4AA)}
        .switch input:checked+.slider::before{transform:translateX(20px)}
        .gifts:disabled{opacity:.5}

        /* uploads */
        .ncv-uploads{display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:16px}
        .ncv-upload{background:#101024;border:2px dashed #2E2E5A;border-radius:14px;padding:20px 16px;text-align:center;transition:border-color .2s,background .2s}
        .ncv-upload:hover{border-color:rgba(98,126,234,.55);background:#121230}
        .ncv-upload .up-icon{width:46px;height:46px;margin:0 auto 12px;border-radius:13px;background:linear-gradient(135deg,rgba(98,126,234,.15),rgba(0,212,170,.1));border:1px solid rgba(98,126,234,.3);display:flex;align-items:center;justify-content:center;font-size:17px;color:#8FA3FF}
        .ncv-upload h6{font-size:13px;font-weight:800;color:#E6E7F5;margin:0 0 4px}
        .ncv-upload p{font-size:11px;color:rgba(200,205,235,.38);margin:0 0 14px}
        .ncv-upload .file-label{display:inline-flex;align-items:center;gap:8px;background:rgba(98,126,234,.14);border:1px solid rgba(98,126,234,.4);color:#AAB6FF;font-size:12px;font-weight:700;border-radius:10px;padding:9px 18px;cursor:pointer;transition:all .18s}
        .ncv-upload .file-label:hover{background:rgba(98,126,234,.26);color:#fff}
        .ncv-upload input[type=file]{display:none}
        .file-name{display:block;margin-top:12px;font-size:11.5px;color:#00D4AA;font-weight:700;word-break:break-all}
        .file-preview{max-width:100%;max-height:140px;border-radius:10px;margin-top:12px;display:none}

        /* trust box */
        .ncv-trust{display:flex;flex-direction:column;gap:11px}
        .ncv-trust .t-row{display:flex;align-items:flex-start;gap:10px;font-size:12px;color:rgba(200,205,235,.55);line-height:1.5}
        .ncv-trust .t-row i{color:#00D4AA;margin-top:2px}

        /* submit */
        .ncv-submit-bar{display:flex;align-items:center;justify-content:space-between;gap:16px;background:#14142A;border:1px solid #26264A;border-radius:18px;padding:20px 26px;flex-wrap:wrap}
        .ncv-submit-bar .sb-note{font-size:12px;color:rgba(200,205,235,.45);display:flex;align-items:center;gap:9px}
        .ncv-submit-bar .sb-note i{color:#00D4AA}
        #submit{display:inline-flex;align-items:center;gap:10px;padding:14px 34px;border:none;border-radius:13px;font-size:13.5px;font-weight:800;color:#fff;cursor:pointer;background:linear-gradient(135deg,#627EEA,#00D4AA);letter-spacing:.01em;transition:all .2s}
        #submit:hover{filter:brightness(1.08);box-shadow:0 10px 30px rgba(0,212,170,.28);transform:translateY(-1px)}

        /* summernote seat */
        .note-editor{border-radius:14px !important;border:1px solid #2A2A4A !important;background:#101024 !important;overflow:hidden}
        .note-toolbar{background:#181836 !important;border-bottom:1px solid #2A2A4A !important;border-top:none !important}
        .note-toolbar .note-btn{background:transparent;border-color:transparent;color:#B9BEDF;border-radius:8px}
        .note-editable{background:#0D0D1E !important;color:#EAEBF7 !important;font-size:14.5px;line-height:1.75;padding:18px 20px !important}
        .note-statusbar{background:#181836 !important;border-top:1px solid #2A2A4A !important}

        /* select2 — dropdown is appended to <body>, so it needs its own dark skin */
        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--multiple{background:#101024!important;border:1px solid #2A2A4A!important;border-radius:12px!important;height:auto!important;min-height:48px!important}
        .select2-container--default .select2-selection--single .select2-selection__rendered{color:#EAEBF7!important;padding:13px 16px!important;line-height:1.4!important}
        .select2-container--default .select2-selection--single .select2-selection__arrow{height:48px!important;right:10px!important}
        .select2-container--default .select2-selection--single .select2-selection__placeholder{color:rgba(200,205,235,.4)!important}
        .select2-container--default .select2-selection--multiple .select2-selection__rendered{padding:7px 12px!important;line-height:1.4!important}
        .select2-container--default .select2-selection--multiple .select2-selection__choice{background:rgba(98,126,234,.2)!important;border:1px solid #627EEA!important;color:#E0E0E0!important;border-radius:8px!important;padding:4px 9px!important;font-size:12px}
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove{color:#8FA3FF!important;border-right:1px solid rgba(98,126,234,.4)!important;margin-right:7px!important}
        .select2-dropdown{background:#16162A!important;border-color:#2A2A4A!important}
        .select2-container--default .select2-search--dropdown .select2-search__field{background:#101024!important;border-color:#2A2A4A!important;color:#E0E0E0!important}
        .select2-container--default .select2-results__option{color:#E0E0E0!important;font-size:13px;padding:10px 12px}
        .select2-container--default .select2-results__option[aria-selected=true]{background:rgba(98,126,234,.35)!important}
        .select2-container--default .select2-results__option--highlighted{background:#627EEA!important}

        .btn.slug_update_button{background:#00D4AA;border-color:#00D4AA;color:#06281F !important}
        @media(max-width:1100px){.ncv-layout{grid-template-columns:1fr}.ncv-grid.left{grid-template-columns:1fr}}
        @media(max-width:768px){.ncv-grid{grid-template-columns:1fr}.ncv-step::after{display:none}}
    </style>
@endsection
@section('section')
@php $ethPerUsd = (float) medifund_usd_to_eth(1); @endphp
<div class="ncv">

    {{-- ===== HERO + STEPS ===== --}}
    <div class="ncv-hero">
        <div class="ncv-hero-inner">
            <div>
                <p class="eyebrow"><i class="fas fa-bullhorn" style="margin-right:7px;"></i>{{__('New Fundraiser')}}</p>
                <h2>{{__('Launch Your Medical Campaign')}}</h2>
                <p class="sub">{{__('Tell your story, set a goal and receive donations straight to your MetaMask wallet')}}</p>
            </div>
            <a href="{{route('user.campaign.all')}}" class="ud-btn-ghost"><i class="fas fa-list"></i> {{__('My Campaigns')}}</a>
        </div>
        <div class="ncv-steps">
            <div class="ncv-step"><span class="si">1</span><span class="stx"><span class="st">{{__('Campaign Details')}}</span><span class="sd">{{__('Story, goal & images')}}</span></span></div>
            <div class="ncv-step"><span class="si">2</span><span class="stx"><span class="st">{{__('Connect Wallet')}}</span><span class="sd">{{__('Where donations arrive')}}</span></span></div>
            <div class="ncv-step"><span class="si"><i class="fas fa-shield-alt"></i></span><span class="stx"><span class="st">{{__('Admin Review')}}</span><span class="sd">{{__('Fraud check & approval')}}</span></span></div>
        </div>
    </div>

    @php
        $fraudReport = session('fraud_report');
        $frRiskColors = ['low' => '#00D4AA', 'medium' => '#FFB800', 'high' => '#ff5c7a'];
    @endphp
    @if($fraudReport)
    <div class="ncv-card fr-report">
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
                <h4 style="color:#fff;font-size:16.5px;font-weight:800;margin:0 0 6px;">{{__('Fraud Screening Report')}} — <span style="color:{{ $frRiskColors[$fraudReport['risk_level']] ?? '#FFB800' }};">{{ ucfirst($fraudReport['risk_level']) }} risk</span></h4>
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
    <div class="ncv-layout">

        {{-- ============ MAIN COLUMN ============ --}}
        <div class="ncv-main">

            {{-- STORY --}}
            <div class="ncv-card">
                <div class="ncv-head">
                    <div class="ncv-head-icon"><i class="fas fa-feather-alt"></i></div>
                    <div><h4>{{__('Campaign Story')}}</h4><p>{{__('Tell donors who you are helping and why it matters')}}</p></div>
                </div>

                <div class="form-group">
                    <label for="title">{{__('Campaign Title')}}</label>
                    <input type="text" class="form-control" id="title" name="title" value="{{old('title')}}" placeholder="{{__('e.g. Emergency Open-Heart Surgery for My Father')}}">
                    <div id="slugPreviewWrap" style="display:none;" class="ncv-hint"><i class="fas fa-link"></i><span>{{__('Live at')}} <code id="slugPreview" style="color:#8fa3f5;"></code></span></div>
                </div>

                <div class="form-group permalink_label" style="display:none;">
                    <label>{{__('Permalink')}}
                        <span style="display:inline-flex;margin-left:8px;vertical-align:middle;">
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

                <div class="ncv-grid">
                    <div class="form-group">
                        <label for="patient_name">{{__('Patient Name')}}</label>
                        <input type="text" class="form-control" id="patient_name" name="patient_name" value="{{old('patient_name')}}" placeholder="{{__('Who is receiving the treatment?')}}">
                        <small class="ncv-hint"><i class="fas fa-user-injured"></i>{{__('Verified privately by the admin review team')}}</small>
                    </div>
                    <div class="form-group">
                        <label for="hospital_name">{{__('Treating Hospital / Clinic')}}</label>
                        <input type="text" class="form-control" id="hospital_name" name="hospital_name" value="{{old('hospital_name')}}" placeholder="{{__('e.g. Square Hospital Ltd')}}">
                        <small class="ncv-hint"><i class="fas fa-hospital"></i>{{__('Where the treatment will take place')}}</small>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:0">
                    <label for="excerpt">{{__('Short Summary')}}</label>
                    <textarea class="form-control" name="excerpt" rows="3" placeholder="{{__('One or two sentences shown on campaign cards')}}"></textarea>
                </div>
            </div>

            {{-- GOAL & CATEGORY --}}
            <div class="ncv-card">
                <div class="ncv-head">
                    <div class="ncv-head-icon"><i class="fas fa-bullseye"></i></div>
                    <div><h4>{{__('Funding Goal & Category')}}</h4><p>{{__('Set your target — we convert to ETH automatically')}}</p></div>
                </div>

                <div class="ncv-grid left">
                    <div class="form-group" style="margin-bottom:0">
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
                        <div class="amt-eth" id="ethPreview">
                            <i class="fab fa-ethereum"></i>
                            <span class="val" id="ethValue">0 ETH</span>
                            <span class="lbl">≈ {{__('converted at')}} 1 ETH = ${{number_format($ethPerUsd > 0 ? 1/$ethPerUsd : 0, 0)}}</span>
                        </div>
                    </div>
                    <div class="ncv-grid">
                        <div class="form-group">
                            <label for="categories_id">{{__('Category')}}</label>
                            <select name="categories_id" class="form-control ncv-select">
                                <option value="">{{__('Select Category')}}</option>
                                @foreach($all_category as $cat)
                                    <option value="{{$cat->id}}" {{ old('categories_id') == $cat->id ? 'selected' : '' }}>{{$cat->title}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom:0">
                            <label for="date">{{__('Deadline')}}</label>
                            <input type="date" class="form-control" name="deadline" value="{{old('deadline')}}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- MEDIA --}}
            <div class="ncv-card">
                <div class="ncv-head">
                    <div class="ncv-head-icon"><i class="fas fa-images"></i></div>
                    <div><h4>{{__('Media & Documents')}}</h4><p>{{__('A strong cover photo builds donor trust')}}</p></div>
                </div>
                <div class="ncv-uploads">
                    <div class="ncv-upload">
                        <div class="up-icon"><i class="fas fa-camera"></i></div>
                        <h6>{{__('Cover Image')}}</h6>
                        <p>{{__('Recommended 1920x1280')}}</p>
                        <label class="file-label"><i class="fas fa-folder-open"></i> {{__('Choose File')}}
                            <input type="file" name="cover_image" accept="image/*" onchange="previewFile(this)">
                        </label>
                        <span class="file-name"></span>
                        <img class="file-preview" alt="">
                    </div>
                    <div class="ncv-upload">
                        <div class="up-icon"><i class="fas fa-layer-group"></i></div>
                        <h6>{{__('Image Gallery')}}</h6>
                        <p>{{__('Add more photos of the treatment')}}</p>
                        <label class="file-label"><i class="fas fa-folder-open"></i> {{__('Choose Files')}}
                            <input type="file" name="gallery_images[]" accept="image/*" multiple onchange="previewFiles(this)">
                        </label>
                        <span class="file-name"></span>
                    </div>
                    <div class="ncv-upload">
                        <div class="up-icon" style="background:linear-gradient(135deg,rgba(0,212,170,.14),rgba(0,212,170,.06));border-color:rgba(0,212,170,.3);color:#00D4AA"><i class="fas fa-file-medical"></i></div>
                        <h6>{{__('Medical Documents')}}</h6>
                        <p>{{__('Reports & proofs — verified privately by admin (images or PDF)')}}</p>
                        <label class="file-label" style="border-color:rgba(0,212,170,.4);color:#7EEFDB"><i class="fas fa-folder-open"></i> {{__('Choose Files')}}
                            <input type="file" name="document_files[]" accept="image/*,.pdf,.doc,.docx" multiple onchange="previewFiles(this)">
                        </label>
                        <span class="file-name"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ SIDEBAR ============ --}}
        <div class="ncv-side">

            {{-- WALLET --}}
            <div class="ncv-wallet" style="margin-bottom:22px">
                <div class="ncv-wallet-inner">
                    <div class="ncv-head" style="margin-bottom:16px;padding:0">
                        <div class="ncv-head-icon" style="background:linear-gradient(135deg,#627EEA,#8FA3FF);border:none;color:#fff"><i class="fab fa-ethereum"></i></div>
                        <div><h4>{{__('Where donations go')}}</h4><p>{{__('Verified by our admin team before launch')}}</p></div>
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label for="wallet_address">{{__('Wallet Address (ETH)')}}</label>
                        <input type="text" class="form-control" id="wallet_address" name="wallet_address" value="{{old('wallet_address', auth()->user()->wallet_address)}}" placeholder="0x71C7...">
                        <div class="wallet-ok" id="walletOkChip"><i class="fas fa-check-circle"></i> {{__('Wallet connected via MetaMask')}}</div>
                        @error('wallet_address') <small class="text-danger">{{$message}}</small> @enderror
                    </div>
                    <button type="button" class="ncv-connect-btn" onclick="connectCampaignWallet()"><i class="fab fa-meta"></i> {{__('Connect with MetaMask')}}</button>
                </div>
            </div>

            {{-- GIFTS --}}
            <div class="ncv-card">
                <div class="ncv-head">
                    <div class="ncv-head-icon"><i class="fas fa-gift"></i></div>
                    <div><h4>{{__('Gifts & Rewards')}}</h4><p>{{__('Optional perks for donors')}}</p></div>
                </div>
                <div class="gift_toggle">
                    <label class="switch" style="margin:0">
                        <input type="checkbox" name="gift_status" class="add_gift_status" value="on">
                        <span class="slider"></span>
                    </label>
                    <span style="font-size:12px;color:rgba(200,205,235,.5);">{{__('Enable gifts for this campaign')}}</span>
                </div>
                <div class="form-group gift_select_wrapper" style="margin:16px 0 0">
                    <label>{{__('Select Gift')}}</label>
                    <select name="gifts[]" class="form-control gifts ncv-select" multiple>
                        @foreach($all_gifts as $gift)
                            <option value="{{$gift->id}}">{{$gift->title}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- TRUST --}}
            <div class="ncv-card">
                <div class="ncv-head">
                    <div class="ncv-head-icon" style="background:linear-gradient(135deg,rgba(0,212,170,.16),rgba(0,212,170,.06));border-color:rgba(0,212,170,.3);color:#00D4AA"><i class="fas fa-shield-halved"></i></div>
                    <div><h4>{{__('Trust & Safety')}}</h4></div>
                </div>
                <div class="ncv-trust">
                    <div class="t-row"><i class="fas fa-user-check"></i><span>{{__('Identity and hospital records are verified privately by our admin team')}}</span></div>
                    <div class="t-row"><i class="fas fa-robot"></i><span>{{__('A fraud engine screens every campaign before it goes public')}}</span></div>
                    <div class="t-row"><i class="fas fa-hourglass-half"></i><span>{{__('Most campaigns are reviewed within 24 hours')}}</span></div>
                </div>
            </div>
        </div>
    </div>

    {{-- SUBMIT --}}
    <div class="ncv-submit-bar">
        <span class="sb-note"><i class="fas fa-shield-alt"></i>{{__('Your campaign goes live after admin review & wallet verification')}}</span>
        <button id="submit" type="submit">{{__('Publish Campaign')}} <i class="fas fa-arrow-right"></i></button>
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
        function previewFile(input){
            var wrap = input.closest('.ncv-upload');
            var name = wrap.querySelector('.file-name');
            var img  = wrap.querySelector('.file-preview');
            if (input.files && input.files[0]){
                name.textContent = input.files[0].name;
                if (img){
                    var r = new FileReader();
                    r.onload = function(e){ img.src = e.target.result; img.style.display = 'inline-block'; };
                    if (input.files[0].type.indexOf('image') === 0){ r.readAsDataURL(input.files[0]); }
                    else { img.style.display = 'none'; }
                }
            }
        }
        function previewFiles(input){
            var wrap = input.closest('.ncv-upload');
            var name = wrap.querySelector('.file-name');
            if (input.files && input.files.length){
                var names = [];
                for (var i = 0; i < input.files.length; i++){ names.push(input.files[i].name); }
                name.textContent = names.join(', ');
            }
        }
    </script>
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

                //main2.js applies niceSelect to every <select> on the site; strip those
                //wrappers and re-show our controls so they render exactly once.
                $('select.ncv-select').each(function () {
                    $(this).siblings('.nice-select').remove();
                    $(this).css('display', '');
                });

                //Category dropdown — select2 (reliable, custom-styled alternative to the native popup)
                $('select[name="categories_id"]').select2({
                    placeholder: '{{__("Select Category")}}',
                    allowClear: true,
                    width: '100%',
                    minimumResultsForSearch: Infinity
                });

                //Gifts dropdown
                $('.gifts').select2({
                    placeholder: '{{__("Select gifts (optional)")}}',
                    width: '100%'
                });
                var giftEnabled = $('.add_gift_status').is(':checked');
                if (!giftEnabled){ $('.gifts').prop('disabled', true); }
                $(document).on('change', '.add_gift_status', function(){
                    $('.gifts').prop('disabled', !$(this).is(':checked'));
                });

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

  <script>
      $(function (){
          let data;
          data = $('.data').children();
          data[data.length-1].remove();
      });
  </script>
@endsection
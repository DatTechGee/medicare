@extends('frontend.frontend-page-master')
@php
  $post_img = null;
  $blog_image = get_attachment_image_by_id($donation->image,"full",false);
  $post_img = !empty($blog_image) ? $blog_image['img_url'] : '';
 @endphp
@section('og-meta')

@endsection

@section('site-title')
    {{$donation->title}}
@endsection

@section('page-title')
    {{$donation->title}}
@endsection
@section('page-meta-data')
     <meta property="og:type" content="website">
    <meta property="og:title" content="{{$donation->title}}">
    <meta property="og:description" content="{{strip_tags(\Str::words($donation->cause_content,150))}}">
    <meta property="og:image:width" content="600" />
    <meta property="og:image:height" content="315" />
    <meta property="og:image" content="{{$post_img}}"/>
	<meta property="og:image:secure" content="{{$post_img}}">
    
    <meta property="og:url" content="{{route('frontend.donations.single',$donation->slug)}}">
    <meta property="twitter:card" content="summary_large_image">


   <meta property="title" content="{{$donation->title}}">
    <meta property="description" content="{{$donation->meta_tags}}">
    <meta property="tags" content="{{$donation->meta_description}}">
@endsection

@section('style')
<style>
/* ============================================================
   MediFund â€” Campaign Details (dark design system, ds- tokens)
   ============================================================ */
body{background:#0B0E1A !important}
.donation-single-content-area{
    --ds-primary:#627EEA;--ds-accent:#00D4AA;--ds-danger:#ff5c7a;--ds-warn:#FFB800;
    --ds-bg:#0B0E1A;--ds-card:#12162A;--ds-card2:#181D33;--ds-line:#232A47;
    --ds-text:#EAECF5;--ds-muted:#9AA3BF;--ds-faint:#6B7594;
    background:
        radial-gradient(1000px 520px at 12% -8%, rgba(98,126,234,.14), transparent 60%),
        radial-gradient(900px 480px at 92% 108%, rgba(0,212,170,.10), transparent 60%),
        var(--ds-bg) !important;
    color:var(--ds-text);
    padding-top:48px !important;padding-bottom:80px !important;
    font-family:'Inter','Segoe UI',system-ui,-apple-system,sans-serif;
}
.donation-single-content-area .container{max-width:1140px}

/* ---------- hero ---------- */
.mf-donate-hero{
    display:flex;align-items:center;justify-content:space-between;gap:28px;flex-wrap:wrap;
    background:linear-gradient(135deg,#151A32 0%,#101426 100%) !important;
    border:1px solid var(--ds-line) !important;border-radius:20px !important;
    padding:26px 30px !important;margin-bottom:26px;position:relative;overflow:hidden;
    box-shadow:0 18px 50px rgba(0,0,0,.35);
}
.mf-donate-hero::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(98,126,234,.5),rgba(0,212,170,.4),transparent)}
.mf-dh-left .raised{color:#fff !important;font-size:27px !important;font-weight:800 !important;margin:0 0 4px !important;letter-spacing:-.02em}
.mf-dh-left .raised .goal{color:var(--ds-muted) !important;font-size:13px !important;font-weight:600 !important}
.mf-dh-crypto{margin:2px 0 14px;font-size:12.5px;color:var(--ds-accent);font-weight:700;display:flex;align-items:center;gap:6px}
.mf-dh-crypto span{color:var(--ds-faint);font-weight:500}
.mf-dh-progress{height:9px !important;border-radius:99px !important;background:rgba(255,255,255,.07) !important;overflow:hidden;margin-top:0 !important}
.mf-dh-progress .progress-bar{background:linear-gradient(90deg,var(--ds-primary),var(--ds-accent)) !important;border-radius:99px !important;transition:width .8s cubic-bezier(.25,1,.5,1)}
.mf-dh-pct{display:block;text-align:right;margin-top:8px;color:var(--ds-accent);font-size:12px;font-weight:700}
.mf-btn-primary{
    display:flex;align-items:center;justify-content:center;gap:9px;
    background:linear-gradient(135deg,#627EEA,#00D4AA);color:#fff !important;
    border-radius:13px;padding:15px 30px;font-weight:800;font-size:15px;letter-spacing:.01em;
    text-decoration:none !important;transition:all .2s;box-shadow:0 10px 26px rgba(75,108,235,.38);border:none;
}
.mf-btn-primary:hover{transform:translateY(-2px);box-shadow:0 14px 34px rgba(75,108,235,.5);color:#fff}
.mf-btn-ghost{
    display:flex;align-items:center;justify-content:center;gap:9px;
    background:rgba(255,255,255,.05);color:#E4E7F5 !important;
    border:1px solid rgba(255,255,255,.13);border-radius:13px;padding:13px 30px;
    font-weight:700;font-size:14px;text-decoration:none !important;transition:all .2s;
}
.mf-btn-ghost:hover{background:rgba(255,255,255,.1);color:#fff;border-color:rgba(255,255,255,.22)}

/* ---------- main card shell ---------- */
.contribute-single-page-item{
    background:linear-gradient(180deg,#131830,#101426) !important;
    border-radius:22px !important;border:1px solid var(--ds-line) !important;
    padding:28px !important;color:#C9CFE4 !important;position:relative;
    box-shadow:0 18px 50px rgba(0,0,0,.3);
}
#mobile_btn a{display:none}

/* ---------- gallery ---------- */
.donation-image-gallery .single-gallery-image,
.thumb.single-featured{border-radius:18px;overflow:hidden;border:1px solid var(--ds-line);background:#0E1222;position:relative}
.donation-image-gallery img,.thumb.single-featured img{width:100%;display:block;object-fit:cover;max-height:480px}
.donation-image-gallery .owl-dots .owl-dot span{background:rgba(255,255,255,.18) !important;width:8px;height:8px;border-radius:99px;transition:all .2s}
.donation-image-gallery .owl-dots .owl-dot.active span{background:var(--ds-accent) !important;width:22px}
.donation-image-gallery .owl-nav{display:none}
.flag-icon,.gift-icon-two{background:rgba(11,14,26,.72) !important;backdrop-filter:blur(6px);border:1px solid rgba(255,255,255,.12);border-radius:10px !important;color:#C9CFE4 !important}
.flag-icon:hover{color:var(--ds-danger) !important;border-color:rgba(255,92,122,.4)}
.award-icon-two{background:linear-gradient(135deg,var(--ds-warn),#ff9f43) !important;color:#241a00 !important;border-radius:10px !important}

/* ---------- author box ---------- */
.post-meta-wrap{margin-top:22px}
.author-data.author-data-new{display:flex;align-items:center;gap:16px;flex-wrap:wrap;background:var(--ds-card2);border:1px solid var(--ds-line);border-radius:16px;padding:16px 18px}
.medical-documents{display:flex;align-items:center;gap:16px;flex-wrap:wrap;flex:1}
.medical-documents .thumb{width:52px;height:52px;border-radius:50%;overflow:hidden;border:2px solid rgba(98,126,234,.45);flex-shrink:0;background:#0E1222}
.medical-documents .thumb img{width:100%;height:100%;object-fit:cover}
.medical-documents .auth-details{flex:1;min-width:200px}
.medical-documents .auth-details ul{list-style:none;margin:0;padding:0}
.auth-details .name{color:#fff !important;font-size:15.5px;font-weight:800;margin:0}
.auth-details .user_verified_check_icon{color:var(--ds-accent) !important;margin-left:4px}
.auth-details ul li{color:var(--ds-muted) !important;font-size:12.5px;display:inline-flex;align-items:center;gap:6px;margin-right:18px}
.auth-details ul li i{color:var(--ds-faint)}
.auth-details ul li a{color:var(--ds-accent) !important;text-decoration:none;font-weight:600}
.follow_btn{background:rgba(98,126,234,.15) !important;border:1px solid rgba(98,126,234,.4) !important;color:#AAB6F8 !important;font-weight:700;font-size:12px;border-radius:99px !important;padding:6px 16px !important;cursor:pointer;transition:all .15s}
.follow_btn:hover{background:rgba(98,126,234,.28) !important;color:#fff !important}
.medical-document-btn .boxed-btn.btn-color-three{
    display:inline-flex;align-items:center;gap:8px;background:rgba(0,212,170,.1) !important;
    border:1px solid rgba(0,212,170,.35) !important;color:var(--ds-accent) !important;
    border-radius:11px !important;padding:11px 22px;font-weight:700;font-size:13px;text-decoration:none !important;transition:all .15s;
}
.medical-document-btn .boxed-btn.btn-color-three:hover{background:rgba(0,212,170,.2) !important}

/* ---------- tabs ---------- */
.tab-area-new{margin-top:26px}
.author-data-tab{background:transparent;border:none;padding:0}
.author-data-tab ul.tabs{display:flex;gap:8px;list-style:none;margin:0 0 -1px;padding:0;flex-wrap:wrap;border-bottom:1px solid var(--ds-line);position:relative;z-index:1}
.author-data-tab ul.tabs li{
    background:transparent;border:1px solid transparent;border-bottom:none;color:var(--ds-muted) !important;
    font-size:13.5px;font-weight:700;padding:12px 22px;border-radius:12px 12px 0 0;cursor:pointer;transition:all .15s;position:relative;
}
.author-data-tab ul.tabs li:hover{color:#fff !important;background:rgba(255,255,255,.03)}
.author-data-tab ul.tabs li.active{background:var(--ds-card2);border-color:var(--ds-line);color:#fff !important}
.author-data-tab ul.tabs li.active::after{content:'';position:absolute;left:20%;right:20%;bottom:-1px;height:2px;background:linear-gradient(90deg,var(--ds-primary),var(--ds-accent));border-radius:2px}
.tab-content{background:var(--ds-card2);border:1px solid var(--ds-line);border-radius:0 16px 16px 16px;padding:26px 28px}
.shotcontent-wrapper,.single-tabs{color:#C9CFE4}
#main-data{font-size:14.5px;line-height:1.85;color:#C4CBDF}
#main-data h1,#main-data h2,#main-data h3,#main-data h4,#main-data h5{color:#fff;margin:1.2em 0 .5em;font-weight:800}
#main-data p{margin:0 0 1em}
#main-data img{max-width:100%;border-radius:14px;border:1px solid var(--ds-line);margin:10px 0}
#main-data ul,#main-data ol{padding-left:22px;margin:0 0 1em}
#main-data table{width:100%;border-collapse:collapse}#main-data td,#main-data th{border:1px solid var(--ds-line);padding:8px 12px}
#ReadMoreButton{color:var(--ds-primary);font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;margin-top:6px}
#ReadMoreButton:hover{color:#8FA3FF}

/* ---------- FAQ accordion ---------- */
.accordion-wrapper h2.title,.accordion-wrapper .title{color:#fff !important;font-size:17px;font-weight:800;margin-bottom:16px}
.accordion-wrapper .card{background:#0F1426;border:1px solid var(--ds-line);border-radius:13px;margin-bottom:11px;overflow:hidden}
.accordion-wrapper .card-header{background:transparent;border:none;padding:0}
.accordion-wrapper .card-header h5{margin:0}
.accordion-wrapper .card-header a,.accordion-wrapper .card-header button{
    display:flex;width:100%;align-items:center;justify-content:space-between;gap:12px;
    color:#E4E7F5 !important;font-size:14.5px;font-weight:700;padding:17px 20px;text-decoration:none !important;background:transparent;border:none;text-align:left;
}
.accordion-wrapper .card-header a::after{content:'\f078';font-family:'Font Awesome 5 Free';font-weight:900;font-size:11px;color:var(--ds-faint);transition:transform .2s}
.accordion-wrapper .card-header a:not(.collapsed)::after{transform:rotate(180deg);color:var(--ds-accent)}
.accordion-wrapper .card-body{border-top:1px solid var(--ds-line);padding:16px 20px;font-size:13.5px;line-height:1.75;color:var(--ds-muted)}

/* ---------- updates & comments ---------- */
.cause-update-section h3.title,.cause-comment-section h3{
    color:#fff !important;font-size:16.5px;font-weight:800;display:flex;align-items:center;gap:9px;margin:0 0 16px;
}
.case-update-item{background:#0F1426 !important;border:1px solid var(--ds-line) !important;border-radius:14px !important;padding:18px 20px !important;margin-bottom:13px !important}
.case-update-item .title{color:#fff !important;font-size:14.5px;font-weight:800;display:flex;align-items:center;gap:8px}
.case-update-item p{color:var(--ds-muted) !important;font-size:13.5px;line-height:1.7;margin:8px 0 0}
.load_more_case_update_wrapper,.btn-wrapper{text-align:left}
.load-more-btn,#load_more_case_update_button{
    display:inline-flex;align-items:center;gap:8px;background:rgba(98,126,234,.12) !important;
    border:1px solid rgba(98,126,234,.35) !important;color:#AAB6F8 !important;font-size:12.5px;font-weight:700;
    border-radius:99px;padding:9px 20px;cursor:pointer;transition:all .15s;
}
.load-more-btn:hover,#load_more_case_update_button:hover{background:rgba(98,126,234,.24) !important;color:#fff !important}
.donor-load-box{background:transparent !important;border:none !important}
.cause-comment-section .panel{background:#0F1426;border:1px solid var(--ds-line);border-radius:14px;margin-bottom:12px;overflow:hidden}
.cause-comment-section .panel-body{padding:16px 18px}
.single-comment-body{background:#0F1426 !important;border:1px solid var(--ds-line) !important;border-radius:14px !important;padding:16px 18px !important;margin-bottom:12px}
.comment-author-meta h5 a{color:#fff !important;font-size:14px;font-weight:700;text-decoration:none}
.comment-body p{color:var(--ds-muted) !important;font-size:13.5px;line-height:1.7;margin:6px 0 0}
.comment-date{color:var(--ds-faint) !important;font-size:11.5px}
.reply-btn{color:var(--ds-primary) !important;font-weight:700;font-size:12.5px;text-decoration:none}
#comment_content,.cause-comment-section textarea.form-control{
    background:#0E1226 !important;border:1px solid var(--ds-line) !important;border-radius:12px !important;
    color:#E4E7F5 !important;padding:13px 16px;font-size:13.5px;box-shadow:none !important;
}
#comment_content:focus{border-color:var(--ds-primary) !important;box-shadow:0 0 0 3px rgba(98,126,234,.15) !important}
#submitComment{
    display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#627EEA,#4B6CEB) !important;
    border:none !important;border-radius:11px !important;padding:11px 26px !important;color:#fff !important;
    font-weight:800;font-size:13px;cursor:pointer;transition:all .15s;box-shadow:0 6px 18px rgba(75,108,235,.3);
}
#submitComment:hover{filter:brightness(1.1)}

/* ---------- related campaigns ---------- */
.related-post-area{margin-top:44px}
.related-post-area .section-title .title{color:#fff !important;font-size:19px;font-weight:800;letter-spacing:-.01em;position:relative;padding-bottom:12px;margin-bottom:6px}
.related-post-area .section-title .title::after{content:'';position:absolute;left:0;bottom:0;width:46px;height:3px;border-radius:3px;background:linear-gradient(90deg,var(--ds-primary),var(--ds-accent))}
.related-news-carousel .owl-dots .owl-dot span{background:rgba(255,255,255,.18) !important}
.related-news-carousel .owl-dots .owl-dot.active span{background:var(--ds-accent) !important}

/* ---------- alerts / badges / misc ---------- */
.alert-danger{background:rgba(255,92,122,.08) !important;border:1px solid rgba(255,92,122,.3) !important;border-radius:13px !important;color:#ff8ba3 !important}
.alert-warning{background:rgba(255,184,0,.07) !important;border:1px solid rgba(255,184,0,.25) !important;border-radius:13px !important;color:#ffd166 !important}
.contribute-alert{background:transparent !important;color:#ff8ba3 !important;font-weight:700}
.counterdown-wrap{background:#0F1426 !important;border:1px solid var(--ds-line) !important;border-radius:14px !important}
.counterdown-wrap .countdown__item span{color:var(--ds-primary) !important}
.social-share-wrap,.social-texarea-share,.embed_area{background:#0F1426 !important;border:1px solid var(--ds-line) !important;border-radius:14px !important}
.social-texarea-share label{color:var(--ds-muted) !important}
.social-texarea-share .form-control,.social-share-wrap .form-group .form-control{background:#0E1226 !important;color:#E4E7F5 !important;border:1px solid var(--ds-line) !important;border-radius:11px !important}
.copy_btn,.copy_embed_btn{background:rgba(98,126,234,.15) !important;color:#AAB6F8 !important;border:1px solid rgba(98,126,234,.35) !important;border-radius:9px !important}
.section-title h2,.section-title h3,.box-title{color:#fff !important}
.donation-gift-box-inner{background:#0F1426 !important;border:1px solid var(--ds-line) !important;border-radius:14px !important}
.donation-gift-box .thumb span{background:var(--ds-accent) !important;color:#04231C !important}

/* ---------- flag modal ---------- */
.modal#flag_store_modal .modal-content{background:#141A31;border:1px solid var(--ds-line);border-radius:18px;color:#E4E7F5}
.modal#flag_store_modal .modal-header{border-bottom:1px solid var(--ds-line)}
.modal#flag_store_modal .modal-footer{border-top:1px solid var(--ds-line)}
.modal#flag_store_modal .modal-title{color:#fff;font-weight:800}
.modal#flag_store_modal .close{color:var(--ds-muted);text-shadow:none}
.modal#flag_store_modal .form-control{background:#0E1226;border-color:var(--ds-line);color:#E4E7F5;border-radius:11px}
.modal#flag_store_modal .form-control:focus{border-color:var(--ds-primary);box-shadow:0 0 0 3px rgba(98,126,234,.15)}
.modal#flag_store_modal label{color:var(--ds-muted);font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.05em}
.modal#flag_store_modal .btn-secondary{background:#232A47;border-color:var(--ds-line);color:#E4E7F5;border-radius:11px}
.modal#flag_store_modal .btn-primary{background:linear-gradient(135deg,#627EEA,#4B6CEB);border:none;border-radius:11px;font-weight:700}
@endsection

@section('content')

    <section class="donation-single-content-area padding-top-120 padding-bottom-120">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    {{-- Full-width donate hero card --}}
                    <div class="mf-donate-hero" id="donate_box_wrapper">
                        <div class="mf-dh-left">
                            <h3 class="raised">{{amount_with_currency_symbol($donation->raised ? $donation->raised : 0 )}}
                                <span class="goal">{{__('raised of')}} {{amount_with_currency_symbol($donation->amount)}} {{__('goal')}}</span>
                            </h3>
                            <p class="mf-dh-crypto"><i class="fab fa-ethereum"></i> {{__('Crypto Goal')}}: &asymp; {{number_format(($donation->amount ?? 0)/3450, 4)}} ETH <span>(1 ETH = $3,450)</span></p>
                            <div class="progress mf-dh-progress">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                     aria-valuenow="{{get_percentage($donation->amount,$donation->raised)}}" aria-valuemin="0" aria-valuemax="100" style="width: {{get_percentage($donation->amount,$donation->raised)}}%"></div>
                            </div>
                            <small class="mf-dh-pct">{{get_percentage($donation->amount,$donation->raised)}}% {{__('funded')}}</small>
                        </div>
                        <div class="mf-dh-right">
                            @if(!empty(get_static_option('donation_login_user_donate_show_hide')) && !auth()->guard('web')->check())
                                <a class="mf-btn-primary" href="{{ route('user.login') }}"><i class="fas fa-sign-in-alt"></i> {{ __('Login to Donate') }}</a>
                            @else
                                <a class="mf-btn-primary" href="{{ route('blockchain.donate.form', $donation->id) }}"><i class="fas fa-wallet"></i> {{__('Donate with MetaMask')}}</a>
                                <a class="mf-btn-ghost" href="{{ route('user.login') }}?redirect={{urlencode(route('blockchain.donate.form', $donation->id))}}"><i class="fas fa-user-secret"></i> {{__('Donate Anonymously')}}</a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    {{-- LIVE smart-contract transparency panel (reads MediFundCampaign + MediFundEscrow) --}}
                    <div id="mfChainPanel" style="display:none; background: linear-gradient(135deg, #12162A, #0E1226); border: 1px solid #232A47; border-radius: 16px; padding: 22px 24px; margin-bottom: 25px; position: relative; overflow: hidden;">
                        <div style="position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(0,212,170,.55),transparent);"></div>
                        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                            <div style="width:42px;height:42px;border-radius:12px;background:linear-gradient(135deg,#627EEA,#00D4AA);display:flex;align-items:center;justify-content:center;color:#fff;font-size:17px;"><i class="fas fa-cubes"></i></div>
                            <div style="flex:1">
                                <h5 style="margin:0;color:#fff;font-size:15px;font-weight:800;">{{__('Live On-Chain State')}}
                                    <span style="font-size:9px;color:#00D4AA;border:1px solid rgba(0,212,170,.4);border-radius:99px;padding:2px 8px;margin-left:8px;vertical-align:middle;">{{__('SMART CONTRACT')}}</span>
                                </h5>
                                <small style="color:#6B7594;">{{__('Read directly from MediFundCampaign & MediFundEscrow — no database involved')}}.</small>
                            </div>
                            <a href="{{route('blockchain.explorer')}}" style="color:#8FA3FF;font-size:12px;font-weight:700;text-decoration:none;white-space:nowrap;">{{__('Open Explorer')}} <i class="fas fa-arrow-right"></i></a>
                        </div>
                        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:10px;">
                            <div style="background:rgba(255,255,255,.03);border:1px solid #232A47;border-radius:12px;padding:13px 15px;">
                                <div style="font-size:9.5px;text-transform:uppercase;letter-spacing:.08em;color:#6B7594;font-weight:800;margin-bottom:5px;">{{__('Contract Status')}}</div>
                                <div id="mfcStatus" style="font-size:14.5px;font-weight:800;color:#EAECF5;">&mdash;</div>
                            </div>
                            <div style="background:rgba(255,255,255,.03);border:1px solid #232A47;border-radius:12px;padding:13px 15px;">
                                <div style="font-size:9.5px;text-transform:uppercase;letter-spacing:.08em;color:#6B7594;font-weight:800;margin-bottom:5px;">{{__('Fraud Gate')}}</div>
                                <div id="mfcGate" style="font-size:14.5px;font-weight:800;">&mdash;</div>
                            </div>
                            <div style="background:rgba(255,255,255,.03);border:1px solid #232A47;border-radius:12px;padding:13px 15px;">
                                <div style="font-size:9.5px;text-transform:uppercase;letter-spacing:.08em;color:#6B7594;font-weight:800;margin-bottom:5px;">{{__('Escrow Held')}}</div>
                                <div id="mfcEscrow" style="font-size:14.5px;font-weight:800;color:#00D4AA;font-variant-numeric:tabular-nums;">&mdash;</div>
                            </div>
                            <div style="background:rgba(255,255,255,.03);border:1px solid #232A47;border-radius:12px;padding:13px 15px;">
                                <div style="font-size:9.5px;text-transform:uppercase;letter-spacing:.08em;color:#6B7594;font-weight:800;margin-bottom:5px;">{{__('Beneficiary Wallet')}}</div>
                                <div id="mfcBeneficiary" style="font-size:11.5px;font-family:'SFMono-Regular',Consolas,monospace;color:#9AA3BF;word-break:break-all;">&mdash;</div>
                            </div>
                        </div>
                        <div style="margin-top:14px;">
                            <div style="display:flex;justify-content:space-between;font-size:10px;text-transform:uppercase;letter-spacing:.06em;font-weight:800;color:#6B7594;margin-bottom:7px;">
                                <span>{{__('On-chain fraud score')}} <b id="mfcScoreNum" style="color:#EAECF5">—</b>/100</span>
                                <span>{{__('gate threshold ≥ 50 blocks funding')}}</span>
                            </div>
                            <div style="height:7px;background:rgba(255,255,255,.07);border-radius:99px;overflow:hidden;">
                                <div id="mfcScoreBar" style="height:100%;width:0%;border-radius:99px;background:linear-gradient(90deg,#00D4AA,#FFB800 50%,#ff5c7a);transition:width .9s cubic-bezier(.25,1,.5,1);"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="contribute-single-page-item single-flag-contribute">
                        <div id="mobile_btn">
                            <a href="#"> {{ get_static_option('cause_single_donate_button_text') }}</a>
                        </div>
                        <x-msg.success/>
                        <x-msg.error/>

                        <div style="background: linear-gradient(135deg, #111128, #16162e); border: 1px solid #1e1e3a; border-radius: 14px; padding: 20px; margin-bottom: 25px;">
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
                                <div style="width: 45px; height: 45px; border-radius: 12px; background: rgba(0,212,170,0.12); display: flex; align-items: center; justify-content: center; font-size: 20px; color: #00D4AA; flex-shrink: 0;">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div>
                                    <h5 style="margin: 0; color: #e8e8f0; font-size: 15px; font-weight: 700;">{{__('Blockchain Verification')}}</h5>
                                    <small style="color: #8b8ba3;">{{__('Campaign authenticity verified on-chain')}}</small>
                                </div>
                                <div style="margin-left: auto;">
                                    @php
                                        $vStatus = $donation->verification_status ?? 'pending';
                                        $vColors = ['approved' => '#00D4AA', 'pending' => '#FFB800', 'rejected' => '#ff6b6b'];
                                        $vColor = $vColors[$vStatus] ?? '#FFB800';
                                    @endphp
                                    <span style="background: rgba({{ $vStatus === 'approved' ? '0,212,170' : ($vStatus === 'rejected' ? '255,107,107' : '255,184,0') }},0.15); color: {{ $vColor }}; padding: 5px 14px; border-radius: 8px; font-size: 12px; font-weight: 600;">
                                        <i class="fas fa-{{ $vStatus === 'approved' ? 'check-circle' : ($vStatus === 'rejected' ? 'times-circle' : 'clock') }}"></i>
                                        {{ ucfirst(str_replace('_', ' ', $vStatus)) }}
                                    </span>
                                </div>
                            </div>
                            @if(!empty($donation->patient_name) || !empty($donation->hospital_name))
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                @if(!empty($donation->patient_name))
                                <div style="background: #0a0a1a; border-radius: 10px; padding: 12px;">
                                    <small style="color: #8b8ba3; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;"><i class="fas fa-user"></i> {{__('Patient')}}</small>
                                    <div style="color: #e8e8f0; font-weight: 600; font-size: 14px; margin-top: 4px;">{{$donation->patient_name}}</div>
                                </div>
                                @endif
                                @if(!empty($donation->hospital_name))
                                <div style="background: #0a0a1a; border-radius: 10px; padding: 12px;">
                                    <small style="color: #8b8ba3; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;"><i class="fas fa-hospital"></i> {{__('Hospital')}}</small>
                                    <div style="color: #e8e8f0; font-weight: 600; font-size: 14px; margin-top: 4px;">{{$donation->hospital_name}}</div>
                                </div>
                                @endif
                            </div>
                            @endif
                            @if(!empty($donation->medical_details))
                            <div style="background: #0a0a1a; border-radius: 10px; padding: 12px; margin-top: 12px;">
                                <small style="color: #8b8ba3; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;"><i class="fas fa-file-medical"></i> {{__('Medical Details')}}</small>
                                <div style="color: #b8b8d0; font-size: 13px; margin-top: 4px; line-height: 1.6;">{{$donation->medical_details}}</div>
                            </div>
                            @endif
                            @if(!empty($donation->fraud_score))
                            <div style="margin-top: 12px; display: flex; align-items: center; gap: 10px;">
                                <small style="color: #8b8ba3;">{{__('Fraud Score')}}:</small>
                                @php $fs = $donation->fraud_score; @endphp
                                <div style="flex: 1; height: 6px; background: #1a1a2e; border-radius: 10px; overflow: hidden; max-width: 120px;">
                                    <div style="height: 100%; width: {{$fs}}%; background: {{ $fs <= 20 ? '#00D4AA' : ($fs <= 50 ? '#FFB800' : '#ff6b6b') }}; border-radius: 10px;"></div>
                                </div>
                                <span style="color: {{ $fs <= 20 ? '#00D4AA' : ($fs <= 50 ? '#FFB800' : '#ff6b6b') }}; font-weight: 700; font-size: 13px;">{{$fs}}/100</span>
                                <small style="color: #8b8ba3;">({{ $fs <= 20 ? __('Low Risk') : ($fs <= 50 ? __('Medium Risk') : __('High Risk')) }})</small>
                            </div>
                            @endif
                        </div>


                        @if(!empty($donation->image_gallery))
                            @if($donation->emmergency === 'on')
                                <div class="alert alert-danger">
                                    <div class="contribute-alert">
                                        <span> <i class="lab la-android"></i> {{ get_static_option('emmergency_donation_text') }} </span>
                                    </div>
                                </div>
                            @endif

                            <div class="donation-image-gallery global-carousel-init"
                                 data-loop="true"
                                 data-desktopitem="1"
                                 data-mobileitem="1"
                                 data-tabletitem="1"
                                 data-dots="true"
                                 data-autoplay="true"
                            >
                                @php
                                $images = explode("|",$donation->image_gallery);
                                @endphp

                        <div class="single-gallery-image single-featured">
                            {!! render_image_markup_by_attachment_id($donation->image,'','large') !!}
                            @if(get_static_option('donation_flag_show_hide'))
                            <a href="#0" data-toggle="modal" data-target="#flag_store_modal" class="flag-icon">
                                <i class="fas fa-flag"></i>
                            </a>
                            @endif

                            @if($donation->reward === 'on')
                                <div class="flag-icon gift-icon-two mt-5">
                                    <i class="las la-gift"></i>
                                </div>
                            @endif



                            @if($donation->featured === 'on')
                            <div class="award-icon-two">
                                <i class="las la-award"></i>
                            </div>
                             @endif


                        </div>


                                @foreach($images as $image)
                                    @php 
                                    	$image_details = get_attachment_image_by_id($image);
                                    	if(empty($image_details)){
                                    		continue;
                                    	}
                                	@endphp
                                    <div class="single-gallery-image single-featured">
                                        {!! render_image_markup_by_attachment_id($image,'large') !!}

                                        @if(get_static_option('donation_flag_show_hide'))
                                        <a href="#0" data-toggle="modal" data-target="#flag_store_modal" class="flag-icon">
                                            <i class="fas fa-flag"></i>
                                        </a>
                                        @endif

                                        @if($donation->reward === 'on')
                                            <div class="flag-icon gift-icon-two mt-5">
                                                <i class="las la-gift"></i>
                                            </div>
                                        @endif

                                        @if($donation->featured === 'on')
                                        <div class="award-icon-two">
                                            <i class="las la-award"></i>
                                        </div>
                                         @endif

                                    </div>
                                @endforeach
                            </div>
                        @else

                            @if($donation->emmergency === 'on')
                                <div class="alert alert-danger">
                                    <div class="contribute-alert">
                                        <span> <i class="lab la-android"></i> {{ get_static_option('emmergency_donation_text') }} </span>
                                    </div>
                                </div>
                            @endif
                        <div class="thumb single-featured">
                            {!! render_image_markup_by_attachment_id($donation->image,'','large') !!}
                            @if(get_static_option('donation_flag_show_hide'))
                            <a href="#0" data-toggle="modal" data-target="#flag_store_modal" class="flag-icon">
                                <i class="fas fa-flag"></i>
                            </a>
                            @endif

                            @if($donation->reward === 'on')
                                <div class="flag-icon gift-icon-two mt-5">
                                    <i class="las la-gift"></i>
                                </div>
                            @endif



                            @if($donation->featured === 'on')
                            <div class="award-icon-two">
                                <i class="las la-award"></i>
                            </div>
                             @endif


                        </div>
                        @endif
                        <div class="post-meta-wrap ">
                            <div class="author-data author-data-new margin-top-20">
                                @if($donation->created_by === 'user')
                                    @php $user = $donation->user; @endphp
                                @else
                                    @php $user = $donation->admin; @endphp
                                @endif
                                <div class="medical-documents">
                                    <div class="thumb">
                                        {!! render_image_markup_by_attachment_id(optional($user)->image,'','thumb') !!}
                                    </div>

                                    <div class="auth-details">
                                    <ul class="my-3">
                                      <li>  <a @if(!empty($user->id)) href="{{route('frontend.user.created.donations',['user' => $donation->created_by,'id' => $user->id ])}}" @endif>
                                            <h4 class="name">
                                                {{$user ? $user->name  : __('Anonymous')}}
                                                @if(!is_null($user) && $user->user_verify_status == 2)
                                                    <a href="#!" class="user_verified_check">
                                                        <i class="fas fa-check-circle text-success user_verified_check_icon" data-toggle="tooltip" data-title="{{__('Verified')}}"></i>
                                                    </a>
                                                @endif
                                            </h4>

                                        </a>

                                      </li>


                                        @php $follow_condition = __('Follow'); @endphp

                                         @if(!empty($follow_user) && $follow_user->user_id != $user->id)
                                            @php
                                                  if(!empty($follow_user) && $follow_user->follow_status == 'follow'){

                                                       $follow_condition =  __('Following');
                                                  }else{

                                                       $follow_condition =  __('Follow');
                                                  }
                                            @endphp
                                        @endif
                                        @if(!is_null($user) && auth()->guard('web')->check())
                                      <li>
                                          <a class="follow_btn badge badge-success p-2" data-campaign_owner_id="{{$user->id}}" data-user_type ="{{$donation->created_by}}" href="#">
                                              {{$follow_condition}}
                                          </a>
                                      </li>
                                       @endif

                                    </ul>
                                        <ul>
                                            <li><i class="fas fa-clock"></i> {{$donation->created_at->diffForHumans()}}</li>

                                            <li>
                                                <i class="fas fa-tag"></i>
                                                <a href="{{route('frontend.donations.category',['id' => $donation->categories_id,'any' => Str::slug($donation->category->title ?? __('Uncategorized')) ?? '' ])}}">{{$donation->category->title ?? __('Uncategorized')}}</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>



                              @if(get_static_option('donation_medical_document_button_show_hide'))
                                @if($donation->medical_document)
                                <div class="medical-document-btn">
                                    <div class="btn-wrapper">
                                        @php
                                            $medical_document_images = explode("|",$donation->medical_document);
                                        @endphp
                                        @foreach($medical_document_images as $image_id)
                                            @php
                                                $image_url = get_attachment_image_by_id($image_id,'full');
                                            @endphp
                                            @if($loop->index === 0)
                                                <a href="{{$image_url['img_url'] ?? ''}}" class="boxed-btn btn-color-three medical-image-popup" >
                                                    {!! get_static_option('donation_medical_document_button_text') !!}
                                                </a>
                                            @else
                                                <a class="d-none medical-image-popup" href="{{$image_url['img_url'] ?? ''}}"></a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                                @endif
                            </div>
                            @php 
                                $style = ['frontend.partials.donation-single.tab-view' => '02','frontend.partials.donation-single.general-view' =>'01'];
                                $get_view = !empty($type) && in_array($type,['tab','general']) ? 'frontend.partials.donation-single.'.$type.'-view' : array_search(get_static_option('donation_single_page_variant'),$style); 
                            @endphp

                            {{-- Transparency timeline --}}
                            @php
                                $tlDonations = \App\CauseLogs::where('cause_id',$donation->id)->where('status','complete')->orderBy('created_at')->get();
                                $tlTxns = \App\BlockchainTransaction::where('campaign_id',$donation->id)->where('transaction_type','withdrawal')->orderBy('created_at')->get();
                                $tlEscrowHeld = \Illuminate\Support\Facades\DB::table('escrow')->where('campaign_id',$donation->id)->where('status','held')->sum('amount');
                                $tlEscrowReleased = \Illuminate\Support\Facades\DB::table('escrow')->where('campaign_id',$donation->id)->where('status','released')->sum('amount');
                            @endphp
                            <div class="tl-card" style="margin-top:26px;background:#0F1426;border:1px solid #232A47;border-radius:16px;padding:24px 26px;">
                                <h4 style="color:#fff;font-size:16px;font-weight:800;display:flex;align-items:center;gap:9px;margin:0 0 4px;"><i class="fas fa-stream" style="color:#627EEA"></i> {{__('Transparency Timeline')}}</h4>
                                <p style="color:#6B7594;font-size:12px;margin:0 0 20px;">{{__('Every step of this campaign is recorded and publicly auditable')}}</p>
                                <div class="tl-list">
                                    <div class="tl-item">
                                        <span class="tl-dot" style="background:#627EEA"></span>
                                        <div><b>{{__('Campaign Created')}}</b><small>{{__($donation->created_by === 'user' ? 'Submitted by patient' : 'Published by MediFund')}} &bull; {{\Carbon\Carbon::parse($donation->created_at)->diffForHumans()}}</small></div>
                                    </div>
                                    @if(!empty($donation->wallet_verified_at))
                                    <div class="tl-item">
                                        <span class="tl-dot" style="background:#00D4AA"></span>
                                        <div><b>{{__('Receiving Wallet Verified')}}</b><small>{{__('Admin verified beneficiary wallet on-chain ownership')}} &bull; {{\Carbon\Carbon::parse($donation->wallet_verified_at)->diffForHumans()}}</small></div>
                                    </div>
                                    @endif
                                    @if(($donation->verification_status ?? '') === 'approved')
                                    <div class="tl-item">
                                        <span class="tl-dot" style="background:#00D4AA"></span>
                                        <div>
                                            <b>{{__('Medical Documents Approved')}}</b>
                                            <small>{{__('Fraud score')}}: {{$donation->fraud_score}}/100 &bull; {{__('cleared for fundraising')}}</small>
                                            @if($donation->document_hash)
                                                <small style="display:block;margin-top:2px;color:#00D4AA;">
                                                    <i class="fas fa-fingerprint"></i> {{__('SHA-256 sealed at approval')}} &bull;
                                                    <span style="font-family:monospace;opacity:.85;">{{ substr($donation->document_hash,0,16) }}…</span>
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                    @if($tlDonations->count() > 0)
                                    <div class="tl-item">
                                        <span class="tl-dot" style="background:#627EEA"></span>
                                        <div><b>{{__('First Donation Received')}}</b><small>{{$tlDonations->first()->anonymous ? 'Anonymous' : $tlDonations->first()->name}} &bull; {{amount_with_currency_symbol($tlDonations->first()->amount)}} &bull; {{\Carbon\Carbon::parse($tlDonations->first()->created_at)->diffForHumans()}}</small></div>
                                    </div>
                                    <div class="tl-item">
                                        <span class="tl-dot" style="background:#627EEA"></span>
                                        <div><b>{{$tlDonations->count()}} {{__('Donations in Escrow Pipeline')}}</b><small>{{amount_with_currency_symbol($donation->raised)}} {{__('raised')}} &bull; &asymp;{{number_format((float)$tlEscrowHeld/3450,4)}} ETH {{__('currently held in escrow')}}</small></div>
                                    </div>
                                    @endif
                                    @foreach($tlTxns as $tx)
                                    <div class="tl-item">
                                        <span class="tl-dot" style="background:#FFB800"></span>
                                        <div><b>{{__('Milestone Funds Released')}}</b><small>{{number_format($tx->amount,4)}} ETH &bull; <a href="{{route('blockchain.transaction.show',$tx->transaction_hash)}}" target="_blank" style="color:#627EEA;text-decoration:none;">{{substr($tx->transaction_hash,0,12)}}&hellip;</a> &bull; {{\Carbon\Carbon::parse($tx->created_at)->diffForHumans()}}</small></div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            @if(in_array(get_static_option('donation_single_page_variant'),$style))
                             @include( $get_view)
                            @endif

                            @if(count($all_related_cause) > 1)
                                <div class="related-post-area margin-top-40">
                                    <div class="section-title ">
                                        <h4 class="title ">{{ get_static_option('releated_donation_text') }}</h4>
                                    </div>
                                    <div class="related-news-carousel global-carousel-init"
                                         data-desktopitem="2"
                                         data-mobileitem="1"
                                         data-tabletitem="1"
                                         data-margin="30"
                                         data-dots="true"
                                    >
                                        @foreach($all_related_cause as $data)
                                            @if($data->id === $donation->id) @continue @endif
                                            <x-frontend.donation.related
                                                    :featured="$data->featured"
                                                    :image="$data->image"
                                                    :amount="$data->amount"
                                                    :raised="$data->raised"
                                                    :slug="$data->slug"
                                                    :title="$data->title"
                                                    :excerpt="$data->excerpt"
                                                    :deadline="$data->deadline"
                                                    :buttontext="get_static_option('donation_button_text')">
                                            </x-frontend.donation.related>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>

            </div>
        </div>
        </div>
    </section>

{{--Flag Store Modal--}}
    <div class="modal fade" id="flag_store_modal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('Cause Claim')}}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>Ã—</span></button>
                </div>
                <form action="{{route('frontend.donation.flag.report.store')}}" id="faq_edit_modal_form" enctype="multipart/form-data"
                      method="post">
                        @csrf
                    <div class="modal-body">
                        <input type="hidden" name="cause_id"  value="{{$donation->id}}">

                        @php
                            $userAuthCheck = auth()->check();
                            $authUser = auth()->guard('web')->user();
                        @endphp

                        <div class="form-group">
                            <label for="edit_title">{{__('Name')}}</label>
                            <input type="text" class="form-control" name="name"  value="{{ $userAuthCheck ? $authUser->name : ''  }}"
                                   placeholder="{{__('Name')}}">
                        </div>

                        <div class="form-group">
                            <label for="edit_title">{{__('Email')}}</label>
                            <input type="email" class="form-control" name="email" value="{{ $userAuthCheck ? $authUser->email : ''  }}"
                                   placeholder="{{__('Email')}}">
                        </div>

                        <div class="form-group">
                            <label for="edit_title">{{__('Subject')}}</label>
                            <input type="text" class="form-control" name="subject"
                                   placeholder="{{__('Subject')}}">
                        </div>

                        <div class="form-group">
                            <label for="edit_description">{{__('Description')}}</label>
                           <textarea class="form-control" name="description" rows="5"></textarea>

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
                        <button id="submit" type="submit" class="btn btn-primary">{{__('Submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
{{--Flag Store Modal--}}


@endsection

@section('scripts')

 <script src="{{asset('assets/common/js/countdown.jquery.js')}}"></script>
    <script src="{{asset('assets/blockchain/ethers.min.js')}}"></script>
 <script src="{{asset('assets/blockchain/medi-fund-web3.js')}}"></script>
 <script>
        /* ---------- live on-chain transparency panel ---------- */
        (async function () {
            try {
                if (typeof MediFundWeb3 === 'undefined' || !(await MediFundWeb3.ready())) return;
                const trust = await MediFundWeb3.readCampaignTrust({{$donation->id}});
                if (!trust || !trust.exists) return;
                document.getElementById('mfChainPanel').style.display = '';

                const st = document.getElementById('mfcStatus');
                st.textContent = trust.statusLabel;
                st.style.color = (trust.status === 4) ? '#00D4AA' : (trust.status === 3 ? '#ff5c7a' : '#FFD166');

                const gate = document.getElementById('mfcGate');
                gate.textContent = trust.isDonatable ? '{{__("OPEN")}}' : '{{__("BLOCKED")}}';
                gate.style.color = trust.isDonatable ? '#00D4AA' : '#ff5c7a';

                document.getElementById('mfcScoreNum').textContent = trust.fraudScore;
                setTimeout(function () {
                    document.getElementById('mfcScoreBar').style.width = Math.min(trust.fraudScore, 100) + '%';
                }, 150);

                const ben = document.getElementById('mfcBeneficiary');
                if (trust.beneficiary) {
                    ben.textContent = String(trust.beneficiary).slice(0, 12) + '…' + String(trust.beneficiary).slice(-8);
                }

                const escrow = await MediFundWeb3.escrowBalance({{$donation->id}});
                if (escrow !== null && escrow !== undefined) {
                    document.getElementById('mfcEscrow').textContent =
                        parseFloat(MediFundWeb3.formatEther(escrow)).toFixed(6) + ' ETH';
                }
            } catch (e) { console.warn('on-chain panel unavailable', e); }
        })();
    </script>
    <script>
        (function ($) {
            'use strict';

            $(document).ready(function () {
                <x-btn.submit/>



            @if(!empty(get_static_option('donation_single_page_countdown_status')))
            var ev_offerTime = "{{$donation->deadline}}";
            var ev_year = ev_offerTime.substr(0, 4);
            var ev_month = ev_offerTime.substr(5, 2);
            var ev_day = ev_offerTime.substr(8, 2);

            if (ev_offerTime) {
                $('#event_countdown').countdown({
                    year: ev_year,
                    month: ev_month,
                    day: ev_day,
                    labels: true,
                    labelText: {
                        'days': "{{__('days')}}",
                        'hours': "{{__('hours')}}",
                        'minutes': "{{__('min')}}",
                        'seconds': "{{__('sec')}}",
                    }
                });
            }
            @endif

                //Cause content
                  $(document).on('click', '#ReadMoreButton', function (e) {
                    e.preventDefault();
                    var data = "";
                    $(this).parent().parent().css({
                        'max-height' : '100%',
                        'margin-bottom' : '0px'
                    });
                    $(this).parent().hide();

                });


                //Cause Comment Insert
                $(document).on('click', '#submitComment', function (e) {
                    e.preventDefault();
                    var erContainer = $(".error-message");
                    var el = $(this);
                    var form = $('#cause-comment-form');
                    var user_id = $('#user_id').val();
                    var cause_id = $('#cause_id').val();
                    var commented_by = $('#commented_by').val();
                    var comment_content = $('#comment_content').val();
                    el.text('{{__('Submitting')}}..');

                    $.ajax({
                        url: form.attr('action'),
                        method: 'POST',
                        data: {
                            _token: "{{csrf_token()}}",
                            user_id: user_id,
                            cause_id: cause_id,
                            commented_by: commented_by,
                            comment_content: comment_content,
                        },
                        success: function (data) {
                            location.reload();
                        },
                        error: function (data) {
                            var errors = data.responseJSON;
                            erContainer.html('<div class="alert alert-danger"></div>');
                            $.each(errors.errors, function (index, value) {
                                erContainer.find('.alert.alert-danger').append('<p>' + value + '</p>');
                            });
                            el.text('{{__('Comment')}}');
                        },

                    });

                });

                //Load More Cause Comment Data
                var _token = $('input[name="_token"]').val();
                load_comment_data("{{$donation->id}}", _token);

                function load_comment_data(id = "", _token) {
                    var page = $('#comment_data').attr('data-page');

                    $.ajax({
                        url: "{{ route('frontend.load.cause.comment.data') }}",
                        method: "POST",
                        data: {id: id, _token: _token, page: page},
                        success: function (data) {
                            var appendData = '';

                            $.each(data, function (index, value) {
                                appendData += ' <div class="donor-comment"> '+
                                    '<span class="commented_by"> {{__('By')}} '+value.commented_by+' {{__('at')}} ' + value.date + '</span>' +
                                    '<p class="description">' + value.comment_content + '</p>' +
                                    '</div>';
                            });

                            if (data.length > 4) {
                                appendData += '<div id="load_more_div"> <button type="button" class="load-more-btn" id="load_more_comment_button">{{__('Load More')}}</button> </div>';
                            }
                            $('#load_more_div').remove();
                            $('#comment_data').append(appendData);
                            $('#comment_data').attr('data-page', parseInt(page) + 5);

                        }
                    })
                }

                $(document).on('click', '#load_more_comment_button', function () {
                    $('#load_more_comment_button').html('<b>{{__('Loading...')}}</b>');
                    load_comment_data('{{$donation->id}}', _token);
                });


                //Load More Donors Data
                var _token = $('input[name="_token"]').val();


                $(document).on('click', '#load_more_case_update_button', function () {
                    $('#load_more_case_update_button').html('<b>{{__('Loading...')}}</b>');
                    load_donation_update('{{$donation->id}}');
                });


                load_donation_update("{{$donation->id}}");

                function load_donation_update(id){
                    var parentContainer = $('#recent_update_about_cause');
                    var page = parentContainer.attr('data-page');
                    $.ajax({
                        url: "{{ route('frontend.load.cause.donation.update.data') }}", // defaine route for update load more
                        method: "POST",
                        data: {id: id, _token: "{{csrf_token()}}", page: page},
                        success: function (data) {
                            var appendData = '';
                            $('#load_more_case_update_button').remove();
                            $.each(data,function (index,value){
                               appendData += '<div class="cause-update-section-body">';
                               if (value.img_url){
                                   appendData += '<div class="thumb">' +value.img_markup+'<div class="img-pop-wrap"><a href="'+value.img_url+'" class="image-popup"><i class="fas fa-search"></i></a></div></div>';
                               }
                             appendData += '<div class="content">'+
                            '<h3 class="title">'+value.title+'</h3>'+
                            '<div id="time-creator">'+value.date+' {{__('by ')}}'+
                            '<span id="creator">'+value.created_by+'</span>'+
                            '</div> <p>'+value.description+'</p></div></div>';
                            });
                            if (data.length < 1) {
                                appendData += '<p class="not-found-button">{{__('No more update found')}}</p>';
                            } else {
                                appendData += '<div class="btn-wrapper load_more"> <button type="button" class="load-more-btn" id="load_more_case_update_button">{{__('Load More')}}</button> </div>';
                            }
                            parentContainer.append(appendData);
                            parentContainer.attr('data-page', parseInt(page) + 5);

                            $('.image-popup').magnificPopup({
                                type: 'image',
                                gallery: {
                                    // options for gallery
                                    enabled: true
                                },
                            });
                        }
                    })
                }
                load_data("{{$donation->id}}", _token);

                function load_data(id = "", _token) {
                    var page = $('#post_data').attr('data-page');
                    $.ajax({
                        url: "{{ route('frontend.load.cause.donor.data') }}",
                        method: "POST",
                        data: {id: id, _token: _token, page: page},
                        success: function (data) {
                            var appendData = '';
                            $('#load_more').remove();
                            $.each(data, function (index, value) {
                                appendData += ' <div class="donoer-info">' +
                                    '<div class="icon"><i class="fas fa-donate"></i></div>' +
                                    '<div class="content"><h3 class="title">' + value.name + '</h3>' +
                                    '<div class="dinfo"><span>' + value.amount + '</span>{{__('at')}} ' + value.date + '</div>' +
                                    '</div></div>';
                            });
                            if (data.length < 1) {
                                appendData += '<p class="not-found-button">{{__('No donor found')}}</p>';
                            } else {
                                appendData += '<div id="load_more" class="btn-wrapper"> <button type="button" class="load-more-btn" id="load_more_button">{{__('Load More')}}</button> </div>';
                            }
                            $('#post_data').append(appendData);
                            $('#post_data').attr('data-page', parseInt(page) + 5);
                        }
                    })
                }

                $(document).on('click', '#load_more_button', function () {
                    $('#load_more_button').html('<b>{{__('Loading...')}}</b>');
                    load_data('{{$donation->id}}', _token);
                });

                //Donation Charge
                $(document).on('keyup', '#donation_amount_user_input', function () {
                    var donation_amount_user_input = $('#donation_amount_user_input').val();
                    var show_charge_amount = $('#show_charge_amount').val();

                    $.ajax({
                        url: "{{ route('frontend.get.donation.charges.by.ajax') }}",
                        type: 'get',
                        dataType: 'JSON',

                        success: function (data) {
                            if (data.amount === 'percentage' && data.donation_charge_button_on) {
                                $('.amount_show').text(parseInt(donation_amount_user_input) * data.percentage / 100 + '{{site_currency_symbol()}}');

                            } else if (data.amount === 'fixed' && data.donation_charge_button_on) {

                                $('.amount_show').text(parseInt(data.fixed) + parseInt(donation_amount_user_input + '{{site_currency_symbol()}}'));

                            } else if (!data.donation_charge_button_on) {
                                $('#show_charge_amount').val('');
                            } else {
                                $('#show_charge_amount').val('');
                            }

                        }
                    });
                })


                //Copy Url
                var url = $('#donation_copy_id').data(url);
                var copy_field = $('#copy_field').val(url.url);

                //Copy Url Code
                $(document).on('click','.copy_btn',function(){
                    navigator.clipboard.writeText(copy_field.val())
                    $(this).html('<i class="fas fa-check"> {{__('Copied')}}</i>');
                     setTimeout(function(){
                         $('.copy_btn').text('Copy');
                     },3000);
                 });
            });


            //Copy Embed Code
            var url = $('#donation_copy_id').data(url);
            var copy_field = $('.copy_embed_field').val(url.url);
            let imf_container = '<iframe class="iframe_field" width="560" height="315" src="'+copy_field.val()+'" frameborder="0" '+
                'allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
            $('.embed_area').val(imf_container);

            $(document).on('click','.copy_embed_btn',function(){
                navigator.clipboard.writeText(imf_container)
                $(this).html('<i class="fas fa-check"> {{__('Copied')}}</i>');
                setTimeout(function(){
                    $('.copy_embed_btn').text('Copy');
                },3000);
            });


            $(document).on('click','.follow_btn',function(e){
                e.preventDefault();
                let el = $(this);
                let text = el.text();

                let campaign_owner_id = $(this).data('campaign_owner_id');
                let user_type = $(this).data('user_type');
                $.ajax({
                    url: '{{route('user.home.user.follow.store')}}',
                    method: 'post',
                    data:{campaign_owner_id:campaign_owner_id, text:text,user_type:user_type, _token: '{{csrf_token()}}'},

                    success: function(data){

                        if (data.type === 'success') {
                            toastr.success("{{__('You are now following this user..')}}");
                            el.text(data.text)

                        } else if(data.type === 'updated'){
                            if(data.text == "{{__('Follow')}}"){
                                el.text(data.text)
                                toastr.error("{{__('You are not following this user..')}}");
                            }else{
                                el.text(data.text)
                                toastr.success("{{__('You are now following this user..')}}");
                            }
                        } else {
                            toastr.error("{{__('Something went wrong!')}}");
                        }
                    },
                    error: function (response) {
                        let data = response.responseJSON;
                        if(data.message === 'Unauthenticated.'){
                            toastr.error(`{{__('login to follow this campaign owner')}}`);

                            return true;
                        }
                        toastr.error(data.message);
                    }
                });

            })

        })(jQuery);
    </script>
    @include('frontend.partials.ajax-login-js')
@endsection

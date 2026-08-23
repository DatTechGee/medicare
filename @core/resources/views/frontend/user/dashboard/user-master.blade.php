@extends('frontend.frontend-page-master')
@section('page-title')
    {{ auth()->guard('web')->user()->isPatient() ? __('Patient Dashboard') : __('Donor Dashboard') }}
@endsection
@section('style')
<script src="https://cdn.tailwindcss.com"></script>
<script>
if (window.tailwind) {
    tailwind.config = {
        corePlugins: { preflight: false },
        theme: { extend: { colors: {
            d: {900:'#0D0D1A',800:'#16162A',700:'#1D1D35',600:'#252542'},
            t: {400:'#00D4AA',500:'#0d9488',600:'#0f766e'},
            b: {400:'#627EEA',500:'#4B6CEB'}
        }}}
    };
}
</script>
<style>
body{background:#080814}
.ud-shell{background:linear-gradient(145deg,rgba(13,13,26,.92) 0%,rgba(10,10,31,.95) 50%,rgba(8,12,20,.92) 100%);backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);border:1px solid rgba(98,126,234,.15);border-radius:20px;box-shadow:0 16px 64px rgba(0,0,0,.6),0 0 0 1px rgba(98,126,234,.06);position:relative;overflow:hidden;font-family:'Inter','Segoe UI',sans-serif}
.ud-shell::before{content:'';position:absolute;top:-180px;right:-180px;width:420px;height:420px;background:radial-gradient(circle,rgba(98,126,234,.12) 0%,transparent 65%);pointer-events:none}
.ud-shell::after{content:'';position:absolute;bottom:-200px;left:-140px;width:400px;height:400px;background:radial-gradient(circle,rgba(0,212,170,.08) 0%,transparent 65%);pointer-events:none}
.ud-nav{display:flex;flex-wrap:wrap;align-items:center;gap:6px;background:rgba(8,8,20,.7);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);border:1px solid rgba(98,126,234,.12);border-radius:14px;padding:8px;margin-bottom:24px;position:relative;z-index:2}
.ud-nav a,.ud-nav button{display:inline-flex;align-items:center;gap:7px;color:rgba(224,224,224,.5);font-size:12.5px;font-weight:600;padding:9px 16px;border-radius:10px;transition:all .22s ease;text-decoration:none!important;border:none;background:transparent;cursor:pointer;white-space:nowrap;letter-spacing:.3px}
.ud-nav a:hover{color:#fff;background:rgba(98,126,234,.14)}
.ud-nav a.ud-active{background:linear-gradient(135deg,#627EEA,#00D4AA);color:#fff;box-shadow:0 4px 20px rgba(0,212,170,.3)}
.ud-nav .ud-user{background:rgba(0,212,170,.08);border:1px solid rgba(0,212,170,.25);color:#00D4AA;font-weight:700;cursor:default}
.ud-nav .ud-logout:hover{background:rgba(255,107,107,.12);color:#ff6b6b}
.ud-mobile-toggle{display:none}
@media(max-width:860px){
    .ud-mobile-toggle{display:inline-flex}
    .ud-nav .ud-link{display:none}
    .ud-nav.ud-open .ud-link{display:inline-flex}
}
.ud-card{background:#16162A;border:1px solid #2A2A4A;border-radius:18px;padding:22px;transition:all .3s ease;height:100%;position:relative;z-index:1}
.ud-card:hover{transform:translateY(-3px);border-color:rgba(0,212,170,.4);box-shadow:0 12px 32px rgba(0,0,0,.45)}
.ud-card-accent{border-left:3px solid #00D4AA}
.ud-stat-icon{width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:21px;background:linear-gradient(135deg,rgba(98,126,234,.16),rgba(0,212,170,.16));color:#00D4AA;flex-shrink:0}
.ud-stat-label{color:rgba(224,224,224,.5)!important;font-size:11px;font-weight:700;margin:0 0 5px;text-transform:uppercase;letter-spacing:.9px}
.ud-stat-value{color:#fff!important;font-size:24px;font-weight:800;line-height:1.15;margin:0}
.ud-section-title{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px;margin-bottom:24px;position:relative;z-index:1}
.ud-section-title h2{color:#fff;font-size:20px;font-weight:800;margin:0;display:flex;align-items:center;gap:10px;letter-spacing:.3px}
.ud-section-title h2 i{color:#00D4AA}
.ud-btn-primary{display:inline-flex;align-items:center;gap:9px;padding:12px 22px;border-radius:13px;font-size:13px;font-weight:700;background:linear-gradient(135deg,#627EEA,#00D4AA);color:#fff!important;box-shadow:0 6px 22px rgba(0,212,170,.25);transition:all .26s ease;text-decoration:none!important;border:none;cursor:pointer}
.ud-btn-primary:hover{transform:translateY(-2px);box-shadow:0 10px 30px rgba(0,212,170,.38)}
.ud-btn-ghost{display:inline-flex;align-items:center;gap:9px;padding:12px 22px;border-radius:13px;font-size:13px;font-weight:700;background:rgba(255,255,255,.04);border:1px solid #2A2A4A;color:#E0E0E0!important;transition:all .26s ease;text-decoration:none!important;cursor:pointer}
.ud-btn-ghost:hover{border-color:#00D4AA;color:#00D4AA!important;transform:translateY(-2px)}
.ud-divider{border:0;border-top:1px solid rgba(42,42,74,.6);margin:18px 0}
.ud-body{position:relative;z-index:1}
.ud-body .table{color:#E0E0E0;border-color:#2A2A4A;margin-bottom:0}
.ud-body .table th{background:rgba(98,126,234,.08);color:#8fa3f5!important;font-size:11px;text-transform:uppercase;letter-spacing:.8px;border-bottom:1px solid #2A2A4A!important;padding:12px 14px}
.ud-body .table td{border-color:rgba(42,42,74,.7)!important;padding:14px;vertical-align:middle}
.ud-body .table-striped tbody tr:nth-of-type(odd){background-color:rgba(255,255,255,.02)}
.ud-body .table a{color:#00D4AA}
.ud-body .table small{color:rgba(224,224,224,.55)!important}
.ud-body .table small strong{color:#E0E0E0!important}
.ud-body .table code{color:#00D4AA;background:rgba(0,212,170,.07);padding:2px 7px;border-radius:6px;font-size:11px}
.ud-body .badge{border-radius:20px;padding:5px 11px;font-size:10.5px;font-weight:700;letter-spacing:.4px}
.ud-body .badge-success{background:rgba(0,212,170,.12);color:#00D4AA;border:1px solid rgba(0,212,170,.3)}
.ud-body .badge-warning{background:rgba(255,184,0,.12);color:#FFB800;border:1px solid rgba(255,184,0,.3)}
.ud-body .badge-danger{background:rgba(255,107,107,.12);color:#ff6b6b;border:1px solid rgba(255,107,107,.3)}
.ud-body .badge-secondary{background:rgba(160,160,180,.12);color:#a0a0b4;border:1px solid rgba(160,160,180,.3)}
.ud-body .btn-info{background:linear-gradient(135deg,#627EEA,#4B6CEB);border:none;color:#fff;border-radius:10px;font-weight:600}
.ud-body .btn-primary{background:linear-gradient(135deg,#0d9488,#0f766e);border:none;color:#fff;border-radius:10px;font-weight:600}
.ud-body .btn-secondary{background:rgba(255,255,255,.06);border:1px solid #2A2A4A;color:#E0E0E0;border-radius:10px}
.ud-body .btn-danger{background:rgba(255,107,107,.12);border:1px solid rgba(255,107,107,.3);color:#ff6b6b;border-radius:10px}
.ud-body .btn-dark{background:rgba(255,255,255,.06);border:1px solid #2A2A4A;color:#E0E0E0;border-radius:10px}
.ud-body .btn-success{background:linear-gradient(135deg,#0d9488,#00D4AA);border:none;color:#fff;border-radius:10px;font-weight:600}
.ud-body .alert{border-radius:12px;border:1px solid transparent;padding:10px 16px;font-size:12.5px;display:inline-block}
.ud-body .alert-success{background:rgba(0,212,170,.1);border-color:rgba(0,212,170,.3);color:#00D4AA}
.ud-body .alert-warning{background:rgba(255,184,0,.1);border-color:rgba(255,184,0,.3);color:#FFB800}
.ud-body .alert-danger{background:rgba(255,107,107,.1);border-color:rgba(255,107,107,.3);color:#ff6b6b}
.ud-body .alert-info{background:rgba(98,126,234,.1);border-color:rgba(98,126,234,.3);color:#8fa3f5}
.ud-body .form-group label{color:#E0E0E0!important;font-size:13px;font-weight:600}
.ud-body .form-control{background:rgba(13,13,26,.85);border:1px solid #2A2A4A;color:#E0E0E0;border-radius:12px;padding:11px 15px;font-size:14px;transition:all .25s ease;height:auto}
.ud-body .form-control:focus{outline:none;border-color:#627EEA;box-shadow:0 0 0 3px rgba(98,126,234,.18);background:rgba(13,13,26,1);color:#fff}
.ud-body .form-control::placeholder{color:rgba(224,224,224,.35)}
.ud-body select.form-control option{background:#16162A;color:#E0E0E0}
.ud-body .info-text{color:rgba(224,224,224,.5)!important}
.ud-body .input-group-append .btn{border-radius:0 12px 12px 0}
.ud-body .input-group .form-control{border-radius:12px 0 0 12px}
.ud-body .text-danger{color:#ff6b6b!important}
.ud-body .text-warning{color:#FFB800!important}
.ud-body ul{list-style:none;padding-left:0;margin:0}
.ud-body ul li{color:rgba(224,224,224,.55);font-size:13px;padding:3px 0}
.ud-body ul li strong{color:#E0E0E0}
.ud-body .note-editor{background:rgba(13,13,26,.85)!important;border-color:#2A2A4A!important;color:#E0E0E0;border-radius:12px;overflow:hidden}
.ud-body .note-toolbar{background:#12121F!important;border-bottom-color:#2A2A4A!important}
.ud-body .note-editable{color:#E0E0E0!important;background:transparent!important}
.ud-body .select2-container--default .select2-selection--single,
.ud-body .select2-container--default .select2-selection--multiple{background:rgba(13,13,26,.85)!important;border-color:#2A2A4A!important;border-radius:12px!important;height:auto}
.ud-body .select2-container--default .select2-selection--single .select2-selection__rendered{color:#E0E0E0!important;padding:9px 15px}
.ud-body .select2-dropdown{background:#16162A!important;border-color:#2A2A4A!important}
.ud-body .bootstrap-tagsinput{background:rgba(13,13,26,.85);border-color:#2A2A4A;color:#fff;border-radius:12px}
.ud-body .media-upload-btn-wrapper .media_upload_form_btn{background:rgba(98,126,234,.15);border:1px dashed #627EEA;color:#8fa3f5;border-radius:10px}
.ud-body .campaign-image-wrap img{border-radius:12px;border:1px solid #2A2A4A}
.ud-alert{border-radius:14px;padding:14px 20px;font-size:13.5px;margin-bottom:18px;border:1px solid}
.ud-alert.warning{background:rgba(255,184,0,.08);border-color:rgba(255,184,0,.25);color:#FFB800}
.ud-alert.success{background:rgba(0,212,170,.08);border-color:rgba(0,212,170,.25);color:#00D4AA}

/* ===== dashboard home widgets ===== */
.ud-hero{position:relative;overflow:hidden;border-radius:20px;padding:26px 28px;margin-bottom:22px;background:linear-gradient(120deg,#1D1D3F 0%,#16162A 55%,#101028 100%);border:1px solid rgba(98,126,234,.25);box-shadow:0 10px 34px rgba(0,0,0,.4)}
.ud-hero-glow{position:absolute;inset:0;background:radial-gradient(420px 190px at 88% 12%,rgba(98,126,234,.16),transparent 62%),radial-gradient(360px 170px at 6% 92%,rgba(0,212,170,.13),transparent 60%);pointer-events:none}
.ud-hero-date{color:#8fa3f5;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1.2px;margin:0 0 7px;display:flex;align-items:center;gap:7px}
.ud-hero-title{color:#fff;font-size:21px;font-weight:800;margin:0;letter-spacing:.3px}
.ud-hero-sub{color:rgba(224,224,224,.45);font-size:12.5px;margin:6px 0 0}
.ud-chip{padding:12px 16px;border-radius:14px;background:rgba(8,8,20,.55);border:1px solid rgba(98,126,234,.18);backdrop-filter:blur(8px)}
.ud-chip p{color:rgba(224,224,224,.45);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.9px;margin:0}
.ud-chip span{color:#fff;font-size:15px;font-weight:800;margin-top:3px;display:block}
.ud-step{width:36px;height:36px;border-radius:50%;border:2px solid #2A2A4A;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0}
.ud-stepper-line{width:40px;height:2px;flex-shrink:0;margin:0 8px;border-radius:2px}
.ud-inline-note{margin-top:16px;padding:12px 16px;border-radius:12px;display:flex;align-items:center;gap:10px;font-size:12.5px;font-weight:600}
.ud-inline-note.warn{background:rgba(255,184,0,.06);border:1px solid rgba(255,184,0,.2);color:#FFB800}
.ud-inline-note.info{background:rgba(98,126,234,.06);border:1px solid rgba(98,126,234,.2);color:#8fa3f5}
.ud-stat-hover:hover .ud-stat-icon{transform:scale(1.08) rotate(-4deg)}
.ud-stat-icon{transition:transform .28s ease}
.ud-pill{display:inline-flex;align-items:center;gap:5px;border-radius:20px;padding:4px 12px;font-size:10px;font-weight:700}
.ud-pill.ok{background:rgba(0,212,170,.12);color:#00D4AA;border:1px solid rgba(0,212,170,.3)}
.ud-pill.pending{background:rgba(255,184,0,.12);color:#FFB800;border:1px solid rgba(255,184,0,.3)}
.ud-tile{padding:16px;background:rgba(0,212,170,.05);border:1px dashed rgba(0,212,170,.3);border-radius:14px;display:flex;align-items:center;gap:12px;text-decoration:none!important;transition:all .22s ease}
.ud-tile i{font-size:18px}
.ud-tile p{color:rgba(224,224,224,.5);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;margin:0}
.ud-tile span{color:#fff;font-size:15px;font-weight:800;margin-top:2px;display:block}
.ud-tile:hover{transform:translateY(-2px);background:rgba(0,212,170,.09)}
.ud-donation-row{display:flex;align-items:center;gap:14px;padding:13px 16px;background:rgba(13,13,26,.5);border:1px solid rgba(42,42,74,.7);border-radius:14px;transition:all .22s ease}
.ud-donation-row:hover{border-color:rgba(98,126,234,.35);transform:translateX(4px)}
.ud-avatar{width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:15px;flex-shrink:0}
.ud-link-more{color:#00D4AA;font-size:12px;font-weight:700;text-decoration:none!important;display:inline-flex;align-items:center;gap:6px}
.ud-link-more:hover{color:#fff}
.ud-quick{padding:18px;background:#16162A;border:1px solid #2A2A4A;border-radius:14px;text-decoration:none!important;display:flex;flex-direction:column;align-items:center;gap:8px;transition:all .25s ease}
.ud-quick i{font-size:20px}
.ud-quick p{color:#E0E0E0;font-size:12.5px;font-weight:700;margin:0}
.ud-quick:hover{transform:translateY(-3px);border-color:rgba(0,212,170,.4);box-shadow:0 10px 26px rgba(0,0,0,.45)}
{{-- merge child-page styles: any child @section('style') content is appended here --}}
@yield('page-style')
@yield('style')
</style>
@endsection
@section('content')
    <section class="py-5">
        <div class="container">
            <div class="ud-shell">
                <nav class="ud-nav" id="udNav">
                    <button type="button" class="ud-mobile-toggle ud-btn-ghost" onclick="document.getElementById('udNav').classList.toggle('ud-open')" style="padding:9px 15px;"><i class="fas fa-bars"></i> {{__('Menu')}}</button>
                    <span class="ud-nav-item ud-user"><i class="fas fa-user-circle mr-1"></i>{{ optional(Auth::guard('web')->user())->name }}</span>
                    <a href="{{route('user.home')}}" class="ud-link @if(request()->routeIs('user.home')) ud-active @endif"><i class="fas fa-home"></i>{{__('Dashboard')}}</a>
                    @if(Auth::guard('web')->user()->campaign_permission == 'on')
                        <a href="{{route('user.campaign.all')}}" class="ud-link @if(request()->routeIs('user.campaign.*') || request()->routeIs('user.all.update.cause.page') || request()->routeIs('user.add.new.update.cause.page')) ud-active @endif"><i class="fas fa-file-medical"></i>{{__('My Campaigns')}}</a>
                    @endif
                    @if(!empty(get_static_option('donations_module_status')))
                        <a href="{{route('user.home.donations')}}" class="ud-link @if(request()->routeIs('user.home.donations')) ud-active @endif"><i class="fas fa-hand-holding-heart"></i>{{auth()->guard('web')->user()->isPatient() ? __('Received Donations') : __('My Donations')}}</a>
                    @endif
                    <a href="{{route('user.home.edit.profile')}}" class="ud-link @if(request()->routeIs('user.home.edit.profile')) ud-active @endif"><i class="fas fa-user-edit"></i>{{__('Edit Profile')}}</a>
                    <a href="{{route('user.home.change.password')}}" class="ud-link @if(request()->routeIs('user.home.change.password')) ud-active @endif"><i class="fas fa-lock"></i>{{__('Change Password')}}</a>
                    <a href="{{ route('user.logout') }}" class="ud-link ud-logout" style="margin-left:auto;" onclick="event.preventDefault();document.getElementById('logout_submit_btn').dispatchEvent(new MouseEvent('click'));">
                        <i class="fas fa-sign-out-alt"></i>{{ __('Logout') }}
                    </a>
                    <form id="logout-form" action="{{ route('user.logout') }}" method="POST" style="display: none;">
                        @csrf
                        <button id="logout_submit_btn" type="submit"></button>
                    </form>
                </nav>
                <div class="ud-body">
                    <div class="message-show margin-top-10">
                        <x-msg.success/>
                        <x-msg.error/>
                    </div>
                    @yield('section')
                </div>
            </div>
        </div>
    </section>
    <x-metamask-sim/>
@endsection

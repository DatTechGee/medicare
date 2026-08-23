@php
    $home_page_variant = isset($home_page) ? $home_page : get_static_option('home_page_variant');
    $isHome = request()->path() === '/';
@endphp

<style>
    .fr-topbar {
        background: #0b1224;
        padding: 7px 0;
        font-size: 12px;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }
    .fr-topbar .topbar-inner {
        display: flex; align-items: center; justify-content: space-between;
    }
    .fr-topbar .info-items {
        list-style: none; margin: 0; padding: 0; display: flex; gap: 22px; align-items: center;
    }
    .fr-topbar .info-items li {
        color: rgba(255,255,255,0.55); font-size: 12px; display: flex; align-items: center; gap: 6px;
        margin: 0; line-height: 1;
    }
    .fr-topbar .info-items li i { color: #2dd4bf; font-size: 11px; }
    .fr-topbar a { color: rgba(255,255,255,0.55); text-decoration: none; font-size: 12px; transition: color .2s; }
    .fr-topbar a:hover { color: #2dd4bf; }

    .fr-nav {
        position: sticky; top: 0; z-index: 1050;
        background: rgba(255,255,255,0.92);
        backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
        border-bottom: 1px solid rgba(15,23,42,0.07);
        box-shadow: 0 1px 0 rgba(15,23,42,0.02);
        transition: background .3s ease, box-shadow .3s ease, border-color .3s ease;
    }
    .fr-nav.dark-mode {
        background: rgba(10,10,26,0.95);
        border-bottom-color: rgba(255,255,255,0.06);
    }
    .fr-nav.dark-mode .logo-text { color: #e8e8f0; }
    .fr-nav.dark-mode .logo-sub { color: rgba(255,255,255,0.4); }
    .fr-nav.dark-mode .nav-links > li > a { color: rgba(255,255,255,0.7); }
    .fr-nav.dark-mode .nav-links > li > a:hover,
    .fr-nav.dark-mode .nav-links > li > a.active { color: #00D4AA; background: rgba(0,212,170,0.08); }
    .fr-nav.dark-mode .fr-search-btn { border-color: rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: rgba(255,255,255,0.6); }
    .fr-nav.dark-mode .fr-search-btn:hover { background: #00D4AA; border-color: #00D4AA; color: #fff; }
    .fr-nav.dark-mode .fr-login-link { color: rgba(255,255,255,0.7); }
    .fr-nav.dark-mode .fr-login-link:hover { color: #00D4AA; background: rgba(0,212,170,0.08); }
    .fr-nav.dark-mode .fr-dropdown { background: #111128; border-color: rgba(255,255,255,0.08); }
    .fr-nav.dark-mode .fr-dropdown::before { background: #111128; border-color: rgba(255,255,255,0.08); }
    .fr-nav.dark-mode .fr-dropdown a { color: rgba(255,255,255,0.7); }
    .fr-nav.dark-mode .fr-dropdown a:hover { background: rgba(0,212,170,0.1); color: #00D4AA; }
    .fr-nav.dark-mode .fr-dropdown a i { background: rgba(0,212,170,0.1); color: #00D4AA; }
    .fr-nav.dark-mode .fr-search-panel { background: #111128; border-bottom-color: rgba(255,255,255,0.08); }
    .fr-nav.dark-mode .fr-search-panel input { background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.1); color: #fff; }
    .fr-nav.dark-mode .fr-search-panel input::placeholder { color: rgba(255,255,255,0.3); }
    .fr-nav.dark-mode .fr-mobile-toggle span { background: #e8e8f0; }
    .fr-nav.dark-mode .fr-mobile-toggle { border-color: rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); }
    .fr-nav.scrolled { box-shadow: 0 10px 34px -12px rgba(2,6,23,0.18); }
    .fr-nav.dark-mode.scrolled { box-shadow: 0 10px 34px -12px rgba(0,0,0,0.5); }
    .fr-nav .nav-container {
        display: flex; align-items: center; justify-content: space-between;
        max-width: 1320px; margin: 0 auto; padding: 0 24px; height: 70px; gap: 20px;
    }
    .fr-nav .logo a { display: flex; align-items: center; gap: 11px; text-decoration: none; }
    .fr-nav .logo-icon {
        width: 41px; height: 41px; border-radius: 13px;
        background: linear-gradient(135deg, #0d9488, #0f766e);
        color: #fff; display: flex; align-items: center; justify-content: center; font-size: 17px;
        box-shadow: 0 6px 16px -4px rgba(13,148,136,0.5);
    }
    .fr-nav .logo-text {
        font-size: 21.5px; font-weight: 800; letter-spacing: -0.4px; color: #0f172a; line-height: 1;
    }
    .fr-nav .logo-text span {
        background: linear-gradient(135deg, #0d9488, #14b8a6);
        -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;
    }
    .fr-nav .logo-sub {
        display: block; font-size: 9.5px; font-weight: 600; letter-spacing: 1.6px;
        text-transform: uppercase; color: #94a3b8; margin-top: 3px;
    }

    .fr-nav .nav-links {
        display: flex; align-items: center; gap: 4px; list-style: none; margin: 0; padding: 0;
    }
    .fr-nav .nav-links > li { position: relative; }
    .fr-nav .nav-links > li > a {
        display: block; padding: 9px 14px; border-radius: 9px;
        color: #334155; font-weight: 600; font-size: 14px; text-decoration: none;
        transition: all .18s ease; white-space: nowrap;
    }
    .fr-nav .nav-links > li > a i.chev { font-size: 9px; margin-left: 4px; opacity: .55; }
    .fr-nav .nav-links > li > a:hover,
    .fr-nav .nav-links > li > a.active { color: #0d9488; background: rgba(13,148,136,0.07); }

    /* dropdown */
    .fr-nav .has-dropdown:hover .fr-dropdown { opacity: 1; visibility: visible; transform: translateY(0); }
    .fr-dropdown {
        position: absolute; top: calc(100% + 8px); left: 0; min-width: 230px;
        background: #fff; border: 1px solid rgba(15,23,42,0.08); border-radius: 14px;
        box-shadow: 0 24px 50px -12px rgba(2,6,23,0.25); padding: 8px;
        opacity: 0; visibility: hidden; transform: translateY(8px);
        transition: all .22s cubic-bezier(.4,0,.2,1);
    }
    .fr-dropdown::before {
        content: ''; position: absolute; top: -6px; left: 28px;
        width: 12px; height: 12px; background: #fff; border-left: 1px solid rgba(15,23,42,0.08);
        border-top: 1px solid rgba(15,23,42,0.08); transform: rotate(45deg);
    }
    .fr-dropdown a {
        display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 9px;
        color: #334155; font-size: 13.5px; font-weight: 500; text-decoration: none; transition: all .15s;
    }
    .fr-dropdown a:hover { background: rgba(13,148,136,0.06); color: #0d9488; }
    .fr-dropdown a i { width: 30px; height: 30px; border-radius: 8px; background: rgba(13,148,136,0.08); color: #0d9488; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; flex-shrink: 0; }

    .fr-nav .nav-right { display: flex; align-items: center; gap: 10px; }

    .fr-search-btn {
        width: 38px; height: 38px; border-radius: 10px; border: 1px solid rgba(15,23,42,0.09);
        background: #f8fafc; color: #475569; cursor: pointer; display: flex; align-items: center;
        justify-content: center; font-size: 13px; transition: all .2s;
    }
    .fr-search-btn:hover { background: #0d9488; color: #fff; border-color: #0d9488; }

    .fr-search-panel {
        display: none; position: absolute; top: 100%; left: 0; right: 0;
        background: #fff; border-bottom: 1px solid rgba(15,23,42,0.08); box-shadow: 0 24px 40px -18px rgba(2,6,23,0.25);
    }
    .fr-search-panel.open { display: block; }
    .fr-search-panel form { max-width: 720px; margin: 0 auto; padding: 22px 24px; display: flex; gap: 10px; }
    .fr-search-panel input {
        flex: 1; height: 48px; border-radius: 12px; border: 1.5px solid rgba(15,23,42,0.12);
        padding: 0 18px; font-size: 14.5px; outline: none; transition: all .2s; background: #f8fafc;
    }
    .fr-search-panel input:focus { border-color: #0d9488; background: #fff; box-shadow: 0 0 0 4px rgba(13,148,136,0.1); }
    .fr-search-panel button {
        height: 48px; padding: 0 26px; border-radius: 12px; border: none;
        background: linear-gradient(135deg, #0d9488, #0f766e); color: #fff; font-weight: 700; cursor: pointer;
    }

    .fr-login-link {
        display: inline-flex; align-items: center; gap: 7px; padding: 9px 14px; border-radius: 10px;
        color: #334155; font-weight: 700; font-size: 13.5px; text-decoration: none; white-space: nowrap; transition: all .18s;
    }
    .fr-login-link:hover { color: #0d9488; background: rgba(13,148,136,0.06); }

    .fr-donate-btn {
        display: inline-flex; align-items: center; gap: 8px;
        background: linear-gradient(135deg, #0d9488, #0f766e); color: #fff;
        padding: 11px 24px; border-radius: 12px; font-weight: 700; font-size: 14px;
        text-decoration: none; white-space: nowrap;
        box-shadow: 0 8px 20px -6px rgba(13,148,136,0.55), inset 0 1px 0 rgba(255,255,255,0.18);
        transition: all .25s cubic-bezier(.4,0,.2,1);
    }
    .fr-donate-btn:hover { transform: translateY(-2px); box-shadow: 0 14px 28px -8px rgba(13,148,136,0.65), inset 0 1px 0 rgba(255,255,255,0.18); color: #fff; }
    .fr-donate-btn i { animation: frHeart 1.6s ease infinite; }
    @keyframes frHeart { 0%,100%{transform:scale(1)} 12%{transform:scale(1.18)} 24%{transform:scale(1)} 36%{transform:scale(1.12)} 48%{transform:scale(1)} }

    .fr-mobile-toggle {
        display: none; width: 42px; height: 42px; border-radius: 10px; border: 1px solid rgba(15,23,42,0.09);
        background: #f8fafc; cursor: pointer; flex-direction: column; align-items: center; justify-content: center; gap: 5px;
    }
    .fr-mobile-toggle span { display: block; width: 19px; height: 2px; background: #0f172a; border-radius: 2px; transition: all .3s; }
    .fr-mobile-toggle.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
    .fr-mobile-toggle.open span:nth-child(2) { opacity: 0; }
    .fr-mobile-toggle.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

    @media (max-width: 1280px) {
        .fr-nav .nav-links > li > a { padding: 9px 10px; font-size: 13.5px; }
        .fr-donate-btn { padding: 10px 18px; }
    }
    @media (max-width: 991px) {
        .fr-topbar .left-content { display: none; }
        .fr-nav .nav-container { height: 64px; padding: 0 16px; }
        .fr-mobile-toggle { display: flex; }
        .fr-login-link { display: none; }
        .fr-nav .nav-links {
            display: none; position: absolute; top: 100%; left: 0; right: 0;
            flex-direction: column; align-items: stretch; gap: 2px;
            background: #fff; border-bottom: 1px solid rgba(15,23,42,0.08);
            box-shadow: 0 24px 40px -18px rgba(2,6,23,0.25); padding: 12px 16px 18px;
            max-height: calc(100vh - 64px); overflow-y: auto;
        }
        .fr-nav .nav-links.open { display: flex; }
        .fr-nav .nav-links > li > a { padding: 12px 14px; font-size: 15px; border-radius: 10px; }
        .fr-dropdown {
            position: static; opacity: 1; visibility: visible; transform: none;
            box-shadow: none; border: none; padding: 0 0 0 14px; min-width: 0;
        }
        .fr-dropdown::before { display: none; }
        .fr-search-panel form { padding: 16px; }
    }
</style>

<div class="header-variant-{{$home_page_variant}} @if(!$isHome) inner-page @endif" style="position:relative">
    <div class="fr-topbar">
        <div class="container">
            <div class="topbar-inner">
                <div class="left-content">
                    <ul class="info-items">
                        <li><i class="fas fa-shield-alt"></i> {{__('Fraud Prevention + Blockchain Transparency')}}</li>
                        <li><i class="fas fa-hospital"></i> {{__('Hospital Verified Campaigns')}}</li>
                    </ul>
                </div>
                <div class="right-content">
                    <ul class="info-items">
                        <x-front-user-login-li/>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <nav class="fr-nav" id="frNav" style="position:sticky">
        <div class="nav-container">
            <div class="logo">
                <a href="{{url('/')}}">
                    <div class="logo-icon"><i class="fas fa-heartbeat"></i></div>
                    <div>
                        <span class="logo-text">Medi<span>Fund</span></span>
                        <span class="logo-sub">{{__('Secure Medical Giving')}}</span>
                    </div>
                </a>
            </div>

            <ul class="nav-links" id="navLinks">
                <li><a href="{{url('/')}}" class="{{request()->routeIs('homepage') ? 'active' : ''}}">{{__('Home')}}</a></li>
                <li>
                    <a href="{{route('frontend.donations')}}" class="{{request()->routeIs('frontend.donations*') ? 'active' : ''}}">
                        {{__('Campaigns')}} <i class="fas fa-chevron-down chev"></i>
                    </a>
                    <div class="fr-dropdown">
                        <a href="{{route('frontend.donations')}}"><i class="fas fa-list"></i> {{__('All Campaigns')}}</a>
                        <a href="{{route('frontend.donation.search')}}"><i class="fas fa-magnifying-glass"></i> {{__('Search Campaigns')}}</a>
                        <a href="{{url('/blockchain/explorer')}}"><i class="fas fa-cubes"></i> {{__('Blockchain Explorer')}}</a>
                    </div>
                </li>
                <li>
                    <a href="{{url('/#how-it-works')}}">{{__('How It Works')}} <i class="fas fa-chevron-down chev"></i></a>
                    <div class="fr-dropdown">
                        <a href="{{url('/#how-it-works')}}"><i class="fas fa-circle-question"></i> {{__('How MediFund Works')}}</a>
                        <a href="{{route('user.campaign.new')}}"><i class="fas fa-bullhorn"></i> {{__('Start a Campaign')}}</a>
                        <a href="{{route('user.login')}}"><i class="fas fa-wallet"></i> {{__('Get Your Account')}}</a>
                    </div>
                </li>
                <li>
                    <a href="{{url('/#trust')}}">{{__('Trust & Safety')}} <i class="fas fa-chevron-down chev"></i></a>
                    <div class="fr-dropdown">
                        <a href="{{url('/#trust')}}"><i class="fas fa-shield-halved"></i> {{__('Fraud Prevention')}}</a>
                        <a href="{{url('/blockchain/explorer')}}"><i class="fas fa-cube"></i> {{__('On-chain Transparency')}}</a>
                        <a href="{{url('/#escrow')}}"><i class="fas fa-vault"></i> {{__('Escrow & Milestones')}}</a>
                    </div>
                </li>
            </ul>

            <div class="nav-right">
                <button type="button" class="fr-search-btn" onclick="document.getElementById('frSearchPanel').classList.toggle('open')" aria-label="Search">
                    <i class="fas fa-magnifying-glass"></i>
                </button>

                <x-frontend.wallet-connect />

                @php
                    $user = auth()->guard('web')->user();
                    $admin = auth()->guard('admin')->user();
                @endphp

                @if($user || $admin)
                    <a href="{{$admin ? route('admin.home') : route('user.home')}}" class="fr-login-link">
                        <i class="fas fa-table-columns"></i> {{__('Dashboard')}}
                    </a>
                @else
                    <a href="{{route('user.login')}}" class="fr-login-link"><i class="far fa-circle-user"></i> {{__('Login')}}</a>
                @endif

                <a href="{{route('frontend.donations')}}" class="fr-donate-btn">
                    <i class="fas fa-heart"></i> {{__('Donate')}}
                </a>

                <button class="fr-mobile-toggle" id="frMobileToggle" onclick="this.classList.toggle('open');document.getElementById('navLinks').classList.toggle('open')" aria-label="Menu">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>

        <div class="fr-search-panel" id="frSearchPanel">
            <form action="{{route('frontend.donation.search')}}" method="GET">
                <input type="text" name="search" placeholder="{{__('Search campaigns by title, hospital or condition...')}}" autofocus>
                <button type="submit"><i class="fas fa-magnifying-glass"></i>&nbsp; {{__('Search')}}</button>
            </form>
        </div>
    </nav>
</div>

<script>
(function(){
    var nav = document.getElementById('frNav');
    var isHome = {{ $isHome ? 'true' : 'false' }};

    if (isHome) { nav.classList.add('dark-mode'); }

    window.addEventListener('scroll', function() {
        if (window.scrollY > 10) { nav.classList.add('scrolled'); } else { nav.classList.remove('scrolled'); }
    });

    document.addEventListener('click', function(e){
        var panel = document.getElementById('frSearchPanel');
        if (panel && !e.target.closest('#frSearchPanel') && !e.target.closest('.fr-search-btn')) panel.classList.remove('open');
    });
    document.querySelectorAll('#navLinks a').forEach(function(a){
        a.addEventListener('click', function(){
            document.getElementById('navLinks').classList.remove('open');
            var t = document.getElementById('frMobileToggle');
            if (t) t.classList.remove('open');
        });
    });
})();
</script>

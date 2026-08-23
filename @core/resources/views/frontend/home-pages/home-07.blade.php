@include('frontend.partials.navbar')

<style>
:root {
    --bg-dark: #0a0a1a;
    --bg-card: #111128;
    --bg-card-alt: #16162e;
    --border: #1e1e3a;
    --border-light: #2a2a4a;
    --purple: #627EEA;
    --purple-dark: #4B6CEB;
    --green: #00D4AA;
    --gold: #FFB800;
    --red: #ff6b6b;
    --text: #e8e8f0;
    --text-dim: #8b8ba3;
    --text-body: #b8b8d0;
}

/* ===== HERO SECTION ===== */
.blockchain-hero {
    background: linear-gradient(160deg, #0a0a1a 0%, #0d1117 30%, #111128 70%, #0a0a1a 100%);
    min-height: 92vh;
    display: flex;
    align-items: center;
    position: relative;
    overflow: hidden;
    padding-top: 80px;
}

.blockchain-hero::before {
    content: '';
    position: absolute;
    top: -30%;
    left: -20%;
    width: 60%;
    height: 80%;
    background: radial-gradient(circle, rgba(98,126,234,0.08) 0%, transparent 70%);
    border-radius: 50%;
    animation: heroPulse 8s ease-in-out infinite;
}

.blockchain-hero::after {
    content: '';
    position: absolute;
    bottom: -20%;
    right: -10%;
    width: 50%;
    height: 70%;
    background: radial-gradient(circle, rgba(0,212,170,0.06) 0%, transparent 70%);
    border-radius: 50%;
    animation: heroPulse 10s ease-in-out infinite reverse;
}

@keyframes heroPulse {
    0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.6; }
    50% { transform: translate(30px, -20px) scale(1.1); opacity: 1; }
}

.hero-grid-bg {
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(98,126,234,0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(98,126,234,0.03) 1px, transparent 1px);
    background-size: 60px 60px;
    z-index: 0;
}

.hero-content {
    position: relative;
    z-index: 2;
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(98,126,234,0.12);
    border: 1px solid rgba(98,126,234,0.3);
    border-radius: 50px;
    padding: 8px 20px;
    margin-bottom: 25px;
    font-size: 13px;
    color: var(--purple);
    font-weight: 600;
    letter-spacing: 0.5px;
}

.hero-badge .dot {
    width: 8px;
    height: 8px;
    background: var(--green);
    border-radius: 50%;
    animation: dotPulse 2s ease-in-out infinite;
}

@keyframes dotPulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.3); }
}

.hero-title {
    font-size: 52px;
    font-weight: 800;
    color: var(--text);
    line-height: 1.15;
    margin-bottom: 20px;
    letter-spacing: -0.5px;
}

.hero-title .highlight {
    background: linear-gradient(135deg, var(--purple), var(--green));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-subtitle {
    font-size: 18px;
    color: var(--text-dim);
    line-height: 1.7;
    margin-bottom: 35px;
    max-width: 520px;
}

.hero-stats {
    display: flex;
    gap: 40px;
    margin-top: 50px;
    padding-top: 30px;
    border-top: 1px solid var(--border);
}

.hero-stat-value {
    font-size: 32px;
    font-weight: 800;
    color: var(--text);
}

.hero-stat-value.green { color: var(--green); }
.hero-stat-value.purple { color: var(--purple); }
.hero-stat-value.gold { color: var(--gold); }

.hero-stat-label {
    font-size: 13px;
    color: var(--text-dim);
    margin-top: 4px;
}

.hero-visual {
    position: relative;
    z-index: 2;
    text-align: right;
}

.hero-chain-card {
    background: rgba(17, 17, 40, 0.85);
    backdrop-filter: blur(20px);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 30px;
    display: inline-block;
    text-align: left;
    box-shadow: 0 20px 60px rgba(0,0,0,0.4);
}

.chain-row {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 12px 0;
    border-bottom: 1px solid var(--border);
}

.chain-row:last-child { border-bottom: none; }

.chain-icon {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}

.chain-icon.blue { background: rgba(98,126,234,0.15); color: var(--purple); }
.chain-icon.green { background: rgba(0,212,170,0.15); color: var(--green); }
.chain-icon.gold { background: rgba(255,184,0,0.15); color: var(--gold); }

.chain-detail h5 {
    font-size: 14px;
    color: var(--text);
    margin: 0;
    font-weight: 600;
}

.chain-detail small {
    color: var(--text-dim);
    font-size: 12px;
}

/* ===== SECTION COMMONS ===== */
.section-dark {
    background: var(--bg-dark);
    padding: 90px 0;
    position: relative;
}

.section-dark-alt {
    background: var(--bg-card);
    padding: 90px 0;
    position: relative;
}

.section-title-dark {
    text-align: center;
    margin-bottom: 60px;
}

.section-title-dark .badge-tag {
    display: inline-block;
    background: rgba(98,126,234,0.12);
    border: 1px solid rgba(98,126,234,0.25);
    color: var(--purple);
    font-size: 12px;
    font-weight: 600;
    padding: 6px 16px;
    border-radius: 50px;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 15px;
}

.section-title-dark h2 {
    font-size: 36px;
    font-weight: 800;
    color: var(--text);
    margin-bottom: 15px;
}

.section-title-dark p {
    font-size: 16px;
    color: var(--text-dim);
    max-width: 600px;
    margin: 0 auto;
}

/* ===== HOW IT WORKS ===== */
.step-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 35px 25px;
    text-align: center;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.step-card:hover {
    border-color: var(--purple);
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(98,126,234,0.1);
}

.step-number {
    position: absolute;
    top: 15px;
    right: 20px;
    font-size: 48px;
    font-weight: 900;
    color: rgba(98,126,234,0.08);
}

.step-icon {
    width: 65px;
    height: 65px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    margin: 0 auto 20px;
}

.step-icon.blue { background: rgba(98,126,234,0.12); color: var(--purple); }
.step-icon.green { background: rgba(0,212,170,0.12); color: var(--green); }
.step-icon.gold { background: rgba(255,184,0,0.12); color: var(--gold); }
.step-icon.red { background: rgba(255,107,107,0.12); color: var(--red); }

.step-card h4 {
    font-size: 17px;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 10px;
}

.step-card p {
    font-size: 13px;
    color: var(--text-dim);
    line-height: 1.6;
}

/* ===== CAMPAIGN CARDS ===== */
.campaign-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s ease;
    margin-bottom: 25px;
}

.campaign-card:hover {
    border-color: var(--purple);
    transform: translateY(-4px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.3);
}

.campaign-card .thumb {
    position: relative;
    overflow: hidden;
    height: 220px;
}

.campaign-card .thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.campaign-card:hover .thumb img {
    transform: scale(1.05);
}

.campaign-card .thumb .hospital-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(transparent, rgba(0,0,0,0.7));
    padding: 12px 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.campaign-card .thumb .hospital-overlay .hospital-icon {
    width: 28px;
    height: 28px;
    background: rgba(98,126,234,0.9);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    color: #fff;
    flex-shrink: 0;
}

.campaign-card .thumb .hospital-overlay .hospital-name {
    font-size: 11px;
    color: rgba(255,255,255,0.9);
    font-weight: 600;
}

.campaign-card .thumb .verified-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    display: flex;
    align-items: center;
    gap: 5px;
    background: rgba(16,185,129,0.9);
    backdrop-filter: blur(10px);
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    padding: 5px 10px;
    border-radius: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.campaign-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    display: flex;
    gap: 6px;
}

.campaign-badge span {
    background: rgba(10,10,26,0.8);
    backdrop-filter: blur(10px);
    color: var(--text);
    font-size: 11px;
    font-weight: 600;
    padding: 5px 10px;
    border-radius: 8px;
}

.campaign-badge span.featured { background: rgba(98,126,234,0.85); color: #fff; }
.campaign-badge span.crypto {
    background: rgba(0,212,170,0.85);
    color: #000;
}

.campaign-body {
    padding: 22px;
}

.campaign-progress-wrap {
    margin-bottom: 15px;
}

.campaign-progress-bar {
    width: 100%;
    height: 6px;
    background: var(--border);
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 10px;
}

.campaign-progress-fill {
    height: 100%;
    border-radius: 10px;
    background: linear-gradient(90deg, var(--purple), var(--green));
    transition: width 1s ease;
}

.campaign-amounts {
    display: flex;
    justify-content: space-between;
}

.campaign-raised {
    font-size: 15px;
    font-weight: 700;
    color: var(--green);
}

.campaign-goal {
    font-size: 13px;
    color: var(--text-dim);
}

.campaign-body h3 {
    font-size: 17px;
    font-weight: 700;
    margin-bottom: 8px;
}

.campaign-body h3 a {
    color: var(--text);
    text-decoration: none;
    transition: color 0.3s;
}

.campaign-body h3 a:hover {
    color: var(--purple);
}

.campaign-body .excerpt {
    font-size: 13px;
    color: var(--text-dim);
    line-height: 1.6;
    margin-bottom: 18px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.campaign-donate-btn {
    display: block;
    text-align: center;
    background: linear-gradient(135deg, var(--purple), var(--purple-dark));
    color: #fff;
    padding: 11px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 14px;
    text-decoration: none;
    transition: all 0.3s;
}

.campaign-donate-btn:hover {
    box-shadow: 0 6px 20px rgba(98,126,234,0.35);
    transform: translateY(-1px);
    color: #fff;
    text-decoration: none;
}

/* ===== CATEGORIES ===== */
.category-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 30px 20px;
    text-align: center;
    transition: all 0.3s;
    text-decoration: none;
    display: block;
}

.category-card:hover {
    border-color: var(--purple);
    transform: translateY(-4px);
    box-shadow: 0 12px 35px rgba(0,0,0,0.3);
}

.category-icon {
    width: 60px;
    height: 60px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin: 0 auto 15px;
    background: rgba(98,126,234,0.1);
    color: var(--purple);
}

.category-card h5 {
    font-size: 15px;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 5px;
}

.category-card small {
    color: var(--text-dim);
    font-size: 12px;
}

/* ===== CTA SECTION ===== */
.cta-section {
    background: linear-gradient(135deg, var(--purple) 0%, #4B6CEB 50%, var(--green) 100%);
    padding: 80px 0;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.cta-section::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    z-index: 0;
}

.cta-section h2 {
    font-size: 38px;
    font-weight: 800;
    color: #fff;
    margin-bottom: 15px;
    position: relative;
    z-index: 1;
}

.cta-section p {
    font-size: 17px;
    color: rgba(255,255,255,0.85);
    margin-bottom: 30px;
    position: relative;
    z-index: 1;
}

.cta-btn {
    display: inline-block;
    background: #fff;
    color: var(--purple);
    padding: 14px 40px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 16px;
    text-decoration: none;
    transition: all 0.3s;
    position: relative;
    z-index: 1;
}

.cta-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    color: var(--purple-dark);
    text-decoration: none;
}

/* ===== AFRICAN PRIDE SECTION ===== */
.african-pride {
    background: linear-gradient(160deg, #0d1117, #111128, #0a0a1a);
    padding: 90px 0;
    position: relative;
    overflow: hidden;
}

.african-pride::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 40%;
    height: 100%;
    background: radial-gradient(circle at 70% 50%, rgba(255,184,0,0.04) 0%, transparent 60%);
}

.pride-content h2 {
    font-size: 36px;
    font-weight: 800;
    color: var(--text);
    margin-bottom: 20px;
    line-height: 1.2;
}

.pride-content h2 .gold { color: var(--gold); }

.pride-content p {
    font-size: 16px;
    color: var(--text-dim);
    line-height: 1.8;
    margin-bottom: 25px;
}

.pride-features {
    list-style: none;
    padding: 0;
    margin: 0;
}

.pride-features li {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    margin-bottom: 18px;
}

.pride-features li .icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
    background: rgba(0,212,170,0.12);
    color: var(--green);
}

.pride-features li .text h5 {
    font-size: 15px;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 3px;
}

.pride-features li .text small {
    font-size: 13px;
    color: var(--text-dim);
}

.pride-image {
    text-align: center;
    position: relative;
}

.pride-image-frame {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 20px;
    overflow: hidden;
    position: relative;
}

.pride-image-frame img {
    width: 100%;
    height: 400px;
    object-fit: cover;
}

.pride-image-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 25px;
    background: linear-gradient(transparent, rgba(0,0,0,0.8));
}

.pride-image-overlay h4 {
    color: #fff;
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 5px;
}

.pride-image-overlay p {
    color: rgba(255,255,255,0.7);
    font-size: 13px;
    margin: 0;
}

/* ===== IMPACT COUNTER ===== */
.impact-counter {
    background: var(--bg-card);
    border-top: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
    padding: 60px 0;
}

.counter-item {
    text-align: center;
    padding: 15px;
}

.counter-value {
    font-size: 42px;
    font-weight: 900;
    margin-bottom: 5px;
}

.counter-value.green { color: var(--green); }
.counter-value.purple { color: var(--purple); }
.counter-value.gold { color: var(--gold); }
.counter-value.blue { color: #4facfe; }

.counter-label {
    font-size: 14px;
    color: var(--text-dim);
    font-weight: 500;
}

/* ===== FOOTER DARK ===== */
.footer-dark-custom {
    background: #060612;
    border-top: 1px solid var(--border);
    padding: 60px 0 30px;
}

.footer-dark-custom h5 {
    color: var(--text);
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 20px;
}

.footer-dark-custom p,
.footer-dark-custom a {
    color: var(--text-dim);
    font-size: 14px;
    text-decoration: none;
    transition: color 0.3s;
}

.footer-dark-custom a:hover {
    color: var(--purple);
}

.footer-dark-custom ul {
    list-style: none;
    padding: 0;
}

.footer-dark-custom ul li {
    margin-bottom: 10px;
}

.footer-dark-custom ul li a {
    color: var(--text-dim);
    font-size: 14px;
}

.footer-dark-custom .copyright {
    border-top: 1px solid var(--border);
    margin-top: 40px;
    padding-top: 25px;
    text-align: center;
    color: var(--text-dim);
    font-size: 13px;
}

.footer-dark-custom .social-links {
    display: flex;
    gap: 12px;
    margin-top: 15px;
}

.footer-dark-custom .social-links a {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: var(--bg-card-alt);
    border: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-dim);
    transition: all 0.3s;
}

.footer-dark-custom .social-links a:hover {
    background: var(--purple);
    color: #fff;
    border-color: var(--purple);
}

/* ===== SCROLL REVEAL ===== */
.reveal { opacity: 0; transform: translateY(30px); transition: all 0.7s cubic-bezier(0.4,0,0.2,1); }
.reveal.visible { opacity: 1; transform: translateY(0); }
.reveal-delay-1 { transition-delay: 0.1s; }
.reveal-delay-2 { transition-delay: 0.2s; }
.reveal-delay-3 { transition-delay: 0.3s; }
.reveal-delay-4 { transition-delay: 0.4s; }

/* Hero entrance */
.hero-content { animation: heroSlideIn 0.8s cubic-bezier(0.4,0,0.2,1) both; }
.hero-visual { animation: heroSlideIn 0.8s cubic-bezier(0.4,0,0.2,1) 0.2s both; }
@keyframes heroSlideIn { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

/* Card hover glow */
.campaign-card:hover { box-shadow: 0 20px 50px rgba(98,126,234,0.15); }
.step-card:hover { box-shadow: 0 20px 50px rgba(98,126,234,0.12); }
.category-card:hover { box-shadow: 0 15px 40px rgba(98,126,234,0.12); }

/* Floating animation for chain card */
.hero-chain-card { animation: floatCard 4s ease-in-out infinite; }
@keyframes floatCard { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }

/* Counter number animation */
.counter-value { transition: all 0.3s; }

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .hero-title { font-size: 32px; }
    .hero-stats { flex-direction: column; gap: 20px; }
    .hero-visual { display: none; }
    .section-title-dark h2 { font-size: 28px; }
    .counter-value { font-size: 32px; }
    .cta-section h2 { font-size: 28px; }
}
</style>

<!-- ===== HERO ===== -->
<section class="blockchain-hero">
    <div class="hero-grid-bg"></div>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-content">
                    <div class="hero-badge">
                        <span class="dot"></span>
                        {{__('Fraud Prevention + Blockchain Transparency')}}
                    </div>
                    <h1 class="hero-title">
                        {{__('Transparent Crowdfunding for ')}}
                        <span class="highlight">{{__('Medical Bills')}}</span>
                    </h1>
                    <p class="hero-subtitle">
                        {{__('A decentralised application that helps patients raise funds for medical bills with fraud prevention, hospital verification, and blockchain transparency. Every transaction is permanently recorded on-chain.')}}
                    </p>
                    <div class="btn-wrapper" style="display:flex;gap:15px;flex-wrap:wrap;">
                        <a href="{{route('frontend.donations')}}" class="campaign-donate-btn" style="padding: 14px 35px; font-size: 15px;">
                            <i class="fas fa-heart"></i> {{__('Browse Campaigns')}}
                        </a>
                        <a href="#how-it-works" style="display:inline-flex;align-items:center;gap:8px;background:rgba(98,126,234,0.1);border:1px solid rgba(98,126,234,0.3);color:var(--purple);padding:14px 30px;border-radius:12px;font-weight:700;font-size:15px;text-decoration:none;transition:all 0.3s;">
                            <i class="fas fa-play-circle"></i> {{__('How It Works')}}
                        </a>
                    </div>
                    <div class="hero-stats">
                        <div>
                            <div class="hero-stat-value green">
                                @php $totalRaised = \App\CauseLogs::where('status','complete')->sum('amount'); @endphp
                                {{amount_with_currency_symbol($totalRaised)}}
                            </div>
                            <div class="hero-stat-label">{{__('Total Raised')}}</div>
                        </div>
                        <div>
                            <div class="hero-stat-value purple">
                                @php $totalCampaigns = \App\Cause::where('status','publish')->count(); @endphp
                                {{$totalCampaigns}}+
                            </div>
                            <div class="hero-stat-label">{{__('Active Campaigns')}}</div>
                        </div>
                        <div>
                            <div class="hero-stat-value gold">
                                @php $totalDonors = \App\CauseLogs::where('status','complete')->count(); @endphp
                                {{$totalDonors}}+
                            </div>
                            <div class="hero-stat-label">{{__('Donors')}}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-visual">
                    <div class="hero-chain-card">
                        <div style="margin-bottom:18px;">
                            <span style="color:var(--green);font-size:13px;font-weight:600;"><i class="fas fa-shield-alt"></i> {{__('Blockchain Secured')}}</span>
                        </div>
                        <div class="chain-row">
                            <div class="chain-icon green"><i class="fas fa-check-circle"></i></div>
                            <div class="chain-detail">
                                <h5>{{__('Donation Confirmed')}}</h5>
                                <small>0x8f3a...e4b2 &bull; 2 min ago</small>
                            </div>
                        </div>
                        <div class="chain-row">
                            <div class="chain-icon blue"><i class="fas fa-link"></i></div>
                            <div class="chain-detail">
                                <h5>{{__('Medical Campaign Funded')}}</h5>
                                <small>Block #18,442,109 &bull; 5 min ago</small>
                            </div>
                        </div>
                        <div class="chain-row">
                            <div class="chain-icon gold"><i class="fas fa-hospital"></i></div>
                            <div class="chain-detail">
                                <h5>{{__('Hospital Verified')}}</h5>
                                <small>Korle-Bu Teaching Hospital</small>
                            </div>
                        </div>
                        <div class="chain-row">
                            <div class="chain-icon green"><i class="fas fa-user-check"></i></div>
                            <div class="chain-detail">
                                <h5>{{__('Patient Identity Verified')}}</h5>
                                <small>Fraud Score: <span style="color:var(--green);">12/100</span></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== IMPACT COUNTER ===== -->
<section class="impact-counter">
    <div class="container">
        <div class="row">
            <div class="col-6 col-lg-3">
                <div class="counter-item">
                    <div class="counter-value green">{{$totalCampaigns}}</div>
                    <div class="counter-label">{{__('Campaigns')}}</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="counter-item">
                    <div class="counter-value purple">{{$totalDonors}}</div>
                    <div class="counter-label">{{__('Donations')}}</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="counter-item">
                    <div class="counter-value gold">
                        @php $txCount = \App\BlockchainTransaction::where('status','confirmed')->count(); @endphp
                        {{$txCount}}
                    </div>
                    <div class="counter-label">{{__('Blockchain Txns')}}</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="counter-item">
                    <div class="counter-value blue">100%</div>
                    <div class="counter-label">{{__('Transparent')}}</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== HOW IT WORKS ===== -->
<section class="section-dark" id="how-it-works">
    <div class="container">
        <div class="section-title-dark">
            <div class="badge-tag">{{__('How It Works')}}</div>
            <h2>{{__('Three Steps to Transparent Medical Funding')}}</h2>
            <p>{{__('Our decentralised platform combines fraud detection, hospital verification, and blockchain technology to ensure every medical campaign is genuine and every donation is transparently managed.')}}</p>
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="step-card">
                    <div class="step-number">01</div>
                    <div class="step-icon blue"><i class="fas fa-file-medical"></i></div>
                    <h4>{{__('Submit Campaign')}}</h4>
                    <p>{{__('Patients submit medical crowdfunding requests with supporting documents. The fraud detection engine and hospital verification ensure only genuine campaigns go live.')}}</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="step-card">
                    <div class="step-number">02</div>
                    <div class="step-icon green"><i class="fas fa-lock"></i></div>
                    <h4>{{__('Secure Donations')}}</h4>
                    <p>{{__('Donors contribute through our blockchain demo system. Funds are held in escrow, not sent directly to campaign owners, ensuring safe custody until verified.')}}</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="step-card">
                    <div class="step-number">03</div>
                    <div class="step-icon gold"><i class="fas fa-unlock-alt"></i></div>
                    <h4>{{__('Milestone Release')}}</h4>
                    <p>{{__('Funds are released from escrow only when milestone proof (hospital receipts, invoices) is submitted and verified, ensuring donations reach genuine medical needs.')}}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== FEATURED CAMPAIGNS ===== -->
@if(!empty($feature_cause) && count($feature_cause) > 0)
<section class="section-dark-alt">
    <div class="container">
        <div class="section-title-dark">
            <div class="badge-tag">{{__('Featured')}}</div>
            <h2>{{__('Featured Medical Campaigns')}}</h2>
            <p>{{__('Urgent medical campaigns that need immediate support. Funds are held in escrow and released upon verified milestone proof.')}}</p>
        </div>
        <div class="row">
            @foreach($feature_cause->take(3) as $campaign)
                <div class="col-lg-4 col-md-6">
                    <div class="campaign-card">
                        <div class="thumb">
                            <a href="{{route('frontend.donations.single',$campaign->slug)}}">
                                {!! render_image_markup_by_attachment_id($campaign->image,'','grid') !!}
                            </a>
                            @if(!empty($campaign->hospital_name))
                            <div class="hospital-overlay">
                                <div class="hospital-icon"><i class="fas fa-hospital"></i></div>
                                <span class="hospital-name">{{ $campaign->hospital_name }}</span>
                            </div>
                            @endif
                            <div class="campaign-badge">
                                @if($campaign->featured === 'on')
                                    <span class="featured"><i class="fas fa-award"></i> {{__('Featured')}}</span>
                                @endif
                                <span class="crypto"><i class="fas fa-link"></i> {{__('Crypto')}}</span>
                            </div>
                            @if(!empty($campaign->wallet_verified))
                            <div class="verified-badge"><i class="fas fa-shield-halved"></i> {{__('Verified')}}</div>
                            @endif
                        </div>
                        <div class="campaign-body">
                            <div class="campaign-progress-wrap">
                                <div class="campaign-progress-bar">
                                    @php $pct = get_percentage($campaign->amount, $campaign->raised); @endphp
                                    <div class="campaign-progress-fill" style="width: {{$pct}}%"></div>
                                </div>
                                <div class="campaign-amounts">
                                    <span class="campaign-raised">{{amount_with_currency_symbol($campaign->raised ?? 0)}}</span>
                                    <span class="campaign-goal">{{__('of')}} {{amount_with_currency_symbol($campaign->amount)}}</span>
                                </div>
                            </div>
                            <h3><a href="{{route('frontend.donations.single',$campaign->slug)}}">{{$campaign->title}}</a></h3>
                            <p class="excerpt">{{Str::limit(strip_tags($campaign->excerpt ?: $campaign->cause_content), 100)}}</p>
                            <a href="{{route('blockchain.donate.form', $campaign->id)}}" class="campaign-donate-btn">
                                <i class="fas fa-heart"></i> {{__('Donate Now')}}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- ===== AFRICAN PRIDE SECTION ===== -->
<section class="african-pride">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="pride-content">
                    <h2>
                        {{__('Empowering ')}}
                        <span class="gold">{{__('African Communities')}}</span>
                        {{__('with Blockchain Trust')}}
                    </h2>
                    <p>
                        {{__('FundoRex is built for Africa. We combine the power of blockchain transparency with community-driven fundraising to ensure every donation reaches those who need it most.')}}
                    </p>
                    <ul class="pride-features">
                        <li>
                            <div class="icon"><i class="fas fa-hospital"></i></div>
                            <div class="text">
                                <h5>{{__('Hospital Verification')}}</h5>
                                <small>{{__('All medical campaigns are verified with partner hospitals across Africa')}}</small>
                            </div>
                        </li>
                        <li>
                            <div class="icon"><i class="fas fa-user-shield"></i></div>
                            <div class="text">
                                <h5>{{__('Patient Identity Protection')}}</h5>
                                <small>{{__('Patient data is secured while maintaining full transparency for donors')}}</small>
                            </div>
                        </li>
                        <li>
                            <div class="icon"><i class="fas fa-chart-line"></i></div>
                            <div class="text">
                                <h5>{{__('Real-Time Fraud Scoring')}}</h5>
                                <small>{{__('AI engine scores every campaign to protect donors from scams')}}</small>
                            </div>
                        </li>
                        <li>
                            <div class="icon"><i class="fas fa-globe-africa"></i></div>
                            <div class="text">
                                <h5>{{__('Pan-African Reach')}}</h5>
                                <small>{{__('Supporting communities from Lagos to Nairobi, Accra to Cairo')}}</small>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="pride-image">
                    <div class="pride-image-frame">
                        <div style="background: linear-gradient(135deg, var(--bg-card), var(--bg-card-alt)); height: 400px; display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 20px;">
                            <div style="font-size: 80px; opacity: 0.8;">🌍</div>
                            <div style="text-align: center; padding: 0 30px;">
                                <h4 style="color: var(--text); font-size: 22px; font-weight: 700; margin-bottom: 8px;">{{__('Built for Africa')}}</h4>
                                <p style="color: var(--text-dim); font-size: 14px; margin: 0;">{{__('Medical crowdfunding with blockchain transparency and fraud prevention for African communities')}}</p>
                            </div>
                            <div style="display: flex; gap: 10px; margin-top: 10px;">
                                <span style="background: rgba(98,126,234,0.15); color: var(--purple); padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600;">Ghana</span>
                                <span style="background: rgba(0,212,170,0.15); color: var(--green); padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600;">Nigeria</span>
                                <span style="background: rgba(255,184,0,0.15); color: var(--gold); padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600;">Kenya</span>
                                <span style="background: rgba(255,107,107,0.15); color: var(--red); padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600;">Egypt</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== RECENT CAMPAIGNS ===== -->
@if(!empty($all_recent_donation) && count($all_recent_donation) > 0)
<section class="section-dark">
    <div class="container">
        <div class="section-title-dark">
            <div class="badge-tag">{{__('Latest')}}</div>
            <h2>{{__('Recent Medical Campaigns')}}</h2>
            <p>{{__('Support patients with genuine medical needs. Every donation is securely held in escrow and released only upon verified milestone proof.')}}</p>
        </div>
        <div class="row">
            @foreach($all_recent_donation as $campaign)
                <div class="col-lg-4 col-md-6">
                    <div class="campaign-card">
                        <div class="thumb">
                            <a href="{{route('frontend.donations.single',$campaign->slug)}}">
                                {!! render_image_markup_by_attachment_id($campaign->image,'','grid') !!}
                            </a>
                            <div class="campaign-badge">
                                @if($campaign->featured === 'on')
                                    <span class="featured"><i class="fas fa-award"></i> {{__('Featured')}}</span>
                                @endif
                                <span class="crypto"><i class="fas fa-link"></i> {{__('Crypto')}}</span>
                            </div>
                        </div>
                        <div class="campaign-body">
                            <div class="campaign-progress-wrap">
                                <div class="campaign-progress-bar">
                                    @php $pct = get_percentage($campaign->amount, $campaign->raised); @endphp
                                    <div class="campaign-progress-fill" style="width: {{$pct}}%"></div>
                                </div>
                                <div class="campaign-amounts">
                                    <span class="campaign-raised">{{amount_with_currency_symbol($campaign->raised ?? 0)}}</span>
                                    <span class="campaign-goal">{{__('of')}} {{amount_with_currency_symbol($campaign->amount)}}</span>
                                </div>
                            </div>
                            <h3><a href="{{route('frontend.donations.single',$campaign->slug)}}">{{$campaign->title}}</a></h3>
                            <p class="excerpt">{{Str::limit(strip_tags($campaign->excerpt ?: $campaign->cause_content), 100)}}</p>
                            <a href="{{route('blockchain.donate.form', $campaign->id)}}" class="campaign-donate-btn">
                                <i class="fas fa-heart"></i> {{__('Donate Now')}}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="text-center" style="margin-top: 40px;">
            <a href="{{route('frontend.donations')}}" style="display:inline-block;background:rgba(98,126,234,0.1);border:1px solid rgba(98,126,234,0.3);color:var(--purple);padding:14px 40px;border-radius:12px;font-weight:700;font-size:15px;text-decoration:none;transition:all 0.3s;">
                {{__('View All Campaigns')}} <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>
@endif

<!-- ===== CATEGORIES ===== -->
@if(!empty($all_donation_category) && count($all_donation_category) > 0)
<section class="section-dark-alt">
    <div class="container">
        <div class="section-title-dark">
            <div class="badge-tag">{{__('Categories')}}</div>
            <h2>{{__('Browse by Category')}}</h2>
            <p>{{__('Find campaigns that matter to you.')}}</p>
        </div>
        <div class="row">
            @foreach($all_donation_category->take(6) as $cat)
                <div class="col-lg-2 col-md-4 col-6">
                    <a href="{{route('frontend.donations.category', $cat->id)}}" class="category-card">
                        <div class="category-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h5>{{$cat->title}}</h5>
                        <small>{{__('Campaigns')}}</small>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- ===== CTA ===== -->
<section class="cta-section">
    <div class="container">
        <h2>{{__('Ready to Make a Difference?')}}</h2>
        <p>{{__('Join thousands of donors who trust blockchain-secured crowdfunding.')}}</p>
        <a href="{{route('frontend.donations')}}" class="cta-btn">
            <i class="fas fa-heart"></i> {{__('Browse Medical Campaigns')}}
        </a>
    </div>
</section>

<!-- ===== CUSTOM FOOTER ===== -->
<footer class="footer-dark-custom">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
                    <div style="width:38px;height:38px;background:linear-gradient(135deg,#0d9488,#0f766e);border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px;"><i class="fas fa-heartbeat"></i></div>
                    <h5 style="margin:0;">Medi<span style="color:#14b8a6;">Fund</span></h5>
                </div>
                <p>{{__('Transparent medical crowdfunding DApp with fraud prevention and blockchain transparency. Built for Africa.')}}</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <h5>{{__('Platform')}}</h5>
                <ul>
                    <li><a href="{{route('frontend.donations')}}">{{__('All Campaigns')}}</a></li>
                    <li><a href="#how-it-works">{{__('How It Works')}}</a></li>
                    <li><a href="#">{{__('For Hospitals')}}</a></li>
                    <li><a href="#">{{__('For Patients')}}</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6">
                <h5>{{__('Resources')}}</h5>
                <ul>
                    <li><a href="#">{{__('Blockchain FAQ')}}</a></li>
                    <li><a href="#">{{__('Privacy Policy')}}</a></li>
                    <li><a href="#">{{__('Terms of Service')}}</a></li>
                    <li><a href="#">{{__('Contact Us')}}</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-6">
                <h5>{{__('Blockchain Status')}}</h5>
                <div style="background:var(--bg-card-alt);border:1px solid var(--border);border-radius:12px;padding:20px;">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                        <span style="width:10px;height:10px;background:var(--green);border-radius:50%;display:inline-block;"></span>
                        <span style="color:var(--text);font-size:14px;font-weight:600;">{{__('Demo Network Active')}}</span>
                    </div>
                    <div style="color:var(--text-dim);font-size:13px;line-height:1.8;">
                        <div>{{__('Network')}}: Demo Ethereum</div>
                        <div>{{__('Status')}}: <span style="color:var(--green);">{{__('Operational')}}</span></div>
                        <div>{{__('Total Txns')}}: {{\App\BlockchainTransaction::where('status','confirmed')->count()}}</div>
                        <div>{{__('Fraud Engine')}}: <span style="color:var(--green);">{{__('Active')}}</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="copyright">
            &copy; {{date('Y')}} MediFund. {{__('All rights reserved.')}} {{__('Transparent medical crowdfunding with fraud prevention and blockchain transparency.')}}
        </div>
    </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Scroll reveal
    const reveals = document.querySelectorAll('.step-card, .campaign-card, .category-card, .counter-item, .pride-content, .pride-image-frame, .cta-section h2, .cta-section p');
    reveals.forEach(function(el) { el.classList.add('reveal'); });

    function checkReveal() {
        var windowHeight = window.innerHeight;
        document.querySelectorAll('.reveal').forEach(function(el) {
            var top = el.getBoundingClientRect().top;
            if (top < windowHeight - 80) { el.classList.add('visible'); }
        });
    }
    window.addEventListener('scroll', checkReveal);
    checkReveal();

    // Animated counters
    document.querySelectorAll('.counter-value').forEach(function(el) {
        var text = el.textContent.trim();
        var hasPercent = text.indexOf('%') !== -1;
        var cleanText = text.replace(/[^0-9]/g, '');
        var target = parseInt(cleanText);
        if (isNaN(target) || target === 0) return;
        var duration = 1500;
        var start = 0;
        var startTime = null;
        function animate(time) {
            if (!startTime) startTime = time;
            var progress = Math.min((time - startTime) / duration, 1);
            var eased = 1 - Math.pow(1 - progress, 3);
            var current = Math.floor(eased * target);
            el.textContent = current.toLocaleString() + (hasPercent ? '%' : '+');
            if (progress < 1) requestAnimationFrame(animate);
        }
        var obs = new IntersectionObserver(function(entries) {
            if (entries[0].isIntersecting) {
                requestAnimationFrame(animate);
                obs.disconnect();
            }
        }, { threshold: 0.5 });
        obs.observe(el);
    });
});
</script>

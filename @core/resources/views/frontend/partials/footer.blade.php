@php
    $home_page_variant = $home_page ?? get_static_option('home_page_variant');
@endphp

<style>
    .fr-footer {
        background: #0f172a;
        color: #94a3b8;
    }
    .fr-footer-main {
        padding: 70px 0 40px;
    }
    .fr-footer-grid {
        display: grid;
        grid-template-columns: 1.4fr 1fr 1fr 1.2fr;
        gap: 40px;
    }
    .fr-footer h5 {
        color: #f1f5f9;
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 20px;
        position: relative;
        padding-bottom: 12px;
    }
    .fr-footer h5::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 30px;
        height: 2px;
        background: #0d9488;
        border-radius: 2px;
    }
    .fr-footer .brand-block p {
        font-size: 14px;
        line-height: 1.7;
        margin-top: 14px;
    }
    .fr-footer .brand-block .logo-row {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .fr-footer .brand-block .logo-icon {
        width: 38px;
        height: 38px;
        background: linear-gradient(135deg, #0d9488, #0f766e);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 16px;
    }
    .fr-footer .brand-block .logo-text {
        font-size: 20px;
        font-weight: 800;
        color: #f1f5f9;
    }
    .fr-footer .brand-block .logo-text span { color: #0d9488; }
    .fr-footer ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .fr-footer ul li {
        margin-bottom: 10px;
    }
    .fr-footer ul li a {
        color: #94a3b8;
        font-size: 14px;
        text-decoration: none;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .fr-footer ul li a:hover {
        color: #14b8a6;
        padding-left: 4px;
    }
    .fr-footer ul li a i {
        font-size: 10px;
        color: #0d9488;
    }
    .fr-footer .status-card {
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 12px;
        padding: 18px;
    }
    .fr-footer .status-card .status-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
    }
    .fr-footer .status-card .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #10b981;
        animation: statusPulse 2s ease-in-out infinite;
    }
    @keyframes statusPulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }
    .fr-footer .status-card .status-text {
        color: #f1f5f9;
        font-size: 14px;
        font-weight: 600;
    }
    .fr-footer .status-card .status-details {
        font-size: 13px;
        line-height: 1.8;
        color: #64748b;
    }
    .fr-footer .status-card .status-details span {
        color: #10b981;
        font-weight: 600;
    }
    .fr-footer .social-icons {
        display: flex;
        gap: 10px;
        margin-top: 18px;
    }
    .fr-footer .social-icons a {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        font-size: 14px;
        transition: all 0.2s;
        text-decoration: none;
    }
    .fr-footer .social-icons a:hover {
        background: #0d9488;
        border-color: #0d9488;
        color: #fff;
    }
    .fr-footer-bottom {
        border-top: 1px solid rgba(255,255,255,0.06);
        padding: 20px 0;
    }
    .fr-footer-bottom .inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 13px;
        color: #64748b;
    }
    .fr-footer-bottom .inner a {
        color: #94a3b8;
        text-decoration: none;
        transition: color 0.2s;
    }
    .fr-footer-bottom .inner a:hover { color: #14b8a6; }

    @media (max-width: 768px) {
        .fr-footer-grid {
            grid-template-columns: 1fr;
            gap: 30px;
        }
        .fr-footer-bottom .inner {
            flex-direction: column;
            gap: 10px;
            text-align: center;
        }
    }
</style>

<footer class="fr-footer home-variant-{{$home_page_variant}}">
    <div class="fr-footer-main">
        <div class="container">
            <div class="fr-footer-grid">
                <!-- Brand -->
                <div class="brand-block">
                    <div class="logo-row">
                        <div class="logo-icon"><i class="fas fa-heartbeat"></i></div>
                        <div class="logo-text">Medi<span>Fund</span></div>
                    </div>
                    <p>{{__('Transparent medical crowdfunding DApp with fraud prevention and blockchain transparency. Built for Africa.')}}</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>

                <!-- Platform -->
                <div>
                    <h5>{{__('Platform')}}</h5>
                    <ul>
                        <li><a href="{{route('frontend.donations')}}"><i class="fas fa-chevron-right"></i> {{__('All Campaigns')}}</a></li>
                        <li><a href="#how-it-works"><i class="fas fa-chevron-right"></i> {{__('How It Works')}}</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> {{__('For Hospitals')}}</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> {{__('For Patients')}}</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> {{__('Start a Campaign')}}</a></li>
                    </ul>
                </div>

                <!-- Resources -->
                <div>
                    <h5>{{__('Resources')}}</h5>
                    <ul>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> {{__('Blockchain FAQ')}}</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> {{__('How Escrow Works')}}</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> {{__('Privacy Policy')}}</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> {{__('Terms of Service')}}</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> {{__('Contact Us')}}</a></li>
                    </ul>
                </div>

                <!-- Blockchain Status -->
                <div>
                    <h5>{{__('Blockchain Status')}}</h5>
                    <div class="status-card">
                        <div class="status-row">
                            <span class="status-dot"></span>
                            <span class="status-text">{{__('Demo Network Active')}}</span>
                        </div>
                        <div class="status-details">
                            <div>{{__('Network')}}: Demo Ethereum</div>
                            <div>{{__('Status')}}: <span>{{__('Operational')}}</span></div>
                            <div>{{__('Total Txns')}}: {{\App\BlockchainTransaction::where('status','confirmed')->count()}}</div>
                            <div>{{__('Fraud Engine')}}: <span>{{__('Active')}}</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="fr-footer-bottom">
        <div class="container">
            <div class="inner">
                <span>&copy; {{date('Y')}} MediFund. {{__('All rights reserved.')}}</span>
                <span>{{__('Transparent medical crowdfunding with fraud prevention and blockchain transparency.')}}</span>
            </div>
        </div>
    </div>
</footer>

<div class="back-to-top">
    <span class="back-top">
        <i class="fas fa-angle-up"></i>
    </span>
</div>

<!-- load all script -->
<script src="{{asset('assets/frontend/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('assets/frontend/js/dynamic-script.js')}}"></script>
<script src="{{asset('assets/frontend/js/jquery.magnific-popup.js')}}"></script>
<script src="{{asset('assets/frontend/js/jquery.waypoints.js')}}"></script>
<script src="{{asset('assets/frontend/js/jquery.counterup.min.js')}}"></script>
<script src="{{asset('assets/frontend/js/owl.carousel.min.js')}}"></script>
<script src="{{asset('assets/frontend/js/jQuery.rProgressbar.min.js')}}"></script>
<script src="{{asset('assets/frontend/js/active.rProgressbar.js')}}"></script>
<script src="{{asset('assets/frontend/js/wow.min.js')}}"></script>
<script src="{{asset('assets/backend/js/sweetalert2.js')}}"></script>
<script src="{{asset('assets/frontend/js/imagesloaded.pkgd.min.js')}}"></script>
<script src="{{asset('assets/frontend/js/isotope.pkgd.min.js')}}"></script>
<script src="{{asset('assets/common/js/toastr.min.js')}}"></script>
<script src="{{asset('assets/frontend/js/slick.min.js')}}"></script>
<script src="{{asset('assets/backend/js/jquery.nice-select.min.js')}}"></script>
<script src="{{asset('assets/frontend/js/main.js')}}"></script>
<script src="{{asset('assets/frontend/js/main2.js')}}"></script>

@include('frontend.partials.google-captcha')
@include('frontend.partials.gdpr-cookie')
@include('frontend.partials.inline-script')

<script>
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip({'placement': 'left','color':'green'});
    });
</script>

<x-sweet-alert-msg/>
<script>
window.MF_CHAIN = {
    mode: "{{ get_static_option('blockchain_transfer_mode') ?? 'simulated' }}",
    networkName: "{{ get_static_option('blockchain_network_name') ?? 'Demo Ethereum Network' }}",
    chainId: parseInt("{{ get_static_option('blockchain_chain_id') ?? 31337 }}", 10),
    rpcUrl: "{{ get_static_option('blockchain_rpc_url') }}",
    donationContract: "{{ get_static_option('blockchain_contract_address') }}",
    escrowContract: "{{ get_static_option('blockchain_escrow_contract_address') }}",
    receivingWallet: "{{ get_static_option('site_receiving_wallet') ?? '0x80354450F4c300F178de2Ab718AbA6D2818CE102' }}",
    currency: "{{ get_static_option('blockchain_currency') ?? 'ETH' }}"
};
</script>
@yield('scripts')
@yield('script')

</body>
</html>

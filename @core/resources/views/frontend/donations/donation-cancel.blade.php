@extends('frontend.frontend-page-master')

@section('site-title') {{__('Payment Cancelled')}} - MediFund @endsection
@section('page-title') {{__('Payment Cancelled')}} @endsection

@section('style')
<style>
    body{background:#0B0E1A !important}
    .dc-page { padding: 80px 20px 100px; min-height:100vh;
        background:
            radial-gradient(900px 420px at 85% -80px, rgba(255,92,122,.09), transparent 60%),
            radial-gradient(700px 380px at 0% 110%, rgba(98,126,234,.08), transparent 55%), #0B0E1A;
        font-family:'Inter','Segoe UI',system-ui,-apple-system,sans-serif; }
    .dc-card { max-width: 560px; margin: 0 auto; text-align: center; background:linear-gradient(180deg,#141936,#10142a);
        border:1px solid #232A47; border-radius:24px; box-shadow:0 24px 60px rgba(3,6,23,.6);
        padding: 50px 44px; position:relative; overflow:hidden; color:#EAECF5; }
    .dc-card::before { content:''; display:block; position:absolute; top:0; left:0; right:0; height:4px;
        background:linear-gradient(135deg,#ff5c7a,#FFB800); }
    .dc-ico { width:84px; height:84px; margin:0 auto 22px; border-radius:50%;
        background:rgba(255,92,122,.1); color:#ff5c7a; font-size:32px;
        display:flex; align-items:center; justify-content:center; border:2px dashed rgba(255,92,122,.35); }
    .dc-card h1 { font-size:23px; font-weight:800; letter-spacing:-.02em; color:#fff; margin-bottom:10px; }
    .dc-card p { color:#9AA3BF; font-size:14.5px; line-height:1.7; max-width:400px; margin:0 auto 28px; }
    .dc-actions { display:flex; gap:12px; justify-content:center; flex-wrap:wrap; }
    .dc-btn { display:inline-flex; align-items:center; gap:9px; border:none; border-radius:12px;
        padding:13px 28px; font-size:13.5px; font-weight:800; text-decoration:none; cursor:pointer;
        transition: transform .22s ease, box-shadow .22s ease; }
    .dc-btn:hover { transform: translateY(-2px); }
    .dc-btn-primary { background:linear-gradient(135deg,#627EEA,#4B6CEB); color:#fff; box-shadow:0 8px 22px rgba(98,126,234,.35); }
    .dc-btn-primary:hover { box-shadow:0 14px 32px rgba(98,126,234,.5); color:#fff; }
    .dc-btn-ghost { background:transparent; color:#EAECF5; border:1.5px solid #232A47; }
    .dc-btn-ghost:hover { border-color: rgba(0,212,170,.45); color:#00D4AA; }
</style>
@endsection

@section('content')
<div class="dc-page">
    <div class="container">
        <div class="dc-card">
            <div class="dc-ico"><i class="fas fa-times"></i></div>
            <h1>{{get_static_option('donation_cancel_page_title') ?: __('Your Payment Was Cancelled')}}</h1>
            <p>{{get_static_option('donation_cancel_page_description') ?: __('No charge was made to your account. If this was a mistake, you can safely complete your donation again — every contribution makes a difference.')}}</p>
            <div class="dc-actions">
                <a href="{{url('/')}}" class="dc-btn dc-btn-primary"><i class="fas fa-home"></i> {{__('Back To Home')}}</a>
                <a href="{{route('frontend.donations')}}" class="dc-btn dc-btn-ghost"><i class="fas fa-hand-holding-heart"></i> {{__('Browse Campaigns')}}</a>
            </div>
        </div>
    </div>
</div>
@endsection

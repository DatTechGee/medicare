@extends('frontend.frontend-page-master')

@section('site-title') {{__('Donation Successful')}} - MediFund @endsection
@section('page-title') {{__('Payment Success For:')}} {{optional($donation_logs->cause)->title}} @endsection

@section('style')
<style>
    body{background:#0B0E1A !important}
    .ds-page { --p:#627EEA; --a:#00D4AA; padding: 70px 20px 90px; min-height:100vh;
        background:
            radial-gradient(900px 420px at 85% -80px, rgba(0,212,170,.10), transparent 60%),
            radial-gradient(700px 380px at 0% 110%, rgba(98,126,234,.10), transparent 55%), #0B0E1A;
        font-family:'Inter','Segoe UI',system-ui,-apple-system,sans-serif; color:#EAECF5; }
    .ds-card { max-width: 720px; margin: 0 auto; background:linear-gradient(180deg,#141936,#10142a); border:1px solid #232A47;
        border-radius: 24px; box-shadow: 0 24px 60px rgba(3,6,23,.6); overflow:hidden; position:relative; }
    .ds-card::before { content:''; display:block; height:4px; background:linear-gradient(135deg,#627EEA,#00D4AA); }
    .ds-head { text-align:center; padding: 42px 40px 30px; }
    .ds-ico { width:84px; height:84px; margin:0 auto 20px; border-radius:50%;
        background: linear-gradient(135deg,#627EEA,#00D4AA); color:#fff; font-size:34px;
        display:flex; align-items:center; justify-content:center; box-shadow:0 14px 34px rgba(0,212,170,.35);
        animation: dsPop .5s cubic-bezier(.34,1.56,.64,1); }
    @keyframes dsPop { from { transform:scale(.4); opacity:0; } to { transform:scale(1); opacity:1; } }
    .ds-head h1 { font-size:26px; font-weight:800; letter-spacing:-.02em; color:#fff; margin-bottom:8px; }
    .ds-head p { color:#9AA3BF; font-size:14.5px; max-width:480px; margin:0 auto; line-height:1.65; }
    .ds-body { padding: 0 40px 40px; }
    .ds-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-top:26px; }
    .ds-row { background:rgba(255,255,255,.04); border:1px solid #232A47; border-radius:12px; padding:13px 16px; }
    .ds-row.full { grid-column:1/-1; }
    .ds-row .k { font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.07em; color:#6B7594; margin-bottom:4px; }
    .ds-row .v { font-size:14px; font-weight:600; color:#EAECF5; word-break:break-word; }
    .ds-badge { display:inline-flex; align-items:center; gap:6px; background:rgba(0,212,170,.1); color:#00D4AA;
        border:1px solid rgba(0,212,170,.3);
        border-radius:99px; padding:6px 16px; font-size:12.5px; font-weight:800; }
    .ds-campaign { display:flex; gap:18px; align-items:center; margin-top:22px; background:#181D33;
        border:1px solid #232A47; border-radius:16px; padding:16px; transition: box-shadow .25s ease, transform .25s ease; }
    .ds-campaign:hover { box-shadow: 0 12px 32px rgba(3,6,23,.5); transform: translateY(-2px); }
    .ds-campaign img { width:96px; height:76px; object-fit:cover; border-radius:11px; flex-shrink:0; }
    .ds-campaign h4 { font-size:15px; font-weight:800; color:#fff; margin-bottom:5px; }
    .ds-campaign p { font-size:12.5px; color:#9AA3BF; margin:0 0 9px; }
    .ds-campaign a.ds-view { display:inline-flex; align-items:center; gap:7px; font-size:12.5px; font-weight:800;
        color:#00D4AA; text-decoration:none; }
    .ds-campaign a.ds-view:hover { color:#8FA3FF; }
    .ds-actions { display:flex; gap:12px; justify-content:center; margin-top:30px; flex-wrap:wrap; }
    .ds-btn { display:inline-flex; align-items:center; gap:9px; border:none; border-radius:12px;
        padding:13px 28px; font-size:13.5px; font-weight:800; text-decoration:none; cursor:pointer;
        transition: transform .22s ease, box-shadow .22s ease; }
    .ds-btn:hover { transform: translateY(-2px); }
    .ds-btn-primary { background:linear-gradient(135deg,#627EEA,#4B6CEB); color:#fff; box-shadow:0 8px 22px rgba(98,126,234,.35); }
    .ds-btn-primary:hover { box-shadow: 0 14px 32px rgba(98,126,234,.5); color:#fff; }
    .ds-btn-ghost { background:transparent; color:#EAECF5; border:1.5px solid #232A47; }
    .ds-btn-ghost:hover { border-color: rgba(0,212,170,.45); color:#00D4AA; }
    @media(max-width:600px){ .ds-grid{grid-template-columns:1fr;} .ds-campaign{flex-direction:column;text-align:center;} }
</style>
@endsection

@section('content')
<div class="ds-page">
    <div class="container">
        <div class="ds-card">
            <div class="ds-head">
                <div class="ds-ico"><i class="fas fa-heart"></i></div>
                <h1>{{get_static_option('donation_success_page_title') ?: __('Thank You for Your Generosity!')}}</h1>
                <p>{{get_static_option('donation_success_page_description') ?: __('Your donation brings us one step closer to the goal. A confirmation has been sent to your email.')}}</p>
            </div>
            <div class="ds-body">
                <span class="ds-badge"><i class="fas fa-check-circle"></i> {{__('Payment Confirmed')}}</span>

                <div class="ds-grid">
                    <div class="ds-row">
                        <div class="k">{{__('Name')}}</div>
                        <div class="v">{{$donation_logs->name}}</div>
                    </div>
                    <div class="ds-row">
                        <div class="k">{{__('Email')}}</div>
                        <div class="v">{{$donation_logs->email}}</div>
                    </div>
                    <div class="ds-row">
                        <div class="k">{{__('Amount')}}</div>
                        <div class="v" style="color:#00D4AA;">{{amount_with_currency_symbol($donation_logs->amount)}}</div>
                    </div>
                    <div class="ds-row">
                        <div class="k">{{__('Payment Method')}}</div>
                        <div class="v">{{str_replace('_',' ',title_case($donation_logs->payment_gateway))}}</div>
                    </div>
                    <div class="ds-row">
                        <div class="k">{{__('Payment Status')}}</div>
                        <div class="v" style="text-transform:capitalize;">{{$donation_logs->status}}</div>
                    </div>
                    <div class="ds-row">
                        <div class="k">{{__('Transaction ID')}}</div>
                        <div class="v" style="font-family:'SFMono-Regular',Consolas,monospace;font-size:12px;">{{$donation_logs->transaction_id ?? '-'}}</div>
                    </div>

                    @if(!empty($donation_logs->gift))
                        <div class="ds-row full">
                            <div class="k">{{__('Donation Gifts')}}</div>
                            <div class="v">{{optional($donation_logs->gift)->title}}
                                @foreach(json_decode(optional($donation_logs->gift)->gifts) ?? [] as $item)
                                    <span style="background:rgba(98,126,234,.15);color:#8FA3FF;border-radius:8px;padding:3px 10px;font-size:11.5px;font-weight:700;margin-left:6px;">{{ $item }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @php
                        $all_custom_fields = json_decode($donation_logs->custom_fields) ?? [];
                    @endphp
                    @foreach($all_custom_fields ?? [] as $key=> $field)
                        @php if(is_object($field)){ continue; } @endphp
                        <div class="ds-row full">
                            <div class="k">{{ucfirst(str_replace('_',' ',$key))}}</div>
                            <div class="v">{{$field}}</div>
                        </div>
                    @endforeach

                    @if(!empty($donation_logs->reward_point))
                        <div class="ds-row full">
                            <div class="k">{{__('Reward Points Earned')}}</div>
                            <div class="v">+{{$donation_logs->reward_point}}</div>
                        </div>
                    @endif
                </div>

                <div class="ds-campaign">
                    {!! render_image_markup_by_attachment_id($donation->image,'','grid') !!}
                    <div style="flex:1;">
                        <h4>{{$donation->title}}</h4>
                        <p>{{strip_tags(Str::words(strip_tags($donation->donation_content),20))}}</p>
                        <a class="ds-view" href="{{route('frontend.donations.single',$donation->slug)}}">{{__('View Campaign')}} <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>

                <div class="ds-actions">
                    @if(auth()->guard('web')->check())
                        <a href="{{route('user.home')}}" class="ds-btn ds-btn-primary"><i class="fas fa-th-large"></i> {{__('Go To Dashboard')}}</a>
                    @else
                        <a href="{{url('/')}}" class="ds-btn ds-btn-primary"><i class="fas fa-home"></i> {{__('Back To Home')}}</a>
                    @endif
                    <a href="{{route('frontend.donations')}}" class="ds-btn ds-btn-ghost"><i class="fas fa-hand-holding-heart"></i> {{__('Browse More Campaigns')}}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@php
    /* Donate Now must always reach the blockchain payment form: resolve id from slug when :id not passed */
    $bfCauseId = isset($id) && $id ? $id : \App\Cause::where('slug',$slug)->value('id');
    /* trust signals for public badges */
    $bfCause = $bfCauseId ? \App\Cause::find($bfCauseId) : null;
    $bfWalletVerified = (bool) ($bfCause->wallet_verified ?? false);
    $bfScore = (int) ($bfCause->fraud_score ?? 0);
    $bfRisk = $bfScore <= 20 ? 'low' : ($bfScore <= 50 ? 'medium' : 'high');
    $bfPct = $amount > 0 ? min(100, round((($raised ?? 0) / $amount) * 100)) : 0;
@endphp
<div class="mfc-card" style="animation: fadeInUp .5s ease both;">
    <div class="mfc-thumb">
        <a href="{{route('frontend.donations.single',$slug)}}">
           {!! render_image_markup_by_attachment_id($image,'','grid') !!}
        </a>
        <div class="mfc-thumb-shade"></div>
        @if(!empty($bfCause->hospital_name))
        <div class="mfc-hospital">
            <div class="mfc-hospital-ico"><i class="fas fa-hospital"></i></div>
            <span>{{ $bfCause->hospital_name }}</span>
        </div>
        @endif
        <div class="mfc-tags">
            <span class="mfc-tag mfc-tag--chain"><i class="fas fa-link"></i> {{__('On-chain')}}</span>
            @if(isset($featured) && $featured === 'on')
                <span class="mfc-tag mfc-tag--feat"><i class="fas fa-star"></i> {{__('Featured')}}</span>
            @endif
        </div>
        <div class="mfc-badges">
            @if($bfWalletVerified)
                <span title="{{__('Receiving wallet verified by MediFund admin')}}" class="mfc-badge mfc-badge--verified"><i class="fas fa-shield-halved"></i> {{__('Wallet Verified')}}</span>
            @endif
            @if($bfRisk === 'low')
                <span title="{{__('Passed automated fraud screening')}}" class="mfc-badge mfc-badge--low"><i class="fas fa-circle-check"></i> {{__('Low Risk')}}</span>
            @elseif($bfRisk === 'medium')
                <span title="{{__('Under additional review')}}" class="mfc-badge mfc-badge--med"><i class="fas fa-triangle-exclamation"></i> {{__('Medium Risk')}}</span>
            @else
                <span title="{{__('Flagged by fraud engine — under investigation')}}" class="mfc-badge mfc-badge--high"><i class="fas fa-triangle-exclamation"></i> {{__('High Risk')}}</span>
            @endif
        </div>
    </div>
    <div class="mfc-body">
        <div class="mfc-progress-head">
            <span class="mfc-raised">{{amount_with_currency_symbol($raised ?? 0)}} <em>{{__('raised')}}</em></span>
            <span class="mfc-pct">{{$bfPct}}%</span>
        </div>
        <div class="mfc-bar"><i style="width:{{$bfPct}}%"></i></div>
        <div class="mfc-goal">{{__('Goal')}}: {{amount_with_currency_symbol($amount)}}</div>
        <h3 class="mfc-title">
            <a href="{{route('frontend.donations.single',$slug)}}">{{$title}}</a>
        </h3>
        <p class="mfc-excerpt">{{Str::limit(strip_tags($excerpt), 96)}}</p>
        <div class="mfc-actions">
            <a href="{{ $bfCauseId ? route('blockchain.donate.form', $bfCauseId) : route('frontend.donations.single',$slug) }}" class="mfc-btn-donate">
                <i class="fas fa-heart"></i> {{__('Donate')}}
            </a>
            <a href="{{route('frontend.donations.single',$slug)}}" class="mfc-btn-details">{{__('Details')}}</a>
        </div>
    </div>
</div>

<style>
/* ====== MediFund campaign card (dark system) ====== */
.mfc-card{
    --c-primary:#627EEA; --c-accent:#00D4AA;
    background:linear-gradient(180deg,#141936 0%,#10142a 100%);
    border:1px solid #232a47; border-radius:18px; overflow:hidden; height:100%;
    display:flex; flex-direction:column;
    box-shadow:0 10px 34px rgba(3,6,23,.45);
    transition:transform .28s cubic-bezier(.4,0,.2,1), box-shadow .28s, border-color .28s;
}
.mfc-card:hover{ transform:translateY(-5px); box-shadow:0 22px 54px -12px rgba(0,0,0,.65); border-color:rgba(98,126,234,.45); }
.mfc-thumb{ position:relative; overflow:hidden; height:198px; flex-shrink:0; }
.mfc-thumb img{ width:100%; height:100%; object-fit:cover; transition:transform .5s ease; }
.mfc-card:hover .mfc-thumb img{ transform:scale(1.06); }
.mfc-thumb-shade{ position:absolute; inset:0; background:linear-gradient(to bottom, rgba(11,14,26,.05) 55%, rgba(11,14,26,.78) 100%); }
.mfc-hospital{ position:absolute; bottom:0; left:0; right:0; padding:10px 14px; display:flex; align-items:center; gap:8px; z-index:2; }
.mfc-hospital-ico{ width:26px;height:26px;border-radius:8px;background:rgba(98,126,234,.92);display:flex;align-items:center;justify-content:center;font-size:11px;color:#fff;flex-shrink:0; }
.mfc-hospital span{ font-size:11px;color:rgba(255,255,255,.88);font-weight:600; text-shadow:0 1px 6px rgba(0,0,0,.6); }
.mfc-tags{ position:absolute; top:12px; left:12px; display:flex; gap:6px; z-index:2; flex-wrap:wrap; }
.mfc-tag{ font-size:10px;font-weight:700;padding:5px 11px;border-radius:8px;display:inline-flex;align-items:center;gap:5px;backdrop-filter:blur(8px); }
.mfc-tag i{font-size:9px}
.mfc-tag--chain{ background:rgba(98,126,234,.9); color:#fff; box-shadow:0 3px 12px rgba(98,126,234,.4); }
.mfc-tag--feat{ background:rgba(255,184,0,.94); color:#3d2800; }
.mfc-badges{ position:absolute; top:12px; right:12px; display:flex; flex-direction:column; gap:6px; z-index:2; align-items:flex-end; }
.mfc-badge{ font-size:10px;font-weight:800;padding:5px 11px;border-radius:8px;display:inline-flex;align-items:center;gap:5px; }
.mfc-badge i{font-size:9px}
.mfc-badge--verified{ background:rgba(0,212,170,.95); color:#04231C; box-shadow:0 3px 10px rgba(0,212,170,.35); }
.mfc-badge--low{ background:rgba(255,255,255,.94); color:#0f766e; }
.mfc-badge--med{ background:rgba(255,184,0,.95); color:#3d2800; }
.mfc-badge--high{ background:rgba(255,92,122,.95); color:#fff; box-shadow:0 3px 10px rgba(255,92,122,.35); }
.mfc-body{ padding:18px 20px 20px; display:flex; flex-direction:column; flex:1; }
.mfc-progress-head{ display:flex; justify-content:space-between; align-items:baseline; margin-bottom:7px; }
.mfc-raised{ font-size:16px;font-weight:800;color:#00D4AA;font-variant-numeric:tabular-nums;letter-spacing:-.01em; }
.mfc-raised em{ font-style:normal;font-size:10.5px;font-weight:700;color:#6B7594;text-transform:uppercase;letter-spacing:.06em;margin-left:4px; }
.mfc-pct{ font-size:12px;font-weight:800;color:#8FA3FF; }
.mfc-bar{ height:7px;background:rgba(255,255,255,.07);border-radius:99px;overflow:hidden; }
.mfc-bar i{ display:block;height:100%;border-radius:99px;background:linear-gradient(90deg,#627EEA,#00D4AA);box-shadow:0 0 12px rgba(0,212,170,.4); }
.mfc-goal{ font-size:11px;font-weight:600;color:#6B7594;margin-top:6px; }
.mfc-title{ margin:10px 0 6px !important; }
.mfc-title a{ font-size:15px;font-weight:700;line-height:1.42;color:#EAECF5;text-decoration:none;transition:color .2s; }
.mfc-title a:hover{ color:#8FA3FF; }
.mfc-excerpt{ font-size:12.5px;line-height:1.6;color:#9AA3BF;margin-bottom:16px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;flex:1; }
.mfc-actions{ display:flex; gap:8px; }
.mfc-btn-donate{ flex:1;text-align:center;background:linear-gradient(135deg,#627EEA,#4B6CEB);color:#fff;padding:11px;border-radius:11px;font-weight:800;font-size:13px;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:6px;box-shadow:0 6px 18px rgba(98,126,234,.35);transition:all .25s cubic-bezier(.4,0,.2,1); }
.mfc-btn-donate:hover{ transform:translateY(-2px); box-shadow:0 12px 26px rgba(98,126,234,.5); color:#fff; }
.mfc-btn-details{ flex:0 0 auto;background:rgba(0,212,170,.07);border:1px solid rgba(0,212,170,.25);color:#00D4AA;padding:11px 16px;border-radius:11px;font-weight:700;font-size:13px;text-decoration:none;display:flex;align-items:center;transition:all .2s; }
.mfc-btn-details:hover{ background:rgba(0,212,170,.14); color:#00D4AA; }
</style>

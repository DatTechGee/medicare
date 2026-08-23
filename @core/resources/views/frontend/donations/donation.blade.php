@extends('frontend.frontend-page-master')
@section('site-title')
    {{__('Medical Campaigns')}}
@endsection
@section('page-title')
    {{__('Medical Campaigns')}}
@endsection
@section('page-meta-data')
    <meta name="description" content="{{__('Browse verified medical crowdfunding campaigns on the blockchain')}}">
    <meta name="tags" content="{{__('medical crowdfunding, blockchain, donate')}}">
@endsection
@section('style')
<style>
/* ===== MediFund campaigns listing (dark system) ===== */
body{background:#0B0E1A !important}
.mf-listing{--p:#627EEA;--a:#00D4AA;--card:#12162A;--card2:#181D33;--line:#232A47;--txt:#EAECF5;--mut:#9AA3BF;--fnt:#6B7594;
    min-height:100vh;color:var(--txt);
    background:
        radial-gradient(1000px 520px at 12% -8%, rgba(98,126,234,.14), transparent 60%),
        radial-gradient(900px 480px at 92% 108%, rgba(0,212,170,.10), transparent 60%),
        var(--card) !important;
    padding:56px 0 90px;
    font-family:'Inter','Segoe UI',system-ui,-apple-system,sans-serif;}
.mf-listing .container{max-width:1200px}

.mf-hero{text-align:center;margin-bottom:38px}
.mf-hero-pill{display:inline-flex;align-items:center;gap:8px;background:rgba(0,212,170,.08);border:1px solid rgba(0,212,170,.28);color:var(--a);font-size:11px;font-weight:800;letter-spacing:.09em;text-transform:uppercase;border-radius:999px;padding:7px 16px;margin-bottom:16px}
.mf-hero-pill i{animation:mfPulseDot 2s infinite}
@keyframes mfPulseDot{0%{opacity:1}50%{opacity:.35}100%{opacity:1}}
.mf-hero h1{font-size:34px;font-weight:800;letter-spacing:-.02em;color:#fff;margin:0 0 10px}
.mf-hero p{color:var(--mut);font-size:14.5px;max-width:640px;margin:0 auto;line-height:1.65}

/* filter bar */
.mf-filters{background:linear-gradient(180deg,#141936,#10142a);border:1px solid var(--line);border-radius:18px;padding:20px 22px;margin-bottom:34px;box-shadow:0 14px 44px rgba(3,6,23,.5)}
.mf-filters label.fl{display:block;font-size:10.5px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--fnt);margin-bottom:7px}
.mf-filters input[type=text],.mf-filters select{
    width:100%;height:46px;background:rgba(255,255,255,.04);border:1.5px solid var(--line);border-radius:12px;
    color:var(--txt);font-size:13.5px;font-weight:600;padding:0 14px;outline:none;transition:border-color .2s, box-shadow .2s;}
.mf-filters select option{background:#141936;color:var(--txt)}
.mf-filters input[type=text]:focus,.mf-filters select:focus{border-color:var(--p);box-shadow:0 0 0 3px rgba(98,126,234,.16)}
.mf-filters button[type=submit]{border:none;border-radius:12px;height:46px;padding:0 26px;font-weight:800;font-size:13px;cursor:pointer;background:linear-gradient(135deg,var(--p),#4B6CEB);color:#fff;box-shadow:0 8px 22px rgba(98,126,234,.35);transition:all .25s cubic-bezier(.4,0,.2,1)}
.mf-filters button[type=submit]:hover{transform:translateY(-2px);box-shadow:0 14px 30px rgba(98,126,234,.5)}
.mf-filters a.mf-reset{display:inline-flex;align-items:center;height:46px;padding:0 20px;border:1.5px solid var(--line);border-radius:12px;color:var(--mut);text-decoration:none;font-weight:700;font-size:13px;transition:all .2s}
.mf-filters a.mf-reset:hover{color:#fff;border-color:rgba(98,126,234,.5)}
.mf-checkline{display:inline-flex;align-items:center;gap:8px;cursor:pointer;height:46px;width:100%;justify-content:flex-start;
    border:1.5px solid var(--line);border-radius:12px;background:rgba(255,255,255,.04);
    font-size:12.5px;font-weight:700;color:var(--txt);padding:0 14px;transition:border-color .2s}
.mf-checkline:hover{border-color:rgba(0,212,170,.45)}
.mf-checkline input{accent-color:var(--a);width:16px;height:16px}
.mf-checkline i.fa-shield-halved{color:var(--a);font-size:11px}
.mf-count-row{display:flex;flex-wrap:wrap;gap:10px;align-items:center;justify-content:space-between;margin-top:16px}
.mf-count{font-size:12px;font-weight:700;color:var(--fnt)}

/* empty + pagination */
.mf-empty{text-align:center;padding:70px 20px;color:var(--mut)}
.mf-empty i{font-size:42px;color:var(--fnt);margin-bottom:16px;display:block}
.mf-empty h3{color:#fff;font-size:19px;font-weight:800;margin-bottom:8px}
.pagination-wrapper .page-item .page-link{background:#141936 !important;color:var(--mut) !important;border-color:var(--line) !important;border-radius:10px !important;font-weight:700;font-size:13px}
.pagination-wrapper .page-item.active .page-link{background:var(--p) !important;color:#fff !important;border-color:var(--p) !important}

/* trust stats strip */
.mf-trust{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;margin-bottom:38px}
.mf-trust-item{background:linear-gradient(180deg,#141936,#10142a);border:1px solid var(--line);border-radius:16px;padding:18px 20px;display:flex;align-items:center;gap:14px;transition:transform .25s cubic-bezier(.4,0,.2,1),border-color .25s}
.mf-trust-item:hover{transform:translateY(-3px);border-color:rgba(98,126,234,.45)}
.mf-trust-ico{width:44px;height:44px;border-radius:13px;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0}
.mf-trust-ico.ti-shield{background:rgba(0,212,170,.12);color:var(--a)}
.mf-trust-ico.ti-cube{background:rgba(98,126,234,.14);color:#8A9FF5}
.mf-trust-ico.ti-lock{background:rgba(255,184,0,.12);color:#FFD166}
.mf-trust-ico.ti-users{background:rgba(255,92,122,.12);color:#ff8fa3}
.mf-trust-val{font-size:19px;font-weight:800;color:#fff;line-height:1.15;font-variant-numeric:tabular-nums}
.mf-trust-lbl{font-size:11px;font-weight:700;color:var(--fnt);text-transform:uppercase;letter-spacing:.06em;margin-top:3px}
</style>
@endsection
@section('content')
<section class="mf-listing">
    <div class="container">
        <div class="mf-hero">
            <span class="mf-hero-pill"><i class="fas fa-circle"></i> {{__('Live on-chain')}}</span>
            <h1><i class="fas fa-heartbeat" style="background:linear-gradient(135deg,#627EEA,#00D4AA);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;"></i> {{__('Verified Medical Campaigns')}}</h1>
            <p>{{__('Every campaign passes automated fraud screening before it goes live, and every donation is escrowed on the blockchain — auditable by anyone, anytime.')}}</p>
        </div>

        {{-- platform trust strip: real numbers from the database + on-chain reads --}}
        @php
            $trustApproved = ($all_donations->getCollection()->where('verification_status','approved')->count());
            $trustTotal = $all_donations->total();
            $trustRaised = $all_donations->getCollection()->sum('raised');
        @endphp
        <div class="mf-trust" id="mfTrustStrip">
            <div class="mf-trust-item">
                <div class="mf-trust-ico ti-shield"><i class="fas fa-shield-halved"></i></div>
                <div>
                    <div class="mf-trust-val">{{$trustTotal}}</div>
                    <div class="mf-trust-lbl">{{__('Fraud-Screened Campaigns')}}</div>
                </div>
            </div>
            <div class="mf-trust-item">
                <div class="mf-trust-ico ti-cube"><i class="fas fa-cubes"></i></div>
                <div>
                    <div class="mf-trust-val" id="mfTrustChain">&mdash;</div>
                    <div class="mf-trust-lbl">{{__('On-Chain Escrow (ETH)')}}</div>
                </div>
            </div>
            <div class="mf-trust-item">
                <div class="mf-trust-ico ti-lock"><i class="fas fa-vault"></i></div>
                <div>
                    <div class="mf-trust-val">{{amount_with_currency_symbol($trustRaised)}}</div>
                    <div class="mf-trust-lbl">{{__('Funds Released to Patients')}}</div>
                </div>
            </div>
            <div class="mf-trust-item">
                <div class="mf-trust-ico ti-users"><i class="fas fa-user-shield"></i></div>
                <div>
                    <div class="mf-trust-val">100%</div>
                    <div class="mf-trust-lbl">{{__('Donations On-Chain Auditable')}}</div>
                </div>
            </div>
        </div>

        <form method="get" class="mf-filters">
            <div class="row align-items-end" style="row-gap:14px;">
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <label class="fl">{{__('Search')}}</label>
                    <input type="text" name="search" value="{{ $currentFilters['search'] ?? '' }}" placeholder="{{__('Search campaigns…')}}">
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6">
                    <label class="fl">{{__('Sort by')}}</label>
                    <select name="sort">
                        <option value="" {{ ($currentFilters['sort'] ?? '') === '' ? 'selected' : '' }}>{{ __('Newest first') }}</option>
                        <option value="funded" {{ ($currentFilters['sort'] ?? '') === 'funded' ? 'selected' : '' }}>{{ __('Most funded (%)') }}</option>
                        <option value="urgency" {{ ($currentFilters['sort'] ?? '') === 'urgency' ? 'selected' : '' }}>{{ __('Urgent only') }}</option>
                        <option value="trusted" {{ ($currentFilters['sort'] ?? '') === 'trusted' ? 'selected' : '' }}>{{ __('Lowest fraud score') }}</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6">
                    <label class="fl">{{__('Risk level')}}</label>
                    <select name="risk">
                        <option value="">{{ __('Any') }}</option>
                        <option value="low" {{ ($currentFilters['risk'] ?? '') === 'low' ? 'selected' : '' }}>{{ __('Low risk (score ≤ 20)') }}</option>
                        <option value="medium" {{ ($currentFilters['risk'] ?? '') === 'medium' ? 'selected' : '' }}>{{ __('Medium or better (≤ 50)') }}</option>
                        <option value="high" {{ ($currentFilters['risk'] ?? '') === 'high' ? 'selected' : '' }}>{{ __('High risk (needs scrutiny)') }}</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-6">
                    <label class="mf-checkline">
                        <input type="checkbox" name="wallet_verified" value="1" {{ !empty($currentFilters['wallet_verified']) ? 'checked' : '' }}>
                        <i class="fas fa-shield-halved"></i> {{__('Wallet Verified')}}
                    </label>
                </div>
                <div class="col-12 mf-count-row">
                    <div class="d-flex" style="gap:10px;">
                        <button type="submit"><i class="fas fa-filter"></i> {{__('Apply Filters')}}</button>
                        <a href="{{ url()->current() }}" class="mf-reset">{{__('Reset')}}</a>
                    </div>
                    <span class="mf-count">{{ $all_donations->total() }} {{ __('campaigns')}} · {{ $all_donations->count() }} {{ __('on this page')}}</span>
                </div>
            </div>
        </form>

        <div class="row">
            @forelse($all_donations as $data)
                <div class="col-lg-4 col-md-6 mb-4">
                    <x-frontend.donation.grid
                            :featured="$data->featured"
                            :image="$data->image"
                            :amount="$data->amount"
                            :raised="$data->raised"
                            :slug="$data->slug"
                            :title="$data->title"
                            :excerpt="$data->excerpt"
                            :deadline="$data->deadline"
                            :reward="$data->reward"
                            :buttontext="__('Donate Now')">
                    </x-frontend.donation.grid>
                </div>
            @empty
                <div class="col-lg-12 mf-empty">
                    <i class="fas fa-cubes"></i>
                    <h3>{{__('No campaigns found')}}</h3>
                    <p>{{__('No medical campaigns match your filters right now. Try widening the search or check back later.')}}</p>
                </div>
            @endforelse
            <div class="col-lg-12 text-center">
                <nav class="pagination-wrapper" aria-label="Page navigation">
                    {{$all_donations->links()}}
                </nav>
            </div>
        </div>
    </div>
</section>
@endsection
@section('scripts')
<script src="{{asset('assets/blockchain/ethers.min.js')}}"></script>
<script src="{{asset('assets/blockchain/medi-fund-web3.js')}}"></script>
<script>
(async function () {
    try {
        if (typeof MediFundWeb3 === 'undefined' || !(await MediFundWeb3.ready())) return;
        /* sum escrow held across the campaigns shown on this page */
        let total = 0;
        const ids = @json($all_donations->getCollection()->pluck('id')->values());
        await Promise.all(ids.map(async function (id) {
            const bal = await MediFundWeb3.escrowBalance(id);
            if (bal) { total += MediFundWeb3.formatEther(bal); }
        }));
        const el = document.getElementById('mfTrustChain');
        if (el) { el.textContent = total.toFixed(4); }
    } catch (e) {
        const el = document.getElementById('mfTrustChain');
        if (el) el.textContent = '—';
    }
})();
</script>
@endsection

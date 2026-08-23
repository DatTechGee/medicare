@extends('frontend.frontend-page-master')
@section('site-title')
    {{$user_info->name}} : {{__('My Campaigns')}}
@endsection
@section('page-title')
    {{$user_info->name}} : {{__('My Campaigns')}}
@endsection
@section('style')
<style>
body{background:#0B0E1A !important}
.mf-usercamp{--p:#627EEA;--a:#00D4AA;--line:#232A47;--txt:#EAECF5;--mut:#9AA3BF;--fnt:#6B7594;
    min-height:100vh;color:var(--txt);
    background:
        radial-gradient(1000px 520px at 12% -8%, rgba(98,126,234,.14), transparent 60%),
        radial-gradient(900px 480px at 92% 108%, rgba(0,212,170,.10), transparent 60%),
        #12162A !important;
    padding:56px 0 90px;font-family:'Inter','Segoe UI',system-ui,-apple-system,sans-serif;}
.mf-usercamp-head{display:flex;align-items:center;gap:16px;margin-bottom:34px;flex-wrap:wrap}
.mf-usercamp-ava{width:58px;height:58px;border-radius:16px;background:linear-gradient(135deg,#627EEA,#00D4AA);display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;font-weight:800;flex-shrink:0;text-transform:uppercase}
.mf-usercamp-head h1{color:#fff;font-size:24px;font-weight:800;margin:0;letter-spacing:-.02em}
.mf-usercamp-head p{color:var(--mut);font-size:13px;margin:3px 0 0}
.pagination-wrapper .page-item .page-link{background:#141936 !important;color:var(--mut) !important;border-color:var(--line) !important;border-radius:10px !important;font-weight:700;font-size:13px}
.pagination-wrapper .page-item.active .page-link{background:var(--p) !important;color:#fff !important;border-color:var(--p) !important}
</style>
@endsection
@section('content')
<section class="mf-usercamp">
    <div class="container">
        <div class="mf-usercamp-head">
            <div class="mf-usercamp-ava">{{mb_substr($user_info->name,0,1)}}</div>
            <div>
                <h1>{{$user_info->name}}</h1>
                <p>{{__('Campaigns created by this member on MediFund')}} &bull; {{__('Wallet')}}: {{\Illuminate\Support\Str::limit($user_info->wallet_address ?? __('not linked'), 14, '…')}}</p>
            </div>
        </div>
        <div class="row">
            @foreach($user_donations as $data)
                <div class="col-lg-4 col-md-6 mb-4">
                    <x-frontend.donation.user-donation
                            :featured="$data->featured"
                            :image="$data->image"
                            :amount="$data->amount"
                            :raised="$data->raised"
                            :slug="$data->slug"
                            :id="$data->id"
                            :title="$data->title"
                            :excerpt="$data->excerpt"
                            :buttontext="__('Donate Now')">
                    </x-frontend.donation.user-donation>
                </div>
            @endforeach
            <div class="col-lg-12 text-center">
                <nav class="pagination-wrapper" aria-label="Page navigation">
                    {{$user_donations->links()}}
                </nav>
            </div>
        </div>
    </div>
</section>
@endsection

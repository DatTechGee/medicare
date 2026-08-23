@extends('frontend.frontend-page-master')
@section('site-title')
    {{__('Medical Campaigns')}}
@endsection
@section('page-title')
    {{__('Medical Campaigns')}}
@endsection
@section('page-meta-data')
    <meta name="description" content="{{__('Browse verified medical crowdfunding campaigns by category')}}">
@endsection
@section('style')
<style>
body{background:#0B0E1A !important}
.mf-cat{--p:#627EEA;--a:#00D4AA;--line:#232A47;--txt:#EAECF5;--mut:#9AA3BF;--fnt:#6B7594;
    min-height:100vh;color:var(--txt);
    background:
        radial-gradient(1000px 520px at 12% -8%, rgba(98,126,234,.14), transparent 60%),
        radial-gradient(900px 480px at 92% 108%, rgba(0,212,170,.10), transparent 60%),
        #12162A !important;
    padding:56px 0 90px;font-family:'Inter','Segoe UI',system-ui,-apple-system,sans-serif;}
.mf-cat-head{margin-bottom:34px}
.mf-cat-head h1{color:#fff;font-size:26px;font-weight:800;letter-spacing:-.02em;margin:0}
.mf-cat-head .back-all{display:inline-flex;align-items:center;gap:7px;margin-top:12px;color:var(--mut);font-size:13px;font-weight:700;text-decoration:none;transition:color .2s}
.mf-cat-head .back-all:hover{color:var(--a)}
.mf-empty{text-align:center;padding:70px 20px;color:var(--mut)}
.mf-empty i{font-size:42px;color:var(--fnt);margin-bottom:16px;display:block}
.mf-empty h3{color:#fff;font-size:19px;font-weight:800;margin-bottom:8px}
</style>
@endsection
@section('content')
<section class="mf-cat">
    <div class="container">
        <div class="mf-cat-head">
            <h1>{{__('Campaigns in this category')}}</h1>
            <a href="{{route('frontend.donations')}}" class="back-all"><i class="fas fa-arrow-left"></i> {{__('Browse all campaigns')}}</a>
        </div>
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
                            :buttontext="__('Donate Now')">
                    </x-frontend.donation.grid>
                </div>
            @empty
                <div class="col-lg-12 mf-empty">
                    <i class="fas fa-cubes"></i>
                    <h3>{{__('No campaigns in this category')}}</h3>
                    <p>{{__('No medical campaigns are currently available in this category.')}}</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection

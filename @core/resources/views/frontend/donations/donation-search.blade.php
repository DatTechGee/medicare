@extends('frontend.frontend-page-master')
@section('site-title')
    {{__('Search Campaigns')}}
@endsection
@section('page-title')
    {{__('Search Results:')}} {{$search_term}}
@endsection
@section('page-meta-data')
    <meta name="description" content="{{__('Search medical crowdfunding campaigns')}}">
@endsection
@section('style')
<style>
body{background:#0B0E1A !important}
.mf-search{--p:#627EEA;--a:#00D4AA;--line:#232A47;--txt:#EAECF5;--mut:#9AA3BF;--fnt:#6B7594;
    min-height:100vh;color:var(--txt);
    background:
        radial-gradient(1000px 520px at 12% -8%, rgba(98,126,234,.14), transparent 60%),
        radial-gradient(900px 480px at 92% 108%, rgba(0,212,170,.10), transparent 60%),
        #12162A !important;
    padding:56px 0 90px;font-family:'Inter','Segoe UI',system-ui,-apple-system,sans-serif;}
.mf-search-head{margin-bottom:34px}
.mf-search-head h1{color:#fff;font-size:26px;font-weight:800;letter-spacing:-.02em;margin:0}
.mf-search-head h1 em{font-style:normal;color:var(--a)}
.mf-search-head .back-all{display:inline-flex;align-items:center;gap:7px;margin-top:12px;color:var(--mut);font-size:13px;font-weight:700;text-decoration:none;transition:color .2s}
.mf-search-head .back-all:hover{color:var(--a)}
.mf-empty{text-align:center;padding:70px 20px;color:var(--mut)}
.mf-empty i{font-size:42px;color:var(--fnt);margin-bottom:16px;display:block}
.mf-empty h3{color:#fff;font-size:19px;font-weight:800;margin-bottom:8px}
.pagination-wrapper .page-item .page-link{background:#141936 !important;color:var(--mut) !important;border-color:var(--line) !important;border-radius:10px !important;font-weight:700;font-size:13px}
.pagination-wrapper .page-item.active .page-link{background:var(--p) !important;color:#fff !important;border-color:var(--p) !important}
</style>
@endsection
@section('content')
<section class="mf-search">
    <div class="container">
        <div class="mf-search-head">
            <h1>{{__('Results for')}} &ldquo;<em>{{$search_term}}</em>&rdquo;</h1>
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
                    <i class="fas fa-magnifying-glass"></i>
                    <h3>{{__('No campaigns found for')}} "{{$search_term}}"</h3>
                    <p>{{__('Try different keywords or browse all campaigns.')}}</p>
                </div>
            @endforelse
            @if(method_exists($all_donations,'links'))
            <div class="col-lg-12 text-center">
                <nav class="pagination-wrapper" aria-label="Page navigation">{{$all_donations->links()}}</nav>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection

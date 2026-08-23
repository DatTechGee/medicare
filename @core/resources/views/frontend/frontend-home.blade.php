@extends('frontend.frontend-master')

@section('content')
@php
    $variant = get_static_option('home_page_variant');
    if(empty($variant) || !file_exists(resource_path('views/frontend/home-pages/home-'.$variant.'.blade.php'))) {
        $variant = '07';
    }
@endphp
@include('frontend.home-pages.home-'.$variant)

@endsection

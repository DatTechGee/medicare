<section class="breadcrumb-area" style="position:relative; padding:64px 0 56px !important; background:#0B0E1A !important; overflow:hidden;">
    <div style="position:absolute;inset:0;pointer-events:none;background:
        radial-gradient(700px 320px at 12% -30%, rgba(98,126,234,.16), transparent 60%),
        radial-gradient(600px 300px at 92% 130%, rgba(0,212,170,.12), transparent 60%);"></div>
    <div class="container" style="position:relative">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-inner">
                    <h1 class="page-title" style="color:#fff !important; font-weight:800; font-size:32px; letter-spacing:-.02em; margin:0;">@yield('page-title')</h1>
                    <ul class="page-list" style="list-style: none; padding: 0; margin: 14px 0 0; display: flex; gap: 10px; align-items: center; flex-wrap:wrap;">
                        <li><a href="{{url('/')}}" style="color:rgba(154,163,191,.85); text-decoration:none; font-size:13px; font-weight:600; transition:color .2s;" onmouseover="this.style.color='#00D4AA'" onmouseout="this.style.color='rgba(154,163,191,.85)'"><i class="fas fa-home" style="margin-right:5px;"></i> {{__('Home')}}</a></li>
                        <li style="color:#3d4569;"><i class="fas fa-chevron-right" style="font-size:9px;"></i></li>
                        @if(request()->is(get_static_option('blog_page_slug').'/*') || request()->is(get_static_option('blog_page_slug').'-category'.'/*'))
                            <li><a href="{{url('/').'/'.get_static_option('blog_page_slug')}}" style="color:rgba(154,163,191,.85); text-decoration:none;font-size:13px;font-weight:600;">{{get_static_option('blog_page_name')}}</a></li>
                            <li style="color:#3d4569;"><i class="fas fa-chevron-right" style="font-size:9px;"></i></li>
                        @elseif(request()->is(get_static_option('career_with_us_page_slug').'/*') || request()->is(get_static_option('career_with_us_page_slug').'-category'.'/*'))
                            <li><a href="{{url('/').'/'.get_static_option('career_with_us_page_slug')}}" style="color:rgba(154,163,191,.85); text-decoration:none;font-size:13px;font-weight:600;">{{get_static_option('career_with_us_page_name')}}</a></li>
                            <li style="color:#3d4569;"><i class="fas fa-chevron-right" style="font-size:9px;"></i></li>
                        @elseif(request()->is(get_static_option('events_page_slug').'/*') || request()->is(get_static_option('events_page_slug').'-category'.'/*'))
                            <li><a href="{{url('/').'/'.get_static_option('events_page_slug')}}" style="color:rgba(154,163,191,.85); text-decoration:none;font-size:13px;font-weight:600;">{{get_static_option('events_page_name')}}</a></li>
                            <li style="color:#3d4569;"><i class="fas fa-chevron-right" style="font-size:9px;"></i></li>
                        @elseif(request()->is(get_static_option('success_story_page_slug').'/*') || request()->is(get_static_option('success_story_page_slug').'-category'.'/*'))
                            <li><a href="{{url('/').'/'.get_static_option('success_story_page_slug')}}" style="color:rgba(154,163,191,.85); text-decoration:none;font-size:13px;font-weight:600;">{{get_static_option('success_story_page_name')}}</a></li>
                            <li style="color:#3d4569;"><i class="fas fa-chevron-right" style="font-size:9px;"></i></li>
                        @elseif(request()->is(get_static_option('donation_page_slug').'/*'))
                            <li><a href="{{url('/').'/'.get_static_option('donation_page_slug')}}" style="color:rgba(154,163,191,.85); text-decoration:none;font-size:13px;font-weight:600;">{{get_static_option('donation_page_name')}}</a></li>
                            <li style="color:#3d4569;"><i class="fas fa-chevron-right" style="font-size:9px;"></i></li>
                        @endif
                        <li style="color:#EAECF5; font-weight:700; font-size:13px;">@yield('page-title')</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

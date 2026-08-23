<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{get_static_option('site_meta_description')}}">
    <meta name="tags" content="{{get_static_option('site_meta_tags')}}">

    <title>{{get_static_option('site_title')}} - {{__('Page Not Found')}}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    {!! render_favicon_by_id(get_static_option('site_favicon')) !!}
    <style>
        *{box-sizing:border-box}
        body{margin:0;padding:0;font-family:'Inter','Segoe UI',system-ui,-apple-system,sans-serif;
            background:#0B0E1A;color:#EAECF5;min-height:100vh;position:relative;overflow:hidden}
        body::before{content:'';position:absolute;top:-30%;left:-15%;width:60%;height:80%;
            background:radial-gradient(circle,rgba(98,126,234,.12) 0%,transparent 70%);border-radius:50%}
        body::after{content:'';position:absolute;bottom:-25%;right:-10%;width:55%;height:75%;
            background:radial-gradient(circle,rgba(0,212,170,.09) 0%,transparent 70%);border-radius:50%}
        #notfound{position:relative;height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;z-index:1}
        .nf{text-align:center;max-width:560px}
        .nf-badge{display:inline-flex;align-items:center;gap:8px;background:rgba(0,212,170,.08);
            border:1px solid rgba(0,212,170,.28);color:#00D4AA;font-size:11px;font-weight:800;
            letter-spacing:.09em;text-transform:uppercase;border-radius:999px;padding:7px 16px;margin-bottom:22px}
        .nf-code{font-size:120px;font-weight:900;line-height:1;margin-bottom:14px;
            background:linear-gradient(135deg,#627EEA,#00D4AA);-webkit-background-clip:text;background-clip:text;
            -webkit-text-fill-color:transparent;letter-spacing:-.04em}
        .nf h2{font-size:24px;font-weight:800;color:#fff;margin:0 0 10px;letter-spacing:-.02em}
        .nf p{font-size:14.5px;color:#9AA3BF;line-height:1.7;margin:0 auto 30px;max-width:420px}
        .nf-actions{display:flex;gap:12px;justify-content:center;flex-wrap:wrap}
        .nf-btn{display:inline-flex;align-items:center;gap:9px;border:none;border-radius:12px;
            padding:13px 26px;font-size:13.5px;font-weight:800;text-decoration:none;cursor:pointer;
            transition:transform .22s ease, box-shadow .22s ease}
        .nf-btn:hover{transform:translateY(-2px)}
        .nf-btn-primary{background:linear-gradient(135deg,#627EEA,#4B6CEB);color:#fff;box-shadow:0 8px 22px rgba(98,126,234,.35)}
        .nf-btn-primary:hover{box-shadow:0 14px 32px rgba(98,126,234,.5);color:#fff}
        .nf-btn-ghost{background:transparent;color:#EAECF5;border:1.5px solid #232A47}
        .nf-btn-ghost:hover{border-color:rgba(0,212,170,.45);color:#00D4AA}
        @media(max-width:600px){.nf-code{font-size:84px}}
    </style>
</head>
<body>
    <div id="notfound">
        <div class="nf">
            <span class="nf-badge"><i class="fas fa-triangle-exclamation"></i> {{__('Error')}} 404</span>
            <div class="nf-code">{{get_static_option('error_404_page_title') ?: '404'}}</div>
            <h2>{{get_static_option('error_404_page_subtitle') ?: __('This page could not be found')}}</h2>
            <p>{{get_static_option('error_404_page_paragraph') ?: __('The page you are looking for may have been moved or no longer exists. Head back to safety and continue supporting verified medical campaigns.')}}</p>
            <div class="nf-actions">
                <a href="{{route('homepage')}}" class="nf-btn nf-btn-primary"><i class="fas fa-home"></i> {{get_static_option('error_404_page_button_text') ?: __('Back To Home')}}</a>
                <a href="{{route('frontend.donations')}}" class="nf-btn nf-btn-ghost"><i class="fas fa-hand-holding-heart"></i> {{__('Browse Campaigns')}}</a>
            </div>
        </div>
    </div>
</body>
</html>

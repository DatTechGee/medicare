<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{get_static_option('site_title') ?? 'MediFund'}} - {{__('Server Error')}}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    {!! render_favicon_by_id(get_static_option('site_favicon')) !!}
    <style>
        *{box-sizing:border-box}
        body{margin:0;padding:0;font-family:'Inter','Segoe UI',system-ui,-apple-system,sans-serif;
            background:#0B0E1A;color:#EAECF5;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
        .er{text-align:center;max-width:540px}
        .er-badge{display:inline-flex;align-items:center;gap:8px;background:rgba(255,92,122,.09);
            border:1px solid rgba(255,92,122,.3);color:#ff5c7a;font-size:11px;font-weight:800;
            letter-spacing:.09em;text-transform:uppercase;border-radius:999px;padding:7px 16px;margin-bottom:22px}
        .er-code{font-size:110px;font-weight:900;line-height:1;margin-bottom:14px;
            background:linear-gradient(135deg,#627EEA,#00D4AA);-webkit-background-clip:text;background-clip:text;
            -webkit-text-fill-color:transparent;letter-spacing:-.04em}
        .er h2{font-size:22px;font-weight:800;color:#fff;margin:0 0 10px}
        .er p{font-size:14px;color:#9AA3BF;line-height:1.7;margin:0 auto 30px;max-width:420px}
        .er a{display:inline-flex;align-items:center;gap:9px;border:none;border-radius:12px;
            padding:13px 26px;font-size:13.5px;font-weight:800;text-decoration:none;
            background:linear-gradient(135deg,#627EEA,#4B6CEB);color:#fff;
            box-shadow:0 8px 22px rgba(98,126,234,.35);transition:transform .2s}
        .er a:hover{transform:translateY(-2px);color:#fff}
    </style>
</head>
<body>
<div class="er">
    <span class="er-badge"><i class="fas fa-triangle-exclamation"></i> {{__('Server Error')}} 500</span>
    <div class="er-code">500</div>
    <h2>{{__('Something went wrong on our side')}}</h2>
    <p>{{__('Our team has been notified. Please try again in a few moments — your donations and campaigns are safe on-chain.')}}</p>
    <a href="{{url('/')}}"><i class="fas fa-home"></i> {{__('Back To Home')}}</a>
</div>
</body>
</html>

@extends('frontend.frontend-page-master')
@section('page-title')
    {{__('Login')}}
@endsection
@section('content')
<section style="min-height:80vh;display:flex;align-items:center;background:#080814">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div style="background:#111128;border:1px solid #1e1e3a;border-radius:20px;padding:40px;margin-bottom:20px">
                    <div style="text-align:center;margin-bottom:32px">
                        <div style="display:inline-flex;align-items:center;justify-content:center;width:56px;height:56px;background:linear-gradient(135deg,#627EEA,#00D4AA);border-radius:14px;margin-bottom:14px">
                            <i class="fas fa-heartbeat" style="font-size:26px;color:#fff"></i>
                        </div>
                        <h1 style="margin:0;font-size:24px;font-weight:700;color:#fff;letter-spacing:-0.5px">MediFund</h1>
                        <p style="margin:6px 0 0;font-size:13px;color:#8888aa;letter-spacing:0.5px;text-transform:uppercase">Secure Medical Giving</p>
                    </div>
                    <x-msg.error/>
                    <x-msg.success/>
                    <form action="{{route('user.login')}}" method="post" enctype="multipart/form-data" class="account-form" id="login_form_order_page">
                        @csrf
                        <div class="error-wrap"></div>
                        <div style="margin-bottom:18px">
                            <label style="display:block;font-size:12px;font-weight:600;color:#aaa;margin-bottom:6px;text-transform:uppercase;letter-spacing:0.5px">{{__('Username or Email')}}</label>
                            <input type="text" name="username" placeholder="{{__('Enter username or email')}}" required style="width:100%;padding:12px 16px;background:#0a0a1a;border:1px solid #1e1e3a;border-radius:10px;color:#fff;font-size:14px;outline:none;transition:border-color .2s">
                        </div>
                        <div style="margin-bottom:18px">
                            <label style="display:block;font-size:12px;font-weight:600;color:#aaa;margin-bottom:6px;text-transform:uppercase;letter-spacing:0.5px">{{__('Password')}}</label>
                            <input type="password" name="password" placeholder="{{__('Enter your password')}}" required style="width:100%;padding:12px 16px;background:#0a0a1a;border:1px solid #1e1e3a;border-radius:10px;color:#fff;font-size:14px;outline:none;transition:border-color .2s">
                        </div>
                        <div style="margin-bottom:18px">
                            <label style="display:block;font-size:12px;font-weight:600;color:#aaa;margin-bottom:6px;text-transform:uppercase;letter-spacing:0.5px">{{__('Security Check')}}</label>
                            <div style="display:flex;align-items:center;gap:10px">
                                <div style="padding:12px 16px;background:#0a0a1a;border:1px solid #1e1e3a;border-radius:10px;color:#00D4AA;font-size:15px;font-weight:700;white-space:nowrap">
                                    {{ session('math_captcha_a', '?') }} + {{ session('math_captcha_b', '?') }} = ?
                                </div>
                                <input type="number" name="captcha_answer" id="captcha_answer" placeholder="{{__('Your answer')}}" min="0" required style="flex:1;padding:12px 16px;background:#0a0a1a;border:1px solid #1e1e3a;border-radius:10px;color:#fff;font-size:14px;outline:none;transition:border-color .2s">
                            </div>
                        </div>
                        <div style="margin-bottom:20px">
                            <div style="display:flex;align-items:center;justify-content:space-between">
                                <label style="display:flex;align-items:center;gap:6px;cursor:pointer;color:#aaa;font-size:13px">
                                    <input type="checkbox" name="remember" id="remember" style="accent-color:#627EEA"> {{__('Remember Me')}}
                                </label>
                            </div>
                        </div>
                        <button type="submit" id="login_btn" style="width:100%;padding:14px;background:linear-gradient(135deg,#627EEA,#00D4AA);border:none;border-radius:10px;color:#fff;font-size:15px;font-weight:700;cursor:pointer;transition:opacity .2s;letter-spacing:0.3px">
                            {{__('Login')}}
                        </button>
                        <div style="display:flex;align-items:center;justify-content:center;gap:24px;margin-top:18px">
                            <a href="{{route('user.register')}}" style="color:#aaa;font-size:13px;text-decoration:none;transition:color .2s">
                                {{__("Don't have an account?")}} <span style="color:#627EEA;font-weight:600">{{__('Create one')}}</span>
                            </a>
                        </div>
                        <div style="display:flex;align-items:center;justify-content:center;margin-top:12px">
                            <a href="{{route('user.forget.password')}}" style="color:#8888aa;font-size:12px;text-decoration:none;transition:color .2s">
                                {{__('Forgot Password?')}}
                            </a>
                        </div>
                    </form>
                </div>
                <div style="display:flex;align-items:center;justify-content:center;margin-bottom:24px;gap:14px">
                    <div style="flex:1;height:1px;background:#1e1e3a"></div>
                    <span style="color:#555;font-size:11px;font-weight:600;letter-spacing:1px;text-transform:uppercase">{{__('OR')}}</span>
                    <div style="flex:1;height:1px;background:#1e1e3a"></div>
                </div>
                <div style="text-align:center;margin-bottom:24px">
                    <a href="{{route('user.wallet.login')}}" style="display:inline-flex;align-items:center;gap:10px;padding:14px 28px;background:#111128;border:1px solid #1e1e3a;border-radius:12px;color:#aaa;font-size:14px;font-weight:600;text-decoration:none;transition:all .2s">
                        <i class="fab fa-ethereum" style="font-size:18px;color:#627EEA"></i>
                        {{__('Login with MetaMask Wallet')}}
                    </a>
                </div>
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:8px;">
                    <div style="text-align:center;padding:12px 8px;background:rgba(98,126,234,.05);border:1px solid rgba(98,126,234,.12);border-radius:10px;">
                        <i class="fas fa-shield-halved" style="color:#627EEA;font-size:18px;display:block;margin-bottom:6px;"></i>
                        <p style="color:#888;font-size:10px;font-weight:700;margin:0;text-transform:uppercase;letter-spacing:.5px;">{{__('Escrow Protected')}}</p>
                    </div>
                    <div style="text-align:center;padding:12px 8px;background:rgba(0,212,170,.05);border:1px solid rgba(0,212,170,.12);border-radius:10px;">
                        <i class="fas fa-hospital" style="color:#00D4AA;font-size:18px;display:block;margin-bottom:6px;"></i>
                        <p style="color:#888;font-size:10px;font-weight:700;margin:0;text-transform:uppercase;letter-spacing:.5px;">{{__('Hospital Verified')}}</p>
                    </div>
                    <div style="text-align:center;padding:12px 8px;background:rgba(255,184,0,.05);border:1px solid rgba(255,184,0,.12);border-radius:10px;">
                        <i class="fas fa-cubes" style="color:#FFB800;font-size:18px;display:block;margin-bottom:6px;"></i>
                        <p style="color:#888;font-size:10px;font-weight:700;margin:0;text-transform:uppercase;letter-spacing:.5px;">{{__('On-Chain')}}</p>
                    </div>
                </div>
                @if(get_static_option('enable_facebook_login') || get_static_option('enable_google_login'))
                <div style="display:flex;align-items:center;gap:12px;justify-content:center">
                    @if(get_static_option('enable_facebook_login'))
                    <a href="{{route('login.facebook.redirect')}}" style="display:inline-flex;align-items:center;justify-content:center;width:48px;height:48px;background:#111128;border:1px solid #1e1e3a;border-radius:12px;color:#aaa;font-size:18px;text-decoration:none;transition:all .2s" title="{{__('Login With Facebook')}}">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    @endif
                    @if(get_static_option('enable_google_login'))
                    <a href="{{route('login.google.redirect')}}" style="display:inline-flex;align-items:center;justify-content:center;width:48px;height:48px;background:#111128;border:1px solid #1e1e3a;border-radius:12px;color:#aaa;font-size:18px;text-decoration:none;transition:all .2s" title="{{__('Login With Google')}}">
                        <i class="fab fa-google"></i>
                    </a>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
@section('scripts')
    <script>
        (function($){
            "use strict";
            $(document).on('click', '#login_btn', function (e) {
                e.preventDefault();
                var formContainer = $('#login_form_order_page');
                var el = $(this);
                var username = formContainer.find('input[name="username"]').val();
                var password = formContainer.find('input[name="password"]').val();
                var remember = formContainer.find('input[name="remember"]').val();
                var captchaAnswer = formContainer.find('input[name="captcha_answer"]').val();

                el.text('{{__("Please Wait")}}');

                $.ajax({
                    type: 'post',
                    url: "{{route('user.ajax.login')}}",
                    data: {
                        _token: "{{csrf_token()}}",
                        username : username,
                        password : password,
                        remember : remember,
                        captcha_answer : captchaAnswer,
                    },
                    success: function (data){
                        if(data.status == 'invalid'){
                            el.text('{{__("Login")}}')
                            formContainer.find('.error-wrap').html('<div class="alert alert-danger">'+data.msg+'</div>');
                        }else{
                            formContainer.find('.error-wrap').html('');
                            el.text('{{__("Login Success.. Redirecting ..")}}');
                            location.reload();
                        }
                    },
                    error: function (data){
                        var response = data.responseJSON.errors;
                        formContainer.find('.error-wrap').html('<ul class="alert alert-danger"></ul>');
                        $.each(response,function (value,index){
                            formContainer.find('.error-wrap ul').append('<li>'+index+'</li>');
                        });
                        el.text('{{__("Login")}}');
                    }
                });
            });
        })(jQuery)
    </script>
@endsection
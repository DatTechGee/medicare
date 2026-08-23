@extends('frontend.frontend-page-master')
@section('page-title')
    {{__('Create Account')}}
@endsection
@section('style')
<style>
.role-select-cards{display:flex;gap:14px;margin-bottom:6px}
.role-card{flex:1;border:2px solid #e5e7eb;border-radius:14px;padding:18px 12px;text-align:center;cursor:pointer;transition:all .25s ease;background:#fff;position:relative}
.role-card:hover{border-color:#0d9488;transform:translateY(-2px);box-shadow:0 6px 18px rgba(13,148,136,.15)}
.role-card input{position:absolute;opacity:0}
.role-card .rc-icon{width:46px;height:46px;margin:0 auto 10px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:20px;background:#f0fdfa;color:#0d9488;transition:all .25s}
.role-card .rc-title{font-weight:700;font-size:14.5px;color:#111827}
.role-card .rc-desc{font-size:11.5px;color:#6b7280;margin-top:4px;line-height:1.4}
.role-card.selected{border-color:#0d9488;background:#f0fdfa}
.role-card.selected .rc-icon{background:#0d9488;color:#fff}
</style>
@endsection
@section('content')
    <section class="login-page-wrapper py-5 my-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="login-form-wrapper">
                        <h2 class="text-center">{{__('Create Your Account')}}</h2>
                        <p class="text-center text-muted">{{__('Join MediFund to start medical campaigns or support patients with secure blockchain donations.')}}</p><br>
                        @include('backend.partials.message')
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->all() as $error)
                                        <li>{{$error}}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="{{route('user.register')}}" method="post" enctype="multipart/form-data" class="account-form" id="registerForm">
                            @csrf
                            <input type="hidden" name="captcha_token" id="gcaptcha_token">

                            <label style="font-weight:600;font-size:13.5px;margin-bottom:10px;display:block;">{{__('I am registering as')}} <span class="text-danger">*</span></label>
                            <div class="role-select-cards">
                                <label class="role-card selected" id="roleCardPatient">
                                    <input type="radio" name="role" value="patient" checked>
                                    <div class="rc-icon"><i class="fas fa-user-injured"></i></div>
                                    <div class="rc-title">{{__('Patient')}}</div>
                                    <div class="rc-desc">{{__('Create verified medical campaigns & receive donations to your wallet')}}</div>
                                </label>
                                <label class="role-card" id="roleCardDonor">
                                    <input type="radio" name="role" value="donor">
                                    <div class="rc-icon"><i class="fas fa-hand-holding-heart"></i></div>
                                    <div class="rc-title">{{__('Donor')}}</div>
                                    <div class="rc-desc">{{__('Support patients by donating via MetaMask — anonymously or openly')}}</div>
                                </label>
                            </div>
                            <br>

                            <div class="form-group">
                                <input type="text" name="name" class="form-control" placeholder="{{__('Name')}}">
                            </div>
                            <div class="form-group">
                                <input type="text" name="username" class="form-control" placeholder="{{__('Username')}}">
                            </div>
                            <div class="form-group">
                                <input type="email" name="email" class="form-control" placeholder="{{__('Email')}}">
                            </div>
                            <div class="form-group">
                                <select id="country" class="form-control" name="country_id">
                                    @foreach($all_countries as $country)
                                     <option value="{{$country->id}}">{{$country->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="text" name="city" class="form-control" placeholder="{{__('City')}}">
                            </div>
                            <div class="form-group">
                                <input type="password" name="password" class="form-control" placeholder="{{__('Password')}}">
                            </div>
                            <div class="form-group">
                                <input type="password" name="password_confirmation" class="form-control" placeholder="{{__('Confirm Password')}}">
                            </div>

                            <div class="form-group form-check col-12">
                                <input type="checkbox" class="form-check-input" name="agree_terms" id="Check11">
                                <label class="form-check-label" for="Check11">
                                   {{__('By creating an account, you agree to the')}}
                                    <a href="{{get_static_option('register_page_terms_of_service_url')}}">{{__('terms of service and Conditions')}},</a> {{__('and')}}
                                    <a href="{{get_static_option('register_page_privacy_policy_url')}}">{{__('privacy policy')}}</a>
                                </label>
                            </div>

                            <div class="form-group btn-wrapper">
                                <button type="submit" class="submit-btn boxed-btn reverse-color">{{__('Register')}}</button>
                            </div>
                            <div class="row mb-4 rmber-area">
                                <div class="col-12 text-center">
                                    <a href="{{route('user.login')}}">{{__('Already Have account?')}}</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('scripts')
    <script src="https://www.google.com/recaptcha/api.js?render={{get_static_option('site_google_captcha_v3_site_key')}}"></script>
    <script>

        grecaptcha.ready(function() {
            grecaptcha.execute("{{get_static_option('site_google_captcha_v3_site_key')}}", {action: 'homepage'}).then(function(token) {
                document.getElementById('gcaptcha_token').value = token;
            });
        });

        (function(){
            var cards = {patient: document.getElementById('roleCardPatient'), donor: document.getElementById('roleCardDonor')};
            Object.keys(cards).forEach(function(role){
                cards[role].addEventListener('click', function(){
                    this.querySelector('input[type=radio]').checked = true;
                    Object.keys(cards).forEach(function(r){ cards[r].classList.toggle('selected', r === role); });
                });
            });
        })();
    </script>
@endsection

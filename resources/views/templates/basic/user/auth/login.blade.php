@extends($activeTemplate . 'layouts.auth')

@section('app')
    @php
        $login = getContent('login.content', true);
    @endphp
    <section class="account-section bg-overlay-black bg_img" data-background="{{ getImage('assets/images/frontend/login/' . @$login->data_values->background_image, '1780x760') }}">
        <div class="container">
            <div class="row account-area align-items-center justify-content-center">
                <div class="col-xxl-4 col-xl-5 col-lg-6 col-md-8">
                    <div class="account-form-area">
                        <div class="account-logo-area text-center">
                            <div class="account-logo">
                                <a href="{{ route('home') }}"><img src="{{ siteLogo() }}" alt="logo"></a>
                            </div>
                        </div>
                        <div class="account-header text-center">
                            @php
                                $credentials = $general->socialite_credentials;
                            @endphp
                            @if (@$credentials->google->status == Status::ENABLE || @$credentials->facebook->status == Status::ENABLE || @$credentials->linkedin->status == Status::ENABLE)
                                <h3 class="title mb-3">@lang('Login with')</h3>
                                <ul class="login-social">
                                    @if (@$credentials->facebook->status == Status::ENABLE)
                                        <li>
                                            <a class="facebook" href="{{ route('user.social.login', 'facebook') }}"><i class="lab la-facebook-f"></i></a>
                                        </li>
                                    @endif
                                    @if (@$credentials->google->status == Status::ENABLE)
                                        <li>
                                            <a class="google" href="{{ route('user.social.login', 'google') }}"><i class="lab la-google"></i></a>
                                        </li>
                                    @endif
                                    @if (@$credentials->linkedin->status == Status::ENABLE)
                                        <li>
                                            <a class="linkedin" href="{{ route('user.social.login', 'linkedin') }}"><i class="lab la-linkedin-in"></i></a>
                                        </li>
                                    @endif
                                </ul>
                                <div class="form-separator"><span>@lang('or')</span></div>
                            @endif
                        </div>
                        <form class="account-form verify-gcaptcha" method="POST" action="{{ route('user.login') }}">
                            @csrf
                            <div class="row ml-b-20">
                                <div class="col-lg-12 form-group">
                                    <label>@lang('Username Or Email')</label>
                                    <input class="form-control form--control" name="username" type="text" value="{{ old('username') }}" required>
                                </div>
                                <div class="col-lg-12 form-group">
                                    <label>{{ __('Password') }}</label>
                                    <input class="form-control form--control" id="password" name="password" type="password" required>
                                </div>

                                <x-captcha />

                                <div class="col-lg-12">
                                    <div class="checkbox-wrapper d-flex align-items-center flex-wrap mt-0">
                                        <div class="checkbox-item">
                                            <label><a href="{{ route('user.password.request') }}">@lang('Forgot Your Password?')</a></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 form-group text-center">
                                    <button class="submit-btn" id="recaptcha" type="submit">
                                        @lang('Login')
                                    </button>
                                </div>
                                <div class="col-lg-12 text-center">
                                    <div class="account-item mt-10">
                                        <label>@lang("Don't Have An Account?") <a class="text--base" href="{{ route('user.register') }}">@lang('Register Now')</a></label>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        "use strict";

        function submitUserForm() {
            var response = grecaptcha.getResponse();
            if (response.length == 0) {
                document.getElementById('g-recaptcha-error').innerHTML = '<span class="text-danger">@lang('Captcha field is required.')</span>';
                return false;
            }
            return true;
        }
    </script>
@endpush

@extends($activeTemplate . 'layouts.frontend')

@section('content')
    @php
        $login = getContent('login.content', true);
    @endphp

    <section class="pt-80 pb-80">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="account-area">
                        <div class="left text-center">
                            <img src="{{ getImage(getFilePath('logoIcon') . '/logo.png') }}" alt="logo">
                        </div>
                        <div class="right">

                            <div class="text-center">
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
                            <form class="account-from verify-gcaptcha" action="{{ route('user.login') }}" method="post">
                                @csrf
                                <div class="form-group">
                                    <label>@lang('Username')</label>
                                    <input class="form-control" name="username" type="text" value="{{ old('username') }}" placeholder="@lang('Username')">
                                </div>
                                <div class="form-group">
                                    <label>@lang('Password')</label>
                                    <input class="form-control" name="password" type="password" placeholder="@lang('Password')">
                                </div>
                                <x-captcha />
                                <div class="text-center">
                                    <button class="cmn-btn w-100" type="submit">@lang('Login')</button>
                                </div>
                                <p class="mt-3">@lang('Forgate password?') <a class="base--color" href="{{ route('user.password.request') }}">@lang('Reset now')</a></p>
                            </form>

                        </div>
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

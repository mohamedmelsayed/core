@extends($activeTemplate . 'layouts.auth')
@section('app')
    @php
        $policyPages = getContent('policy_pages.element', false, null, true);
        $register = getContent('register.content', true);
    @endphp
    <section class="account-section bg-overlay-black bg_img" data-background="{{ getImage('assets/images/frontend/register/' . @$register->data_values->background_image, '1780x760') }}">
        <div class="container">
            <div class="row account-area align-items-center justify-content-center">
                <div class="col-xxl-4 col-xl-5 col-lg-6 col-md-8">
                    <div class="account-form-area">
                        <div class="account-logo-area text-center">
                            <div class="account-logo">
                                <a href="{{ route('home') }}"><img src="{{ siteLogo() }}" alt="logo"></a>
                            </div>
                        </div>
                        <div class="text-center">
                            @php
                                $credentials = $general->socialite_credentials;
                            @endphp
                            @if (@$credentials->google->status == Status::ENABLE || @$credentials->facebook->status == Status::ENABLE || @$credentials->linkedin->status == Status::ENABLE)
                                <h3 class="title mb-3">@lang('Register with')</h3>
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
                        <form class="account-form verify-gcaptcha" action="{{ route('user.register') }}" method="POST">
                            @csrf

                            <div class="row ml-b-20">
                                <div class="col-lg-6 form-group">
                                    <label>{{ __('Username') }}*</label>
                                    <input class="form-control form--control checkUser" id="username" name="username" type="text" value="{{ old('username') }}" required>
                                    <small class="text-danger usernameExist"></small>
                                </div>

                                <div class="col-lg-6 form-group">
                                    <label>@lang('E-Mail Address')*</label>
                                    <input class="form-control form--control checkUser" id="email" name="email" type="email" value="{{ old('email') }}" required>
                                </div>
                                <!-- <div class="col-lg-6 form-group">
                                    <label>@lang('Country')*</label>
                                    <div class="input-group">
                                        <select class="form-control form--control" id="country" name="country">
                                            @foreach ($countries as $key => $country)
                                                <option class="text-dark" data-mobile_code="{{ $country->dial_code }}" data-code="{{ $key }}" value="{{ $country->country }}">{{ __($country->country) }}</option>
                                            @endforeach
                                        </select>
                                        <span class="input-group-text"><i class="las la-globe"></i></span>
                                    </div>
                                </div>

                                <div class="col-lg-6 form-group">
                                    <label>@lang('Mobile')*</label>
                                    <div class="input-group">
                                        <span class="input-group-text mobile-code bg--base"></span>
                                        <input name="mobile_code" type="hidden">
                                        <input name="country_code" type="hidden">
                                        <input class="form-control form--control checkUser" id="mobile" name="mobile" type="number" value="{{ old('mobile') }}">
                                    </div>
                                    <small class="text-danger mobileExist"></small>
                                </div> -->

                                <div class="col-lg-6 form-group">
                                    <label>@lang('Password')*</label>
                                    <input type="password" class="form-control form--control @if ($general->secure_password) secure-password @endif" name="password" required>
                                </div>
                                <div class="col-lg-6 form-group">
                                    <label>@lang('Confirm Password')*</label>
                                    <input class="form-control form--control" id="password-confirm" name="password_confirmation" type="password" required autocomplete="new-password">
                                </div>

                                <x-captcha />

                                @if ($general->agree)
                                    <div class="col-lg-12 form-group">
                                        <div class="checkbox-wrapper">
                                            <div class="checkbox-item custom--checkbox">
                                                <input class="checkbox--input" id="agree" name="agree" type="checkbox">
                                                <label class="checkbox--label pe-1" for="agree">
                                                    @lang('I agree with')
                                                </label>
                                                <span>
                                                    @forelse($policyPages as $item)
                                                        <a class="text--base" href="{{ route('policies', [$item->id, slug($item->data_values->title)]) }}" target="_blank">{{ __($item->data_values->title) }}</a>
                                                        {{ $loop->last ? '' : ',' }}
                                                    @empty
                                                    @endforelse
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="col-lg-12 form-group text-center">
                                    <button class="submit-btn" id="recaptcha" type="submit">
                                        @lang('Register')
                                    </button>
                                </div>
                                <div class="col-lg-12 text-center">
                                    <div class="account-item mt-10">
                                        <label>@lang('Already Have An Account?') <a class="text--base" href="{{ route('user.login') }}">@lang('Login Now')</a></label>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    </section>

    <div class="modal fade" id="existModalCenter" role="dialog" aria-labelledby="existModalCenterTitle" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="existModalLongTitle">@lang('You are with us')</h5>
                    <span class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <h6 class="text-center">@lang('You already have an account please Login ')</h6>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-dark btn-sm" data-bs-dismiss="modal" type="button">@lang('Close')</button>
                    <a class="btn btn--base btn-sm" href="{{ route('user.login') }}">@lang('Login')</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .country-code .input-group-text {
            background: #fff !important;
        }

        .country-code select {
            border: none;
        }

        .country-code select:focus {
            border: none;
            outline: none;
        }
    </style>
@endpush
@if ($general->secure_password)
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif
@push('script')
    <script>
        "use strict";
        (function($) {
            @if ($mobileCode)
                $(`option[data-code={{ $mobileCode }}]`).attr('selected', '');
            @endif
            $('select[name=country]').change(function() {
                $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
                $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
                $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));
            });
            $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
            $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
            $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));
            $('.checkUser').on('focusout', function(e) {
                var url = '{{ route('user.checkUser') }}';
                var value = $(this).val();
                var token = '{{ csrf_token() }}';
                if ($(this).attr('name') == 'mobile') {
                    var mobile = `${$('.mobile-code').text().substr(1)}${value}`;
                    var data = {
                        mobile: mobile,
                        _token: token
                    }
                }
                if ($(this).attr('name') == 'email') {
                    var data = {
                        email: value,
                        _token: token
                    }
                }
                if ($(this).attr('name') == 'username') {
                    var data = {
                        username: value,
                        _token: token
                    }
                }
                $.post(url, data, function(response) {
                    if (response.data != false && response.type == 'email') {
                        $('#existModalCenter').modal('show');
                    } else if (response.data != false) {
                        $(`.${response.type}Exist`).text(`${response.type} already exist`);
                    } else {
                        $(`.${response.type}Exist`).text('');
                    }
                });
            });
        })(jQuery);
    </script>
@endpush

@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $policyPages = getContent('policy_pages.element', false, null, true);
        $register = getContent('register.content', true);
    @endphp

    <section class="pt-80 pb-80">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">

                    <div class="account-area">

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

                        <form class="account-from w-100 verify-gcaptcha" action="{{ route('user.register') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('Username')</label>
                                        <input class="form-control checkUser" name="username" type="text" value="{{ old('username') }}" required>
                                        <small class="text-danger usernameExist"></small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('Email')</label>
                                        <input class="form-control checkUser" name="email" type="email" value="{{ old('email') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('Country')</label>
                                        <div class="input-group">
                                            <select class="form-control form-select" id="country" name="country">
                                                @foreach ($countries as $key => $country)
                                                    <option class="text-white" data-mobile_code="{{ $country->dial_code }}" data-code="{{ $key }}" value="{{ $country->country }}">{{ __($country->country) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('Mobile')</label>
                                        <div class="input-group">
                                            <span class="input-group-text mobile-code"></span>
                                            <input name="mobile_code" type="hidden">
                                            <input name="country_code" type="hidden">
                                            <input class="form-control checkUser" id="mobile" name="mobile" type="number" value="{{ old('mobile') }}">
                                        </div>
                                        <small class="text-danger mobileExist"></small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('Password')</label>
                                        <input type="password" class="form-control @if ($general->secure_password) secure-password @endif" name="password" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('Re-Password')</label>
                                        <input class="form-control" name="password_confirmation" type="password" required>
                                    </div>
                                </div>
                            </div>
                            <x-captcha />
                            @if ($general->agree)
                                <div class="col-lg-12 form-group">
                                    <div class="checkbox-wrapper d-flex align-items-center flex-wrap">
                                        <div class="custom--checkbox">
                                            <input class="checkbox--input" id="agree" name="agree" type="checkbox">
                                            <label class="checkbox--label" for="agree">
                                                @lang('I agree with')
                                            </label>
                                            <span>
                                                @forelse($policyPages as $item)
                                                    <a class="base--color" href="{{ route('policies', [$item->id, slug($item->data_values->title)]) }}" target="_blank">{{ __($item->data_values->title) }}</a>
                                                    {{ $loop->last ? '' : ',' }}
                                                @empty
                                                @endforelse
                                            </span>
                                        </div>

                                    </div>
                                </div>
                            @endif
                            <div class="text-center">
                                <button class="cmn-btn w-100" type="submit">@lang('Register')</button>
                            </div>
                            <p class="mt-3">@lang('Already have an account?') <a class="base--color" href="{{ route('user.login') }}">@lang('Login now')</a></p>
                        </form>
                    </div>
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
                    <a class="cmn-btn btn-sm" href="{{ route('user.login') }}">@lang('Login')</a>
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

        .account-area {
            display: block;
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

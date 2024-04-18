@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="account-section bg-overlay-black ptb-80">
        <div class="container">
            <div class="d-flex justify-content-center">
                <div class="verification-code-wrapper">
                    <div class="verification-area">
                        <form class="submit-form" action="{{ route('user.verify.mobile') }}" method="POST">
                            @csrf
                            <p>@lang('A 6 digit verification code sent to your mobile number') : +{{ showMobileNumber(auth()->user()->mobile) }}</p>
                            @include($activeTemplate . 'partials.verification_code')
                            <div class="mb-3">
                                <button class="btn btn--base w-100" type="submit">@lang('Submit')</button>
                            </div>
                            <p>
                                @lang('If you don\'t get any code'), <a class="forget-pass text--base" href="{{ route('user.send.verify.code', 'phone') }}"> @lang('Try again')</a>
                            </p>
                            @if ($errors->has('resend'))
                                <br />
                                <small class="text--danger">{{ $errors->first('resend') }}</small>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('style')
    <style>
        .verification-code-wrapper {
            border: 1px solid #3e2d34;
        }

        .verification-code::after {
            background-color: #11141d;
        }

        .verification-code span {
            border: solid 1px #3e2d34;
        }
    </style>
@endpush

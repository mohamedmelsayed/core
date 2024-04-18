@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="pt-80 pb-80">
        <div class="container">
            <div class="d-flex justify-content-center">
                <div class="verification-code-wrapper">
                    <div class="verification-area">
                        <h5 class="pb-3 text-center">@lang('Verify Mobile Number')</h5>
                        <form class="submit-form" action="{{ route('user.verify.mobile') }}" method="POST">
                            @csrf
                            <p class="py-3">@lang('A 6 digit verification code sent to your mobile number') : +{{ showMobileNumber(auth()->user()->mobile) }}</p>
                            @include($activeTemplate . 'partials.verification_code')
                            <div class="mb-3">
                                <button class="cmn-btn w-100" type="submit">@lang('Submit')</button>
                            </div>
                            <p>
                                @lang('If you don\'t get any code'), <a class="forget-pass base--color" href="{{ route('user.send.verify.code', 'phone') }}"> @lang('Try again')</a>
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
            border: 1px solid #28284a;
        }

        .verification-code::after {
            background-color: #0d0d31;
        }

        .verification-code span {
            border: solid 1px #28284a;
        }
    </style>
@endpush

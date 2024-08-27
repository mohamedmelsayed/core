@extends($activeTemplate . 'layouts.frontend')

@section('content')
<section class="account-section bg-overlay-black ptb-80">
    <div class="container">
        <div class="d-flex justify-content-center">
            <div class="verification-code-wrapper">
                <div class="verification-area">
                    <form class="submit-form" action="{{ route('user.add.mobile') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6 form-group">
                                <label>@lang('Country')*</label>
                                <div class="input-group">
                                    <select class="form-control form--control" id="country" name="country">
                                        @foreach ($countries as $key => $country)
                                            <option class="text-dark" data-mobile_code="{{ $country->dial_code }}" data-code="{{ $key }}" value="{{ $key }}">{{ __($country->country) }}</option>
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
                            </div>
                        </div>
                        <div class="mb-3">
                            <button class="btn btn--base w-100" type="submit">@lang('Submit')</button>
                        </div>
                        <p>
                            @lang('If you don\'t get any code'), 
                            <a class="forget-pass text--base" href="{{ route('user.send.verify.code', 'phone') }}"> @lang('Try again')</a>
                        </p>
                        @if ($errors->has('resend'))
                            <br />
                            <small class="text--danger">{{ $errors->first('resend') }}</small>
                        @endif
                    </form>
                    <!-- Added Skip button -->
                    <div class="text-center mt-3">
                        <a href="{{ route('home') }}" class="btn btn--base">@lang('Skip and Continue')</a>
                    </div>
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

@push('script')
<script>
    "use strict";
    (function($) {
        @if ($mobileCode)
            $(`option[data-code={{ $mobileCode }}]`).attr('selected', '');
        @endif
    })(jQuery);
    
    $(document).ready(function() {
        $('#country').change(function() {
            var mobileCode = $(this).find(':selected').data('mobile_code');
            var countryCode = $(this).find(':selected').data('code');
            $('.mobile-code').text('+' + mobileCode);
            $('input[name=mobile_code]').val(mobileCode);
            $('input[name=country_code]').val(countryCode);
        }).trigger('change');
    });
</script>
@endpush

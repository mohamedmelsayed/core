@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="account-section bg-overlay-black ptb-80">
        <div class="container">
            <div class="d-flex justify-content-center">
                <div class="verification-code-wrapper">
                    <div class="verification-area">

                    <h3>@lang('A verification link is sent to your email address'): {{ showEmailAddress($user->email) }}</h3>
                    <h5>
                                @lang('If you don\'t get any code'), <a class="text--base" href="{{ route('user.send.verify.code', 'email') }}"> @lang('Try again')</a>
</h5>
                
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

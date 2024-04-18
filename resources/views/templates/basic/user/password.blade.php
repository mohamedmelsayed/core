@extends($activeTemplate . 'layouts.master')

@section('content')
    <section class="section--bg ptb-80">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-xl-6">

                    <div class="card custom--card">
                        <div class="card-header">
                            <h5 class="title">{{ __($pageTitle) }}</h5>
                        </div>
                        <div class="card-body">

                            <form action="" method="post">
                                @csrf
                                <div class="form-group">
                                    <label class="form-label">@lang('Current Password')</label>
                                    <input class="form-control form--control" name="current_password" type="password" required autocomplete="current-password">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">@lang('Password')</label>
                                    <input type="password" class="form-control form--control @if ($general->secure_password) secure-password @endif" name="password" required autocomplete="current-password">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">@lang('Confirm Password')</label>
                                    <input class="form-control form--control" name="password_confirmation" type="password" required autocomplete="current-password">
                                </div>
                                <button class="btn btn--base w-100" type="submit">@lang('Submit')</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@if ($general->secure_password)
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif

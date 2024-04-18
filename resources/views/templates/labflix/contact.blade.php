@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="pt-80 pb-80">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card custom--card">
                        <div class="card-header">
                            <h2 class="contact-form__title">{{ __($pageTitle) }}</h2>
                        </div>
                        <div class="card-body">
                            <form class="verify-gcaptcha" method="post" action="">
                                @csrf
                                <div class="form-group">
                                    <label class="form-label">@lang('Name')</label>
                                    <input class="form-control form--control" name="name" type="text" value="{{ old('name', @$user->fullname) }}" @if ($user && $user->profile_complete) readonly @endif required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">@lang('Email')</label>
                                    <input class="form-control form--control" name="email" type="email" value="{{ old('email', @$user->email) }}" @if ($user) readonly @endif required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">@lang('Subject')</label>
                                    <input class="form-control form--control" name="subject" type="text" value="{{ old('subject') }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">@lang('Message')</label>
                                    <textarea class="form-control form--control" name="message" wrap="off" required>{{ old('message') }}</textarea>
                                </div>
                                <x-captcha />
                                <div class="form-group">
                                    <button class="cmn-btn w-100" type="submit">@lang('Submit')</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="contact-map">
                        <iframe class="contact-map__iframe"
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d29624007.58460168!2d115.22979863156776!3d-24.992915938390176!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2b2bfd076787c5df%3A0x538267a1955b1352!2sAustralia!5e0!3m2!1sen!2sbd!4v1656857476955!5m2!1sen!2sbd"
                            style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

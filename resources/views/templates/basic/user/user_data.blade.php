@extends($activeTemplate . 'layouts.frontend')

@section('content')
    <section class="section--bg ptb-80">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-7 col-xl-5">
                    <div class="card custom--card">
                        <div class="card-header">
                            <h5 class="card-title">{{ __($pageTitle) }}</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('user.data.submit') }}">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label">@lang('First Name')</label>
                                            <input type="text" class="form-control form--control" name="firstname" value="{{ old('firstname') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Last Name')</label>
                                            <input type="text" class="form-control form--control" name="lastname" value="{{ old('lastname') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Address')</label>
                                            <input type="text" class="form-control form--control" name="address" value="{{ old('address') }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label">@lang('State')</label>
                                            <input type="text" class="form-control form--control" name="state" value="{{ old('state') }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Zip Code')</label>
                                            <input type="text" class="form-control form--control" name="zip" value="{{ old('zip') }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label">@lang('City')</label>
                                            <input type="text" class="form-control form--control" name="city" value="{{ old('city') }}">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

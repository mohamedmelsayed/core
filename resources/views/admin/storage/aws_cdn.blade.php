@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <form action="" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('CDN Domain')</label>
                                    <input class="form-control form-control-lg" name="aws_cdn[domain]" type="text" value="{{ @$aws_cdn['domain'] }}" required>
                                    <small class="text-muted d-block">@lang('Enter the URL with HTTP/HTTPS protocol')</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Access Key')</label>
                                    <input class="form-control form-control-lg" name="aws_cdn[access_key]" type="text" value="{{ @$aws_cdn['access_key'] }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Secret Key')</label>
                                    <input class="form-control form-control-lg" name="aws_cdn[secret_key]" type="text" value="{{ @$aws_cdn['secret_key'] }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Region')</label>
                                    <input class="form-control form-control-lg" name="aws_cdn[region]" type="text" value="{{ @$aws_cdn['region'] }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Bucket Name')</label>
                                    <input class="form-control form-control-lg" name="aws_cdn[bucket]" type="text" value="{{ @$aws_cdn['bucket'] }}" required>
                                </div>
                            </div>

                        </div>
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Update')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

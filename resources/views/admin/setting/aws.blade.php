@extends('admin.layouts.app')

@section('panel')
<div class="row mb-none-30">
    <div class="col-lg-12 col-md-12 mb-30">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.settings.updateAwsCdn') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label font-weight-bold">@lang('AWS CDN Domain')</label>
                                <input class="form-control form-control-lg" type="text" name="aws_cdn[domain]" value="{{ @$aws_cdn['domain'] }}">
                                <small class="text-muted">@lang('Please Enter With http protocol')</small>
                                <code>@lang('https://yourdomain.com')</code>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label font-weight-bold">@lang('AWS Access Key')</label>
                                <input class="form-control form-control-lg" type="text" name="aws_cdn[access_key]" value="{{ @$aws_cdn['access_key'] }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label font-weight-bold">@lang('AWS Secret Key')</label>
                                <input class="form-control form-control-lg" type="text" name="aws_cdn[secret_key]" value="{{ @$aws_cdn['secret_key'] }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Update')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

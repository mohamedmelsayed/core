@extends('admin.layouts.app')

@section('panel')
<div class="row mb-none-30">
    <div class="col-lg-12 col-md-12 mb-30">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">@lang('Configure AWS S3 Settings')</h5>
            </div>
            <div class="card-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="aws_cdn_domain" class="form-control-label font-weight-bold">@lang('AWS CDN Domain')</label>
                                <input
                                    type="text"
                                    id="aws_cdn_domain"
                                    class="form-control form-control-lg @error('aws_cdn.domain') is-invalid @enderror"
                                    name="aws_cdn[domain]"
                                    value="{{ old('aws_cdn.domain', @$aws_cdn->domain) }}"
                                    placeholder="https://yourdomain.com">
                                <small class="text-muted d-block">@lang('Please enter the full URL with HTTP/HTTPS protocol')</small>
                                @error('aws_cdn.domain')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="aws_access_key" class="form-control-label font-weight-bold">@lang('AWS Access Key')</label>
                                <input
                                    type="text"
                                    id="aws_access_key"
                                    class="form-control form-control-lg @error('aws_cdn.access_key') is-invalid @enderror"
                                    name="aws_cdn[access_key]"
                                    value="{{ old('aws_cdn.access_key', @$aws_cdn->access_key) }}"
                                    placeholder="Your AWS Access Key">
                                @error('aws_cdn.access_key')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="aws_secret_key" class="form-control-label font-weight-bold">@lang('AWS Secret Key')</label>
                                <input
                                    type="password"
                                    id="aws_secret_key"
                                    class="form-control form-control-lg @error('aws_cdn.secret_key') is-invalid @enderror"
                                    name="aws_cdn[secret_key]"
                                    value="{{ old('aws_cdn.secret_key', @$aws_cdn->secret_key) }}"
                                    placeholder="Your AWS Secret Key">
                                @error('aws_cdn.secret_key')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="aws_bucket" class="form-control-label font-weight-bold">@lang('AWS Bucket Name')</label>
                                <input
                                    type="text"
                                    id="aws_bucket"
                                    class="form-control form-control-lg @error('aws_cdn.bucket') is-invalid @enderror"
                                    name="aws_cdn[bucket]"
                                    value="{{ old('aws_cdn.bucket', @$aws_cdn->bucket) }}"
                                    placeholder="Your S3 Bucket Name">
                                @error('aws_cdn.bucket')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="aws_region" class="form-control-label font-weight-bold">@lang('AWS Region')</label>
                                <input
                                    type="text"
                                    id="aws_region"
                                    class="form-control form-control-lg @error('aws_cdn.region') is-invalid @enderror"
                                    name="aws_cdn[region]"
                                    value="{{ old('aws_cdn.region', @$aws_cdn->region) }}"
                                    placeholder="e.g., us-east-1">
                                <small class="text-muted d-block">@lang('The AWS region where your bucket is hosted')</small>
                                @error('aws_cdn.region')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Update Settings')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

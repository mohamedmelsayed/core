@extends('admin.layouts.app')

@section('panel')
    <div class="row mb-none-30">

        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <form action="" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('FTP Hosting root path') <small class="text--primary">( @lang('Please Enter With http protocol') )</small></label>
                                    <input class="form-control form-control-lg" type="text" name="ftp[domain]" value="{{@$setting->ftp->domain}}">
                                    <code>@lang('https://yourdomain.com')</code>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('Host')</label>
                                    <input class="form-control form-control-lg" type="text" name="ftp[host]" value="{{@$setting->ftp->host}}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('Username')</label>
                                    <input class="form-control form-control-lg" type="text" name="ftp[username]" value="{{@$setting->ftp->username}}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('Password')</label>
                                    <input class="form-control form-control-lg" type="text" name="ftp[password]" value="{{@$setting->ftp->password}}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('Port')</label>
                                    <input class="form-control form-control-lg" type="text" name="ftp[port]" value="{{@$setting->ftp->port}}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('Root Folder')</label>
                                    <input class="form-control form-control-lg" type="text" name="ftp[root]" value="{{@$setting->ftp->root}}">
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


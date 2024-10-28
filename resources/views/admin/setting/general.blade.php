@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <form action="" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group">
                                    <label> @lang('Site Title')</label>
                                    <input class="form-control" name="site_name" type="text" value="{{ $general->site_name }}" required>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group">
                                    <label>@lang('Currency')</label>
                                    <input class="form-control" name="cur_text" type="text" value="{{ $general->cur_text }}" required>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group">
                                    <label>@lang('Currency Symbol')</label>
                                    <input class="form-control" name="cur_sym" type="text" value="{{ $general->cur_sym }}" required>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group">
                                    <label> @lang('Timezone')</label>
                                    <select class="select2-basic" name="timezone">
                                        @foreach ($timezones as $key => $timezone)
                                            <option value="{{ @$key }}">{{ __($timezone) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group">
                                    <label> @lang('Site Base Color')</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-0 p-0">
                                            <input class="form-control colorPicker" type='text' value="{{ $general->base_color }}" />
                                        </span>
                                        <input class="form-control colorCode" name="base_color" type="text" value="{{ $general->base_color }}" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group">
                                    <label> @lang('Site Secondary Color')</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-0 p-0">
                                            <input class="form-control colorPicker" type='text' value="{{ $general->secondary_color }}" />
                                        </span>
                                        <input class="form-control colorCode" name="secondary_color" type="text" value="{{ $general->secondary_color }}" />
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4 col-md-6">
                                <div class="form-group">
                                    <label>@lang('File Upload Server')</label>
                                    <select class="form-control" name="file_server">
                                        <option value="current" @selected($general->server == 'current')>@lang('Current Server')</option>
                                        <option value="custom-ftp" @selected($general->server == 'custom-ftp')>@lang('FTP')</option>
                                        <option value="wasabi" @selected($general->server == 'wasabi')>@lang('Wasabi')</option>
                                        <option value="digital_ocean" @selected($general->server == 'digital_ocean')>@lang('Digital Ocean')</option>
                                        <option value="digital_ocean" @selected($general->server == 'aws')>@lang('Amazon AWS')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group">
                                    <label>@lang('Video Skip Time')</label>
                                    <div class="input-group">
                                        <input class="form-control" name="skip_time" type="number" value="{{ $general->skip_time }}">
                                        <span class="input-group-text">@lang('Seconds')</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-12">
                                <div class="form-group">
                                    <label>@lang('TMDB API KEY')</label>
                                    <input class="form-control" name="tmdb_api" type="text" value="{{ $general->tmdb_api }}">
                                </div>
                            </div>
                            <h5 class="mt-4 mb-2">@lang('Pusher Configuration')</h5>
                            <div class="col-xl-3 col-md-6">
                                <div class="form-group">
                                    <label>@lang('App ID')</label>
                                    <input class="form-control" name="app_id" type="text" value="{{ @$general->pusher_config->app_id }}" required>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="form-group">
                                    <label>@lang('App Key')</label>
                                    <input class="form-control" name="app_key" type="text" value="{{ @$general->pusher_config->app_key }}" required>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="form-group">
                                    <label>@lang('App Secret Key')</label>
                                    <input class="form-control" name="app_secret_key" type="text" value="{{ @$general->pusher_config->app_secret_key }}" required>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="form-group">
                                    <label>@lang('Cluster')</label>
                                    <input class="form-control" name="cluster" type="text" value="{{ @$general->pusher_config->cluster }}" required>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-lib')
    <script src="{{ asset('assets/admin/js/spectrum.js') }}"></script>
@endpush

@push('style-lib')
    <link href="{{ asset('assets/admin/css/spectrum.css') }}" rel="stylesheet">
@endpush

@push('style')
    <style>
        .select2-container {
            z-index: 0 !important;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.colorPicker').spectrum({
                color: $(this).data('color'),
                change: function(color) {
                    $(this).parent().siblings('.colorCode').val(color.toHexString().replace(/^#?/, ''));
                }
            });

            $('.colorCode').on('input', function() {
                var clr = $(this).val();
                $(this).parents('.input-group').find('.colorPicker').spectrum({
                    color: clr,
                });
            });

            $('select[name=timezone]').val("{{ $currentTimezone }}").select2();
            $('.select2-basic').select2({
                dropdownParent: $('.card-body')
            });
        })(jQuery);
    </script>
@endpush

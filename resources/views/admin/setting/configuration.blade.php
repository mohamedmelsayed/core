@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="" method="post">
                    @csrf
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item d-flex flex-sm-nowrap justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <p class="fw-bold mb-0">@lang('User Registration')</p>
                                    <p class="mb-0"><small>@lang('If you disable this module, no one can register on this system')</small></p>
                                </div>
                                <div class="form-group">
                                    <input name="registration" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="@lang('Enable')" data-off="@lang('Disable')" type="checkbox" @if ($general->registration) checked @endif>
                                </div>
                            </li>
                            <li
                                class="list-group-item d-flex flex-sm-nowrap justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <p class="fw-bold mb-0">@lang('Force SSL')</p>
                                    <p class="mb-0">
                                        <small>@lang('By enabling') <span class="fw-bold">@lang('Force SSL (Secure Sockets Layer)')</span>
                                            @lang('the system will force a visitor that he/she must have to visit in secure mode. Otherwise, the site will be loaded in secure mode.')</small>
                                    </p>
                                </div>
                                <div class="form-group">
                                    <input name="force_ssl" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="@lang('Enable')" data-off="@lang('Disable')" type="checkbox" @if ($general->force_ssl) checked @endif>
                                </div>
                            </li>
                            <li
                                class="list-group-item d-flex flex-sm-nowrap justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <p class="fw-bold mb-0">@lang('Agree Policy')</p>
                                    <p class="mb-0">
                                        <small>@lang('If you enable this module, that means a user must have to agree with your system\'s') <a
                                               href="{{ route('admin.frontend.sections', 'policy_pages') }}">@lang('policies')</a>
                                            @lang('during registration.')</small>
                                    </p>
                                </div>
                                <div class="form-group">
                                    <input name="agree" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="@lang('Enable')" data-off="@lang('Disable')" type="checkbox" @if ($general->agree) checked @endif>
                                </div>
                            </li>

                            <li
                                class="list-group-item d-flex flex-sm-nowrap justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <p class="fw-bold mb-0">@lang('Force Secure Password')</p>
                                    <p class="mb-0">
                                        <small>@lang('By enabling this module, a user must set a secure password while signing up or changing the password.')</small>
                                    </p>
                                </div>
                                <div class="form-group">
                                    <input name="secure_password" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="@lang('Enable')" data-off="@lang('Disable')" type="checkbox" @if ($general->secure_password) checked @endif>
                                </div>
                            </li>

                            <li
                                class="list-group-item d-flex flex-sm-nowrap justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <p class="fw-bold mb-0">@lang('Email Verification')</p>
                                    <p class="mb-0">
                                        <small>
                                            @lang('If you enable') <span class="fw-bold">@lang('Email Verification')</span>,
                                            @lang('users have to verify their email to access the dashboard. A 6-digit verification code will be sent to their email to be verified.')
                                            <br>
                                            <span class="fw-bold"><i>@lang('Note'):</i></span> <i>@lang('Make sure that the')
                                                <span class="fw-bold">@lang('Email Notification') </span> @lang('module is enabled')</i>
                                        </small>
                                    </p>
                                </div>
                                <div class="form-group">
                                    <input name="ev" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="@lang('Enable')" data-off="@lang('Disable')" type="checkbox" @if ($general->ev) checked @endif>
                                </div>
                            </li>

                            <li
                                class="list-group-item d-flex flex-sm-nowrap justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <p class="fw-bold mb-0">@lang('Email Notification')</p>
                                    <p class="mb-0">
                                        <small>@lang('If you enable this module, the system will send emails to users where needed. Otherwise, no email will be sent.') <code>@lang('So be sure before disabling this module that, the system doesn\'t need to send any emails.')</code></small>
                                    </p>
                                </div>
                                <div class="form-group">
                                    <input name="en" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="@lang('Enable')" data-off="@lang('Disable')" type="checkbox" @if ($general->en) checked @endif>
                                </div>
                            </li>

                            <li
                                class="list-group-item d-flex flex-sm-nowrap justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <p class="fw-bold mb-0">@lang('Mobile Verification')</p>
                                    <p class="mb-0">
                                        <small>
                                            @lang('If you enable') <span class="fw-bold">@lang('Mobile Verification')</span>,
                                            @lang('users have to verify their mobile to access the dashboard. A 6-digit verification code will be sent to their mobile to be verified.')
                                            <br>
                                            <span class="fw-bold"><i>@lang('Note'):</i></span> <i>@lang('Make sure that the')
                                                <span class="fw-bold">@lang('SMS Notification') </span> @lang('module is enabled')</i>
                                        </small>
                                    </p>
                                </div>
                                <div class="form-group">
                                    <input name="sv" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="@lang('Enable')" data-off="@lang('Disable')" type="checkbox" @if ($general->sv) checked @endif>
                                </div>
                            </li>

                            <li
                                class="list-group-item d-flex flex-sm-nowrap justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <p class="fw-bold mb-0">@lang('SMS Notification')</p>
                                    <p class="mb-0">
                                        <small>@lang('If you enable this module, the system will send SMS to users where needed. Otherwise, no SMS will be sent.') <code>@lang('So be sure before disabling this module that, the system doesn\'t need to send any SMS.')</code></small>
                                    </p>
                                </div>
                                <div class="form-group">
                                    <input name="sn" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="@lang('Enable')" data-off="@lang('Disable')" type="checkbox" @if ($general->sn) checked @endif>
                                </div>
                            </li>

                            <li
                                class="list-group-item d-flex flex-sm-nowrap justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <p class="fw-bold mb-0">@lang('Push Notification')</p>
                                    <p class="mb-0">
                                        <small>@lang('If you enable this module, the system will send Push Notification to users where needed. Otherwise, no Push Notification will be sent.') </small>
                                    </p>
                                </div>
                                <div class="form-group">
                                    <input name="pn" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="@lang('Enable')" data-off="@lang('Disable')" type="checkbox" @if ($general->pn) checked @endif>
                                </div>
                            </li>
                            <li
                                class="list-group-item d-flex flex-sm-nowrap justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <p class="fw-bold mb-0">@lang('Language Option')</p>
                                    <p class="mb-0">
                                        <small>@lang('If you enable this module, users can change the language according to their needs')</small>
                                    </p>
                                </div>
                                <div class="form-group">
                                    <input name="multi_language" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="@lang('Enable')" data-off="@lang('Disable')" type="checkbox" @if ($general->multi_language) checked @endif>
                                </div>
                            </li>
                            <li
                                class="list-group-item d-flex flex-sm-nowrap justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <p class="fw-bold mb-0">@lang('Ads show for mobile app ')</p>
                                    <p class="mb-0">
                                        <small>@lang('If you enable this module, the users will see ads in mobile app')</small>
                                    </p>
                                </div>
                                <div class="form-group">
                                    <input name="ad_show_mobile" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="@lang('Enable')" data-off="@lang('Disable')" type="checkbox" @if ($general->ad_show_mobile) checked @endif>
                                </div>
                            </li>
                            <li class="list-group-item d-flex flex-sm-nowrap justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <p class="fw-bold mb-0">@lang('Device Limitation Tracker')</p>
                                    <p class="mb-0">
                                        <small>@lang('If you enable this module, the user can use this system with a limited device.')</small>
                                    </p>
                                </div>
                                <div class="form-group">
                                    <input name="device_limit" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="@lang('Enable')" data-off="@lang('Disable')" type="checkbox" @if ($general->device_limit) checked @endif>
                                </div>
                            </li>
                            <li class="list-group-item d-flex flex-sm-nowrap justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <p class="fw-bold mb-0">@lang('Watch Party')</p>
                                    <p class="mb-0">
                                        <small>@lang('If you enable this module, the user can see the watch party option and see the item with family or friends.')</small>
                                    </p>
                                </div>
                                <div class="form-group">
                                    <input name="watch_party" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="@lang('Enable')" data-off="@lang('Disable')" type="checkbox" @if ($general->watch_party) checked @endif>
                                </div>
                            </li>
                            <li class="list-group-item d-flex flex-sm-nowrap justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <p class="fw-bold mb-0">@lang('Only subscription users allow watch party')</p>
                                    <p class="mb-0">
                                        <small>@lang('If you enable this module, only the subscription user can access the watch party option and see the item with family or friends.')</small>
                                    </p>
                                </div>
                                <div class="form-group">
                                    <input name="watch_party_users" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="@lang('Enable')" data-off="@lang('Disable')" type="checkbox" @if ($general->watch_party_users) checked @endif>
                                </div>
                            </li>
                            <li class="list-group-item d-flex flex-sm-nowrap justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <p class="fw-bold mb-0">@lang('In App Purchase')</p>
                                    <p class="mb-0">
                                        <small>@lang('If you enable this module, the user can purchase the plan from Google Play Store and apple play store.')</small>
                                    </p>
                                </div>
                                <div class="form-group">
                                    <input name="app_purchase" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="@lang('Enable')" data-off="@lang('Disable')" type="checkbox" @if ($general->app_purchase) checked @endif>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .toggle.btn-lg {
            height: 37px !important;
            min-height: 37px !important;
        }

        .toggle-handle {
            width: 25px !important;
            padding: 0;
        }

        .form-group {
            width: 125px;
            margin-bottom: 0;
            flex-shrink: 0
        }

        .list-group-item:hover {
            background-color: #F7F7F7;
        }
    </style>
@endpush

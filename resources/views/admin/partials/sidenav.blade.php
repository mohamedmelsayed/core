<div class="sidebar bg--dark">
    <button class="res-sidebar-close-btn"><i class="las la-times"></i></button>
    <div class="sidebar__inner">
        <div class="sidebar__logo">
            <a href="{{ route('admin.dashboard') }}" class="sidebar__main-logo"><img src="{{ siteLogo() }}" alt="@lang('image')"></a>
        </div>

        <div class="sidebar__menu-wrapper" id="sidebar__menuWrapper">
            <ul class="sidebar__menu">
                <li class="sidebar-menu-item {{ menuActive('admin.dashboard') }}">
                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                        <i class="menu-icon las la-home"></i>
                        <span class="menu-title">@lang('Dashboard')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{ menuActive('admin.category.index') }}">
                    <a class="nav-link" href="{{ route('admin.category.index') }}">
                        <i class="menu-icon las la-bars"></i>
                        <span class="menu-title">@lang('Categories')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{ menuActive('admin.subcategory.index') }}">
                    <a class="nav-link" href="{{ route('admin.subcategory.index') }}">
                        <i class="menu-icon las la-stream"></i>
                        <span class="menu-title">@lang('Subcategories')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a class="{{ menuActive('admin.item*', 3) }}" href="javascript:void(0)">
                        <i class="menu-icon la la-video"></i>
                        <span class="menu-title">@lang('Video Items') </span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.item*', 2) }}">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive(['admin.item.index']) }}">
                                <a class="nav-link" href="{{ route('admin.item.index') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('All Items')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive(['admin.item.single']) }}">
                                <a class="nav-link" href="{{ route('admin.item.single') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Single Items')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive(['admin.item.episode']) }}">
                                <a class="nav-link" href="{{ route('admin.item.episode') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Episode Items')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive(['admin.item.trailer']) }}">
                                <a class="nav-link" href="{{ route('admin.item.trailer') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Trailer Items')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive(['admin.item.rents']) }}">
                                <a class="nav-link" href="{{ route('admin.item.rents') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Rent Items')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                @if (gs('watch_party'))
                    <li class="sidebar-menu-item sidebar-dropdown">
                        <a class="{{ menuActive('admin.watch.party*', 3) }}" href="javascript:void(0)">
                            <i class="menu-icon las la-glass-cheers"></i>
                            <span class="menu-title">@lang('Watch Party') </span>
                        </a>
                        <div class="sidebar-submenu {{ menuActive('admin.watch.party*', 2) }}">
                            <ul>
                                <li class="sidebar-menu-item {{ menuActive('admin.watch.party.all') }}">
                                    <a class="nav-link" href="{{ route('admin.watch.party.all') }}">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('All Party')</span>
                                    </a>
                                </li>

                                <li class="sidebar-menu-item {{ menuActive('admin.watch.party.running') }}">
                                    <a class="nav-link" href="{{ route('admin.watch.party.running') }}">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Running Party')</span>
                                    </a>
                                </li>

                                <li class="sidebar-menu-item {{ menuActive('admin.watch.party.canceled') }}">
                                    <a class="nav-link" href="{{ route('admin.watch.party.canceled') }}">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Canceled Party')</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif


                <li class="sidebar-menu-item {{ menuActive('admin.sliders.index') }}">
                    <a class="nav-link" href="{{ route('admin.sliders.index') }}">
                        <i class="menu-icon las la-sliders-h"></i>
                        <span class="menu-title">@lang('Sliders')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{ menuActive('admin.plan.index') }}">
                    <a class="nav-link" href="{{ route('admin.plan.index') }}">
                        <i class="menu-icon las la-dollar-sign"></i>
                        <span class="menu-title">@lang('Plans')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{ menuActive('admin.television.index') }}">
                    <a class="nav-link" href="{{ route('admin.television.index') }}">
                        <i class="menu-icon las la-tv"></i>
                        <span class="menu-title">@lang('Televisions')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{ menuActive('admin.advertise.index') }}">
                    <a class="nav-link" href="{{ route('admin.advertise.index') }}">
                        <i class="menu-icon las la-font"></i>
                        <span class="menu-title">@lang('Advertisement')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{ menuActive('admin.video.advertise.index') }}">
                    <a class="nav-link" href="{{ route('admin.video.advertise.index') }}">
                        <i class="menu-icon las la-video"></i>
                        <span class="menu-title">@lang('Video Advertisement')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a class="{{ menuActive('admin.users*', 3) }}" href="javascript:void(0)">
                        <i class="menu-icon las la-users"></i>
                        <span class="menu-title">@lang('Manage Users')</span>

                        @if ($bannedUsersCount > 0 || $emailUnverifiedUsersCount > 0 || $mobileUnverifiedUsersCount > 0)
                            <span class="menu-badge pill bg--danger ms-auto">
                                <i class="fa fa-exclamation"></i>
                            </span>
                        @endif
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.users*', 2) }}">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('admin.users.active') }}">
                                <a class="nav-link" href="{{ route('admin.users.active') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Active Users')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.users.banned') }}">
                                <a class="nav-link" href="{{ route('admin.users.banned') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Banned Users')</span>
                                    @if ($bannedUsersCount)
                                        <span class="menu-badge pill bg--danger ms-auto">{{ $bannedUsersCount }}</span>
                                    @endif
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.users.email.unverified') }}">
                                <a class="nav-link" href="{{ route('admin.users.email.unverified') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Email Unverified')</span>

                                    @if ($emailUnverifiedUsersCount)
                                        <span class="menu-badge pill bg--danger ms-auto">{{ $emailUnverifiedUsersCount }}</span>
                                    @endif
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.users.mobile.unverified') }}">
                                <a class="nav-link" href="{{ route('admin.users.mobile.unverified') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Mobile Unverified')</span>
                                    @if ($mobileUnverifiedUsersCount)
                                        <span class="menu-badge pill bg--danger ms-auto">{{ $mobileUnverifiedUsersCount }}</span>
                                    @endif
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.users.all') }}">
                                <a class="nav-link" href="{{ route('admin.users.all') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('All Users')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.users.notification.all') }}">
                                <a class="nav-link" href="{{ route('admin.users.notification.all') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Notification to All')</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a class="{{ menuActive('admin.gateway*', 3) }}" href="javascript:void(0)">
                        <i class="menu-icon las la-credit-card"></i>
                        <span class="menu-title">@lang('Payment Gateways')</span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.gateway*', 2) }}">
                        <ul>

                            <li class="sidebar-menu-item {{ menuActive('admin.gateway.automatic.*') }}">
                                <a class="nav-link" href="{{ route('admin.gateway.automatic.index') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Automatic Gateways')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.gateway.manual.*') }}">
                                <a class="nav-link" href="{{ route('admin.gateway.manual.index') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Manual Gateways')</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a class="{{ menuActive('admin.deposit*', 3) }}" href="javascript:void(0)">
                        <i class="menu-icon las la-file-invoice-dollar"></i>
                        <span class="menu-title">@lang('Payments')</span>
                        @if (0 < $pendingDepositsCount)
                            <span class="menu-badge pill bg--danger ms-auto">
                                <i class="fa fa-exclamation"></i>
                            </span>
                        @endif
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.deposit*', 2) }}">
                        <ul>

                            <li class="sidebar-menu-item {{ menuActive('admin.deposit.pending') }}">
                                <a class="nav-link" href="{{ route('admin.deposit.pending') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Pending Payments')</span>
                                    @if ($pendingDepositsCount)
                                        <span class="menu-badge pill bg--danger ms-auto">{{ $pendingDepositsCount }}</span>
                                    @endif
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.deposit.approved') }}">
                                <a class="nav-link" href="{{ route('admin.deposit.approved') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Approved Payments')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.deposit.successful') }}">
                                <a class="nav-link" href="{{ route('admin.deposit.successful') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Successful Payments')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.deposit.rejected') }}">
                                <a class="nav-link" href="{{ route('admin.deposit.rejected') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Rejected Payments')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.deposit.initiated') }}">

                                <a class="nav-link" href="{{ route('admin.deposit.initiated') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Initiated Payments')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.deposit.list') }}">
                                <a class="nav-link" href="{{ route('admin.deposit.list') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('All Payments')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a class="{{ menuActive('admin.ticket*', 3) }}" href="javascript:void(0)">
                        <i class="menu-icon la la-ticket"></i>
                        <span class="menu-title">@lang('Support Ticket') </span>
                        @if (0 < $pendingTicketCount)
                            <span class="menu-badge pill bg--danger ms-auto">
                                <i class="fa fa-exclamation"></i>
                            </span>
                        @endif
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.ticket*', 2) }}">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('admin.ticket.pending') }}">
                                <a class="nav-link" href="{{ route('admin.ticket.pending') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Pending Ticket')</span>
                                    @if ($pendingTicketCount)
                                        <span class="menu-badge pill bg--danger ms-auto">{{ $pendingTicketCount }}</span>
                                    @endif
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.ticket.closed') }}">
                                <a class="nav-link" href="{{ route('admin.ticket.closed') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Closed Ticket')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.ticket.answered') }}">
                                <a class="nav-link" href="{{ route('admin.ticket.answered') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Answered Ticket')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.ticket.index') }}">
                                <a class="nav-link" href="{{ route('admin.ticket.index') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('All Ticket')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a class="{{ menuActive('admin.report*', 3) }}" href="javascript:void(0)">
                        <i class="menu-icon la la-list"></i>
                        <span class="menu-title">@lang('Report') </span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.report*', 2) }}">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive(['admin.report.login.history', 'admin.report.login.ipHistory']) }}">
                                <a class="nav-link" href="{{ route('admin.report.login.history') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Login History')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.report.notification.history') }}">
                                <a class="nav-link" href="{{ route('admin.report.notification.history') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Notification History')</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>

                <li class="sidebar-menu-item {{ menuActive('admin.subscriber.*') }}">
                    <a class="nav-link" data-default-url="{{ route('admin.subscriber.index') }}" href="{{ route('admin.subscriber.index') }}">
                        <i class="menu-icon las la-thumbs-up"></i>
                        <span class="menu-title">@lang('Subscribers') </span>
                    </a>
                </li>

                <li class="sidebar__menu-header">@lang('Settings')</li>

                <li class="sidebar-menu-item {{ menuActive('admin.setting.index') }}">
                    <a class="nav-link" href="{{ route('admin.setting.index') }}">
                        <i class="menu-icon las la-life-ring"></i>
                        <span class="menu-title">@lang('General Setting')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{ menuActive('admin.socialite.credentials') }}">
                    <a class="nav-link" href="{{ route('admin.socialite.credentials') }}">
                        <i class="menu-icon las la-users-cog"></i>
                        <span class="menu-title">@lang('Social Credentials')</span>
                    </a>
                </li>

                @if ($general->app_purchase)
                    <li class="sidebar-menu-item {{ menuActive('admin.app.purchase.credentials') }}">
                        <a class="nav-link" href="{{ route('admin.app.purchase.credentials') }}">
                            <i class="menu-icon lab la-google-play"></i>
                            <span class="menu-title">@lang('App Purchase')</span>
                        </a>
                    </li>
                @endif

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a class="{{ menuActive('admin.storage.*', 3) }}" href="javascript:void(0)">
                        <i class="menu-icon las la-database"></i>
                        <span class="menu-title">@lang('File Storage')</span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.storage.*', 2) }}">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('admin.storage.ftp') }}">
                                <a class="nav-link" href="{{ route('admin.storage.ftp') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('FTP')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.storage.wasabi') }}">
                                <a class="nav-link" href="{{ route('admin.storage.wasabi') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Wasabi')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.storage.digital.ocean') }}">
                                <a class="nav-link" href="{{ route('admin.storage.digital.ocean') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Digital Ocean')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.setting.system.configuration') }}">
                    <a class="nav-link" href="{{ route('admin.setting.system.configuration') }}">
                        <i class="menu-icon las la-cog"></i>
                        <span class="menu-title">@lang('System Configuration')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.setting.logo.icon') }}">
                    <a class="nav-link" href="{{ route('admin.setting.logo.icon') }}">
                        <i class="menu-icon las la-images"></i>
                        <span class="menu-title">@lang('Logo & Favicon')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{ menuActive('admin.extensions.index') }}">
                    <a class="nav-link" href="{{ route('admin.extensions.index') }}">
                        <i class="menu-icon las la-cogs"></i>
                        <span class="menu-title">@lang('Extensions')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{ menuActive(['admin.language.manage', 'admin.language.key']) }}">
                    <a class="nav-link" data-default-url="{{ route('admin.language.manage') }}" href="{{ route('admin.language.manage') }}">
                        <i class="menu-icon las la-language"></i>
                        <span class="menu-title">@lang('Language') </span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{ menuActive('admin.seo') }}">
                    <a class="nav-link" href="{{ route('admin.seo') }}">
                        <i class="menu-icon las la-globe"></i>
                        <span class="menu-title">@lang('SEO Manager')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a class="{{ menuActive('admin.setting.notification*', 3) }}" href="javascript:void(0)">
                        <i class="menu-icon las la-bell"></i>
                        <span class="menu-title">@lang('Notification Setting')</span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.setting.notification*', 2) }}">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('admin.setting.notification.global') }}">
                                <a class="nav-link" href="{{ route('admin.setting.notification.global') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Global Template')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.setting.notification.email') }}">
                                <a class="nav-link" href="{{ route('admin.setting.notification.email') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Email Setting')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.setting.notification.sms') }}">
                                <a class="nav-link" href="{{ route('admin.setting.notification.sms') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('SMS Setting')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.setting.notification.push') }}">
                                <a class="nav-link" href="{{ route('admin.setting.notification.push') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Push Notification')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.setting.notification.templates') }}">
                                <a class="nav-link" href="{{ route('admin.setting.notification.templates') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Notification Templates')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar__menu-header">@lang('Frontend Manager')</li>

                <li class="sidebar-menu-item {{ menuActive('admin.frontend.templates') }}">
                    <a class="nav-link" href="{{ route('admin.frontend.templates') }}">
                        <i class="menu-icon la la-puzzle-piece"></i>
                        <span class="menu-title">@lang('Manage Templates')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a class="{{ menuActive('admin.frontend.sections*', 3) }}" href="javascript:void(0)">
                        <i class="menu-icon la la-html5"></i>
                        <span class="menu-title">@lang('Manage Section')</span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.frontend.sections*', 2) }}">
                        <ul>
                            @php
                                $lastSegment = collect(request()->segments())->last();
                            @endphp
                            @foreach (getPageSections(true) as $k => $secs)
                                @if ($secs['builder'])
                                    <li class="sidebar-menu-item @if ($lastSegment == $k) active @endif">
                                        <a class="nav-link" href="{{ route('admin.frontend.sections', $k) }}">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">{{ __($secs['name']) }}</span>
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </li>

                <li class="sidebar__menu-header">@lang('Extra')</li>

                <li class="sidebar-menu-item {{ menuActive('admin.maintenance.mode') }}">
                    <a class="nav-link" href="{{ route('admin.maintenance.mode') }}">
                        <i class="menu-icon las la-robot"></i>
                        <span class="menu-title">@lang('Maintenance Mode')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{ menuActive('admin.setting.cookie') }}">
                    <a class="nav-link" href="{{ route('admin.setting.cookie') }}">
                        <i class="menu-icon las la-cookie-bite"></i>
                        <span class="menu-title">@lang('GDPR Cookie')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a class="{{ menuActive('admin.system*', 3) }}" href="javascript:void(0)">
                        <i class="menu-icon la la-server"></i>
                        <span class="menu-title">@lang('System')</span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.system*', 2) }}">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('admin.system.info') }}">
                                <a class="nav-link" href="{{ route('admin.system.info') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Application')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.system.server.info') }}">
                                <a class="nav-link" href="{{ route('admin.system.server.info') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Server')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.system.optimize') }}">
                                <a class="nav-link" href="{{ route('admin.system.optimize') }}">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Cache')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.system.update') }} ">
                                <a href="{{ route('admin.system.update') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Update')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar-menu-item {{ menuActive('admin.setting.custom.css') }}">
                    <a class="nav-link" href="{{ route('admin.setting.custom.css') }}">
                        <i class="menu-icon lab la-css3-alt"></i>
                        <span class="menu-title">@lang('Custom CSS')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{ menuActive('admin.request.report') }}">
                    <a class="nav-link" data-default-url="{{ route('admin.request.report') }}" href="{{ route('admin.request.report') }}">
                        <i class="menu-icon las la-bug"></i>
                        <span class="menu-title">@lang('Report & Request') </span>
                    </a>
                </li>
            </ul>
            <div class="text-uppercase mb-3 text-center">
                <span class="text--primary">{{ __(systemDetails()['name']) }}</span>
                <span class="text--success">@lang('V'){{ systemDetails()['version'] }} </span>
            </div>
        </div>
    </div>
</div>
<!-- sidebar end -->

@push('script')
    <script>
        if ($('li').hasClass('active')) {
            $('#sidebar__menuWrapper').animate({
                scrollTop: eval($(".active").offset().top - 320)
            }, 500);
        }
    </script>
@endpush

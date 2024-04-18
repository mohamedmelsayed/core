<header class="header-section">
    <div class="header">
        <div class="header-bottom-area">
            <div class="container">
                <div class="header-menu-content">
                    <nav class="navbar navbar-expand-lg p-0">
                        <a class="site-logo site-title" href="{{ route('home') }}"><img src="{{ siteLogo() }}" alt="site-logo"></a>
                        <div class="search-bar d-block d-lg-none ml-auto">
                            <a href="#0"><i class="fas fa-search"></i></a>
                            <div class="header-top-search-area">
                                <form class="header-search-form" action="{{ route('search') }}">
                                    <input name="search" type="search" placeholder="@lang('Search here')...">
                                    <button class="header-search-btn" type="submit"><i class="fas fa-search"></i></button>
                                </form>
                            </div>
                        </div>
                        <button class="navbar-toggler ml-auto" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" type="button" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="fas fa-bars"></span>
                        </button>
                        <div class="navbar-collapse collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav main-menu ms-auto me-auto">
                                <li><a href="{{ route('user.home') }}">@lang('Dashboard')</a></li>
                                <li><a href="{{ route('user.deposit.history') }}">@lang('Payment History')</a></li>
                                <li><a href="javascript:void(0)">@lang('Ticket') </a>
                                    <span class="menu__icon"><i class="fas fa-caret-down"></i></span>
                                    <ul class="sub-menu">
                                        <li><a href="{{ route('ticket.open') }}">@lang('Create New')</a></li>
                                        <li><a href="{{ route('ticket.index') }}">@lang('My Ticket')</a></li>
                                    </ul>
                                </li>
                                <li><a href="javascript:void(0)">@lang('More') </a>
                                    <span class="menu__icon"><i class="fas fa-caret-down"></i></span>
                                    <ul class="sub-menu">
                                        <li><a href="{{ route('user.wishlist.index') }}">@lang('My Wishlists')</a></li>
                                        <li><a href="{{ route('user.watch.history') }}">@lang('Watch History')</a></li>
                                        <li><a href="{{ route('user.watch.party.history') }}">@lang('Watch Party History')</a></li>
                                    </ul>
                                </li>
                            </ul>
                            <div class="search-bar d-none d-lg-block">
                                <a href="javascript:void(0)"><i class="fas fa-search"></i></a>
                                <div class="header-top-search-area">
                                    <form class="header-search-form" action="{{ route('search') }}">
                                        <input name="search" type="search" placeholder="@lang('Search here')...">
                                        <button class="header-search-btn" type="submit"><i class="fas fa-search"></i></button>
                                    </form>
                                </div>
                            </div>
                            @if ($general->multi_language)
                                @php
                                    $language = App\Models\Language::all();
                                @endphp
                                <div class="header-bottom-right">
                                    <div class="language-select-area">
                                        <select class="language-select langSel" id="langSel">
                                            @foreach ($language as $lang)
                                                <option value="{{ $lang->code }}" @if (Session::get('lang') === $lang->code) selected @endif>{{ __($lang->code) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                            <div class="header-right dropdown">
                                <button class="" data-bs-toggle="dropdown" data-display="static" type="button" aria-haspopup="true" aria-expanded="false">
                                    <div class="header-user-area d-flex align-items-center justify-content-between flex-wrap">
                                        <div class="header-user-content">
                                            <span>{{ __(auth()->user()->fullname) }}</span>
                                        </div>
                                        <span class="header-user-icon"><i class="las la-chevron-circle-down"></i></span>
                                    </div>
                                </button>
                                <div class="dropdown-menu dropdown-menu--sm dropdown-menu-end border-0 p-0">
                                    <a class="dropdown-menu__item d-flex align-items-center px-3 py-2" href="{{ route('user.profile.setting') }}">
                                        <i class="dropdown-menu__icon las la-user-circle"></i>
                                        <span class="dropdown-menu__caption">@lang('Profile Settings')</span>
                                    </a>
                                    <a class="dropdown-menu__item d-flex align-items-center px-3 py-2" href="{{ route('user.change.password') }}">
                                        <i class="dropdown-menu__icon las la-key"></i>
                                        <span class="dropdown-menu__caption">@lang('Change Password')</span>
                                    </a>
                                    <a class="dropdown-menu__item d-flex align-items-center px-3 py-2" href="{{ route('user.logout') }}">
                                        <i class="dropdown-menu__icon las la-sign-out-alt"></i>
                                        <span class="dropdown-menu__caption">@lang('Logout')</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>

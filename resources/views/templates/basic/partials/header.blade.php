<header class="header-section {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <div class="header">
        <div class="header-bottom-area">
            <div class="container">
                <div class="header-menu-content">
                    <nav class="navbar navbar-expand-xl p-0">
                        <a class="site-logo site-title" href="{{ route('home') }}">
                            <img src="{{ siteLogo() }}" alt="site-logo">
                        </a>
                        <div class="search-bar d-block d-xl-none ml-auto">
                            <a href="#0"><i class="fas fa-search"></i></a>
                            <div class="header-top-search-area">
                                <form class="header-search-form" action="{{ route('search') }}">
                                    <input name="search" type="search" placeholder="@lang('Search here')...">
                                    <button class="header-search-btn" type="submit"><i
                                            class="fas fa-search"></i></button>
                                </form>
                            </div>
                        </div>
                        <button class="navbar-toggler ml-auto" data-bs-toggle="collapse"
                            data-bs-target="#navbarSupportedContent" type="button"
                            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="las la-bars"></span>
                        </button>
                        <div class="navbar-collapse collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav main-menu ms-auto me-auto">
                                <li><a href="{{ route('home') }}">@lang('Home')</a></li>
                                <li><a class="nav-link category-nav item" href="#"
                                        target="none">@lang('PlayLists')</a>
                                    <span class="menu__icon"><i class="fas fa-caret-down"></i></span>
                                    <ul class="sub-menu">
                                        @forelse($allPlaylists as $playlist)
                                            <li>
                                                <a href="{{ route('playlist.play', $playlist->id) }}">
                                                    {{ app()->getLocale() === 'ar' ? $playlist->title : $playlist->title_en }}
                                                </a>
                                            </li>
                                        @empty
                                        @endforelse
                                    </ul>
                                </li>
                                @foreach ($categories as $category)
                                    <li>
                                        <a class="nav-link category-nav item"
                                            href="{{ route('category', $category->id) }}">
                                            {{ app()->getLocale() === 'ar' ? $category->name : $category->name_en }}
                                        </a>
                                        @if ($category->subcategories->count())
                                            <span class="menu__icon"><i class="fas fa-caret-down"></i></span>
                                            <ul class="sub-menu">
                                                @foreach ($category->subcategories as $subcategory)
                                                    <li>
                                                        <a href="{{ route('subCategory', $subcategory->id) }}">
                                                            {{ app()->getLocale() === 'ar' ? $subcategory->name : $subcategory->name_en }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach

                                <li><a href="{{ route('live.tv') }}">@lang('Live TV')</a></li>
                                <li><a href="{{ route('subscription') }}">@lang('Subscribe')</a></li>

                                @auth
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
                                            <li><a href="{{ route('user.deposit.history') }}">@lang('Payment History')</a></li>
                                            <li><a href="{{ route('user.wishlist.index') }}">@lang('My Wishlists')</a></li>
                                            <li><a href="{{ route('user.watch.history') }}">@lang('Watch History')</a></li>
                                            @if (gs('watch_party'))
                                                <li><a
                                                        href="{{ route('user.watch.party.history') }}">@lang('Watch Party')</a>
                                                </li>
                                            @endif
                                            <li><a href="{{ route('user.rented.item') }}">@lang('Rented Item')</a></li>
                                        </ul>
                                    </li>
                                @else
                                    <li><a href="{{ route('contact') }}">@lang('Contact')</a></li>
                                @endauth
                            </ul>

                            <div class="search-bar d-none d-xl-block">
                                <a href="javascript:void(0)"><i class="fas fa-search"></i></a>
                                <div class="header-top-search-area">
                                    <form class="header-search-form" action="{{ route('search') }}">
                                        <input name="search" type="search" placeholder="@lang('Search here')...">
                                        <button class="header-search-btn" type="submit"><i
                                                class="fas fa-search"></i></button>
                                    </form>
                                </div>
                            </div>

                            <div class="header-bottom-right">
                                <div class="language-select-area">
                                    <a class="language-select langSel" id="langSel"
                                        href="{{ app()->getLocale() === 'ar' ? route('change-Lang', 'en') : route('change-Lang', 'ar') }}">
                                        {{ app()->getLocale() === 'ar' ? 'EN' : 'AR' }}
                                    </a>
                                </div>
                                @auth
                                    <div class="header-right dropdown">
                                        <button class="" data-bs-toggle="dropdown" data-display="static"
                                            type="button">
                                            <div class="header-user-area">
                                                <div class="header-user-content">
                                                    <span>{{ __(auth()->user()->fullname) }}</span>
                                                </div>
                                                <span class="header-user-icon"><i
                                                        class="las la-chevron-circle-down"></i></span>
                                            </div>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-menu__item" href="{{ route('user.profile.setting') }}">
                                                <i class="dropdown-menu__icon las la-user-circle"></i>
                                                <span>@lang('Profile Settings')</span>
                                            </a>
                                            <a class="dropdown-menu__item" href="{{ route('user.change.password') }}">
                                                <i class="dropdown-menu__icon las la-key"></i>
                                                <span>@lang('Change Password')</span>
                                            </a>
                                            <a class="dropdown-menu__item" href="{{ route('user.logout') }}">
                                                <i class="dropdown-menu__icon las la-sign-out-alt"></i>
                                                <span>@lang('Logout')</span>
                                            </a>
                                        </div>
                                    </div>
                                @else
                                    <div class="header-action">
                                        <a class="btn--base" href="{{ route('user.login') }}"><i
                                                class="las la-user-circle"></i>@lang('Login')</a>
                                    </div>
                                @endauth
                            </div>

                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>

    <style>
        /* RTL Specific Styles */
        .rtl .navbar-nav .main-menu {
            margin-right: auto;
            margin-left: 0;
        }

        .rtl .sub-menu {
            left: auto;
            right: 0;
        }

        .rtl .navbar-toggler {
            margin-right: 0;
            margin-left: auto;
        }

        /* LTR Specific Styles (default) */
        .ltr .navbar-nav .main-menu {
            margin-left: auto;
            margin-right: 0;
        }

        .ltr .sub-menu {
            right: auto;
            left: 0;
        }

        .ltr .navbar-toggler {
            margin-left: 0;
            margin-right: auto;
        }


        /* General RTL submenu styling */
.rtl .navbar-nav .sub-menu {
    right: 0; /* Align submenu to the right */
    left: auto;
    position: absolute; /* Ensure it’s positioned relative to the parent */
    top: 100%; /* Position below the parent menu item */
}

/* Specific styling for the last submenu in RTL */
.rtl .navbar-nav .main-menu > li:last-child .sub-menu {
    right: auto; /* Prevent it from overflowing to the right */
    left: 0; /* Align it to the left for the last item */
}

    </style>

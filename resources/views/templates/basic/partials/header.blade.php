<header class="header-section">
    <div class="header">
        <div class="header-bottom-area">
            <div class="container">
                <div class="header-menu-content">
                    <nav class="navbar navbar-expand-xl p-0">
                        <a class="site-logo site-title" href="{{ route('home') }}"><img src="{{ siteLogo() }}" alt="site-logo"></a>
                        <div class="search-bar d-block d-xl-none ml-auto">
                            <a href="#0"><i class="fas fa-search"></i></a>
                            <div class="header-top-search-area">
                                <form class="header-search-form" action="{{ route('search') }}">
                                    <input name="search" type="search" placeholder="@lang('Search here')...">
                                    <button class="header-search-btn" type="submit"><i class="fas fa-search"></i></button>
                                </form>
                            </div>
                        </div>
                        <button class="navbar-toggler ml-auto" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" type="button" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="las la-bars"></span>
                        </button>
                        <div class="navbar-collapse collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav main-menu ms-auto me-auto">

                                <li><a href="{{ route('home') }}">@lang('Home')</a></li>

                                @foreach ($categories as $category)
                                @if ($category->subcategories->count())
                                <li><a class="nav-link category-nav" href="{{ route('category', $category->id) }}">{{ __($category->name) }}</a>
                                    <span class="menu__icon"><i class="fas fa-caret-down"></i></span>
                                    <ul class="sub-menu">
                                        @forelse($category->subcategories as $subcategory)
                                        <li><a href="{{ route('subCategory', $subcategory->id) }}">{{ __($subcategory->name) }}</a></li>
                                        @empty
                                        @endforelse
                                    </ul>
                                </li>
                                @else
                                <li><a href="{{ route('category', $category->id) }}">{{ __($category->name) }}</a></li>
                                @endif
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
                                        <li><a href="{{ route('user.watch.party.history') }}">@lang('Watch Party')</a></li>
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
                                        <button class="header-search-btn" type="submit"><i class="fas fa-search"></i></button>
                                    </form>
                                </div>
                            </div>

                            <div class="header-bottom-right">
                                @if ($general->multi_language)
                                @php
                                $language = App\Models\Language::all();
                                @endphp
                                @if (!blank($language))
                                <div class="language-select-area">
                                    <select class="language-select langSel" id="langSel">
                                        @foreach ($language as $lang)
                                        <option value="{{ $lang->code }}" @if (Session::get('lang')===$lang->code) selected @endif>{{ __($lang->code) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                                @endif

                                @auth
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
                                    <button id="language-toggle" class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                                        <i class="dropdown-menu__icon las la-language"></i>
                                        <span class="dropdown-menu__caption">@lang('Change Language')</span>
                                    </button>  
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
                                @else
                                <div class="header-action">
                                    <a class="btn--base" href="{{ route('user.login') }}"><i class="las la-user-circle"></i>@lang('Login')</a>
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



<script>
    document.getElementById('language-toggle').addEventListener('click', function() {
        // Determine the target language (Arabic or English)
        var targetLang = "{{ app()->getLocale() === 'en' ? 'ar' : 'en' }}";
        // Send a request to change the language
        fetch("{{ route('user.lang', ['lang' => ':lang']) }}".replace(':lang', targetLang))
            .then(response => {
                if (response.ok) {
                    // Reload the page to apply the new language
                    window.location.reload();
                } else {
                    console.error('Failed to change language:', response.statusText);
                }
            })
            .catch(error => console.error('Error changing language:', error));
    });
</script>
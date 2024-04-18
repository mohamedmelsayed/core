<header class="header-section">
    <div class="header">
        <div class="header-bottom-area">
            <div class="container">
                <div class="header-menu-content">
                    <nav class="navbar navbar-expand-lg p-0">
                        <a class="site-logo site-title mr-auto" href="{{ route('home') }}"><img src="{{ siteLogo() }}" alt="site-logo"></a>
                        <div class="search-bar d-block d-lg-none">
                            <a href="#0"><i class="fas fa-search"></i></a>
                            <div class="header-top-search-area">
                                <form class="header-search-form" action="{{ route('search') }}">
                                    <input name="search" type="search" placeholder="@lang('Search here')...">
                                    <button class="header-search-btn" type="submit"><i class="fas fa-search"></i></button>
                                </form>
                            </div>
                        </div>

                        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" type="button" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="fas fa-bars"></span>
                        </button>
                        <div class="navbar-collapse collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav main-menu ms-auto me-auto">
                                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}" aria-current="page">@lang('Home')</a></li>
                                @forelse($categories as $category)
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
                                @empty
                                @endforelse
                                <li><a href="{{ route('live.tv') }}">@lang('Live TV')</a></li>
                                <li><a href="{{ route('contact') }}">@lang('Contact')</a></li>
                            </ul>
                            <div class="search-bar d-none d-lg-block">
                                <a href="#0"><i class="fas fa-search"></i></a>
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
                            <div class="header-action">
                                @auth
                                    <a class="btn--base" href="{{ route('user.home') }}"><i class="las la-home"></i>@lang('Dashboard')</a>
                                @else
                                    <a class="btn--base" href="{{ route('user.login') }}"><i class="las la-user-circle"></i>@lang('Login')</a>
                                @endauth
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>

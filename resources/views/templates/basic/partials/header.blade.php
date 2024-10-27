<header class="header-section {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <div class="header">
        <div class="header-bottom-area">
            <div class="container">
                <div class="header-menu-content">
                    <nav class="navbar navbar-expand-xl p-0">
                        <a class="site-logo site-title" href="{{ route('home') }}">
                            <img src="{{ siteLogo() }}" alt="site-logo">
                        </a>

                        <div class="navbar-collapse collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav main-menu ms-auto me-auto">
                                <li><a href="{{ route('home') }}">@lang('Home')</a></li>
                                <li>
                                    <a class="nav-link category-nav item" href="#" target="none">@lang('PlayLists')</a>
                                    <span class="menu__icon"><i class="fas fa-caret-down"></i></span>
                                    <ul class="sub-menu"
                                        style="{{ app()->getLocale() === 'ar' ? 'right: 0; left: auto;' : 'left: 0; right: auto;' }}">
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
                                            <ul class="sub-menu"
                                                style="{{ app()->getLocale() === 'ar' ? 'right: 0; left: auto;' : 'left: 0; right: auto;' }}">
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
                                    <li>
                                        <a href="javascript:void(0)">@lang('Ticket') </a>
                                        <span class="menu__icon"><i class="fas fa-caret-down"></i></span>
                                        <ul class="sub-menu"
                                            style="{{ app()->getLocale() === 'ar' ? 'right: 0; left: auto;' : 'left: 0; right: auto;' }}">
                                            <li><a href="{{ route('ticket.open') }}">@lang('Create New')</a></li>
                                            <li><a href="{{ route('ticket.index') }}">@lang('My Ticket')</a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)">@lang('More') </a>
                                        <span class="menu__icon"><i class="fas fa-caret-down"></i></span>
                                        <ul class="sub-menu"
                                            style="{{ app()->getLocale() === 'ar' ? 'right: 0; left: auto;' : 'left: 0; right: auto;' }}">
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
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>

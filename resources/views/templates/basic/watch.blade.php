@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="audio-details-section section--bg ptb-80">
        <div class="container">
            <div class="row @if (blank($episodes)) justify-content-center @endif mb-30-none">
                <div class="col-xl-8 col-lg-8 mb-30">
                    <div class="audio-item">
                        <div class="main-audio">
                            <audio class="audio-player plyr-audio" controls data-poster="{{ getImage(getFilePath('item_landscape') . '/' . $item->image->landscape) }}">
                                @foreach ($audios as $audio)
                                    <source src="{{ $audio->content }}" type="audio/mp3" size="{{ $audio->size }}" />
                                @endforeach
                            </audio>
                            @if ($item->version == Status::RENT_VERSION && !$watchEligable)
                                <div class="main-audio-lock">
                                    <div class="main-audio-lock-content">
                                        <span class="icon"><i class="las la-lock"></i></span>
                                        <p class="title">@lang('Purchase Now')</p>
                                        <p class="price">
                                            <span class="price-amount">{{ $general->cur_sym }}{{ showAmount($item->rent_price) }}</span>
                                            <span class="small-text ms-3">@lang('For') {{ $item->rental_period }} @lang('Days')</span>
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="ad-audio position-relative d-none">
                            <audio class="ad-player" style="display: none" id="ad-audio"></audio>
                            <div class="ad-links d-none">
                                @foreach ($adsTime ?? [] as $ads)
                                    <source src="{{ $ads }}" type="audio/mp3" />
                                @endforeach
                            </div>
                            <div class="d-flex justify-content-between align-items-center flex-wrap skip-audio">
                                <span class="advertise-text d-none">@lang('Advertisement') - <span class="remains-ads-time">00:52</span></span>
                                <button class="skipButton d-none" id="skip-button" data-skip-time="0">@lang('Skip Ad')</button>
                            </div>
                        </div>

                        <div class="audio-content">
                            <div class="audio-content-inner d-sm-flex justify-content-between align-items-center flex-wrap">
                                <div class="audio-content-left">
                                    <h3 class="title">{{ __($seoContents["social_title"]) }}</h3>
                                    <span class="sub-title">@lang('Category') : <span class="cat">{{ @$item->category->name }}</span>
                                    @if ($item->sub_category)
                                            @lang('Sub Category'): {{ @$item->sub_category->name }}
                                        @endif
                                </span>
                                </div>
                                <div class="audio-content-right">
                                    <div class="audio-widget-area align-items-center">

                                        @auth
                                            @if ($watchEligable && gs('watch_party'))
                                                <button type="button" class="watch-party-btn watchPartyBtn">
                                                    <i class="las la-desktop base--color"></i>
                                                    <span>@lang('Listen party')</span>
                                                </button>
                                            @endif
                                        @endauth

                                        <span class="audio-widget"><i class="lar la-star text--warning"></i> {{ getAmount($item->ratings) }}</span>
                                        <span class="audio-widget"><i class="lar la-eye text--danger"></i> {{ getAmount($item->view) }} @lang('views')</span>

                                        @php
                                            $wishlist = $item->wishlists->where('user_id', auth()->id())->count();
                                        @endphp

                                        <span class="audio-widget addWishlist {{ $wishlist ? 'd-none' : '' }}" data-id="{{ $item->id }}" data-type="item"><i class="las la-plus-circle"></i></span>
                                        <span class="audio-widget removeWishlist {{ $wishlist ? '' : 'd-none' }}" data-id="{{ $item->id }}" data-type="item"><i class="las la-minus-circle"></i></span>
                                    </div>

                                    <ul class="post-share d-flex align-items-center justify-content-sm-end mt-2 flex-wrap">
                                        <li class="caption">@lang('Share') : </li>

                                        <li data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('Facebook')">
                                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"><i class="lab la-facebook-f"></i></a>
                                        </li>
                                        <li data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('Linkedin')">
                                            <a href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{ urlencode(url()->current()) }}&amp;title={{ __(@$item->title) }}&amp;summary=@php echo strLimit(strip_tags($item->description), 130); @endphp"><i class="lab la-linkedin-in"></i></a>
                                        </li>
                                        <li data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('Twitter')">
                                            <a href="https://twitter.com/intent/tweet?text={{ __(@$item->title) }}%0A{{ url()->current() }}"><i class="lab la-twitter"></i></a>
                                        </li>
                                        <li data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('Pinterest')">
                                            <a href="http://pinterest.com/pin/create/button/?url={{ urlencode(url()->current()) }}&description={{ __(@$item->title) }}&media={{ getImage(getFilePath('item_landscape') . '/' . @$item->image->landscape) }}"><i class="lab la-pinterest"></i></a>
                                        </li>
                                    </ul>

                                </div>
                            </div>
                            <div class="audio-widget-area">
                            </div>
                            <p class="audio-widget__desc">{{ __($seoContents["social_description"]) }}</p>
                        </div>
                    </div>

                    <div class="product-tab mt-40">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="product-tab-desc" data-bs-toggle="tab" href="#product-desc-content" role="tab" aria-controls="product-desc-content" aria-selected="true">@lang('Description')</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="product-tab-team" data-bs-toggle="tab" href="#product-team-content" role="tab" aria-controls="product-team-content" aria-selected="false">@lang('Team')</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="product-desc-content" role="tabpanel" aria-labelledby="product-tab-desc">
                                <div class="product-desc-content">
                                    {{ __($item->description) }}
                                </div>
                            </div>
                            <div class="tab-pane fade fade" id="product-team-content" role="tabpanel" aria-labelledby="product-tab-team">
                                <div class="product-desc-content">
                                    <ul class="team-list">
                                        <li><span>@lang('Director'):</span> {{ __($item->team->director) }}</li>
                                        <li><span>@lang('Producer'):</span> {{ __($item->team->producer) }}</li>
                                        <li><span>@lang('Cast'):</span> {{ __($item->team->casts) }}</li>
                                        <li><span>@lang('Genres'):</span> {{ __(@$item->team->genres) }}</li>
                                        <li><span>@lang('Language'):</span> {{ __(@$item->team->language) }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                @if (!blank($episodes))
                    <div class="col-xl-4 col-lg-4 mb-30">
                        <div class="widget-box">
                            <div class="widget-wrapper audio-small-list pt-0">
                                @foreach ($episodes as $episode)
                                    @php
                                        $status = checkLockStatus($episode, $userHasSubscribed, $hasSubscribedItem);
                                    @endphp
                                    <div class="widget-item widget-item__overlay d-flex align-items-center justify-content-between" data-img="{{ getImage(getFilePath('episode') . '/' . $episode->image) }}">
                                        <div class="widget-item__content d-flex align-items-center audio-small flex-wrap">
                                            <div class="widget-thumb">
                                                <a href="{{ route('listen', [$item->slug, $episode->id]) }}">
                                                    <img src="{{ getImage(getFilePath('episode') . '/' . $episode->image) }}" alt="audio">
                                                </a>
                                            </div>
                                            <div class="widget-content">
                                                <h4 class="title">{{ __($episode->title) }}</h4>
                                                <div class="widget-btn">
                                                    @if ($status)
                                                        <a class="custom-btn" href="{{ route('listen', [$item->slug, $episode->id]) }}">@lang('Play Now')</a>
                                                    @else
                                                        <a class="custom-btn" href="{{ route('listen', [$item->slug, $episode->id]) }}"><i class="las la-lock"></i> @lang('Locked')</a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @if ($episode->item_number)
                                            <div class="widget-item__number"><span>{{ $episode->item_number }}</span></div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            @if (blank($episodes))
                @include($activeTemplate . 'partials.related_items')
            @endif
        </div>
    </section>
@endsection

@push('style-lib')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/plyr.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/plyr.js') }}"></script>
@endpush

@push('script')
    <script>
        const player = new Plyr('.plyr-audio', {
            settings: ['captions', 'quality', 'speed', 'loop']
        });

        const adPlayer = new Plyr('#ad-audio', {
            controls: ['play', 'progress', 'current-time']
        });

        const adSources = document.querySelectorAll('.ad-links source');
        const skipButton = document.getElementById('skip-button');
        const adAudioElement = document.getElementById('ad-audio');
        const mainAudioLock = document.querySelector('.main-audio-lock');
        const adAudio = new Audio();

        if (adSources.length) {
            adAudio.src = adSources[0].src;
            adAudio.onloadeddata = () => {
                document.querySelector('.advertise-text').classList.remove('d-none');
                skipButton.classList.remove('d-none');
            };

            adAudio.addEventListener('ended', () => {
                adPlayer.stop();
                adAudioElement.style.display = 'none';
                player.play();
            });

            adAudio.addEventListener('timeupdate', () => {
                const remainingTime = adAudio.duration - adAudio.currentTime;
                document.querySelector('.remains-ads-time').textContent = remainingTime.toFixed(0);

                if (remainingTime <= parseInt(skipButton.dataset.skipTime)) {
                    skipButton.removeAttribute('disabled');
                }
            });

            adAudioElement.appendChild(adAudio);
        }

        skipButton.addEventListener('click', () => {
            adAudio.pause();
            player.play();
            adPlayer.stop();
            adAudioElement.style.display = 'none';
        });

        if (mainAudioLock) {
            player.pause();
        } else {
            player.play();
        }
    </script>
@endpush

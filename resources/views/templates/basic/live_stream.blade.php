@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="movie-details-section section--bg ptb-80">
        <div class="container">
            <div class="row justify-content-center mb-30-none">
                <div class="col-xl-8 col-lg-8 mb-30">
                    <div class="movie-item">
                        <div class="main-video position-relative" data-start-at="{{ $item->stream->start_at }}"
                            style="background-image: url('{{ getImage(getFilePath('item_landscape') . '/' . $item->image->landscape) }}'); background-size: cover; background-position: center;">
                            <div id="video-content">

                                @if ($item->version == Status::RENT_VERSION || !$watchEligable)
                                    <div class="main-video-lock">
                                        <div class="main-video-lock-content">
                                            <span class="icon"><i class="las la-lock"></i></span>
                                            <p class="title">@lang('Purchase Now')</p>
                                            <p class="price">
                                                <span
                                                    class="price-amount">{{ $general->cur_sym }}{{ showAmount($item->rent_price) }}</span>
                                                <span class="small-text ms-3">@lang('For') {{ $item->rental_period }}
                                                    @lang('Days')</span>
                                            </p>
                                        </div>
                                    </div>
                                @else
                                    <!-- Countdown Timer (hidden if the stream already started) -->
                                    <div class="countdown-timer"
                                        style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -20%); background-color: rgba(237, 35, 35, 0.8); color: white; padding: 20px; border-radius: 10px;">
                                        <span class="countdown-text">@lang('Starting in:')</span>
                                        <span class="countdown-time"></span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="ad-video position-relative d-none">
                        <video class="ad-player" style="display: none" id="ad-video"></video>
                        <div class="ad-links d-none">
                            @foreach ($adsTime ?? [] as $ads)
                                <source src="{{ $ads }}" type="video/mp4" />
                            @endforeach
                        </div>
                        <div class="d-flex justify-content-between align-items-center flex-wrap skip-video">
                            <span class="advertise-text d-none">@lang('Advertisement') - <span
                                    class="remains-ads-time">00:52</span></span>
                            <button class="skipButton d-none" id="skip-button" data-skip-time="0">@lang('Skip Ad')</button>
                        </div>
                    </div>

                    <div class="movie-content">
                        <div class="movie-content-inner d-sm-flex justify-content-between align-items-center flex-wrap">
                            <div class="movie-content-left">
                                <h3 class="title">{{ __($seoContents['social_title']) }}</h3>
                                <span class="sub-title">@lang('Category') :
                                    <span
                                        class="cat">{{ app()->getLocale() === 'ar' ? $item->category->name : $item->category->name_en }}</span>
                                    @if ($item->sub_category)
                                        @lang('Sub Category'):
                                        {{ app()->getLocale() === 'ar' ? $item->sub_category->name : $item->sub_category->name_en }}
                                    @endif
                                </span>
                            </div>
                            <div class="movie-content-right">
                                <div class="movie-widget-area align-items-center">
                                    @auth
                                        @if ($watchEligable && gs('watch_party'))
                                            <button type="button" class="watch-party-btn watchPartyBtn">
                                                <i class="las la-desktop base--color"></i>
                                                <span>@lang('Watch party')</span>
                                            </button>
                                        @endif
                                    @endauth

                                    <span class="movie-widget"><i class="lar la-star text--warning"></i>
                                        {{ getAmount($item->ratings) }}</span>
                                    <span class="movie-widget"><i class="lar la-eye text--danger"></i>
                                        {{ getAmount($item->view) }} @lang('views')</span>

                                    @php
                                        $wishlist = $item->wishlists->where('user_id', auth()->id())->count();
                                    @endphp

                                    <span class="movie-widget addWishlist {{ $wishlist ? 'd-none' : '' }}"
                                        data-id="{{ $item->id }}" data-type="item"><i
                                            class="las la-plus-circle"></i></span>
                                    <span class="movie-widget removeWishlist {{ $wishlist ? '' : 'd-none' }}"
                                        data-id="{{ $item->id }}" data-type="item"><i
                                            class="las la-minus-circle"></i></span>
                                </div>

                                <ul class="post-share d-flex align-items-center justify-content-sm-end mt-2 flex-wrap">
                                    <li class="caption">@lang('Share') : </li>
                                    <li data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('Facebook')">
                                        <a
                                            href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"><i
                                                class="lab la-facebook-f"></i></a>
                                    </li>
                                    <li data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('Linkedin')">
                                        <a
                                            href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{ urlencode(url()->current()) }}&amp;title={{ __(@$item->title) }}&amp;summary=@php echo strLimit(strip_tags($item->description), 130); @endphp"><i
                                                class="lab la-linkedin-in"></i></a>
                                    </li>
                                    <li data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('Twitter')">
                                        <a
                                            href="https://twitter.com/intent/tweet?text={{ __(@$item->title) }}%0A{{ url()->current() }}"><i
                                                class="lab la-twitter"></i></a>
                                    </li>
                                    <li data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('Pinterest')">
                                        <a
                                            href="http://pinterest.com/pin/create/button/?url={{ urlencode(url()->current()) }}&description={{ __(@$item->title) }}&media={{ getImage(getFilePath('item_landscape') . '/' . @$item->image->landscape) }}"><i
                                                class="lab la-pinterest"></i></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <p class="movie-widget__desc">{{ $seoContents['social_description'] }}</p>
                    </div>
                </div>

                <div class="product-tab mt-40">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="product-tab-desc" data-bs-toggle="tab"
                                href="#product-desc-content" role="tab" aria-controls="product-desc-content"
                                aria-selected="true">@lang('Description')</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="product-tab-team" data-bs-toggle="tab" href="#product-team-content"
                                role="tab" aria-controls="product-team-content"
                                aria-selected="false">@lang('Team')</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="product-desc-content" role="tabpanel"
                            aria-labelledby="product-tab-desc">
                            <div class="product-desc-content">
                                {{ $seoContents['social_description'] }}
                            </div>
                        </div>
                        <div class="tab-pane fade" id="product-team-content" role="tabpanel"
                            aria-labelledby="product-tab-team">
                            <div class="product-desc-content">
                                <ul class="team-list">
                                    <li><span>@lang('Director'):</span> {{ __($item->team->director) }}</li>
                                    <li><span>@lang('Producer'):</span> {{ __($item->team->producer) }}</li>
                                    {{-- <li><span>@lang('Cast'):</span> {{ __($item->team->casts) }}</li> --}}
                                    {{-- <li><span>@lang('Genres'):</span> {{ __(@$item->team->genres) }}</li> --}}
                                    <li><span>@lang('Language'):</span> {{ __(@$item->team->language) }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="movie-section ptb-80">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="section-header">
                        <h2 class="section-title">@lang('Related Items')</h2>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center mb-30-none">
                @foreach ($relatedItems as $related)
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-xs-6 mb-30">
                        <div class="movie-item">
                            <div class="movie-thumb">
                                <img src="{{ getImage(getFilePath('item_portrait') . '/' . $related->image->portrait) }}"
                                    alt="movie">
                                <span class="movie-badge">{{ __($related->versionName) }}</span>
                                <div class="movie-thumb-overlay">
                                    <a class="video-icon"
                                        href="{{ $related->is_audio ? route('preview.audio', $related->slug) : route('watch', $related->slug) }}">
                                        <i class="fas fa-play"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="section-header">
                        <h2 class="section-title">@lang('Ads')</h2>
                    </div>
                </div>
            </div>
            @foreach ($ads ?? [] as $ad)
                <div class="row">
                    <div class="col-xl-12">
                        <div class="ad-item">
                            <img src="{{ getImage(getFilePath('ads') . '/' . $ad->image) }}" alt="ad">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection

@push('style')
    <style>
        .main-video {
            position: relative;
            width: 100%;
            padding-top: 56.25%;
            /* 16:9 Aspect Ratio */
        }

        .main-video iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .main-video-lock {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.5rem;
            text-align: center;
        }

        .main-video-lock-content {
            max-width: 80%;
            padding: 20px;
        }

        .main-video-lock .price {
            font-size: 1.25rem;
            margin: 10px 0;
        }

        .ad-video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }

        .ad-links {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .skip-video {
            position: absolute;
            bottom: 10px;
            right: 10px;
        }
    </style>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            // Handle video ad if exists
            if ($('.ad-video').length > 0) {
                var player = new Plyr('#ad-video');
                player.on('ready', () => {
                    player.play();
                });

                $('#skip-button').on('click', function() {
                    player.stop();
                    $('.ad-video').hide();
                    $('.main-video').show();
                });
            }

            // Handle watch party button if eligible
            $('.watchPartyBtn').on('click', function() {
                // Handle watch party functionality
            });

            // Check if the video player is locked
            if ($('.main-video').hasClass('locked')) {
                $('.main-video iframe').css('pointer-events', 'none');
                $('.main-video iframe').attr('title', '@lang('Locked Video')');
            }
        });
    </script>
@endpush




@push('style')
    <style>
        .main-video {
            position: relative;
            width: 100%;
            padding-top: 56.25%;
            /* 16:9 Aspect Ratio */
        }

        .main-video iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            /* Disable clicks until stream starts */
        }

        .main-video-lock {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.5rem;
            text-align: center;
        }

        .main-video-lock-content {
            max-width: 80%;
            padding: 20px;
        }
    </style>
@endpush

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var mainVideo = document.querySelector('.main-video');
            var countdownTimer = document.querySelector('.countdown-timer');
            var startAt = mainVideo.getAttribute('data-start-at');
            var countdownTimeElement = countdownTimer.querySelector('.countdown-time');
            var videoContent = document.getElementById('video-content');

            function startCountdown() {
                var now = new Date().getTime();
                var eventTime = new Date(startAt).getTime();
                var distance = eventTime - now;

                if (distance <= 0) {
                    // Stream started, show the stream embed code and remove countdown
                    countdownTimer.style.display = 'none';
                    videoContent.innerHTML = `{!! $item->stream->embed_code !!}`;
                } else {
                    // Update countdown
                    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    countdownTimeElement.innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s`;

                    // Run countdown every second
                    setTimeout(startCountdown, 1000);
                }
            }

            // Initialize the countdown
            startCountdown();
        });
    </script>
@endpush

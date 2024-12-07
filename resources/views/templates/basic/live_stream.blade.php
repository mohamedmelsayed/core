@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="movie-details-section section--bg ptb-80">
        <div class="container">
            <div class="row justify-content-center mb-30-none">
                <div class="col-xl-8 col-lg-8 mb-30">
                    <div class="movie-item">
                        <div class="main-video position-relative" 
                             data-start-at="{{ $item->stream->start_at ?? '' }}"
                             style="background-image: url('{{ getImage(getFilePath('item_landscape') . '/' . $item->image->landscape) }}'); background-size: cover; background-position: center;">
                            <div id="video-content">
                                @if ($item->version == Status::RENT_VERSION || !$watchEligable)
                                    <div class="main-video-lock">
                                        <div class="main-video-lock-content">
                                            <span class="icon"><i class="las la-lock"></i></span>
                                            <p class="title">@lang('Purchase Now')</p>
                                            <p class="price">
                                                <span class="price-amount">
                                                    {{ $general->cur_sym }}{{ showAmount($item->rent_price) }}
                                                </span>
                                                <span class="small-text ms-3">
                                                    @lang('For') {{ $item->rental_period }} @lang('Days')
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                @else
                                    <!-- Embedded Stream -->
                                    @if ($item->stream && $item->stream->embed_code)
                                        <div class="embed-stream">
                                            {!! $item->stream->embed_code !!}
                                        </div>
                                    @else
                                        <div class="main-video-lock">
                                            <div class="main-video-lock-content">
                                                <span class="icon"><i class="las la-lock"></i></span>
                                                <p class="title">@lang('Live stream not available')</p>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Countdown Timer -->
                                    @include($activeTemplate . 'partials.countdown-timer', ['item' => $item])
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Ad Video Section -->
                    <div class="ad-video position-relative d-none">
                        <video class="ad-player" id="ad-video"></video>
                        <div class="ad-links d-none">
                            @foreach ($adsTime ?? [] as $ads)
                                <source src="{{ $ads }}" type="video/mp4" />
                            @endforeach
                        </div>
                        <div class="d-flex justify-content-between align-items-center flex-wrap skip-video">
                            <span class="advertise-text d-none">@lang('Advertisement') - 
                                <span class="remains-ads-time">00:52</span>
                            </span>
                            <button class="skipButton d-none" id="skip-button" data-skip-time="0">
                                @lang('Skip Ad')
                            </button>
                        </div>
                    </div>

                    <!-- Movie Content -->
                    <div class="movie-content">
                        <div class="movie-content-inner d-sm-flex justify-content-between align-items-center flex-wrap">
                            <div class="movie-content-left">
                                <h3 class="title">{{ __($seoContents['social_title']) }}</h3>
                                <span class="sub-title">
                                    @lang('Category'): 
                                    <span class="cat">
                                        {{ app()->getLocale() === 'ar' ? $item->category->name : $item->category->name_en }}
                                    </span>
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
                                    <span class="movie-widget">
                                        <i class="lar la-star text--warning"></i> {{ getAmount($item->ratings) }}
                                    </span>
                                    <span class="movie-widget">
                                        <i class="lar la-eye text--danger"></i> {{ getAmount($item->view) }} @lang('views')
                                    </span>

                                    @php
                                        $wishlist = $item->wishlists->where('user_id', auth()->id())->count();
                                    @endphp
                                    <span class="movie-widget addWishlist {{ $wishlist ? 'd-none' : '' }}" 
                                          data-id="{{ $item->id }}" data-type="item">
                                        <i class="las la-plus-circle"></i>
                                    </span>
                                    <span class="movie-widget removeWishlist {{ $wishlist ? '' : 'd-none' }}" 
                                          data-id="{{ $item->id }}" data-type="item">
                                        <i class="las la-minus-circle"></i>
                                    </span>
                                </div>
                                <ul class="post-share d-flex align-items-center justify-content-sm-end mt-2 flex-wrap">
                                    <li class="caption">@lang('Share'):</li>
                                    @php
                                        $currentUrl = urlencode(url()->current());
                                    @endphp
                                    <li data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('Facebook')">
                                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $currentUrl }}">
                                            <i class="lab la-facebook-f"></i>
                                        </a>
                                    </li>
                                    <li data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('Linkedin')">
                                        <a href="http://www.linkedin.com/shareArticle?mini=true&url={{ $currentUrl }}">
                                            <i class="lab la-linkedin-in"></i>
                                        </a>
                                    </li>
                                    <li data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('Twitter')">
                                        <a href="https://twitter.com/intent/tweet?text={{ __($item->title) }}&url={{ $currentUrl }}">
                                            <i class="lab la-twitter"></i>
                                        </a>
                                    </li>
                                    <li data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('Pinterest')">
                                        <a href="http://pinterest.com/pin/create/button/?url={{ $currentUrl }}">
                                            <i class="lab la-pinterest"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <p class="movie-widget__desc">{{ $seoContents['social_description'] }}</p>
                    </div>
                </div>
            </div>
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


@extends($activeTemplate . 'layouts.frontend')

@section('content')
    <section class="movie-details-section section--bg ptb-80">
        <div class="container">
            <div class="row justify-content-center mb-30-none">
                <div class="col-xl-8 col-lg-8 mb-30">
                    <!-- Video Content Section -->
                    <div id="video-content">
                        @php
                            $streamAvailable = $item->stream && $item->is_stream && $item->stream->embed_code;
                        @endphp

                        @if ($item->version == Status::RENT_VERSION || !$watchEligable)
                            <!-- Locked Video Display -->
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
                        @elseif ($streamAvailable)
                            <!-- Live Stream Embed with HLS -->
    <div id="video-container" class="embed-container">
        <video id="hls-player" controls autoplay></video>
    </div>
                            <!-- Countdown Timer -->
                            @include($activeTemplate . 'partials.countdown-timer', ['item' => $item])
                        @else
                            <!-- Stream Not Available -->
                            <div class="main-video-lock">
                                <div class="main-video-lock-content">
                                    <span class="icon"><i class="las la-lock"></i></span>
                                    <p class="title">@lang('Live stream not available')</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Ad Video Section (Hidden by Default) -->
                    <div class="ad-video position-relative d-none">
                        <video class="ad-player" id="ad-video"></video>
                        <div class="ad-links d-none">
                            @foreach ($adsTime ?? [] as $ads)
                                <source src="{{ $ads }}" type="video/mp4" />
                            @endforeach
                        </div>
                        <div class="skip-video d-flex justify-content-between align-items-center">
                            <span class="advertise-text d-none">
                                @lang('Advertisement') - <span class="remains-ads-time">00:52</span>
                            </span>
                            <button class="skipButton d-none" id="skip-button" data-skip-time="0">
                                @lang('Skip Ad')
                            </button>
                        </div>
                    </div>

                    <!-- Movie Details Section -->
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
                                <div class="movie-widget-area">
                                    @auth
                                        @if ($watchEligable && gs('watch_party'))
                                            <button class="watch-party-btn">
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
                                <!-- Social Share Buttons -->
                                <ul class="post-share d-flex align-items-center justify-content-sm-end flex-wrap">
                                    <li class="caption">@lang('Share'):</li>
                                    <li>
                                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank">
                                            <i class="lab la-facebook-f"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="https://www.linkedin.com/shareArticle?url={{ urlencode(url()->current()) }}" target="_blank">
                                            <i class="lab la-linkedin-in"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}" target="_blank">
                                            <i class="lab la-twitter"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="http://pinterest.com/pin/create/button/?url={{ urlencode(url()->current()) }}" target="_blank">
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
            padding-top: 56.25%; /* 16:9 Aspect Ratio */
        }

        .main-video iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
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
        }

        .ad-video {
            display: none;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }

        .embed-container {
    position: relative;
    width: 100%;
    padding-top: 56.25%; /* 16:9 Aspect Ratio */
    margin: 0 auto; /* Center the container */
}

.embed-container iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: 0;
}

/* Mobile Devices (up to 767px) */
@media (max-width: 767px) {
    .embed-container {
        width: calc(100% - 40px); /* 20px margin on left and right */
        margin-left: 20px;
        margin-right: 20px;
    }
}

/* Desktop Devices (768px and above) */
@media (min-width: 768px) {
    .embed-container {
        width: 80%; /* 10% margin on left and right */
        margin-left: 10%;
        margin-right: 10%;
    }
}
    </style>
@endpush

@push('script')
    <script>
        $(document).ready(function () {
            // Handle Ad Video Playback
            if ($('#ad-video').length > 0) {
                const adPlayer = new Plyr('#ad-video');
                adPlayer.on('ready', () => adPlayer.play());

                $('#skip-button').on('click', function () {
                    adPlayer.stop();
                    $('.ad-video').hide();
                    $('.main-video').show();
                });
            }
        });
    </script>
@endpush

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const video = document.getElementById('hls-player');
            const streamUrl = "{{ $item->stream->embed_code }}";

            if (Hls.isSupported()) {
                const hls = new Hls();
                hls.loadSource(streamUrl);
                hls.attachMedia(video);
            } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                video.src = streamUrl;
            } else {
                console.error("HLS is not supported on this browser.");
            }
        });
    </script>
@endpush

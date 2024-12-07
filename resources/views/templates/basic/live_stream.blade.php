@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="movie-details-section section--bg ptb-80">
        <div class="container">
            <div class="row justify-content-center mb-30-none">
                <div class="col-xl-8 col-lg-8 mb-30">
                    <!-- Movie Main Content -->
                    <div class="movie-item">
                        <div class="main-video position-relative" 
                             data-start-at="{{ $item->stream->start_at ?? '' }}" 
                             style="background-image: url('{{ getImage(getFilePath('item_landscape') . '/' . $item->image->landscape) }}'); background-size: cover; background-position: center;">
                            <div id="video-content">
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
                                @else
                                    <!-- Live Stream Embed -->
                                    @if ($item->stream && $item->stream->embed_code)
                                        <div class="embed-container">
                                            {!! $item->stream->embed_code !!}
                                        </div>
                                    @else
                                        <!-- Stream Not Available -->
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
                        <div class="skip-video d-flex justify-content-between align-items-center">
                            <span class="advertise-text d-none">@lang('Advertisement') - 
                                <span class="remains-ads-time">00:52</span>
                            </span>
                            <button class="skipButton d-none" id="skip-button" data-skip-time="0">@lang('Skip Ad')</button>
                        </div>
                    </div>

                    <!-- Movie Details -->
                    <div class="movie-content">
                        <div class="movie-content-inner d-sm-flex justify-content-between align-items-center flex-wrap">
                            <div class="movie-content-left">
                                <h3 class="title">{{ __($seoContents['social_title']) }}</h3>
                                <span class="sub-title">
                                    @lang('Category'): 
                                    <span class="cat">{{ app()->getLocale() === 'ar' ? $item->category->name : $item->category->name_en }}</span>
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
                                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}">
                                            <i class="lab la-facebook-f"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="https://www.linkedin.com/shareArticle?url={{ urlencode(url()->current()) }}">
                                            <i class="lab la-linkedin-in"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}">
                                            <i class="lab la-twitter"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="http://pinterest.com/pin/create/button/?url={{ urlencode(url()->current()) }}">
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

        .embed-container iframe {
            border: 0;
        }
    </style>
@endpush

@push('script')
    <script>
        $(document).ready(function () {
            // Handle Ad Video
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

@extends($activeTemplate . 'layouts.frontend')

@section('content')
    <section class="movie-section section--bg ptb-80">
        <!-- Live Streams Section -->
        @if ($hasStream)
            <div class="container">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="section-header">
                            <h2 class="section-title">@lang('Live Streams')</h2>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center mb-30-none ajaxLoad">
                    @forelse($items as $item)
                        @if ($item->is_stream)
                            @if ($loop->last)
                                <span class="data_id d-none" data-id="{{ $item->id }}"></span>
                                <span class="category_id d-none" data-category_id="{{ @$category->id }}"></span>
                                <span class="subcategory_id d-none" data-subcategory_id="{{ @$subcategory->id }}"></span>
                                <span class="search d-none" data-search="{{ @$search }}"></span>
                            @endif
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-xs-6 mb-30">
                                <div class="movie-item">
                                    <div class="movie-thumb" data-start-at="{{ $item->stream->start_at }}">
                                        <img src="{{ getImage(getFilePath('item_portrait') . '/' . $item->image->portrait) }}"
                                            alt="movie">

                                        <!-- Display "Paid" if the item is not free with a yellow badge -->
                                        @if ($item->version != 0)
                                            <span class="movie-badge"
                                                style="background-color: yellow; color: black;">@lang('Paid')</span>
                                        @else
                                            <span class="movie-badge">@lang('Free')</span>
                                        @endif

                                        <!-- Media type icon -->
                                        <span class="media-type"
                                            style="position: absolute; bottom: 10px; right: 10px; color: #fff; padding: 5px 10px; border-radius: 5px;">
                                            @if ($item->is_audio)
                                                <i class="fas fa-headphones" style="scale: 150%"></i> <!-- Audio Icon -->
                                            @else
                                                <i class="fas fa-video" style="scale: 150%"></i> <!-- Video Icon -->
                                            @endif
                                        </span>

                                        <!-- Countdown timer tag -->
                                        <div class="countdown-timer" data-start-at="{{ $item->start_at }}"
                                            style="position: absolute; bottom: 10px; right: 22px; background-color: rgba(237, 35, 35, 0.7); color: white; padding: 5px 10px; border-radius: 5px;">
                                            <span class="countdown-text">@lang('Starting in:')</span>
                                            <span class="countdown-time"></span>
                                        </div>

                                        <div class="movie-thumb-overlay">
                                            <a class="video-icon" href="{{ route('watch.live', $item->slug) }}">
                                                <i class="fas fa-play"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-xs-12 mb-30">
                            <img src="{{ asset($activeTemplateTrue . 'images/no-results.png') }}" alt="">
                        </div>
                    @endforelse
                </div>

            </div>
        @endif

        <!-- Category Items Section -->
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="section-header">
                        <h2 class="section-title">@lang('Category Items')</h2>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center mb-30-none ajaxLoad">
                @forelse($items as $item)
                    @if (!$item->is_stream)
                        @if ($loop->last)
                            <span class="data_id d-none" data-id="{{ $item->id }}"></span>
                            <span class="category_id d-none" data-category_id="{{ @$category->id }}"></span>
                            <span class="subcategory_id d-none" data-subcategory_id="{{ @$subcategory->id }}"></span>
                            <span class="search d-none" data-search="{{ @$search }}"></span>
                        @endif
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-xs-6 mb-30">
                            <div class="movie-item">
                                <div class="movie-thumb">
                                    <img src="{{ getImage(getFilePath('item_portrait') . '/' . $item->image->portrait) }}"
                                        alt="movie">

                                    <!-- Display "Paid" if the item is not free with a yellow badge -->
                                    @if ($item->version != 0)
                                        <span class="movie-badge">@lang('Paid')</span>
                                    @else
                                        <span class="movie-badge">@lang('Free')</span>
                                    @endif

                                    <div class="movie-thumb-overlay">
                                        <a class="video-icon"
                                            href="{{ $item->is_audio ? route('preview.audio', $item->slug) : route('watch', $item->slug) }}">
                                            <i class="fas fa-play"></i>
                                        </a>
                                    </div>

                                    <!-- Media type icon -->
                                    <span class="media-type"
                                        style="position: absolute; bottom: 10px; right: 10px; color: #fff; padding: 5px 10px; border-radius: 5px;">
                                        @if ($item->is_audio)
                                            <i class="fas fa-headphones" style="scale: 150%"></i> <!-- Audio Icon -->
                                        @else
                                            <i class="fas fa-video" style="scale: 150%"></i> <!-- Video Icon -->
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-xs-12 mb-30">
                        <img src="{{ asset($activeTemplateTrue . 'images/no-results.png') }}" alt="">
                    </div>
                @endforelse
            </div>

            <!-- Playlists Section -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="section-header">
                        <h2 class="section-title">@lang('Playlists')</h2>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center mb-30-none">
                @forelse($playlists as $playlist)
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-xs-6 mb-30">
                        <div class="movie-item">
                            <div class="movie-thumb">
                                <img class="lazy-loading-img"
                                    data-src="{{ getImage(getFilePath('item_portrait') . $playlist->cover_image) }}"
                                    src="{{ asset('assets/global/images/lazy.png') }}" alt="movie">

                                <span class="movie-badge">{{ 'Playlist' }}</span>

                                <div class="movie-thumb-overlay">
                                    <a class="video-icon" href="{{ route('playlist.play', $playlist->id) }}">
                                        <i class="fas fa-play"></i>
                                    </a>
                                </div>

                                <!-- Media type icon -->
                                <span class="media-type"
                                    style="position: absolute; bottom: 10px; right: 10px; color: #fff; padding: 5px 10px; border-radius: 5px;">
                                    @if ($playlist->type == 'audio')
                                        <i class="fas fa-headphones" style="scale: 150%"></i> <!-- Audio Icon -->
                                    @else
                                        <i class="fas fa-video" style="scale: 150%"></i> <!-- Video Icon -->
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-xs-12 mb-30">
                        <img src="{{ asset($activeTemplateTrue . 'images/no-results.png') }}" alt="">
                    </div>
                @endforelse
            </div>
        </div>
    </section>
    <div class="custom_loading"></div>
@endsection


@push('style')
    <style>
        .lazy-loading-img {
            width: auto !important;
            height: auto !important;
            display: block !important;
        }

        .countdown-timer {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
        }

        .countdown-text {
            margin-right: 5px;
        }

        .movie-thumb:hover .countdown-timer {
            background-color: rgba(0, 0, 0, 0.9);
            /* Slightly darker on hover */
        }
    </style>
@endpush
@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js" async=""></script>
@endpush
@push('script')
    <script>
        "use strict"
        var send = 0;
        $(window).scroll(function() {
            if ($(window).scrollTop() + $(window).height() > $(document).height() - 60) {
                if ($('.ajaxLoad').hasClass('loaded')) {
                    $('.custom_loading').removeClass('loader-area');
                    return false;
                }
                $('.custom_loading').addClass('loader-area');
                setTimeout(function() {
                    if (send == 0) {
                        send = 1;
                        var url = "{{ route('loadmore.load_data') }}";
                        var id = $('.data_id').last().data('id');
                        var category_id = $('.category_id').last().data('category_id');
                        var subcategory_id = $('.subcategory_id').last().data('subcategory_id');
                        var search = $('.search').last().data('search');
                        var data = {
                            id: id,
                            category_id: category_id,
                            subcategory_id: subcategory_id,
                            search: search
                        };
                        $.get(url, data, function(response) {
                            if (response == 'end') {
                                $('.custom_loading').removeClass('loader-area');
                                $('.footer').removeClass('d-none');
                                $('.ajaxLoad').addClass('loaded');
                                return false;
                            }
                            $('.custom_loading').removeClass('loader-area');
                            $('.sections').append(response);
                            $('.ajaxLoad').append(response);
                            send = 0;
                        });
                    }
                }, 1000);
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            let lazyImages = [].slice.call(document.querySelectorAll("img.lazy-loading-img"));

            if ("IntersectionObserver" in window) {
                let lazyImageObserver = new IntersectionObserver(function(entries, observer) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            let lazyImage = entry.target;
                            lazyImage.src = lazyImage.dataset.src;
                            lazyImage.classList.remove("lazy-loading-img");
                            lazyImageObserver.unobserve(lazyImage);
                        }
                    });
                });

                lazyImages.forEach(function(lazyImage) {
                    lazyImageObserver.observe(lazyImage);
                });
            } else {
                // Fallback for browsers that don't support IntersectionObserver
                lazyImages.forEach(function(lazyImage) {
                    lazyImage.src = lazyImage.dataset.src;
                });
            }

            // Function to calculate time remaining and update the countdown
            function updateCountdown(startTime, element) {
                const now = new Date().getTime();
                const distance = startTime - now;

                // Time calculations for days, hours, minutes, and seconds
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // Update the countdown timer display
                if (distance > 0) {
                    element.querySelector('.countdown-time').textContent =
                        `${days}d ${hours}h ${minutes}m ${seconds}s`;
                } else {
                    element.querySelector('.countdown-text').textContent = '@lang('Stream Started')';
                    element.querySelector('.countdown-time').textContent = '';
                }
            }

            // Find all movie thumbnails with countdown timers
            const thumbnails = document.querySelectorAll('.movie-thumb[data-start-at]');

            thumbnails.forEach(function(thumb) {
                const startAt = new Date(thumb.getAttribute('data-start-at')).getTime();
                const countdownElement = thumb.querySelector('.countdown-timer');

                // Initial update
                updateCountdown(startAt, countdownElement);

                // Update the countdown every second
                setInterval(function() {
                    updateCountdown(startAt, countdownElement);
                }, 1000);
            });
        });
    </script>
@endpush

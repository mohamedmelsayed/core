@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $bannerContent = getContent('banner.content', true);
    @endphp

    @if ($advertise && !auth()->id())
        <div class="modal" id="adModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true"">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-body position-relative p-0">
                        <div class="ads-close-btn position-absolute">
                            <button class="btn-close btn-close-white" data-bs-dismiss="modal" type="button"
                                aria-label="Close"></button>
                        </div>
                        <a href="{{ $advertise->content->link }}" target="_blank">
                            <img src="{{ getImage(getFilePath('ads') . '/' . @$advertise->content->image) }}"
                                alt="@lang('image')">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <section class="banner-section bg-overlay-black bg_img"
        data-background="{{ getImage('assets/images/frontend/banner/' . @$bannerContent->data_values->background_image, '1778x755') }}">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-xl-6 col-lg-6">
                    <div class="banner-content">
                        <span class="sub-title">{{ __(@$bannerContent->data_values->heading) }}</span>
                        <h1 class="title text-white">{{ __(@$bannerContent->data_values->sub_heading) }}</h1>
                        @guest
                            <div class="banner-btn">
                                <a class="btn--base"
                                    href="{{ @$bannerContent->data_values->button_1_link }}">{{ __(@$bannerContent->data_values->button_1) }}</a>
                                <a class="btn--base active" href="{{ @$bannerContent->data_values->button_2_link }}"><i
                                        class="las la-plus"></i> {{ __(@$bannerContent->data_values->button_2) }}</a>
                            </div>
                        @endguest
                    </div>
                </div>
                <div class="col-xl-6 col-lg-6">
                    <div class="banner-slider">
                        <div class="swiper-wrapper">
                            @foreach ($sliders as $slider)
                                <div class="swiper-slide">
                                    <div class="movie-item">
                                        <div class="movie-thumb">
                                            <img class="lazy-loading-img"
                                                data-src="{{ getImage(getFilePath('item_portrait') . '/' . @$slider->item->image->portrait) }}"
                                                src="{{ asset('assets/global/images/lazy.png') }}" alt="movie">
                                            <div class="movie-thumb-overlay">
                                                <a class="video-icon" href="{{ route('watch', @$slider->item->slug) }}"><i
                                                        class="fas fa-play"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="movie-section ptb-80 section" data-section="single1">
        <div class="container">
            <div class="row justify-content-center align-items-center mb-30-none">
                <div class="col-xl-3 col-lg-4 col-md-12 col-sm-12 mb-30">
                    <div class="movie-section-header-wrapper">
                        <div class="movie-section-header">
                            <h2 class="title">@lang('Featured Movies to Watch Now')</h2>
                            <p>@lang('Most watched movies by days')</p>
                        </div>
                        <div class="movie-slider-arrow">
                            <div class="slider-prev">
                                <i class="fas fa-angle-left"></i>
                            </div>
                            <div class="slider-next">
                                <i class="fas fa-angle-right"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9 col-lg-8 col-md-12 col-sm-12 mb-30">
                    <div class="movie-slider">
                        <div class="swiper-wrapper">
                            @foreach ($featuredMovies as $featured)
                                <div class="swiper-slide">
                                    <div class="movie-item">
                                        <div class="movie-thumb">
                                            <img class="lazy-loading-img"
                                                data-src="{{ getImage(getFilePath('item_portrait') . $featured->image->portrait) }}"
                                                src="{{ asset('assets/global/images/lazy.png') }}" alt="movie">
                                            @if ($featured->item_type == 1 && $featured->version == 0)
                                                <span class="movie-badge free">@lang('Free')</span>
                                            @elseif($featured->item_type == 3)
                                                <span class="movie-badge paid">@lang('Trailer')</span>
                                            @endif
                                            <div class="movie-thumb-overlay">
                                                <a class="video-icon" href="{{ route('watch', $featured->slug) }}"><i
                                                        class="fas fa-play"></i></a>
                                            </div>

                                            <!-- Display Font Awesome icon based on is_audio inside the thumb -->
                                            <span class="media-type"
                                                style="position: absolute; top: 10px; right: 10px; background-color: #000; color: #fff; padding: 5px 10px; border-radius: 5px;">
                                                @if ($featured->is_audio)
                                                    <i class="fas fa-headphones"></i> <!-- Audio Icon -->
                                                @else
                                                    <i class="fas fa-video"></i> <!-- Video Icon -->
                                                @endif
                                            </span>

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="sections"></div>
    <div class="custom_loading"></div>
@endsection

<style>
    /* General styling for media type icon */
.media-type {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: rgba(0, 0, 0, 0.7); /* Semi-transparent black background */
    color: white;
    padding: 5px 10px;
    border-radius: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px; /* Adjust font size */
    width: 40px; /* Fixed size for the icon container */
    height: 40px; /* Fixed height for the icon container */
    transition: transform 0.3s ease; /* Smooth hover effect */
}

/* Hover effect to slightly enlarge the icon */
.movie-thumb:hover .media-type {
    transform: scale(1.1);
}

/* Styling the icon (adjust sizes accordingly) */
.media-type img {
    width: 20px; /* Ensure the icon itself is centered and has consistent size */
    height: 20px;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .media-type {
        width: 30px;
        height: 30px;
        font-size: 12px;
    }

    .media-type img {
        width: 15px;
        height: 15px;
    }
}


/* General styling for movie badge */
.movie-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background-color: yellow; /* Default background color for paid content */
    color: black;
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 14px; /* Adjust font size */
    font-weight: bold;
    text-transform: uppercase; /* Optional: make the text uppercase */
    z-index: 2; /* Ensure it stays on top */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2); /* Slight shadow for better visibility */
    transition: background-color 0.3s ease; /* Smooth transition for hover effect */
}

/* Free badge specific color */
.movie-badge.free {
    background-color: green;
    color: white;
}

/* Paid badge specific color */
.movie-badge.paid {
    background-color: gold;
    color: black;
}

/* Hover effect for the badge */
.movie-thumb:hover .movie-badge {
    background-color: orange; /* Change color slightly on hover */
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .movie-badge {
        font-size: 12px; /* Reduce font size on smaller screens */
        padding: 4px 8px; /* Adjust padding */
    }
}


</style>

@push('script')
    <script>
        "use strict";

        $(document).ready(function() {
            setTimeout(() => {
                $("#adModal").modal('show');
            }, 2000);
        });

        var send = 0;
        $(window).scroll(function() {
            if ($(window).scrollTop() + $(window).height() > $(document).height() - 60) {
                if ($('.section').hasClass('last-item')) {
                    $('.custom_loading').removeClass('loader-area');
                    return false;
                }

                $('.custom_loading').addClass('loader-area');
                setTimeout(function() {
                    if (send == 0) {
                        send = 1;
                        var sec = $('.section').last().data('section');
                        var url = "{{ route('get.section') }}";
                        var data = {
                            sectionName: sec
                        };
                        $.get(url, data, function(response) {
                            if (response == 'end') {
                                $('.section').last().addClass('last-item');
                                $('.custom_loading').removeClass('loader-area');
                                $('.footer').removeClass('d-none');
                                return false;
                            }
                            $('.custom_loading').removeClass('loader-area');
                            $('.sections').append(response);
                            send = 0;
                        });

                    }
                }, 1000)
            }
            let images = document.querySelectorAll('.lazy-loading-img');

            function preloadImage(image) {
                const src = image.getAttribute('data-src');
                image.src = src;
            }

            let imageOptions = {
                threshold: 1,
                border: "5px solid green",
            };

            const imageObserver = new IntersectionObserver((entries, imageObserver) => {
                entries.forEach(entry => {
                    if (!entry.isIntersecting) {
                        return;
                    } else {
                        preloadImage(entry.target)
                        imageObserver.unobserve(entry.target)
                    }
                })
            }, imageOptions)
            images.forEach(image => {
                imageObserver.observe(image)
            });
        });
    </script>
@endpush

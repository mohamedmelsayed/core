@extends($activeTemplate . 'layouts.frontend')
@section('content')

<section class="audio-details-section section--bg ptb-80">
    <div class="container">
        <div class="row @if (blank($episodes)) justify-content-center @endif mb-30-none">
            <div class="col-xl-8 col-lg-8 mb-30">
                <div class="audio-item">
                    <div class="main-audio">
                        @foreach ($audios as $audio)
                        <div>
                            <div id="audio-player">
                                <audio id="audio" src="{{ $audio->content }}" controls style="display:none;"></audio>

                                <div id="audio-controls-container">
                                    <div id="file-title">{{ __($seoContents["social_title"])}}</div>
                                    <div id="audio-controls">
                                        <button class="audio-control" id="play-pause"><i class="las la-play-circle"></i></button>
                                        <button class="audio-control" id="volume-up"><i class="las la-volume-up"></i></button>
                                        <button class="audio-control" id="volume-down"><i class="las la-volume-down"></i></button>
                                        <div id="waveform"></div>
                                        <div id="time-indicator"></div>
                                    </div>
                                </div>
                                <img src="{{ getImage(getFilePath('item_portrait') . '/' . $item->image->portrait) }}" id="thumbnail" />
                            </div>


                        </div>
                        @endforeach

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
                    <div class="audio-content">
                        <div class="audio-content-inner d-sm-flex justify-content-between align-items-center flex-wrap">
                            <div class="audio-content-left">
                                <h3 class="title">{{ __($seoContents["social_title"]) }}</h3>
                                <span class="sub-title">@lang('Category') : <span class="cat">{{app()->getLocale() === 'ar'? $item->category->name :$item->category->name_en}}</span>
                                    @if ($item->sub_category)
                                    @lang('Sub Category'): {{ app()->getLocale() === 'ar'? $item->sub_category->name :$item->sub_category->name_en }}
                                    @endif
                                </span>
                            </div>
                            <div class="audio-content-right">
                                <div class="audio-widget-area align-items-center">
                                    @auth
                                    @if ($watchEligable && gs('watch_party'))
                                    <button type="button" class="watch-party-btn watchPartyBtn">
                                        <i class="las la-desktop base--color"></i>
                                        <span>@lang('Watch party')</span>
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
                                    <li class="caption">@lang('Share') :</li>
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
                        <div class="audio-widget-area"></div>
                        <!-- <p class="audio-widget__desc">{{ __($seoContents["social_description"]) }}</p> -->
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
                                        <a class="custom-btn" href="{{ route('user.login') }}">@lang('Subscribe to listen')</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="widget-item__lock">
                                <span class="widget-item__lock-icon">
                                    @if ($status)
                                    <i class="fas fa-unlock"></i>
                                    @else
                                    <i class="fas fa-lock"></i>
                                    @endif
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
<section class="movie-section ptb-80">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="section-header">
                    <h2 class="section-title">@lang(!$item->is_audio?'Related Video':'Related Audio')</h2>

                    </div>
                </div>
            </div>
            <div class="row justify-content-center mb-30-none">
                @foreach  ($item->is_audio? $relatedAudios:$relatedItems as $related)
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-xs-6 mb-30">
                        <div class="movie-item">
                            <div class="movie-thumb">
                                <img src="{{ getImage(getFilePath('item_portrait') . '/' . $related->image->portrait) }}" alt="movie">
                                <span class="movie-badge">{{ __($related->versionName) }}</span>
                                <div class="movie-thumb-overlay">
                                    <a class="video-icon" href="{{$related->is_audio?route('preview.audio', $related->slug) :route('watch', $related->slug) }}"><i class="fas fa-play"></i></a>
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
                                <h2 class="section-title">@lang($item->is_audio?'Related Video':'Related Audio')</h2>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center mb-30-none">
                @foreach (!$item->is_audio? $relatedAudios:$relatedItems as $related)
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-xs-6 mb-30">
                        <div class="movie-item">
                            <div class="movie-thumb">
                                <img src="{{ getImage(getFilePath('item_portrait') . '/' . $related->image->portrait) }}" alt="movie">
                                <span class="movie-badge">{{ __($related->versionName) }}</span>
                                <div class="movie-thumb-overlay">
                                    <a class="video-icon" href="{{$related->is_audio?route('preview.audio', $related->slug) :route('watch', $related->slug) }}"><i class="fas fa-play"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </section>
@endsection
<style>
    #audio-player {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background-color: rgb(110, 118, 118);
        /* Light         background color for the player */
        width: 100%;
        border-radius: 7%;
        height: 250px;
        padding: 10px;
        box-sizing: border-box;
        position: relative;
    }

    #audio-controls-container {
        display: flex;
        align-items: center;
        flex: 1;
        max-width: calc(100% - 220px);
        /* Adjust to account for thumbnail */
        margin-right: 10px;
        /* Space between controls and thumbnail */
        background-color: rgb(241, 239, 222);
        /* Background color for controls area */
        padding: 10px;
        border-radius: 2px;
        /* Optional: Rounded corners for the controls area */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        /* Optional: Shadow for better visibility */
    }

    #file-title {
        text-align: center;
        margin-bottom: 10px;
        color: #333;
        font-size: 18px;
        font-weight: bold;
    }

    #audio-controls {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
    }

    #audio-controls button {
        background-color: rgb(255, 255, 255);
        border: none;
        padding: 10px;
        border-radius: 5px;
        cursor: pointer;
    }

    #waveform {
        flex: 1;
        height: 100px;
        margin-right: 10px;
        /* Space between waveform and time indicator */
    }

    #time-indicator {
        width: 60px;
        text-align: center;
    }

    #thumbnail {
        width: 200px;
        height: 200px;
        background: url("{{ getImage(getFilePath('item_portrait') . '/' . $item->image->portrait) }}") no-repeat center center;
        background-size: cover;
        border-radius: 20%;
        margin-left: 10px;
    }

    /* Responsive styling */
    @media (max-width: 768px) {
        #audio-player {
            height: 200px;
        }

        #audio-controls-container {
            max-width: 100%;
            margin-right: 0;
        }

        #thumbnail {
            display: none;
        }

        #file-title {
            font-size: 16px;
        }

        #waveform {
            height: 80px;
        }

        #audio-controls button {
            padding: 8px;
        }
    }

    @media (max-width: 480px) {
        #audio-player {
            flex-direction: column;
            align-items: flex-start;
            height: auto;
        }

        #audio-controls-container {
            width: 100%;
            margin: 0;
            padding: 5px;
        }

        #file-title {
            font-size: 14px;
        }

        #waveform {
            height: 60px;
        }

        #audio-controls button {
            padding: 6px;
            font-size: 14px;
        }

        #time-indicator {
            width: 50px;
        }
    }
</style>

@push('script')
<script src="https://unpkg.com/wavesurfer.js@7.7.15/dist/wavesurfer.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const playPauseButton = document.getElementById('play-pause');
        const volumeUpButton = document.getElementById('volume-up');
        const volumeDownButton = document.getElementById('volume-down');
        const audioControlButtons = document.querySelectorAll('.audio-control');

        const wavesurfer = WaveSurfer.create({
            container: '#waveform',
            waveColor: 'blue',
            progressColor: 'purple',
            barWidth: 2,
            responsive: true,
            normalize: true,
            interact: true,
            height: 100,
            barGap: 3,
            partialRender: true
        });

        wavesurfer.load('{{ $audios[0]->content }}');
        wavesurfer.play();

        playPauseButton.addEventListener('click', function(event) {
            event.stopPropagation();
            if (wavesurfer.isPlaying()) {
                wavesurfer.pause();
            } else {
                wavesurfer.play();
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.code === 'Space') {
                event.preventDefault();
                if (wavesurfer.isPlaying()) {
                    wavesurfer.pause();
                } else {
                    wavesurfer.play();
                }
            }
        });

        volumeUpButton.addEventListener('click', function(event) {
            event.stopPropagation();
            let currentVolume = wavesurfer.getVolume();
            wavesurfer.setVolume(Math.min(currentVolume + 0.1, 1));
        });

        volumeDownButton.addEventListener('click', function(event) {
            event.stopPropagation();
            let currentVolume = wavesurfer.getVolume();
            wavesurfer.setVolume(Math.max(currentVolume - 0.1, 0));
        });

        audioControlButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                event.stopPropagation();
            });
        });

        wavesurfer.on('audioprocess', function() {
            const currentTime = wavesurfer.getCurrentTime();
            const formattedTime = formatTime(currentTime);
            document.getElementById('time-indicator').innerText = formattedTime;
        });

        function formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = Math.floor(seconds % 60);
            return `${padZero(minutes)}:${padZero(remainingSeconds)}`;
        }

        function padZero(number) {
            return number < 10 ? `0${number}` : number;
        }
    });
</script>
@endpush

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

                                <div id="audio-controls">


                                    <button class="audio-control" id="play-pause"><i class="las la-play-circle"></i></button>
                                    <button class="audio-control" id="volume-up"><i class="las la-volume-up"></i></button>
                                    <button class="audio-control" id="volume-down"><i class="las la-volume-down"></i></button>
                                    <div id="waveform"></div> <!-- Waveform is now at the bottom of audioBG -->
                                    <div id="time-indicator"></div>

                                </div>
                                <img src="{{ getImage(getFilePath('item_portrait') . '/' . $item->image->portrait) }}" />
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
<section class="audio-section ptb-80">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="section-header">
                    <h2 class="section-title">@lang('Related Videos')</h2>
                </div>
            </div>
        </div>
        <div class="row justify-content-center mb-30-none">
            @foreach ($relatedItems as $relatedItem)
            <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
                <div class="audio-item">
                    <div class="movie-thumb">
                        <a href="{{$relatedItem->is_audio?route('preview.audio', $relatedItem->slug) :route('watch', $relatedItem->slug) }}">
                            <img src="{{ getImage(getFilePath('item_portrait') . '/' . $relatedItem->image->portrait) }}" alt="audio">
                        </a>
                    </div>
                    <div class="movie-thumb-overlay">
                        <h3 class="title"><a href="{{$relatedItem->is_audio?route('preview.audio', $relatedItem->slug) :route('watch', $relatedItem->slug) }}">{{ __($relatedItem->title) }}</a>
                        </h3>
                        <p>{{ strLimit(strip_tags($relatedItem->description), 150) }}</p>
                        <div class="audio-widget-area d-flex align-items-center justify-content-between flex-wrap">
                            <span class="audio-widget"><i class="lar la-star text--warning"></i> {{ getAmount($relatedItem->ratings) }}</span>
                            <span class="audio-widget"><i class="lar la-eye text--danger"></i> {{ getAmount($relatedItem->view) }} @lang('views')</span>
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
                    <h2 class="section-title">@lang('Related Audio')</h2>
                </div>
            </div>
        </div>
        <div class="row justify-content-center mb-30-none">
            @foreach ($relatedAudios as $relatedItem)
            <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
                <div class="audio-item">
                    <div class="movie-thumb">
                        <a href="{{$relatedItem->is_audio?route('preview.audio', $relatedItem->slug) :route('watch', $relatedItem->slug) }}">
                            <img src="{{ getImage(getFilePath('item_portrait') . '/' . $relatedItem->image->portrait) }}" alt="audio">
                        </a>
                    </div>
                    <div class="movie-thumb-overlay">
                        <h3 class="title"><a href="{{$relatedItem->is_audio?route('preview.audio', $relatedItem->slug) :route('watch', $relatedItem->slug) }}">{{ __($relatedItem->title) }}</a>
                        </h3>
                        <p>{{ strLimit(strip_tags($relatedItem->description), 150) }}</p>
                        <div class="audio-widget-area d-flex align-items-center justify-content-between flex-wrap">
                            <span class="audio-widget"><i class="lar la-star text--warning"></i> {{ getAmount($relatedItem->ratings) }}</span>
                            <span class="audio-widget"><i class="lar la-eye text--danger"></i> {{ getAmount($relatedItem->view) }} @lang('views')</span>
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
    position: relative;
    width: 100%;
    max-width: 600px;
    margin: 20px auto;
    background-color: #f5f5f5;
    border-radius: 15px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

#audio-controls {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 15px;
    background-color: #ffffff;
    border-top: 1px solid #ddd;
}

.audio-control {
    background-color: #ffffff;
    border: none;
    font-size: 24px;
    cursor: pointer;
    transition: color 0.3s ease;
}

.audio-control:hover {
    color: #007bff;
}

#audio-controls #play-pause i,
#audio-controls #volume-up i,
#audio-controls #volume-down i {
    color: #555;
}

#waveform {
    flex-grow: 1;
    margin: 0 15px;
    height: 60px;
    background-color: #e9ecef;
    border-radius: 5px;
    overflow: hidden;
}

#time-indicator {
    font-size: 14px;
    color: #666;
    margin-left: 10px;
}

#audio-player img {
    width: 100%;
    border-radius: 0 0 15px 15px;
}

</style>
<!-- 
<style>
    #audio-player {
        width: 100%;
        max-width: 500px;
        margin: 0 auto;
        position: relative;
    }

    #audio-controls {
        width: 100%;
        max-width: 500px;
        margin: 0 auto;
        position: relative;
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    #audioBG {
        height: 500px;
        background: url("{{ getImage(getFilePath('item_portrait') . '/' . $item->image->portrait) }}") no-repeat center center;
        background-size: cover;
        position: relative;
    }

    #waveform {
        position: absolute;
        bottom: 15px;
        width: 100%;
        height: 100px;
        /* Adjust height as needed */
        opacity: 0.9;
        /* 90% transparent */
    }

    .waveform canvas {
        opacity: 0.9;
        /* Ensure the canvas itself is transparent */
    }

    #time-indicator {
        position: absolute;
        bottom: 100px;
        /* This should be equal to the height of the waveform */
        left: 0;
        width: 100%;
        text-align: center;
        padding: 5px 0;
        background-color: rgba(255, 255, 255, 0.8);
    }

    #play-pause,
    #volume-up,
    #volume-down {
        margin-top: 10px;
    }
</style> -->
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
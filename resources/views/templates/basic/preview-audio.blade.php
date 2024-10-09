@extends($activeTemplate . 'layouts.frontend')
@section('content')

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
                                        <button class="audio-control" id="play-pause">
                                            <i class="las la-play-circle"></i>
                                            <i class="las la-pause-circle" style="display: none;"></i>
                                        </button>
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

                    <!-- Audio Content Section -->
                    <div class="audio-content">
                        <div class="audio-content-inner d-sm-flex justify-content-between align-items-center flex-wrap">
                            <div class="audio-content-left">
                                <h3 class="title">{{ __($seoContents["social_title"]) }}</h3>
                                <span class="sub-title">
                                    @lang('Category') : <span class="cat">{{app()->getLocale() === 'ar'? $item->category->name :$item->category->name_en}}</span>
                                    @if ($item->sub_category)
                                    @lang('Sub Category'): {{ app()->getLocale() === 'ar'? $item->sub_category->name :$item->sub_category->name_en }}
                                    @endif
                                </span>
                            </div>

                            <!-- Audio Content Right Section (for wishlist and views) -->
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
                                    <span class="audio-widget">
                                        <i class="lar la-star text--warning"></i> {{ getAmount($item->ratings) }}
                                    </span>
                                    <span class="audio-widget">
                                        <i class="lar la-eye text--danger"></i> {{ getAmount($item->view) }} @lang('views')
                                    </span>
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
                    </div>
                </div>
            </div>

            <!-- Related audio section -->
            <section class="movie-section ptb-80">
                <div class="container">
                    <div class="section-header">
                        <h2 class="section-title">@lang(!$item->is_audio ? 'Related Video' : 'Related Audio')</h2>
                    </div>
                    <div class="row justify-content-center mb-30-none">
                        @foreach ($item->is_audio ? $relatedAudios : $relatedItems as $related)
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-xs-6 mb-30">
                            <div class="movie-item">
                                <div class="movie-thumb">
                                    <img src="{{ getImage(getFilePath('item_portrait') . '/' . $related->image->portrait) }}" alt="movie">
                                    <span class="movie-badge">{{ __($related->versionName) }}</span>
                                    <div class="movie-thumb-overlay">
                                        <a class="video-icon" href="{{ $related->is_audio ? route('preview.audio', $related->slug) : route('watch', $related->slug) }}"><i class="fas fa-play"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </section>
        </div>
    </div>
</section>

@endsection
<style>
  #audio-player {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background-color: cadetblue;
    width: 100%;
    border-radius: 10px;
    padding: 10px;
    position: relative;
}

#audio-controls-container {
    display: flex;
    align-items: center;
    flex: 1;
    max-width: calc(100% - 220px);
    margin-right: 10px;
    background-color: gold;
    padding: 10px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
    background-color: white;
    border: none;
    padding: 10px;
    border-radius: 5px;
    cursor: pointer;
}

#waveform {
    flex: 1;
    height: 100px;
    margin-right: 10px;
}

#time-indicator {
    width: 60px;
    text-align: center;
}

#thumbnail {
    width: 200px;
    height: 200px;
    background-size: cover;
    border-radius: 20%;
    margin-left: 10px;
}

/* Responsive Styling */
@media (max-width: 768px) {
    #audio-player {
        flex-direction: column;
        height: auto;
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
}

</style>

@push('script')
<script src="https://unpkg.com/wavesurfer.js@7.7.15/dist/wavesurfer.min.js"></script>
<script>
   document.addEventListener('DOMContentLoaded', function() {
    const playPauseButton = document.getElementById('play-pause');
    const volumeUpButton = document.getElementById('volume-up');
    const volumeDownButton = document.getElementById('volume-down');
    const wavesurfer = WaveSurfer.create({
        container: '#waveform',
        waveColor: 'blue',
        progressColor: 'purple',
        barWidth: 2,
        responsive: true,
    });

    wavesurfer.load('{{ $audios[0]->content }}');

    playPauseButton.addEventListener('click', function() {
        if (wavesurfer.isPlaying()) {
            wavesurfer.pause();
            playPauseButton.innerHTML = `<i class="las la-play-circle"></i>`;
        } else {
            wavesurfer.play();
            playPauseButton.innerHTML = `<i class="las la-pause-circle"></i>`;
        }
    });

    volumeUpButton.addEventListener('click', function() {
        let currentVolume = wavesurfer.getVolume();
        wavesurfer.setVolume(Math.min(currentVolume + 0.1, 1));
    });

    volumeDownButton.addEventListener('click', function() {
        let currentVolume = wavesurfer.getVolume();
        wavesurfer.setVolume(Math.max(currentVolume - 0.1, 0));
    });

    wavesurfer.on('audioprocess', function() {
        const currentTime = wavesurfer.getCurrentTime();
        document.getElementById('time-indicator').innerText = formatTime(currentTime);
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

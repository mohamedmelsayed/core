@extends($activeTemplate . 'layouts.frontend')
@section('content')

    <section class="audio-details-section section--bg ptb-80">
        <div class="container">
            <div class="row @if (blank($episodes)) justify-content-center @endif mb-30-none">
                <div class="col-xl-8 col-lg-8 mb-30">
                    <div id="audio-player" class="audio-player-container">
                        <audio id="audio" src="{{ $audios[0]->content }}" controls style="display:none;"></audio>

                        <div id="audio-controls-container" class="audio-controls-container">
                            <div id="file-title" class="audio-title">{{ __($seoContents['social_title']) }}</div>
                            <div id="audio-controls" class="audio-controls">
                                <!-- Play/Pause Button -->
                                <button class="audio-control play-btn" id="play-pause">
                                    <i class="las la-play-circle"></i>
                                </button>

                                <!-- Volume Control -->
                                <div class="vlc-volume">
                                    <input type="range" class="volume-slider" id="v-slider" min="0" max="1"
                                        step="0.1" value="0.5">
                                </div>

                                <!-- Repeat Button -->
                                <button class="audio-control repeat-btn" id="repeat-btn">
                                    <i class="las la-redo-alt"></i>
                                </button>

                                <!-- Waveform display -->
                                <div id="waveform" class="waveform"></div>

                                <!-- Time Indicator -->
                                <div id="time-indicator" class="time-indicator"></div>
                            </div>
                        </div>

                        <!-- Thumbnail image (occupying 20% width) -->
                        <img src="{{ getImage(getFilePath('item_portrait') . '/' . $item->image->portrait) }}"
                            id="thumbnail" class="audio-thumbnail" />
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
                                    {{ __($item->description) }}
                                </div>
                            </div>
                            <div class="tab-pane fade fade" id="product-team-content" role="tabpanel"
                                aria-labelledby="product-tab-team">
                                <div class="product-desc-content">
                                    <ul class="team-list">
                                        <li><span>@lang('Director'):</span> {{ __($item->team->director) }}</li>
                                        <li><span>@lang('Producer'):</span> {{ __($item->team->producer) }}</li>
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
                                    <div class="widget-item widget-item__overlay d-flex align-items-center justify-content-between"
                                        data-img="{{ getImage(getFilePath('episode') . '/' . $episode->image) }}">
                                        <div class="widget-item__content d-flex align-items-center audio-small flex-wrap">
                                            <div class="widget-thumb">
                                                <a href="{{ route('listen', [$item->slug, $episode->id]) }}">
                                                    <img src="{{ getImage(getFilePath('episode') . '/' . $episode->image) }}"
                                                        alt="audio">
                                                </a>
                                            </div>
                                            <div class="widget-content">
                                                <h4 class="title">{{ __($episode->title) }}</h4>
                                                <div class="widget-btn">
                                                    @if ($status)
                                                        <a class="custom-btn"
                                                            href="{{ route('listen', [$item->slug, $episode->id]) }}">@lang('Play Now')</a>
                                                    @else
                                                        <a class="custom-btn"
                                                            href="{{ route('user.login') }}">@lang('Subscribe to listen')</a>
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
        @if (($item->is_audio && $relatedAudios->isNotEmpty())  )

        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="section-header">
                        <h2 class="section-title">@lang(!$item->is_audio ? 'Related Video' : 'Related Audio')</h2>

                    </div>
                </div>
            </div>
            <div class="row justify-content-center mb-30-none">
                @foreach ($item->is_audio ? $relatedAudios : $relatedItems as $related)
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-xs-6 mb-30">
                        <div class="movie-item">
                            <div class="movie-thumb">
                                <img src="{{ getImage(getFilePath('item_portrait') . '/' . $related->image->portrait) }}"
                                    alt="movie">
                                <span class="movie-badge">{{ __($related->versionName) }}</span>
                                <div class="movie-thumb-overlay">
                                    <a class="video-icon"
                                        href="{{ $related->is_audio ? route('preview.audio', $related->slug) : route('watch', $related->slug) }}"><i
                                            class="fas fa-play"></i></a>
                                </div>

                                <!-- Display Font Awesome icon based on is_audio inside the thumb -->
                                <span class="media-type"
                                    style="position: absolute; bottom: 10px; right: 10px;  color: #fff; padding: 5px 10px; border-radius: 5px;">
                                    @if ($related->is_audio)
                                        <i class="fas fa-headphones" style="scale: 150%"></i> <!-- Audio Icon -->
                                    @else
                                        <i class="fas fa-video" style="scale: 150%"></i> <!-- Video Icon -->
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
        @endif
        @if (($item->is_audio && $relatedItems->isNotEmpty())  )

        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="section-header">
                        <h2 class="section-title">@lang($item->is_audio ? 'Related Video' : 'Related Audio')</h2>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center mb-30-none">
                @foreach (!$item->is_audio ? $relatedAudios : $relatedItems as $related)
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-xs-6 mb-30">
                        <div class="movie-item">
                            <div class="movie-thumb">
                                <img src="{{ getImage(getFilePath('item_portrait') . '/' . $related->image->portrait) }}"
                                    alt="movie">
                                <span class="movie-badge">{{ __($related->versionName) }}</span>
                                <div class="movie-thumb-overlay">
                                    <a class="video-icon"
                                        href="{{ $related->is_audio ? route('preview.audio', $related->slug) : route('watch', $related->slug) }}"><i
                                            class="fas fa-play"></i></a>
                                </div>
                                <!-- Display Font Awesome icon based on is_audio inside the thumb -->
                                <span class="media-type"
                                    style="position: absolute; bottom: 10px; right: 10px;  color: #fff; padding: 5px 10px; border-radius: 5px;">
                                    @if ($related->is_audio)
                                        <i class="fas fa-headphones" style="scale: 150%"></i> <!-- Audio Icon -->
                                    @else
                                        <i class="fas fa-video" style="scale: 150%"></i> <!-- Video Icon -->
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
        @endif
    </section>
@endsection
<style>
    /* Container for the audio player */
    .audio-player-container {
        width: 100%;
        max-width: 1200px;
        margin: 20px auto;
        background: linear-gradient(to right, #1e3c72, #2a5298);
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Title styling */
    .audio-title {
        color: #fff;
        font-size: 24px;
        font-weight: 600;
        text-align: center;
        margin-bottom: 20px;
    }

    /* Audio controls container (taking 80% width) */
    .audio-controls-container {
        width: 80%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    /* Controls area */
    .audio-controls {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 10px 0;
        gap: 20px;
    }

    /* Buttons (Play, Repeat, etc.) */
    .audio-control {
        background-color: rgba(255, 255, 255, 0.1);
        border: none;
        color: #fff;
        padding: 15px;
        border-radius: 50%;
        font-size: 24px;
        cursor: pointer;
        transition: background 0.3s ease, transform 0.3s ease;
    }

    .audio-control:hover {
        background-color: rgba(255, 255, 255, 0.3);
        transform: scale(1.1);
    }

    .audio-control:active {
        background-color: rgba(255, 255, 255, 0.5);
    }


    /* Play Button */
    .play-button {
        background-color: rgba(88, 191, 225, 0.8);
        border: none;
        padding: 15px;
        border-radius: 50%;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }

    .play-button:hover {
        background-color: rgba(88, 191, 225, 1);
        transform: scale(1.1);
    }

    /* Repeat Button */
    .repeat-button {
        background-color: rgba(255, 165, 0, 0.8);
        border: none;
        padding: 12px;
        border-radius: 50%;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }

    .repeat-button:hover {
        background-color: rgba(255, 165, 0, 1);
        transform: scale(1.1);
    }

    .repeat-button.active {
        background-color: #FFD700;
        /* Gold color when active */
    }

    /* Volume Control */
    .volume-container {
        display: flex;
        align-items: center;
        position: relative;
    }

    .volume-slider {
        -webkit-appearance: none;
        width: 100px;
        height: 5px;
        background: #ddd;
        border-radius: 5px;
        outline: none;
        transition: background 0.3s ease;
    }

    .volume-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 15px;
        height: 15px;
        border-radius: 50%;
        background: #58BFE1;
        cursor: pointer;
    }

    .volume-slider::-moz-range-thumb {
        width: 15px;
        height: 15px;
        border-radius: 50%;
        background: #58BFE1;
        cursor: pointer;
    }


    /* Waveform styling */
    .waveform {
        width: 50%;
        height: 80px;
        background-color: transparent;
    }

    /* Time indicator */
    .time-indicator {
        color: #fff;
        font-size: 16px;
        margin-left: 10px;
    }

    /* Thumbnail styling (taking 20% width) */
    .audio-thumbnail {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 10%;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    /* Responsive styling */
    @media (max-width: 768px) {
        .audio-player-container {
            padding: 15px;
            flex-direction: column;
            align-items: flex-start;
        }

        .audio-controls-container {
            width: 100%;
        }

        .audio-controls {
            flex-direction: row;
            gap: 15px;
        }

        .audio-thumbnail {
            display: none;
        }


    }
</style>

@push('script')
    <script src="https://unpkg.com/wavesurfer.js@7.7.15/dist/wavesurfer.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const playPauseButton = document.getElementById('play-pause');
            const volumeSlider = document.getElementById('v-slider');
            const repeatButton = document.getElementById('repeat-btn');
            let isRepeat = false; // Track repeat mode

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

            // Volume slider control


            // Volume Slider Control
            volumeSlider.addEventListener("input", (event) => {
                event.stopPropagation();
                const volume = event.target.value;
                wavesurfer.setVolume(volume); // Set the volume in the WaveSurfer instance
            });

            // Toggle repeat functionality
            repeatButton.addEventListener('click', function() {
                isRepeat = !isRepeat; // Toggle repeat mode
                repeatButton.classList.toggle('active', isRepeat);
            });

            // When the audio finishes, check if repeat mode is active
            wavesurfer.on('finish', function() {
                if (isRepeat) {
                    wavesurfer.play(); // Repeat the audio
                }
            });

            // Update time indicator
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




        // document.addEventListener('DOMContentLoaded', function() {
        //     const playPauseButton = document.getElementById('play-pause');
        //     const volumeSlider = document.getElementById('v-slider');
        //     // const volumeUpButton = document.getElementById('volume-up');
        //     // const volumeDownButton = document.getElementById('volume-down');
        //     //  const audioControlButtons = document.querySelectorAll('.audio-control');
        //     const repeatButton = document.getElementById('repeat-btn');
        //     let isRepeat = false; // To track the repeat mode
        //     const wavesurfer = WaveSurfer.create({
        //         container: '#waveform',
        //         waveColor: 'blue',
        //         progressColor: 'purple',
        //         barWidth: 2,
        //         responsive: true,
        //         normalize: true,
        //         interact: true,
        //         height: 100,
        //         barGap: 3,
        //         partialRender: true
        //     });

        //     wavesurfer.load('{{ $audios[0]->content }}');
        //     wavesurfer.play();

        //     playPauseButton.addEventListener('click', function(event) {
        //         event.stopPropagation();
        //         if (wavesurfer.isPlaying()) {
        //             wavesurfer.pause();
        //         } else {
        //             wavesurfer.play();
        //         }
        //     });

        //     document.addEventListener('keydown', function(event) {
        //         if (event.code === 'Space') {
        //             event.preventDefault();
        //             if (wavesurfer.isPlaying()) {
        //                 wavesurfer.pause();
        //             } else {
        //                 wavesurfer.play();
        //             }
        //         }
        //     });

        //     // volumeUpButton.addEventListener('click', function(event) {
        //     //     event.stopPropagation();
        //     //     let currentVolume = wavesurfer.getVolume();
        //     //     wavesurfer.setVolume(Math.min(currentVolume + 0.1, 1));
        //     // });

        //     // volumeDownButton.addEventListener('click', function(event) {
        //     //     event.stopPropagation();
        //     //     let currentVolume = wavesurfer.getVolume();
        //     //     wavesurfer.setVolume(Math.max(currentVolume - 0.1, 0));
        //     // });

        //     // audioControlButtons.forEach(button => {
        //     //     button.addEventListener('click', function(event) {
        //     //         event.stopPropagation();
        //     //     });
        //     // });

        //     // Volume Slider Control
        //     volumeSlider.addEventListener("input", (event) => {
        //         event.stopPropagation();
        //         const volume = event.target.value;
        //         wavesurfer.setVolume(volume); // Set the volume in the WaveSurfer instance
        //     });

        //     // Toggle repeat functionality
        //     repeatButton.addEventListener('click', function() {
        //         isRepeat = !isRepeat; // Toggle repeat mode
        //         if (isRepeat) {
        //             repeatButton.classList.add('active'); // Highlight the button
        //         } else {
        //             repeatButton.classList.remove('active');
        //         }
        //     });

        //     // When the audio finishes, check if repeat mode is active
        //     wavesurfer.on('finish', function() {

        //         if (isRepeat) {
        //             wavesurfer.play(); // Repeat the audio
        //         }
        //     });

        //     // Update time indicator
        //     wavesurfer.on('audioprocess', function() {
        //         const currentTime = wavesurfer.getCurrentTime();
        //         document.getElementById('time-indicator').innerText = formatTime(currentTime);
        //     });

        //     function formatTime(seconds) {
        //         const minutes = Math.floor(seconds / 60);
        //         const remainingSeconds = Math.floor(seconds % 60);
        //         return `${padZero(minutes)}:${padZero(remainingSeconds)}`;
        //     }

        //     function padZero(number) {
        //         return number < 10 ? `0${number}` : number;
        //     }
        // });
    </script>
@endpush

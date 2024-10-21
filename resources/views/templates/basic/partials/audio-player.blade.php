<section class="audio-details-section section--bg ptb-80">
    <div class="container">
        <div class="col-xl-8 col-lg-8 mb-30">
            <div id="audio-player" class="audio-player-container">
                <audio id="audio" src="{{ $audios[0]->content }}" controls style="display:none;"></audio>

                <div id="audio-controls-container" class="audio-controls-container">
                    <div id="file-title" class="audio-title">{{ __($seoContents['social_title']) }}</div>
                    <div id="audio-controls" class="audio-controls">
                        <!-- Play/Pause Button -->
                        <button class="audio-control play-btn" id="play-pause">
                            <i class="fas fa-play" style="scale: 120%"></i>
                        </button>

                        <!-- Repeat Button -->
                        <button class="audio-control repeat-btn" id="repeat-btn" style="scale: 70%">
                            <i class="fas fa-redo" style="scale: 120%"></i>
                        </button>

                        <div id="time-indicator" class="time-indicator"></div>

                        <!-- Waveform display -->
                        <div id="waveform" class="waveform" style="width: 70%"></div>

                        <!-- Time Indicator -->
                        <div id="total-time" class="time-indicator"></div>

                        <!-- Volume Control -->
                        <button class="audio-control play-btn" id="v-mute" style="display: none;" style="scale: 70%">
                            <i class="fas fa-volume-mute" style="scale: 120%"></i>
                        </button>
                        <button class="audio-control play-btn" id="v-up" style="scale: 70%">
                            <i class="fas fa-volume-up" style="scale: 120%"></i>
                        </button>
                    </div>
                </div>

                <!-- Thumbnail image (occupying 20% width) -->
                <img src="{{ getImage(getFilePath('item_portrait') . '/' . $item->image->portrait) }}" id="thumbnail"
                    class="audio-thumbnail" />
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
                            {{ __($seoContents['social_description']) }}
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

    </div>
</section>

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
        width: 100%;
        padding: 5px 0;
        gap: 5px;
    }

    /* Buttons (Play, Repeat, etc.) */
    .audio-control {
        background-color: rgba(255, 255, 255, 0.1);
        border: none;
        color: #ffffff;
        padding: 15px;
        border-radius: 50%;
        /* Keeps the buttons circular */
        font-size: 24px;
        cursor: pointer;
        transition: background 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .audio-control:hover {
        background-color: rgba(255, 255, 255, 0.3);
    }

    /* Prevent distortion on hover */
    .audio-control i {
        transform: scale(1);
        transition: transform 0.3s ease;
    }

    .audio-control:hover i {
        transform: scale(1.1);
        /* Only scale the icon, not the button */
    }

    /* Thumbnail styling */
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
            const repeatButton = document.getElementById('repeat-btn');
            const totalTime = document.getElementById('total-time');
            const muteButton = document.getElementById('v-mute');
            const volumeUpButton = document.getElementById('v-up');
            let isRepeat = false;

            const wavesurfer = WaveSurfer.create({
                container: '#waveform',
                waveColor: 'white',
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

            wavesurfer.on('ready', function() {
                const duration = wavesurfer.getDuration();
                totalTime.innerText = formatTime(duration); // Update total time when the audio is ready
            });

            playPauseButton.addEventListener('click', function(event) {
                event.stopPropagation();
                if (wavesurfer.isPlaying()) {
                    wavesurfer.pause();
                    playPauseButton.innerHTML = '<i class="fas fa-play"></i>';
                } else {
                    wavesurfer.play();
                    playPauseButton.innerHTML = '<i class="fas fa-pause"></i>';
                }
            });

            volumeUpButton.addEventListener("click", (event) => {
                event.stopPropagation();
                volumeUpButton.style.display = "none"; // Hide volume up button
                muteButton.style.display = "block"; // Show mute button
                muteButton.style.scale = "70%"; // Show mute button
                wavesurfer.setVolume(0); // Mute audio
            });

            muteButton.addEventListener("click", (event) => {
                event.stopPropagation();
                volumeUpButton.style.display = "block"; // Show volume up button
                volumeUpButton.style.scale = "70%"; // Show volume up button
                muteButton.style.display = "none"; // Hide mute button
                wavesurfer.setVolume(1); // Set volume to full
            });

            repeatButton.addEventListener('click', function() {
                isRepeat = !isRepeat;

                if (isRepeat) {
                    repeatButton.style.backgroundColor =
                        "#f3c56f"; // Set background color when repeat is active
                } else {
                    repeatButton.style.backgroundColor =
                        "transparent"; // Reset background color when repeat is inactive
                }

                repeatButton.classList.toggle('active', isRepeat);
            });


            wavesurfer.on('finish', function() {
                if (isRepeat) {
                    wavesurfer.play();
                } else {
                    // Call the function to play the next item
                    playNextItemAudio();
                }
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

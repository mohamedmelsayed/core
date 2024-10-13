@extends($activeTemplate . 'layouts.frontend')

@section('content')
    <section class="playlist-section section--bg section pb-80">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="section-header">
                        <h2 class="section-title">{{ $playlist->title }}</h2>
                        <p>{{ $playlist->description }}</p> <!-- Display playlist description -->
                    </div>
                </div>
            </div>

            <!-- Display the video/audio player -->
            <div class="row">
                <div class="col-xl-8 col-lg-8 mb-30">
                    @if ($item->is_audio)
                        <!-- Audio Player Widget -->
                        <div id="audio-player" class="audio-player-container">
                            <audio id="audio" src="{{ $item->audio->content }}" controls autoplay></audio>

                            <div id="audio-controls-container" class="audio-controls-container">
                                <div id="file-title" class="audio-title">{{ $item->title }}</div>
                                <div id="audio-controls" class="audio-controls">
                                    <!-- Play/Pause Button -->
                                    <button class="audio-control play-btn" id="play-pause">
                                        <i class="fas fa-play"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- Thumbnail image -->
                            <img src="{{ getImage(getFilePath('item_portrait') . '/' . $item->image->portrait) }}"
                                id="thumbnail" class="audio-thumbnail" />
                        </div>
                    @else
                        <!-- Video Player Widget -->
                        <div class="movie-item">
                            <video class="video-player plyr-video" playsinline controls autoplay
                                data-poster="{{ getImage(getFilePath('item_landscape') . '/' . $item->image->landscape) }}">
                                <source src="{{ $item->video->content }}" type="video/mp4"
                                    size="{{ $item->video->size }}" />
                            </video>
                        </div>
                    @endif
                </div>

                <!-- Display playlist items on the right -->
                <div class="col-xl-4 col-lg-4 mb-30">
                    <div class="playlist-items">
                        <h5>@lang('Playlist Items')</h5>
                        <ul class="list-group">
                            @foreach ($playlistItems as $playlistItem)
                                <li class="list-group-item">

                                    <a
                                        href="{{ route('playlist.item.play', ['playlist' => $playlist->id, 'itemSlug' => $playlistItem->slug]) }}">
                                        {{ $playlistItem->title }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection



@if ($playlist->type == 'audio')
    @push('style')
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
                /* justify-content: space-between; */
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
                font-size: 24px;
                cursor: pointer;
                transition: background 0.3s ease, transform 0.3s ease;
            }

            .audio-control:hover {
                background-color: rgba(255, 255, 255, 0.3);
                transform: scale(1.1);
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
    @endpush

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



                wavesurfer.load('{{ $item->audio->content }}');
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
@endif

@if ($playlist->type == 'video')
    @push('style-lib')
        <link rel="stylesheet" href="{{ asset('assets/global/css/plyr.min.css') }}">
    @endpush

    @push('script-lib')
        <script src="{{ asset('assets/global/js/plyr.min.js') }}"></script>
        <script src="{{ asset('assets/global/js/hls.min.js') }}"></script>
    @endpush

    @push('style')
        <style>
            .main-video:has(.main-video-lock) {
                position: relative;
            }

            .main-video-lock {
                position: absolute;
                height: 100%;
                width: 100%;
                top: 0;
                left: 0;
                background-color: rgba(0, 0, 0, 0.555);
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .main-video-lock-content {
                padding: 20px;
                background: rgb(0 0 0 / 70%);
                border-radius: 4px;
                width: 100%;
                height: 100%;
                cursor: pointer;
                display: grid;
                place-content: center;
            }

            .main-video-lock-content .title {
                text-align: center;
                color: #fff;
                font-size: 14px;
            }

            .main-video-lock-content .icon {
                font-size: 56px;
                display: block;
                text-align: center;
                line-height: 1;
                color: #ee005f;
            }

            .main-video-lock-content .price {
                font-size: 36px;
                display: block;
                text-align: center;
                color: white;
                background: rgb(238 0 5 / 5%);
                margin-top: 10px;
                border-radius: inherit;
                line-height: 1;
                padding: 7px 0;
            }

            .main-video-lock-content .price .price-amount {
                color: #ee005f;
                font-weight: 700;
                letter-spacing: -2;
            }

            .main-video-lock-content .price .small-text {
                font-size: 14px;
            }

            .main-video-lock-content .price span {
                line-height: 1;
            }

            .watch-party-modal .modal-dialog {
                max-width: 500px;
            }
        </style>
    @endpush

    @push('script')
        <script>
            $(document).ready(function() {
                $(document).find('.plyr__controls').addClass('d-none');
                $(document).find('.ad-video').find('.plyr__controls').addClass('d-none');
            });

            (function($) {
                "use strict";

                let rent = "{{ Status::RENT_VERSION }}";

                $('.main-video-lock').on('click', function(e) {
                    var modal = $('#rentModal');
                    modal.modal('show');
                });

                const controls = [
                    'play-large',
                    'rewind',
                    'play',
                    'fast-forward',
                    'progress',
                    'mute',
                    'settings',
                    'pip',
                    'airplay',
                    'fullscreen'
                ];

                let player = new Plyr('.video-player', {
                    controls,
                    ratio: '16:9'
                });


                var data = [
                    @foreach ($videos as $video)
                        {
                            src: "{{ $video->content }}",
                            type: 'video/mp4',
                            size: "{{ $video->size }}",
                        },
                    @endforeach
                ];


                player.on('qualitychange', event => {
                    $.each(data, function() {
                        initData();
                    })
                });

                player.on('play', () => {
                    let watchEligable = "{{ @$watchEligable }}";
                    if (!Number(watchEligable)) {
                        var modal = $('#alertModal');
                        modal.modal('show');
                        player.pause();
                        return false;
                    }
                    $(document).find('.plyr__controls').removeClass('d-none');
                });

                const skipButton = $('#skip-button');

                const adItems = [
                    @foreach ($adsTime as $key => $ads)
                        {
                            timing: "{{ $key }}",
                            source: "{{ $ads }}"
                        },
                    @endforeach
                ];

                const adPlayer = new Plyr('.ad-player', {
                    clickToPlay: false,
                    ratio: '16:9'
                });

                let firstAd = false;
                const result = adItems.filter((obj) => {
                    if (obj.timing == 0) {
                        firstAd = true;
                        return obj;
                    }
                });

                if (firstAd) {
                    adPlayer.source = {
                        type: 'video',
                        sources: [{
                            src: $('.ad-links').children('source:first').attr('src'),
                            type: 'video/mp4'
                        }],
                    };
                    player.pause();
                    $('.main-video').addClass('d-none');
                    $('.ad-video').removeClass('d-none');
                    $(document).find('.ad-video').find('.plyr__controls').hide();
                    adPlayer.play();
                }

                let skipTime = Number("{{ $general->skip_time }}");

                player.on('timeupdate', function() {
                    const currentTime = Math.floor(player.currentTime);
                    for (let i = 0; i < adItems.length; i++) {
                        const adItem = adItems[i];

                        if (currentTime >= adItem.timing && !adItem.played) {
                            skipButton.addClass('d-none');
                            adPlayer.source = {
                                type: 'video',
                                sources: [{
                                    src: $('.ad-links').children('source').eq(i).attr('src'),
                                    type: 'video/mp4'
                                }],
                                poster: "{{ getImage(getFilePath('item_landscape') . '/' . $item->image->landscape) }}",
                            };
                            player.pause();
                            $('.main-video').addClass('d-none');
                            $('.ad-video').removeClass('d-none');
                            $(document).find('.ad-video').find('.plyr__controls').hide();
                            adPlayer.play();
                            adPlayer.on('play', () => {
                                $('.advertise-text').removeClass('d-none');
                            })
                            adPlayer.on('timeupdate', () => {
                                const currentTime = Math.floor(adPlayer.currentTime);
                                const duration = Math.floor(adPlayer.duration);
                                if (!isNaN(currentTime) && !isNaN(duration)) {
                                    const remainingTime = duration - currentTime;
                                    const formattedTime = formatTime(remainingTime);
                                    $('.remains-ads-time').text(formattedTime);
                                }
                                if (adPlayer.currentTime >= skipTime) {
                                    skipButton.removeClass('d-none');
                                }
                            });
                            adItem.played = true;
                            break;
                        }
                    }
                });

                function formatTime(timeInSeconds) {
                    const date = new Date(null);
                    date.setSeconds(timeInSeconds);
                    return date.toISOString().substr(11, 8);
                }

                adPlayer.on('ended', () => {
                    player.play();
                    $('.ad-video').addClass('d-none');
                    $('.main-video').removeClass('d-none');
                    $('.advertise-text').addClass('d-none');
                });

                skipButton.on('click', function() {
                    adPlayer.pause();
                    $('.ad-video').addClass('d-none');
                    $('.main-video').removeClass('d-none');
                    player.play();
                    skipButton.addClass('d-none');
                    $('.advertise-text').addClass('d-none');
                });


                $('.watchPartyBtn').on('click', function(e) {
                    let modal = $("#watchPartyModal");
                    modal.modal('show')
                });

                $('.copy-code').on('click', function() {
                    var copyText = $('.party-code');
                    copyText = copyText[0];
                    copyText.select();
                    copyText.setSelectionRange(0, 99999);
                    document.execCommand("copy");
                    copyText.blur();
                });

                $('.startPartyBtn').on('click', function(e) {
                    let processBtn =
                        `<span class="processing">@lang('Processing') <i class="las la-spinner"></i> </span>`;
                    let startBtn = `@lang('Now Start Your Party') <i class="las la-long-arrow-alt-right"></i>`;
                    $.ajax({
                        type: "POST",
                        url: `{{ route('user.watch.party.create') }}`,
                        data: {
                            _token: "{{ csrf_token() }}",
                            item_id: "{{ @$item->id }}",
                            episode_id: "{{ @$episodeId }}"
                        },
                        beforeSend: function() {
                            $('.startPartyBtn').html('');
                            $('.startPartyBtn').html(processBtn);
                            $('.startPartyBtn').prop('disabled', true);
                        },
                        success: function(response) {
                            if (response.error) {
                                notify('error', response.error)
                                $('.startPartyBtn').html('');
                                $('.startPartyBtn').html(startBtn);
                                $('.startPartyBtn').prop('disabled', false);

                                return;
                            }
                            setTimeout(() => {
                                window.location.href = response.redirect_url
                            }, 3000);
                        }
                    });
                });


                function initData() {
                    const video = document.querySelector('video');
                    $.each(data, function() {
                        if (!Hls.isSupported()) {
                            video.src = this.src;
                        } else {
                            if (isM3U8(this.src)) {
                                const hls = new Hls();
                                hls.loadSource(this.src);
                                hls.attachMedia(video);
                                window.hls = hls;
                            }
                        }
                        window.player = player;
                    })
                }

                initData();

                function isM3U8(url) {
                    return /\.m3u8$/.test(url);
                }
            })(jQuery);
        </script>
    @endpush
    @push('context')
        oncontextmenu="return false"
    @endpush
@endif

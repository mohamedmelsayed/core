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
                    @if ($item)
                        @if ($item->is_audio)
                            <!-- Audio Player Widget -->
                            @if ($item->audio)
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
                                <!-- Fallback message for missing audio content -->
                                <p>@lang('Audio content is not available for this item.')</p>
                            @endif
                        @else
                            <!-- Video Player Widget -->
                            @if ($item->video)
                                <div class="movie-item">
                                    <video class="video-player plyr-video" playsinline controls autoplay
                                        data-poster="{{ getImage(getFilePath('item_landscape') . '/' . $item->image->landscape) }}">
                                        <source src="{{ $item->video->content }}" type="video/mp4" />
                                    </video>
                                </div>
                            @else
                                <!-- Fallback message for missing video content -->
                                <p>@lang('Video content is not available for this item.')</p>
                            @endif
                        @endif
                    @else
                        <!-- Fallback message if no item is selected -->
                        <p>@lang('No media available in this playlist to play.')</p>
                    @endif
                </div>

                <!-- Display playlist items on the right -->
                <div class="col-xl-4 col-lg-4 mb-30">
                    <div class="playlist-items">
                        <h5>@lang('Playlist Items')</h5>
                        <ul class="list-group">
                            @foreach ($playlistItems as $playlistItem)
                                <li class="list-group-item">
                                    <a href="{{ route('playlist.item.play', ['playlist' => $playlist->id, 'itemSlug' => $playlistItem->slug]) }}">
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
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .audio-title {
                color: #fff;
                font-size: 24px;
                font-weight: 600;
                text-align: center;
                margin-bottom: 20px;
            }

            .audio-controls-container {
                width: 80%;
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .audio-controls {
                display: flex;
                align-items: center;
                width: 100%;
                padding: 5px 0;
                gap: 5px;
            }

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

            .audio-thumbnail {
                width: 150px;
                height: 150px;
                object-fit: cover;
                border-radius: 10%;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            }

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

                wavesurfer.load('{{ $item->audio->content ?? '' }}');
                wavesurfer.play();

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
    @endpush

    @push('script')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const player = new Plyr('.video-player', {
                    controls: ['play-large', 'play', 'progress', 'current-time', 'mute', 'volume', 'fullscreen'],
                    autoplay: true,
                    seekTime: 10
                });
            });
        </script>
    @endpush
@endif

@extends($activeTemplate . 'layouts.frontend')

@section('content')
    <section class="playlist-section section--bg section pb-80">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="section-header">
                        <h2 class="section-title">{{ app()->getLocale() == 'ar' ? $playlist->title : $playlist->title_en }}</h2>
                        <p>{{ app()->getLocale() == 'ar' ? $playlist->description : $playlist->description_en }}</p>
                        <!-- Display playlist description -->
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
                                <!-- Include Audio Player Partial -->
                                @include($activeTemplate . 'partials.audio-player', [
                                    'item' => $item,
                                    'subtitles' => $subtitles,
                                    'adsTime' => $adsTime,
                                    'watchEligable' => $watchEligable,
                                ])
                            @else
                                <!-- Fallback message for missing audio content -->
                                <p>@lang('Audio content is not available for this item.')</p>
                            @endif
                        @else
                            <!-- Video Player Widget -->
                            @if ($item->video)
                                <!-- Include Video Player Partial -->
                                @include($activeTemplate . 'partials.video-player', [
                                    'item' => $item,
                                    'subtitles' => $subtitles,
                                    'adsTime' => $adsTime,
                                    'watchEligable' => $watchEligable,
                                ])
                            @else
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
                        <ul class="list-group" id="playlist-items-list">
                            @foreach ($playlistItems as $index => $playlistItem)
                                @php
                                    $translation = $playlistItem->translations
                                        ->where('language', app()->getLocale())
                                        ->first();
                                    $title = $translation ? $translation->translated_title : $playlistItem->title;
                                @endphp
                                <a href="{{ route('playlist.item.play', ['playlist' => $playlist->id, 'itemSlug' => $playlistItem->slug]) }}"
                                    data-item-index="{{ $index }}" class="playlist-item">
                                    <li class="list-group-item d-flex align-items-center">

                                        <!-- Portrait image -->
                                        <img src="{{ getImage(getFilePath('item_portrait') . '/' . $playlistItem->image->portrait) }}"
                                            alt="{{ $title }}" class="playlist-item-image" />

                                        <!-- Title with dynamic language based on translations relation -->
                                        <span class="playlist-item-link">
                                            {{ $title }}
                                        </span>
                                    </li>
                                </a>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
    </section>
@endsection

@push('style')
    <style>
        /* Playlist container */
        .playlist-items {
            background-color: #1e1e2d;
            /* Matching the dark background */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        /* List group item */
        .list-group-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            background-color: rgba(255, 255, 255, 0.05);
            transition: background-color 0.3s ease, transform 0.3s ease;
            margin-bottom: 10px;
            /* Space between items */
        }

        .list-group-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: scale(1.02);
            /* Slight scale on hover */
            color: #fff;
        }

        /* Playlist item portrait image */
        .playlist-item-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 15px;
            /* Space between image and text */
        }

        /* Playlist item link */
        .playlist-item-link {
            font-weight: bold;
            color: #fff;
            transition: color 0.3s ease;
            flex-grow: 1;
            /* Makes the link take remaining space */
            font-size: 16px;
        }

        .playlist-item-link:hover {
            color: #ee005f;
            /* Color change on hover */
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .playlist-item-image {
                width: 50px;
                height: 50px;
            }

            .playlist-item-link {
                font-size: 14px;
            }

            .playlist-items {
                padding: 15px;
            }
        }

        @media (max-width: 576px) {
            .playlist-item-link {
                font-size: 12px;
            }

            .playlist-item-image {
                width: 40px;
                height: 40px;
            }
        }
    </style>
@endpush



@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Helper function to get the media element
            function getMediaElement() {
                return document.querySelector('#waveform') || document.querySelector('video');
            }

            // Helper function to handle when media finishes playing
            function handleMediaEnd() {
                console.log("Media ended, playing next item");
                playNextItem();
            }

            // Attach event listener for audio or video media element
            function attachMediaEndListener() {
                const mediaElement = getMediaElement();

                if (!mediaElement) {
                    console.error("Media element not found");
                    return;
                }

                if (mediaElement.dataset.wavesurfer) {
                    // For WaveSurfer (audio)
                    const wavesurfer = mediaElement.dataset.wavesurfer;
                    wavesurfer.on('finish', handleMediaEnd);
                } else {
                    // For video
                    mediaElement.addEventListener('ended', handleMediaEnd);
                }
            }

            // Function to set the active item in the playlist
            function setActivePlaylistItem(itemIndex) {
                // Remove 'active' class from all playlist items
                const playlistItems = document.querySelectorAll('.playlist-item');
                playlistItems.forEach(item => item.classList.remove('active'));

                // Add 'active' class to the current item
                const currentItem = document.querySelector(`.playlist-item[data-item-index="${itemIndex}"]`);
                if (currentItem) {
                    currentItem.classList.add('active');
                }
            }

            // Function to autoplay the next item in the playlist
            function playNextItem() {
                const currentItem = document.querySelector('.playlist-item.active');

                if (!currentItem) {
                    console.error("No active playlist item found.");
                    return;
                }

                const nextItem = currentItem.nextElementSibling;

                if (nextItem) {
                    console.log(nextItem);

                    const nextItemLink = nextItem.querySelector('a');

                    if (nextItemLink) {
                        // Set the next item as active
                        const nextItemIndex = nextItemLink.getAttribute('data-item-index');
                        setActivePlaylistItem(nextItemIndex);

                        // Redirect to the next item's play route
                        window.location.href = nextItemLink.getAttribute('href');
                    } else {
                        console.error("No link found for next playlist item.");
                    }
                } else {
                    alert('End of playlist');
                }
            }

            // Set the first item as active when the page loads
            setActivePlaylistItem(0);

            // Initialize when DOM is ready
            attachMediaEndListener();
        });

        function playNextItem() {
            // Find the current playing item from the playlist
            const currentItem = document.querySelector('.playlist-item.active');
            if (currentItem) {
                // Find the next item in the playlist
                const nextItem = currentItem.nextElementSibling;
                if (nextItem) {
                    // Redirect to the next item's play route
                    const nextItemLink = nextItem.querySelector('a');
                    if (nextItemLink) {
                        window.location.href = nextItemLink.getAttribute('href');
                    }
                } else {
                    alert('End of playlist');
                }
            }
        }
    </script>
@endpush

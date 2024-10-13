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
                <div class="col-xl-8 col-lg-8 mb-30" id="player-container">
                    @if ($item)
                        @if ($item->is_audio)
                            <!-- Audio Player Widget -->
                            @if ($item->audio)
                                <!-- Include Audio Player Partial -->
                                @include($activeTemplate . 'partials.audio-player', ['item' => $item])
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
                        <ul class="list-group">
                            @foreach ($playlistItems as $playlistItem)
                                @php
                                    $translation = $playlistItem->translations
                                        ->where('language', app()->getLocale())
                                        ->first();
                                    $title = $translation ? $translation->translated_title : $playlistItem->title;
                                @endphp
                                <li class="list-group-item d-flex align-items-center">
                                    <img src="{{ getImage(getFilePath('item_portrait') . '/' . $playlistItem->image->portrait) }}"
                                        alt="{{ $title }}" class="playlist-item-image me-3" />

                                    <!-- AJAX click event for playlist item -->
                                    <a href="javascript:void(0);" class="playlist-item-link"
                                        data-playlist="{{ $playlist->id }}" data-item="{{ $playlistItem->slug }}">
                                        {{ $title }}
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

@push('style')
    <style>
        /* Playlist container */
        .playlist-items {
            background-color: #f7f7f7;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* List group item */
        .list-group-item {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            border: none;
            background-color: transparent;
            transition: background 0.3s ease;
            border-bottom: 1px solid #ececec;
        }

        .list-group-item:last-child {
            border-bottom: none;
        }

        .list-group-item:hover {
            background-color: {{ $general->theme_color ?? '#ee005f' }};
            color: #fff;
        }

        /* Playlist item portrait image */
        .playlist-item-image {
            width: 50px;
            height: 50px;
            border-radius: 5px;
            object-fit: cover;
        }

        /* Playlist item link */
        .playlist-item-link {
            text-decoration: none;
            font-weight: bold;
            color: {{ $general->theme_color ?? '#333' }};
            transition: color 0.3s ease;
        }

        .playlist-item-link:hover {
            color: #fff;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .playlist-item-image {
                width: 40px;
                height: 40px;
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
        }
    </style>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            // Handle click on playlist item
            $('.playlist-item-link').on('click', function(e) {
                e.preventDefault();

                var playlistId = $(this).data('playlist');
                var itemSlug = $(this).data('item');

                $.ajax({
                    url: '/playlist/' + playlistId + '/item/' + itemSlug,
                    type: 'GET',
                    success: function(response) {
                        // Update the player container with the new content
                        $('#player-container').html(response.view);
                    },
                    error: function(error) {
                        console.error('Error fetching item content:', error);
                    }
                });
            });
        });
    </script>
@endpush

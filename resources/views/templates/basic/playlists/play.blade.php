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
                    <!-- This is the container that will be replaced dynamically -->
                    <div id="player-container">
                        @if ($item)
                            @if ($item->is_audio)
                                <!-- Audio Player Widget -->
                                @if ($item->audio)
                                    @include($activeTemplate . 'partials.audio-player', ['item' => $item])
                                @else
                                    <p>@lang('Audio content is not available for this item.')</p>
                                @endif
                            @else
                                <!-- Video Player Widget -->
                                @if ($item->video)
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
                            <p>@lang('No media available in this playlist to play.')</p>
                        @endif
                    </div>
                </div>

                <!-- Display playlist items on the right -->
                <div class="col-xl-4 col-lg-4 mb-30">
                    <div class="playlist-items">
                        <h5>@lang('Playlist Items')</h5>
                        <ul class="list-group">
                            @foreach ($playlistItems as $playlistItem)
                                @php
                                    $translation = $playlistItem->translations->where('language', app()->getLocale())->first();
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

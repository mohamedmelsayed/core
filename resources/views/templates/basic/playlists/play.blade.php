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
                                <!-- Include Audio Player Partial -->
                                @include($activeTemplate .'partials.audio-player', ['item' => $item])
                            @else
                                <!-- Fallback message for missing audio content -->
                                <p>@lang('Audio content is not available for this item.')</p>
                            @endif
                        @else
                            <!-- Video Player Widget -->
                            @if ($item->video)
                                <!-- Include Video Player Partial -->
                                @include($activeTemplate .'partials.video-player', ['item' => $item, 'subtitles' => $subtitles, 'adsTime' => $adsTime, 'watchEligable' => $watchEligable])
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

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

@if ($item->is_audio && $item->audio)
    @include($activeTemplate . 'partials.audio-player', ['item' => $item,'seoContents'=>$seoContents])
@elseif ($item->video)
    @include($activeTemplate . 'partials.video-player', ['item' => $item, 'subtitles' => $subtitles, 'adsTime' => $adsTime, 'watchEligable' => $watchEligable,'seoContents'=>$seoContents])
@else
    <p>@lang('No media available in this playlist to play.')</p>
@endif

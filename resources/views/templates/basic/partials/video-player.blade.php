<div class="movie-item">
    <div class="main-video">
        @if (!empty($item->video))
            <video class="video-player plyr-video" playsinline controls autoplay
                data-poster="{{ getImage(getFilePath('item_landscape') . '/' . $item->image->landscape) }}">
                <source src="{{ $item->video->content ?? '' }}" type="video/mp4" size="{{ $item->video->size ?? '720' }}" />

                @foreach ($subtitles ?? [] as $subtitle)
                    <track kind="captions" label="{{ $subtitle->language }}"
                        src="{{ getImage(getFilePath('subtitle') . '/' . $subtitle->file) }}"
                        srclang="{{ $subtitle->code }}" />
                @endforeach
            </video>
        @else
            <p>@lang('Video content is not available for this item.')</p>
        @endif
    </div>

    @if ($item->version == Status::RENT_VERSION && !$watchEligable)
        <div class="main-video-lock">
            <div class="main-video-lock-content">
                <span class="icon"><i class="las la-lock"></i></span>
                <p class="title">@lang('Purchase Now')</p>
                <p class="price">
                    <span class="price-amount">{{ $general->cur_sym }}{{ showAmount($item->rent_price) }}</span>
                    <span class="small-text ms-3">@lang('For') {{ $item->rental_period }} @lang('Days')</span>
                </p>
            </div>
        </div>
    @endif
</div>

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/global/css/plyr.min.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/plyr.min.js') }}"></script>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            // Hide controls initially
            $(document).find('.plyr__controls').addClass('d-none');
        });

        (function($) {
            "use strict";

            // Initialize video player controls
            const controls = ['play-large', 'rewind', 'play', 'fast-forward', 'progress', 'mute', 'settings', 'pip', 'airplay', 'fullscreen'];

            let player = new Plyr('.video-player', {
                controls,
                ratio: '16:9'
            });

            // Ad player initialization
            const adPlayer = new Plyr('.ad-player', { clickToPlay: false, ratio: '16:9' });
            let data = [];

            // Add video sources to the data array
            if ($item && $item->video) {
                data.push({
                    src: "{{ $item->video->content ?? '' }}",
                    type: 'video/mp4',
                    size: "{{ $item->video->size ?? '720' }}",
                });
            }

            player.on('play', () => {
                let watchEligable = "{{ is_array($watchEligable) ? json_encode($watchEligable) : $watchEligable }}";
                if (!Number(watchEligable)) {
                    $('#alertModal').modal('show');
                    player.pause();
                    return false;
                }
                $(document).find('.plyr__controls').removeClass('d-none');
            });

            // Handle ads and skip button
            let skipButton = $('#skip-button');
            let adsTime = @json($adsTime ?? []);
            const adItems = adsTime.map((ads, key) => ({ timing: key, source: ads }));

            function loadAd(adItem, index) {
                adPlayer.source = {
                    type: 'video',
                    sources: [{ src: adItem.source, type: 'video/mp4' }]
                };
                player.pause();
                $('.main-video').addClass('d-none');
                $('.ad-video').removeClass('d-none');
                adPlayer.play();
            }

            player.on('timeupdate', function() {
                const currentTime = Math.floor(player.currentTime);
                adItems.forEach((adItem, i) => {
                    if (currentTime >= adItem.timing && !adItem.played) {
                        loadAd(adItem, i);
                        adItem.played = true;
                    }
                });
            });

            skipButton.on('click', function() {
                adPlayer.pause();
                $('.ad-video').addClass('d-none');
                $('.main-video').removeClass('d-none');
                player.play();
            });

            adPlayer.on('ended', () => {
                player.play();
                $('.ad-video').addClass('d-none');
                $('.main-video').removeClass('d-none');
            });
        })(jQuery);
    </script>
@endpush

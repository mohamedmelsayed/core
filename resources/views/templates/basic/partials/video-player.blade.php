<div class="movie-item">
    <div class="main-video">
        @if ($item->video)
            <video class="video-player plyr-video" playsinline controls autoplay
                data-poster="{{ getImage(getFilePath('item_landscape') . '/' . $item->image->landscape) }}">
                <source src="{{ $item->video->content }}" type="video/mp4" size="{{ $item->video->size }}" />
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


@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/global/css/plyr.min.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/plyr.min.js') }}"></script>
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

                data.push({
                    src: "{{ $item->video->content }}",
                    type: 'video/mp4',
                    size: "{{ $item->video->size }}",
                });

            ];


            player.on('qualitychange', event => {
                $.each(data, function() {
                    initData();
                })
            });

            player.on('play', () => {
                let watchEligable =
                    "{{ is_array($watchEligable) ? json_encode($watchEligable) : $watchEligable }}";

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

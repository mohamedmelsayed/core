@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="movie-details-section section--bg ptb-80">
        <div class="container">
            <div class="row justify-content-center mb-30-none">
                <div class="col-xl-8 col-lg-8 mb-30">
                <div class="audio-container  follow-scroll">
                <img width="100%" src="{{ getImage(getFilePath('episode') . '/' . @$firstVideoImg) }}" alt="movie">

     <div class="audio"></div>

     <div class="buttons">
       <span class="play-btn btn">
         <i class="fas fa-play"></i>
         <i class="fas fa-pause"></i>
       </span>

       <span class="stop-btn btn">
         <i class="fas fa-stop"></i>
       </span>

       <span class="mute-btn btn">
         <i class="fas fa-volume-up"></i>
         <i class="fas fa-volume-mute"></i>
       </span>

       
     </div>
   </div>

   <script src="https://unpkg.com/wavesurfer.js"></script>

                        </div>
                        <div class="movie-content">
                            <div class="movie-content-inner d-flex flex-wrap justify-content-between align-items-center">
                                <div class="movie-content-left">
                                    <h3 class="title">{{ __($item->title) }}</h3>
                                </div>
                                <div class="movie-content-right">
                                    <div class="movie-widget-area">
                                        <span class="movie-widget"><i class="lar la-star text--warning"></i>
                                            {{ getAmount($item->ratings) }}</span>
                                        <span class="movie-widget"><i class="lar la-eye text--danger"></i>
                                            {{ getAmount($item->view) }} @lang('views')</span>

                                        @php
                                            $wishlist = $activeEpisode->wishlists->where('user_id', auth()->id())->count();
                                        @endphp
                            
                                        <span class="movie-widget addWishlist {{ $wishlist ? 'd-none' : '' }}"><i class="las la-plus-circle"></i></span>
                                        <span class="movie-widget removeWishlist {{ $wishlist ? '' : 'd-none' }}"><i class="las la-minus-circle"></i></span>

                                    </div>
                                </div>
                            </div>
                            <div class="movie-widget-area">
                            </div>
                            <p>{{ __($item->preview_text) }}</p>
                        </div>
                    </div>
                    <div class="product-tab mt-40">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="product-tab-desc" data-bs-toggle="tab"
                                    href="#product-desc-content" role="tab" aria-controls="product-desc-content"
                                    aria-selected="true">@lang('Description')</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="product-tab-team" data-bs-toggle="tab"
                                    href="#product-team-content" role="tab" aria-controls="product-team-content"
                                    aria-selected="false">@lang('Team')</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="product-desc-content" role="tabpanel"
                                aria-labelledby="product-tab-desc">
                                <div class="product-desc-content">
                                    {{ __($item->description) }}
                                </div>
                            </div>
                            <div class="tab-pane fade fade" id="product-team-content" role="tabpanel"
                                aria-labelledby="product-tab-team">
                                <div class="product-desc-content">
                                    <ul class="team-list">
                                        <li><span>@lang('Director'):</span> {{ __($item->team->director) }}</li>
                                        <li><span>@lang('Producer'):</span> {{ __($item->team->producer) }}</li>
                                        <li><span>@lang('Cast'):</span> {{ __($item->team->casts) }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 mb-30">
                    <div class="widget-box">
                        <div class="widget-wrapper movie-small-list pt-0">
                            @forelse($episodes as $episode)
                                @php
                                    $videoSrc = getAudioFile($episode->audio);
                                @endphp
                                <div class="widget-item widget-item__overlay d-flex align-items-center justify-content-between"
                                    data-src="{{ $videoSrc }}"
                                    data-img="{{ getImage(getFilePath('episode') . '/' . $episode->image) }}">
                                   <div class="widget-item__content d-flex flex-wrap align-items-center movie-small">
                                        <div class="widget-thumb">
                                            
                                            <a href="{{ route('watch', [$item->id, $episode->id]) }}">
                                                <img src="{{ getImage(getFilePath('episode') . '/' . $episode->image) }}" alt="movie">
                                                @if ($episode->version == 0)
                                                    <span class="movie-badge">@lang('Free')</span>
                                                @endif
                                            </a>
                                        </div>
                                        <div class="widget-content">
                                            <h4 class="title">{{ __($episode->title) }}</h4>
                                            <div class="widget-btn">
                                                @if($episode->version == 0 || (auth()->check() && auth()->user()->exp > now()))
                                                    <a href="{{ route('watch', [$item->id, $episode->id]) }}" class="custom-btn">@lang('Play Now')</a>
                                                @else
                                                    <a href="{{ route('watch', [$item->id, $episode->id]) }}" class="custom-btn">@lang('Subscribe to watch')</a>
                                                @endif
                                            </div>
                                        </div>
                                   </div>
                                   <div class="widget-item__lock">
                                    <span class="widget-item__lock-icon"> 
                                        @if($episode->version == 0 || (auth()->check() && auth()->user()->exp > now()))
                                        <i class="fas fa-unlock"></i>
                                        @else
                                        <i class="fas fa-lock"></i>
                                        @endif
                                    </span>
                                   </div>
                                </div>
                            @empty
                            @endforelse

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="movie-section ptb-80">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="section-header">
                        <h2 class="section-title">@lang('Related Episode')</h2>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center mb-30-none">

                @forelse($relatedEpisodes as $related)
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-xs-6 mb-30">
                        <div class="movie-item">
                            <div class="movie-thumb">
                                <img src="{{ getImage(getFilePath('item_portrait') . '/' . $related->image->portrait) }}"
                                    alt="movie">
                                @if ($related->item_type == 1 && $related->version == 0)
                                    <span class="movie-badge">@lang('Free')</span>
                                @elseif($related->item_type == 3)
                                    <span class="movie-badge">@lang('Trailer')</span>
                                @endif
                                <div class="movie-thumb-overlay">
                                    <a class="video-icon" href="{{ route('watch', $related->id) }}"><i
                                            class="fas fa-play"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                @endforelse

            </div>
        </div>
    </section>

    <div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> @lang('Subscription Alert')!</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <strong>@lang('Please subscribe a plan to view our paid items')</strong>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('user.home') }}" class="btn btn--default btn-sm w-100">@lang("Subscribe Now")</a>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('style')
    @if(!$watch)
        <style>
            .video-js .vjs-big-play-button .vjs-icon-placeholder:before{
                content: "\f023";
            }

            
        </style>
    @else
            <style>
                .audio-container {
  width: 100%;
  box-shadow: 0 4px 8px -4px rgba(0, 0, 0, 0.3);
  position: relative;
  border-radius: 10px;
  overflow: hidden;
}

.audio-container .audio {
  background: #222;
  width: 100%;
  height: 130px;
}

.audio-container .track-name {
  top: 8px;
  left: 8px;
  color: white;
  background: rgba(0, 0, 0, 0.7);
  padding: 8px 32px;
  border-radius: 10px;
  font-size: 13px;
}

.audio-container .btn {
  padding: 16px;
  width: 24px;
  margin-right: 8px;
  cursor: pointer;
  color:white;
  display: inline-block;
}

.audio-container .volume-slider {
  width: 200px;
}

.audio-container .buttons .play-btn .fa-pause {
  display: none;
}

.audio-container .buttons .play-btn.playing .fa-pause {
  display: inline-block;
}

.audio-container .buttons .play-btn.play-btn.playing .fa-play {
  display: none;
}

.audio-container .buttons .mute-btn .fa-volume-mute {
  display: none;
}

.audio-container .buttons .mute-btn.muted .fa-volume-mute {
  display: inline-block;
}

.audio-container .buttons .mute-btn.muted .fa-volume-up {
  display: none;
}


                </style>
    @endif
@endpush

@push('script')
<script>
  var audioTrack = WaveSurfer.create({
  container: ".audio",
  waveColor: "#1e9ff2",
  progressColor: "#cd81ce",
  barWidth: 2,
});

audioTrack.load(`{{ $firstVideoFile }}`);


const playBtn = document.querySelector(".play-btn");
const stopBtn = document.querySelector(".stop-btn");
const muteBtn = document.querySelector(".mute-btn");
const volumeSlider = document.querySelector(".volume-slider");

playBtn.addEventListener("click", () => {
  audioTrack.playPause();

  if (audioTrack.isPlaying()) {
    playBtn.classList.add("playing");
  } else {
    playBtn.classList.remove("playing");
  }
});

stopBtn.addEventListener("click", () => {
  audioTrack.stop();
  playBtn.classList.remove("playing");
});



const changeVolume = (volume) => {
  if (volume == 0) {
    muteBtn.classList.add("muted");
  } else {
    muteBtn.classList.remove("muted");
  }

  audioTrack.setVolume(volume);
};

muteBtn.addEventListener("click", () => {
  if (muteBtn.classList.contains("muted")) {
    muteBtn.classList.remove("muted");
    audioTrack.setVolume(0.5);
    volumeSlider.value = 0.5;
  } else {
    audioTrack.setVolume(0);
    muteBtn.classList.add("muted");
    volumeSlider.value = 0;
  }
});

        'use strict';
        (function ($) {

                // $(".movie-small-list > .movie-small").each(function() {
            //     $(this).on('click', function() {
            //         var dataSrc = $(this).attr("data-src");
            //         var dataImg = $(this).attr("data-img");

            //         $('#my-video video').attr('src', dataSrc);
            //         $('.vjs-poster').css('background-image', 'url(' + dataImg + ')');

            //         // add active class with "list-btn"
            //         var element = $(this).parent("li");
            //         if (element.hasClass("active")) {
            //             element.find("li").removeClass("active");
            //         } else {
            //             element.addClass("active");
            //             element.siblings("li").removeClass("active");
            //             element.siblings("li").find("li").removeClass("active");
            //         }
            //     });
            // });

        $('.subscribe-alert').on('click', function(){
            var modal = $('#alertModal');

            modal.modal('show');
        });

        $('.addWishlist').on('click', function(){
            let episodeId = `{{ $activeEpisode->id }}`;
            console.log(episodeId)
            let url = `{{ route('wishlist.add') }}`;
            let csrf_token = `{{ csrf_token() }}`;

            $.ajax({
                type: "POST",
                url: url,
                data: { '_token': csrf_token, 'episode_id': episodeId },
                success: function (response) {
                    if(response.status == 'success'){
                        notify('success', response.message);
                        $('.addWishlist').addClass('d-none');
                        $('.removeWishlist').removeClass('d-none');
                    }else{
                        notify('error', response.message);
                    }
                }
            });
        });

        $('.removeWishlist').on('click', function(){
            let episodeId = `{{ $activeEpisode->id }}`;
            let url = `{{ route('wishlist.remove') }}`;
            let csrf_token = `{{ csrf_token() }}`;

            $.ajax({
                type: "POST",
                url: url,
                data: { '_token': csrf_token, 'episode_id': episodeId },
                success: function (response) {
                    console.log(response);
                    if(response.status == 'success'){
                        notify('success', response.message);
                        $('.addWishlist').removeClass('d-none');
                        $('.removeWishlist').addClass('d-none');
                    }else{
                        notify('error', response.message);
                    }
                }
            });
        });


            document.onkeydown = function(e) {
            if (e.keyCode == 123) {
                return false;
            }
            if (e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) {
                return false;
            }
            if (e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) {
                return false;
            }
            if (e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) {
                return false;
            }

            if (e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)) {
                return false;
            }
        }
        })(jQuery);
 </script>
@endpush

@push('context')
    oncontextmenu="return false"
@endpush

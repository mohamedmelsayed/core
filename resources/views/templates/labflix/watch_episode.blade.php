@extends($activeTemplate . 'layouts.frontend')
@section('content')
<div class="pt-80 pb-80">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="movie-single-video">
                    <video id="my-video" class="video-js" controls preload="auto" height="264" poster="{{ getImage(getFilePath('episode') . '/' . @$firstVideoImg) }}" data-setup="{}" controlsList="nodownload">
                        @if ($watch)
                        <source src="{{ $firstVideoFile }}" type="video/mp4" />
                        @endif
                    </video>
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
                </div>

                <div class="movie-details-content">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="season1" role="tabpanel" aria-labelledby="season1-tab">
                            <div class="d-flex flex-wrap">
                                <div class="card mb-sm-3 mt-3 col-12 p-0 order-sm-1 order-2">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <h4 class="mb-3">@lang('Description')</h4>
                                                <p>{{ __($item->description) }}</p>
                                            </div>
                                            <div class="col-lg-6 mt-lg-0 mt-4">
                                                <h4 class="mb-3">@lang('Team')</h4>
                                                <ul class="movie-details-list">
                                                    <li>
                                                        <span class="caption">@lang('Director:')</span>
                                                        <span class="value">{{ __($item->team->director) }}</span>
                                                    </li>
                                                    <li>
                                                        <span class="caption">@lang('Producer:')</span>
                                                        <span class="value">{{ __($item->team->producer) }}</span>
                                                    </li>
                                                    <li>
                                                        <span class="caption">@lang('Cast:')</span>
                                                        <span class="value">{{ __($item->team->casts) }}</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- card end -->

                                <div class="card col-12 p-0 order-sm-2 order-1">
                                    <div class="card-body p-0">
                                        <ul class="movie-small-list movie-list-scroll">
                                            @foreach($episodes as $episode)
                                            @php
                                            $className = '';
                                            $videoSrc = '';
                                            if ($episode->version == 1) {
                                            if (auth()->check() && auth()->user()->plan_id != 0 && auth()->user()->exp != null) {
                                            $className = 'video-item';
                                            $videoSrc = getVideoFile($episode->video);
                                            }
                                            }else{
                                            $className = 'video-item';
                                            $videoSrc = getVideoFile($episode->video);
                                            }
                                            @endphp

                                            <li class="movie-small d-flex align-items-center justify-content-between flex-wrap movie-item__overlay {{ $className }} @if($episode->version == 1) paid @endif" data-src="{{ $videoSrc }}" data-img="{{ getImage(getFilePath('episode') . '/' . $episode->image) }}" @if($episode->version == 0) data-text="Free" @endif >
                                                <div class="caojtyektj d-flex align-items-center flex-wrap">
                                                    <div class="movie-small__thumb">
                                                        <img src="{{ getImage(getFilePath('episode') . '/' . $episode->image) }}" alt="image">
                                                    </div>
                                                    <div class="movie-small__content">
                                                        <h5>{{ __($episode->title) }}</h5>
                                                        @if($episode->version == 0 || (auth()->check() && auth()->user()->exp > now()))
                                                        <a href="{{ route('watch', [$item->id, $episode->id]) }}" class="base--color">@lang('Play Now')</a>
                                                        @else
                                                        <a href="{{ route('user.home') }}" class="base--color">@lang('Subscribe to watch')</a>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="movie-small__lock">
                                                    <span class="movie-small__lock-icon">
                                                        @if($episode->version == 0 || (auth()->check() && auth()->user()->exp > now()))
                                                        <i class="fas fa-unlock"></i></span>
                                                        @else
                                                        <i class="fas fa-lock"></i></span>
                                                        @endif
                                                </div>
                                            </li>

                                            @endforeach
                                        </ul>
                                    </div>
                                </div><!-- card end -->
                            </div>
                        </div>
                    </div>
                </div><!-- movie-details-content end -->
            </div>
        </div>
    </div>
</div>

<section class="movie-section pb-80">
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
            <div class="col-xxl-3 col-md-3 col-4 col-xs-6 mb-30">
                <div class="movie-card @if($related->item_type == 1 && $related->version == 1 || $related->item_type == 2) paid @endif " @if($related->item_type == 1 && $related->version == 0) data-text="@lang('Free')" @elseif($related->item_type == 3) data-text="@lang('Trailer')" @endif>
                    <div class="movie-card__thumb thumb__2">
                        <img src="{{ getImage(getFilePath('item_portrait').'/'.$related->image->portrait) }}" alt="image">
                        <a href="{{ route('watch',$related->id) }}" class="icon"><i class="fas fa-play"></i></a>
                    </div>
                </div><!-- movie-card end -->
            </div>

            @empty
            @endforelse

        </div>
    </div>
</section>
@endsection

@push('style')
@if(!$watch)
<style>
    .video-js .vjs-big-play-button .vjs-icon-placeholder:before {
        content: "\f105";
    }
</style>
@endif
@endpush

@push('script')
<script>
    'use strict';
        (function ($) {

        $('.subscribe-alert').on('click', function(){
            var modal = $('#alertModal');
            modal.modal('show');
        });

        $('.addWishlist').on('click', function(){
            let episodeId = `{{ $activeEpisode->id }}`;
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

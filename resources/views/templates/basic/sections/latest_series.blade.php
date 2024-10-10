<section class="movie-section section--bg section pb-80" data-section="single2">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="section-header">
                    <h2 class="section-title">@lang('Latest Series')</h2>
                </div>
            </div>
        </div>
        <div class="row justify-content-center mb-30-none">

            @foreach ($latestSerieses as $latestSeries)
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-xs-6 mb-30">
                <div class="movie-item">
                    <div class="movie-thumb" style="position: relative; overflow: hidden; border-radius: 10px;">
                        <img class="lazy-loading-img" data-src="{{ getImage(getFilePath('item_portrait') . '/' . $latestSeries->image->portrait) }}" src="{{ asset('assets/global/images/lazy.png') }}" alt="movie" style="width: 100%; height: auto; border-radius: 10px;">

                        <!-- Version Badge (Free, Paid, Rent, etc.) -->
                        <span class="movie-badge" style="background-color: {{ $latestSeries->versionName == 'Free' ? '#28a745' : 'yellow' }}; color: {{ $latestSeries->versionName == 'Free' ? 'white' : 'black' }}; position: absolute; top: 10px; left: 10px; padding: 5px 10px; border-radius: 5px; font-size: 12px; font-weight: bold;">
                            {{ $latestSeries->versionName }}
                        </span>

                        <!-- Play button overlay -->
                        <div class="movie-thumb-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); opacity: 0; display: flex; align-items: center; justify-content: center; transition: opacity 0.3s ease-in-out;">
                            <a class="video-icon" href="{{ route('watch', $latestSeries->slug) }}" style="font-size: 30px; color: white;">
                                <i class="fas fa-play"></i>
                            </a>
                        </div>

                        <!-- Media Type Icon (Audio or Video) -->
                        <span class="media-type" style="position: absolute; top: 10px; right: 10px; background-color: #000; color: #fff; padding: 5px 10px; border-radius: 5px; font-size: 14px;">
                            @if ($latestSeries->is_audio)
                                <i class="fas fa-headphones"></i> <!-- Audio Icon -->
                            @else
                                <i class="fas fa-video"></i> <!-- Video Icon -->
                            @endif
                        </span>

                        <!-- Rental Period Badge (For Rent Items) -->
                        @if ($latestSeries->version == Status::RENT_VERSION)
                            <span class="rent-badge" style="position: absolute; bottom: 10px; left: 10px; background-color: #ff9800; color: white; padding: 5px 10px; border-radius: 5px; font-size: 12px; font-weight: bold;">
                                {{ __('For') }} {{ $latestSeries->rental_period }} {{ __('Days') }}
                            </span>
                        @endif

                        <!-- Trailer Badge (For Trailer Items) -->
                        @if ($latestSeries->is_trailer == Status::TRAILER)
                            <span class="trailer-badge" style="position: absolute; bottom: 10px; right: 10px; background-color: #dc3545; color: white; padding: 5px 10px; border-radius: 5px; font-size: 12px; font-weight: bold;">
                                @lang('Trailer')
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            @endforeach
        </div>
    </div>
</section>

<div class="add-area ad-section section--bg ptb-80">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-12 text-center">
                <div class="add-thumb">
                    @php echo showAd(); @endphp
                </div>
            </div>
        </div>
    </div>
</div>

<section class="movie-section section--bg section pb-80" data-section="latest_series">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="section-header">
                    <h2 class="section-title">@lang('Recently Added')</h2>
                </div>
            </div>
        </div>
        <div class="row justify-content-center mb-30-none">
            @foreach ($recent_added as $latest)
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-xs-6 mb-30">
                <div class="movie-item">
                    <div class="movie-thumb" style="position: relative;">
                        <!-- Lazy loading image with placeholder -->
                        <img class="lazy-loading-img" data-src="{{ getImage(getFilePath('item_portrait') . '/' . $latest->image->portrait) }}"
                             src="{{ asset('assets/global/images/lazy.png') }}" alt="movie" style="width: 100%; height: auto;">

                        <!-- Movie badge for Free/Paid -->
                        <span class="movie-badge" style="position: absolute; top: 10px; left: 10px; background-color: yellow; color: black; padding: 5px 10px; border-radius: 5px;">
                            {{ $latest->versionName }}
                        </span>

                        <!-- Movie thumb overlay with play button -->
                        <div class="movie-thumb-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.5); opacity: 0; transition: opacity 0.3s;">
                            <a class="video-icon" href="{{ route('watch', $latest->slug) }}" style="color: white;">
                                <i class="fas fa-play fa-2x"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

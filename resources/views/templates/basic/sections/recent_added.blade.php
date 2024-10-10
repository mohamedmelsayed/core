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
                        <div class="movie-thumb">
                            <img class="lazy-loading-img"
                                data-src="{{ getImage(getFilePath('item_portrait') . '/' . $latest->image->portrait) }}"
                                src="{{ asset('assets/global/images/lazy.png') }}" alt="movie">
                            <span class="movie-badge">{{ $latest->versionName }}</span>
                            <div class="movie-thumb-overlay">
                                <a class="video-icon"
                                    href="{{ $latest->is_audio ? route('preview.audio', $free->slug) : route('watch', $free->slug) }}">
                                    <i class="fas fa-play"></i>
                                </a>
                            </div>
                            <!-- Display Font Awesome icon based on is_audio inside the thumb -->
                            <span class="media-type"
                                style="position: absolute; bottom: 10px; right: 10px;  color: #fff; padding: 5px 10px; border-radius: 5px;">
                                @if ($latest->is_audio)
                                    <i class="fas fa-headphones" style="scale: 150%"></i> <!-- Audio Icon -->
                                @else
                                    <i class="fas fa-video" style="scale: 150%"></i> <!-- Video Icon -->
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

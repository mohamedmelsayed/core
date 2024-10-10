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
                    <div class="movie-thumb" style="position: relative; overflow: hidden; border-radius: 10px;">
                        <img class="lazy-loading-img" data-src="{{ getImage(getFilePath('item_portrait') . '/' . $latest->image->portrait) }}" src="{{ asset('assets/global/images/lazy.png') }}" alt="movie" style="width: 100%; height: auto; border-radius: 10px;">

                        <!-- Badge to indicate version (Paid/Free) -->
                        <span class="movie-badge" style="background-color: {{ $latest->versionName == 'Free' ? '#28a745' : 'yellow' }}; color: {{ $latest->versionName == 'Free' ? 'white' : 'black' }}; position: absolute; top: 10px; left: 10px; padding: 5px 10px; border-radius: 5px; font-size: 12px; font-weight: bold;">
                            {{ $latest->versionName }}
                        </span>

                        <!-- Play button overlay -->
                        <div class="movie-thumb-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); opacity: 0; display: flex; align-items: center; justify-content: center; transition: opacity 0.3s ease-in-out;">
                            <a class="video-icon" href="{{ route('watch', $latest->slug) }}" style="font-size: 30px; color: white;">
                                <i class="fas fa-play"></i>
                            </a>
                        </div>

                        <!-- Media type icon (Video/Audio) inside the thumb -->
                        <span class="media-type" style="position: absolute; top: 10px; right: 10px; background-color: #000; color: #fff; padding: 5px 10px; border-radius: 5px; font-size: 14px;">
                            @if ($latest->is_audio)
                                <i class="fas fa-headphones"></i> <!-- Audio Icon -->
                            @else
                                <i class="fas fa-video"></i> <!-- Video Icon -->
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            @endforeach
        </div>
    </div>
</section>

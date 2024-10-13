<section class="playlist-section section--bg section pb-80" data-section="latest_series">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="section-header">
                    <h2 class="section-title">@lang('Latest Playlists')</h2> <!-- Updated Title -->
                </div>
            </div>
        </div>
        <div class="row justify-content-center mb-30-none">

            @foreach ($playlists as $playlist)
                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-xs-6 mb-30">
                    <div class="playlist-item"> <!-- Changed to playlist-item -->
                        <div class="playlist-thumb"> <!-- Changed to playlist-thumb -->
                            <img class="lazy-loading-img" data-src="{{ getImage(getFilePath('item_portrait') . '/' . $playlist->cover_image) }}" src="{{ asset('assets/global/images/lazy.png') }}" alt="playlist">
                            <span class="playlist-badge">{{ $playlist->type == 'audio' ? 'Audio' : 'Video' }}</span> <!-- Updated badge for playlist type -->
                            <div class="playlist-thumb-overlay">
                                <a class="video-icon" href="{{ route('playlist.show', $playlist->id) }}"><i class="fas fa-play"></i></a> <!-- Link to playlist -->
                            </div>
                              <!-- Display Font Awesome icon based on type inside the thumb -->
                              <span class="media-type"
                              style="position: absolute; bottom: 10px; right: 10px; color: #fff; padding: 5px 10px; border-radius: 5px;">
                              @if ($playlist->type == 'audio')
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

<section class="movie-section section--bg section pt-80 pb-80" data-section="end">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="section-header">
                    <h2 class="section-title">@lang('Free Zone')</h2>
                </div>
            </div>
        </div>
        <div class="row justify-content-center mb-30-none">
            @foreach ($frees as $free)
                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-xs-6 mb-30">
                    <div class="movie-item">
                        <div class="movie-thumb" style="position: relative;">
                            <img src="{{ getImage(getFilePath('item_portrait') . '/' . $free->image->portrait) }}"
                                alt="movie">

                            <!-- Display "Paid" if the item is not free with a yellow badge -->
                            @if ($free->item_type == 1 && $free->version == 0)
                                <span class="movie-badge free">@lang('Free')</span>
                            @elseif($free->item_type == 3)
                                <span class="movie-badge paid">@lang('Trailer')</span>
                            @endif
                            <div class="movie-thumb-overlay">
                                <a class="video-icon" href="{{ route('watch', $free->slug) }}"><i
                                        class="fas fa-play"></i></a>
                            </div>

                            <!-- Display Font Awesome icon based on is_audio inside the thumb -->
                            <span class="media-type"
                                style="position: absolute; bottom: 10px; right: 10px;  color: #fff; padding: 5px 10px; border-radius: 5px;">
                                @if ($free->is_audio)
                                    <i class="fas fa-headphones" style="scale: 150%"></i> <!-- Audio Icon -->
                                @else
                                    <i class="fas fa-video" style="scale: 150%"></i> <!-- Video Icon -->
                                @endif
                            </span>

                            <div class="movie-thumb-overlay">
                                <a class="video-icon"
                                    href="{{ $free->is_audio ? route('preview.audio', $free->slug) : route('watch', $free->slug) }}">
                                    <i class="fas fa-play"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

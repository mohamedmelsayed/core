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
                    <div class="movie-thumb" style="position: relative; overflow: hidden; border-radius: 10px;">
                        <img src="{{ getImage(getFilePath('item_portrait') . '/' . $free->image->portrait) }}" alt="movie" style="width: 100%; height: auto; border-radius: 10px;">

                        <!-- Display "Paid" if the item is not free with a yellow badge -->
                        @if ($free->version != 0)
                        <span class="movie-badge" style="background-color: yellow; color: black; position: absolute; top: 10px; left: 10px; padding: 5px 10px; border-radius: 5px; font-size: 12px; font-weight: bold;">
                            @lang('Paid')
                        </span>
                        @else
                        <span class="movie-badge" style="background-color: #28a745; color: white; position: absolute; top: 10px; left: 10px; padding: 5px 10px; border-radius: 5px; font-size: 12px; font-weight: bold;">
                            @lang('Free')
                        </span>
                        @endif

                        <!-- Display Font Awesome icon based on is_audio inside the thumb -->
                        <span class="media-type" style="position: absolute; top: 10px; right: 10px; background-color: #000; color: #fff; padding: 5px 10px; border-radius: 5px; font-size: 14px;">
                            @if($free->is_audio)
                                <i class="fas fa-headphones"></i> <!-- Audio Icon -->
                            @else
                                <i class="fas fa-video"></i> <!-- Video Icon -->
                            @endif
                        </span>

                        <div class="movie-thumb-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); opacity: 0; display: flex; align-items: center; justify-content: center; transition: opacity 0.3s ease-in-out;">
                            <a class="video-icon" href="{{ $free->is_audio ? route('preview.audio', $free->slug) : route('watch', $free->slug) }}" style="font-size: 30px; color: white;">
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

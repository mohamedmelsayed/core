@extends($activeTemplate . 'layouts.frontend')

@section('content')
<section class="playlist-section section--bg section pb-80">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="section-header">
                    <h2 class="section-title">{{ $playlist->title }}</h2>
                    <p>{{ $playlist->description }}</p> <!-- Display playlist description -->
                </div>
            </div>
        </div>

        <div class="row justify-content-center mb-30-none">
            @foreach ($playlist->items as $item)
                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-xs-6 mb-30">
                    <div class="item">
                        <div class="item-thumb">
                            <img class="lazy-loading-img" data-src="{{ getImage(getFilePath('item_portrait') . '/' . $item->image->portrait) }}" src="{{ asset('assets/global/images/lazy.png') }}" alt="{{ $item->title }}">
                            <div class="item-thumb-overlay">
                                <a class="video-icon" href="{{ route('watch', $item->slug) }}"><i class="fas fa-play"></i></a>
                            </div>
                        </div>
                        <h5 class="item-title">{{ $item->title }}</h5>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection

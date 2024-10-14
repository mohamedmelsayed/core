
@if($playlists->isNotEmpty())
<div class="playlist-section mt-4">
    <h4 class="section-title">@lang('Playlists Containing this Item')</h4>
    <div class="row">
        @foreach($playlists as $playlist)
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mb-4">
                <div class="playlist-item">
                    <div class="playlist-thumb">
                        <img src="{{ getImage(getFilePath('item_portrait') . '/' . $playlist->cover_image) }}"
                            alt="{{ $playlist->title }}" class="img-fluid">
                    </div>
                    <div class="playlist-content">
                        <h5 class="playlist-title">{{ $playlist->title }}</h5>
                        <a href="{{ route('playlist.play', $playlist->id) }}" class="btn btn--base mt-2">
                            @lang('View Playlist')
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

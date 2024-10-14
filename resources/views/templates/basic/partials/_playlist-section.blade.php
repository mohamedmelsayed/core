<!-- Playlists containing this item -->
@if ($playlists->isNotEmpty())
    <h4 class="playlist-heading">@lang('Playlists Containing this Item')</h4>
    <div class="playlists-container">
        @foreach ($playlists as $playlist)
            <div class="playlist-item">
                <img src="{{ getImage(getFilePath('item_portrait') . '/' . $playlist->cover_image) }}"
                    alt="{{ $playlist->title }}" class="playlist-thumb">
                <h5 class="playlist-title">{{ $playlist->title }}</h5>
                <a href="{{ route('playlist.view', $playlist->id) }}" class="playlist-btn">@lang('View Playlist')</a>
            </div>
        @endforeach
    </div>
@endif




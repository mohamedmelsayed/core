@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10">
            <div class="card-body">
                <h4 class="card-title">{{ $pageTitle }}</h4>

                <!-- Display the item to be added -->
                <div class="mb-4">
                    <h5>@lang('Item to be added to the playlist:')</h5>
                    <p><strong>{{ $item->title }}</strong></p>
                </div>

                <!-- Select Playlist to add item -->
                <form action="{{ route('admin.playlist.storeItemInPlaylist') }}" method="POST">
                    @csrf
                    <input type="hidden" name="item_id" value="{{ $item->id }}">

                    <div class="form-group">
                        <label>@lang('Select Playlist')</label>
                        <select class="form-control" id="playlistSelect" name="playlist_id" required>
                            <option value="">@lang('Select a Playlist')</option>
                            @foreach($playlists as $playlist)
                                <option value="{{ $playlist->id }}">{{ $playlist->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn--primary">@lang('Add to Playlist')</button>
                </form>

                <!-- Display items already in each playlist -->
                <div class="mt-5" id="playlistItemsContainer">
                    <h5>@lang('Items already in this playlist:')</h5>
                    @foreach ($playlists as $playlist)
                        <div class="playlist-items" id="playlist-{{ $playlist->id }}" style="display:none;">
                            <h6 class="mt-4">{{ $playlist->title }}</h6>
                            <ul class="list-group">
                                @forelse ($playlistItems[$playlist->id] ?? [] as $playlistItem)
                                    <li class="list-group-item">{{ $playlistItem->title }}</li>
                                @empty
                                    <li class="list-group-item">@lang('No items in this playlist yet.')</li>
                                @endforelse
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Get the playlist select element
        const playlistSelect = document.getElementById('playlistSelect');
        const playlistItemsContainer = document.getElementById('playlistItemsContainer');

        // Hide all playlist items initially
        function hideAllPlaylistItems() {
            const playlists = document.querySelectorAll('.playlist-items');
            playlists.forEach(playlist => playlist.style.display = 'none');
        }

        // Show the selected playlist's items
        playlistSelect.addEventListener('change', function() {
            hideAllPlaylistItems();
            const selectedPlaylist = this.value;
            if (selectedPlaylist) {
                const selectedItems = document.getElementById(`playlist-${selectedPlaylist}`);
                if (selectedItems) {
                    selectedItems.style.display = 'block';
                }
            }
        });

        // Hide all on load
        hideAllPlaylistItems();
    });
</script>
@endpush

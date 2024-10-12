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
                        <select class="form-control" name="playlist_id" required>
                            @foreach($playlists as $playlist)
                                <option value="{{ $playlist->id }}">{{ $playlist->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn--primary">@lang('Add to Playlist')</button>
                </form>

                <!-- Display items already in each playlist -->
                <div class="mt-5">
                    <h5>@lang('Items already in this playlist:')</h5>
                    @forelse ($playlistItems as $playlistId => $items)
                        <h6 class="mt-4">{{ $playlists->find($playlistId)->title }}</h6>
                        <ul class="list-group">
                            @foreach ($items as $playlistItem)
                                <li class="list-group-item">{{ $playlistItem->title }}</li>
                            @endforeach
                        </ul>
                    @empty
                        <p>@lang('No items in the playlists yet.')</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

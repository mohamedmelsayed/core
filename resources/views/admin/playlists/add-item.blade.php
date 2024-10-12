@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ $pageTitle }}</h5>

                    <form action="{{ route('admin.playlist.storeItemInPlaylist') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="playlist">@lang('Select Playlist')</label>
                            <select class="form-control" name="playlist_id" required>
                                @foreach($playlists as $playlist)
                                    <option value="{{ $playlist->id }}">{{ $playlist->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <input type="hidden" name="item_id" value="{{ $item->id }}">

                        <div class="form-group">
                            <button class="btn btn--primary" type="submit">@lang('Add Item to Playlist')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

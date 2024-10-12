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

                <!-- Playlist Selection with Checkboxes -->
                <div class="form-group">
                    <label>@lang('Select Playlists')</label>
                    <div class="playlist-checkbox-group">
                        @foreach($playlists as $playlist)
                            <div class="form-check">
                                <input class="form-check-input playlist-checkbox"
                                       type="checkbox"
                                       id="playlist-{{ $playlist->id }}"
                                       value="{{ $playlist->id }}"
                                       data-items="{{ $playlist->items->pluck('title')->implode(', ') }}"
                                       @if($item->playlists->contains($playlist->id)) checked @endif>
                                <label class="form-check-label" for="playlist-{{ $playlist->id }}">
                                    {{ $playlist->title }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Display items already in the selected playlist on hover -->
                <div id="playlist-items-list" class="mt-4">
                    <h5>@lang('Items in this playlist:')</h5>
                    <ul id="items-list" class="list-group">
                        <!-- Dynamic list will be populated here -->
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
    .playlist-checkbox-group {
        display: flex;
        flex-wrap: wrap;
    }

    .form-check {
        margin-right: 15px;
        margin-bottom: 10px;
    }

    /* Hover display */
    #playlist-items-list {
        margin-top: 15px;
        display: none;
    }

    #items-list li {
        padding: 5px 10px;
        background: #f7f7f7;
        margin-bottom: 5px;
        border-radius: 4px;
    }
</style>
@endpush

@push('script')
<script>
    $(document).ready(function () {
        const itemId = '{{ $item->id }}'; // The item being added/removed from playlists

        // Function to display items for the selected playlist on hover
        function displayPlaylistItems(playlistId) {
            var option = $('#playlist-' + playlistId);
            var items = option.data('items');

            // If items are available, show the list
            if (items) {
                $('#items-list').html(''); // Clear the existing list
                var itemsArray = items.split(','); // Split items by comma
                itemsArray.forEach(function (item) {
                    $('#items-list').append('<li class="list-group-item" data-playlist-id="' + playlistId + '">' + item + '</li>');
                });

                $('#playlist-items-list').show(); // Show the list container
            } else {
                $('#playlist-items-list').hide(); // Hide if no items
            }
        }

        // Handle mouseover to display playlist items
        $('.playlist-checkbox').on('mouseover', function() {
            var playlistId = $(this).val();
            displayPlaylistItems(playlistId);
        });

        // Handle checkbox change event to add/remove item from playlist
        $('.playlist-checkbox').on('change', function() {
            var playlistId = $(this).val();

            if ($(this).is(':checked')) {
                performAddRequest(playlistId); // Make add request
            } else {
                performRemoveRequest(playlistId); // Make remove request
            }
        });

        // Function to make an AJAX request to add item to playlist
        function performAddRequest(playlistId) {
            $.ajax({
                url: '{{ route("admin.playlist.storeItemInPlaylist") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    playlist_id: playlistId,
                    item_id: itemId
                },
                success: function(response) {
                    console.log('Item added to playlist', playlistId);
                    notify('success', response.message);
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    notify('error', 'Unable to add item to playlist.');
                }
            });
        }

        // Function to make an AJAX request to remove item from playlist
        function performRemoveRequest(playlistId) {
            $.ajax({
                url: '{{ route("admin.playlist.removeItemFromPlaylist") }}', // Assuming you have this route set up
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    playlist_id: playlistId,
                    item_id: itemId
                },
                success: function(response) {
                    console.log('Item removed from playlist', playlistId);
                    notify('success', response.message);
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    notify('error', 'Unable to remove item from playlist.');
                }
            });
        }
    });
</script>
@endpush

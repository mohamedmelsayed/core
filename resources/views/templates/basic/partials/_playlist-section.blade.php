
@if($playlists->isNotEmpty())
<div class="playlist-section mt-4">
    <h4 class="section-title">@lang('Playlists Containing this Item')</h4>
    <div class="row">
        @foreach($playlists as $playlist)
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mb-4">
                <div class="playlist-item">
                    <div class="playlist-thumb">
                        <a href="{{ route('playlist.play', $playlist->id) }}" >
                        <img src="{{ getImage(getFilePath('item_portrait') . '/' . $playlist->cover_image) }}"
                            alt="{{ $playlist->title }}" class="img-fluid">
                        </a>
                    </div>
                    <div class="playlist-content">
                        <a href="{{ route('playlist.play', $playlist->id) }}" >
                            <h5 class="playlist-title">{{ $playlist->title }}</h5>

                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif


@push('style')
    <style>
        /* Container for the playlists */
        .playlists-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
            padding: 20px;
            background-color: #1e1e2d;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Playlist item */
        .playlist-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            background-color: rgba(255, 255, 255, 0.05);
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        /* Thumbnail */
        .playlist-thumb {
            width: 30%;
            height: 90%;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 15px;
        }

        /* Playlist title styling */
        .playlist-title {
            font-size: 16px;
            color: #fff;
            margin: 0;
            font-weight: bold;
        }

        /* Hover effects */
        .playlist-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: scale(1.02);
        }



        .playlist-btn:hover {
            background-color: #d5004f;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .playlist-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .playlist-thumb {
                width: 80px;
                height: 60px;
            }
        }
    </style>
@endpush

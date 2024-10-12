@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between flex-wrap align-items-center mb-4">
                    <h5 class="card-title">Playlists</h5>
                    <a href="{{ route('admin.playlist.create') }}" class="btn btn--primary">@lang('Create Playlist')</a>
                </div>

                <div class="table-responsive">
                    <table class="table align-items-center table-flush">
                        <thead>
                            <tr>
                                <th>@lang('Title')</th>
                                <th>@lang('Type')</th>
                                <th>@lang('Sub Category')</th>
                                <th>@lang('Cover Image')</th>
                                <th>@lang('Actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($playlists as $playlist)
                                <tr>
                                    <td>{{ $playlist->title }}</td>
                                    <td>{{ ucfirst($playlist->type) }}</td>
                                    <td>{{ $playlist->subCategory->name ?? 'N/A' }}</td>
                                    <td>
                                        @if ($playlist->cover_image)
                                            <img src="{{ asset('storage/' . $playlist->cover_image) }}" alt="cover image" width="50px">
                                        @else
                                            @lang('No Image')
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.playlist.edit', $playlist->id) }}" class="btn btn-sm btn--primary">
                                            <i class="las la-edit"></i> @lang('Edit')
                                        </a>
                                        <form action="{{ route('admin.playlist.destroy', $playlist->id) }}" method="DELETE" class="d-inline-block" onsubmit="return confirm('@lang('Are you sure you want to delete this playlist?')');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn--danger">
                                                <i class="las la-trash"></i> @lang('Delete')
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">@lang('No playlists found.')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

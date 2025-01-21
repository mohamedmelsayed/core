@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Title')</th>
                                    <th>@lang('Category')</th>
                                    <th>@lang('Subcategory')</th>
                                    <th>@lang('Playlists')</th> <!-- Column for playlists -->
                                    <th>@lang('Content Type')</th>

                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $item)
                                    <tr>
                                        <td>{{ $item->title }}</td>
                                        <td>{{ $item->category->name }}</td>
                                        <td>{{ optional($item->sub_category)->name ?? 'N/A' }}</td>


                                        <!-- Playlists Column -->
                                        <td>
                                            @if ($item->playlists->isNotEmpty())
                                                <ul>
                                                    @foreach ($item->playlists as $playlist)
                                                        <li>{{ $playlist->title }}</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span class="badge badge--secondary">@lang('No Playlist')</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->is_stream)
                                                <span class="badge badge--danger">@lang('Stream')</span>
                                            @else
                                                <span class="badge badge--success">@lang('Normal Video')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge--{{ $item->status ? 'success' : 'danger' }}">
                                                @lang($item->status ? 'Active' : 'Deactive')
                                            </span>
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <a class="btn btn-sm btn-outline--primary"
                                                    href="{{ route('admin.item.edit', $item->id) }}">
                                                    <i class="la la-pencil"></i>@lang('Edit')
                                                </a>

                                                <!-- Action Dropdown Menu -->
                                                <button class="btn btn-sm btn-outline--info" data-bs-toggle="dropdown"
                                                    type="button" aria-expanded="false"><i
                                                        class="las la-ellipsis-v"></i>@lang('More')</button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item threshold"
                                                        href="{{ route('watch', $item->slug) }}" target="_blank">
                                                        <i class="las la-eye"></i> @lang('Preview')
                                                    </a>

                                                    <!-- Playlist Addition Based on Type -->
                                                    <a class="dropdown-item threshold"
                                                        href="{{ route('admin.playlist.addItem', ['type' => $item->is_audio ? 'audio' : 'video', 'id' => $item->id]) }}">
                                                        <i class="las {{ $item->is_audio ? 'la-music' : 'la-video' }}"></i>
                                                        @lang($item->is_audio ? 'Add to Audio Playlist' : 'Add to Video Playlist')
                                                    </a>

                                                    <!-- Remove from all Playlists -->
                                                    @if ($item->playlists->isNotEmpty())
                                                        <a class="dropdown-item threshold removeFromAllPlaylistsBtn"
                                                            data-item-id="{{ $item->id }}" href="javascript:void(0)">
                                                            <i class="las la-minus-circle"></i> @lang('Remove from all Playlists')
                                                        </a>
                                                    @endif

                                                    <!-- Stream Configuration -->
                                                    @if ($item->sis_stream==1)
                                                        <a class="dropdown-item threshold"
                                                            href="{{ route('admin.item.setStream', $item->id) }}">
                                                            <i class="las la-cloud-upload-alt"></i> @lang('Configure Stream')
                                                        </a>
                                                    @endif

                                                    <!-- Episode and Video Handling -->
                                                    @if ($item->item_type == 2)
                                                        <a class="dropdown-item threshold"
                                                            href="{{ route('admin.item.episodes', $item->id) }}">
                                                            <i class="las la-list"></i> @lang('Episodes')
                                                        </a>
                                                    @else
                                                        @if ($item->video)
                                                            <a class="dropdown-item threshold"
                                                                href="{{ route('admin.item.updateVideo', $item->id) }}">
                                                                <i class="las la-cloud-upload-alt"></i> @lang('Update Video')
                                                            </a>
                                                            <a class="dropdown-item threshold"
                                                                href="{{ route('admin.language.translate2.show', ['type' => 'video', 'id' => $item->id]) }}">
                                                                <i class="las la-language"></i> @lang('Translate Content')
                                                            </a>
                                                        @else
                                                            @if (!$item->is_stream)
                                                                <a class="dropdown-item threshold"
                                                                    href="{{ route('admin.item.uploadVideo', $item->id) }}">
                                                                    <i class="las la-cloud-upload-alt"></i>
                                                                    @lang('Upload Video')
                                                                </a>
                                                            @endif
                                                        @endif
                                                    @endif
                                                    <a class="dropdown-item threshold"
                                                        href="{{ route('admin.item.ads.duration', $item->id) }}">
                                                        <i class="lab la-buysellads"></i> @lang('Update Ads')
                                                    </a>
                                                    <a class="dropdown-item threshold"
                                                        href="{{ route('admin.item.subtitle.list', [$item->id, '']) }}">
                                                        <i class="las la-file-audio"></i> @lang('Subtitles')
                                                    </a>
                                                    <a class="dropdown-item threshold"
                                                        href="{{ route('admin.item.report', [$item->id, '']) }}">
                                                        <i class="las la-chart-area"></i> @lang('Report')
                                                    </a>
                                                    <!-- Send Notification -->
                                                    <a class="dropdown-item threshold confirmationBtn"
                                                        data-action="{{ route('admin.item.send.notification', $item->id) }}"
                                                        data-question="@lang('Are you sure to send notifications to all users?')" href="javascript:void(0)">
                                                        <i class="las la-bell"></i> @lang('Send Notification')
                                                    </a>

                                                    <!-- Delete Item -->
                                                    <a class="dropdown-item deleteBtn" data-item-id="{{ $item->id }}"
                                                        href="javascript:void(0)">
                                                        <i class="las la-trash"></i> @lang('Delete')
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </div>
                </div>
                @if ($items->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($items) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />



@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Search by Name" />
    <a class="btn btn-outline--primary" href="{{ route('admin.item.create') }}"><i
            class="la la-plus"></i>@lang('Add New')</a>
@endpush

@push('style')
    <style>
        .table-responsive {
            overflow-x: unset !important;
        }
    </style>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            $('.deleteBtn').click(function() {
                var itemId = $(this).data('item-id');
                var deleteUrl = "{{ route('admin.item.delete', ':id') }}".replace(':id', itemId);
                $('#deleteForm').attr('action', deleteUrl);
                $('#confirmationModal').modal('show');
            });

            // Remove from all Playlists functionality
            $('.removeFromAllPlaylistsBtn').click(function() {
                var itemId = $(this).data('item-id');
                var removeUrl = "{{ route('admin.item.removeFromAllPlaylists', ':id') }}".replace(':id',
                    itemId);

                if (confirm("@lang('Are you sure you want to remove this item from all playlists?')")) {
                    $.ajax({
                        url: removeUrl,
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            item_id: itemId
                        },
                        success: function(response) {
                            location.reload(); // Refresh the page after removing
                        },
                        error: function() {
                            alert("@lang('Error occurred while removing the item from playlists.')");
                        }
                    });
                }
            });
        });
    </script>
@endpush

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">@lang('Confirmation')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @lang('Are you sure you want to delete this item?')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Cancel')</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">@lang('Delete')</button>
                </form>
            </div>
        </div>
    </div>
</div>

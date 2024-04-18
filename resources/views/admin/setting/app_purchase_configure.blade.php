@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-xl-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <ul class="list-group">
                        @forelse (json_decode($data) ?? [] as $key => $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                <span>{{ __(ucfirst(str_replace('_', ' ', $key))) }}</span>
                                <span>{{ __($item) }}</span>
                            </li>
                        @empty
                            <li class="list-group-item d-flex justify-content-center">
                                <span>@lang('File not found')</span>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="appPurchaseModal" role="dialog" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Update ') {{ ucfirst($type) }} @lang('Pay Credentials')</h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.app.purchase.credentials.update', $type) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="mt-2">@lang('File')</label>
                            <input type="file" class="form-control" name="file" accept=".json" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Update')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <button class="btn btn-outline--primary updateBtn" type="button"><i class="las la-upload"></i>@lang('Update File')</button>
    <x-back route="{{ route('admin.app.purchase.credentials') }}" />
@endpush

@push('style')
    <style>
        .list-group-item span {
            font-size: 22px !important;
            padding: 8px 0px
        }
    </style>
@endpush


@push('script')
    <script>
        (function($) {
            "use strict";
            $('.updateBtn').on('click', function(e) {
                let modal = $("#appPurchaseModal");
                modal.modal('show')
            });
        })(jQuery)
    </script>
@endpush

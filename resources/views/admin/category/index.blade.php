@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('English Name')</th>
                                    <th>@lang('type')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                    <tr>
                                        <td data-label="@lang('Name')">{{ __($category->name) }}</td>
                                        <td data-label="@lang('English Name')">{{ __($category->name_en) }}</td>
                                        <td data-label="@lang('Type')">{{ __($category->type) }}</td>

                                        <td data-label="@lang('Status')">
                                            @if ($category->status == 1)
                                                <span class="badge badge--success">@lang('Enabled')</span>
                                            @else
                                                <span class="badge badge--danger">@lang('Disabled')</span>
                                            @endif
                                        </td>
                                        <td data-label="@lang('Action')">
                                            <button class="btn btn-sm btn-outline--primary editBtn"
                                                data-category="{{ $category }}"><i
                                                    class="la la-pencil"></i>@lang('Edit')</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($categories->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($categories) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!--Category Modal -->
    <div class="modal fade" id="categoryModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Name')</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('English Name')</label>
                            <input type="text" name="name_en" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Category Type')</label>
                            <select name="type" class="form-control">
                                <option value="">-- @lang('Select One') --</option>
                                <option value="vid">@lang('video')</option>
                                <option value="aud">@lang('audio')</option>
                            </select>
                        </div>
                        <div class="form-group statusGroup">
                            <label>@lang('Status')</label>
                            <input type="checkbox" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle"
                                data-on="@lang('Enable')" data-off="@lang('Disable')" data-width="100%" name="status">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@push('breadcrumb-plugins')
    <button class="btn btn-sm btn-outline--primary addBtn"><i class="las la-plus"></i>@lang('Add New')</button>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict"

            var modal = $('#categoryModal');

            $('.addBtn').on('click', function() {
                modal.find('.modal-title').text(`@lang('Add Category')`);
                modal.find('form').attr('action', `{{ route('admin.category.store') }}`);
                modal.find('.statusGroup').hide();
                modal.modal('show');
            })

            $('.editBtn').on('click', function() {
                var category = $(this).data('category');
                modal.find('.modal-title').text(`@lang('Update Category')`);
                modal.find('input[name=type]').val(data.type);

                modal.find('input[name=name_en]').val(category.name_en);
                modal.find('input[name=name]').val(category.name);
                modal.find('form').attr('action', `{{ route('admin.category.store', '') }}/${category.id}`);
                modal.find('.statusGroup').show();

                if (category.status == 1) {
                    modal.find('input[name=status]').bootstrapToggle('on');
                } else {
                    modal.find('input[name=status]').bootstrapToggle('off');
                }

                modal.modal('show');
            });

            $('.removeBtn').on('click', function() {
                let modal = $('#removeModal');
                modal.find('input[name=id]').val($(this).data('id'))
                modal.modal('show');
            });

            modal.on('hidden.bs.modal', function() {
                modal.find('form')[0].reset();
            });
        })(jQuery);
    </script>
@endpush

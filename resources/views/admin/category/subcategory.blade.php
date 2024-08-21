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
                                <th>@lang('Category')</th>
                                <th>@lang('type')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            @forelse($subcategories as $subcategory)
                            @if($subcategory->category->status == 1)
                            <tr>
                                <td data-label="@lang('Name')">{{ __($subcategory->name)}}</td>
                                <td data-label="@lang('English Name')">{{ __($subcategory->name_en)}}</td>
                                <td data-label="@lang('English Name')">{{ __($subcategory->type)}}</td>
                                <td data-label="@lang('Category')">{{ __($subcategory->category->name) }}</td>
                                <td data-label="@lang('Status')">
                                    @if($subcategory->status == 1)
                                    <span class="badge badge--success font-weight-normal text--small">@lang('Enabled')</span>
                                    @else
                                    <span class="badge badge--danger font-weight-normal text--small">@lang('Disbaled')</span>
                                    @endif
                                </td>
                                <td data-label="@lang('Action')">
                                    <button class="btn btn-sm btn-outline--primary editBtn" data-name="{{ $subcategory->name }}" data-id="{{ $subcategory->id }}" data-category_id="{{ $subcategory->category_id }}" data-status="{{ $subcategory->status }}"><i class="la la-pencil"></i>@lang('Edit')</button>
                                </td>
                            </tr>
                            @endif
                            @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($subcategories->hasPages())
            <div class="card-footer py-4">
                {{ paginateLinks($subcategories) }}
            </div>
            @endif
        </div>
    </div>
</div>


@endsection



@push('breadcrumb-plugins')
<button class="btn btn-sm btn-outline--primary addBtn"><i class="las la-plus"></i>@lang('Add New')</button>
@endpush

@push('script')

<script>
    (function($){
        "use strict"
        var modal = $('#subCategoryModal');

        $('.addBtn').on('click', function(){
            modal.find('.modal-title').text(`@lang('Add Subcategory')`);
            modal.find('form').attr('action', `{{ route('admin.subcategory.store') }}`);
            modal.find('.statusGroup').hide();
            modal.modal('show');
        });

        $('.editBtn').on('click',function(){
            var data = $(this).data();
            modal.find('.modal-title').text(`@lang('Update Subcategory')`);
            modal.find('input[name=name]').val(data.name);
            modal.find('input[name=name_en]').val(data.name_en);
            modal.find('input[name=type]').val(data.type);
            modal.find('form').attr('action', `{{ route('admin.subcategory.store', '') }}/${data.id}`);
            modal.find('select[name=category_id]').val(`${data.category_id}`);
            modal.find('.statusGroup').show();

            if(data.status == 1){
                modal.find('input[name=status]').bootstrapToggle('on');
            }else{
                modal.find('input[name=status]').bootstrapToggle('off');
            }

            modal.modal('show');
        });
        $('#subCategoryModal').on('hidden.bs.modal', function () {
            $('#subCategoryModal form')[0].reset();
        });
    })(jQuery);
</script>

@endpush
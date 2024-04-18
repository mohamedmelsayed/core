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
                                <th scope="col">@lang('Name')</th>
                                <th scope="col">@lang('Level')</th>
                                <th scope="col">@lang('Actions')</th>

                            </tr>
                        </thead>
                        <tbody>
                            @forelse($planTypes as $plan)
                            <tr>
                                <td data-label="@lang('Name')">{{ $plan->name }}</td>
                                <td data-label="@lang('Level')">{{$plan->level }} </td>
                                <td data-label="@lang('Action')"> 
                                <button class="btn btn-sm btn-outline--primary editBtn" data-plan="{{ $plan }}" data-id="{{ $plan->id }}" data-name="{{ $plan->name }}" data-level="{{ $plan->level }}" ><i class="la la-pencil"></i>@lang('Edit')</button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($planTypes->hasPages())
            <div class="card-footer py-4">
                {{ paginateLinks($planTypes) }}
            </div>
            @endif
        </div><!-- card end -->
    </div>
</div>


<!-- Plan Modal -->
<div class="modal fade" id="planModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    <div class="form-group">
                        <label>@lang('Plan Type Name')</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>@lang('Plan Type Level')</label>
                        <br>
                        <label><i>@lang('Note :Subscriber of higher level can view lower plans')</i></label>

                        <input type="text" name="level" class="form-control" required>
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
<button class="btn btn-sm btn-outline--primary addBtn"><i class="la la-plus"></i>@lang('Add New')</button>
<br><label><i>@lang('Note :Subscriber of higher level can view/watch lower plans')</i></label>

@endpush

@push('style-lib')
<link href="{{ asset('assets/admin/css/fontawesome-iconpicker.min.css') }}" rel="stylesheet">
@endpush

@push('script-lib')
<script src="{{ asset('assets/admin/js/fontawesome-iconpicker.js') }}"></script>
@endpush

@push('script')
<script>
    (function($) {
        "use strict"
        var modal = $('#planModal');
        var defautlImage = `{{ getImage(getFilePath('plan'), getFileSize('plan')) }}`;
        $('.addBtn').on('click', function() {
            $('.modal-title').text(`@lang('Add New Plan Type')`);
            modal.find('form').attr('action', `{{ route('admin.type.store') }}`);
            modal.find('.statusGroup').hide();
            modal.modal('show');
        });

        $('.editBtn').on('click', function() {
            var plan = $(this).data('plan');
            $('.modal-title').text(`@lang('Update Plan')`);
            modal.find('input[name=name]').val(plan.name);
            modal.find('input[name=level]').val(plan.level);

            modal.find('form').attr('action', `{{ route('admin.type.store', '') }}/${plan.id}`);



            modal.modal('show');
        });

        modal.on('hidden.bs.modal', function() {
            modal.find('.profilePicPreview').attr('style', `background-image: url(${defautlImage})`);
            $('#planModal form')[0].reset();
        });

        $('.iconPicker').iconpicker().on('iconpickerSelected', function(e) {
            $(this).closest('.form-group').find('.iconpicker-input').val(`<i class="${e.iconpickerValue}"></i>`);
        });

    })(jQuery);
</script>
@endpush
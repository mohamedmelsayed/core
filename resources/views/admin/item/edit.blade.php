@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('admin.item.update', $item->id) }}" method="post" enctype="multipart/form-data" id="itemForm">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Portrait Image')</label>
                                    <div class="image--uploader w-100">
                                        <div class="image-upload-wrapper">
                                            <div class="image-upload-preview portrait" style="background-image: url({{ getImage(getFilePath('item_portrait') . '/' . @$item->image->portrait) }})">
                                            </div>
                                            <div class="image-upload-input-wrapper">
                                                <input type="file" class="image-upload-input" name="portrait" id="profilePicUpload1" accept=".png, .jpg, .jpeg">
                                                <label for="profilePicUpload1" class="bg--primary"><i class="la la-cloud-upload"></i></label>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <small class="mt-3 text-muted"> @lang('Supported Files:') <b>@lang('.png, .jpg, .jpeg')</b></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>@lang('Landscape Image')</label>
                                    <div class="image--uploader w-100">
                                        <div class="image-upload-wrapper">
                                            <div class="image-upload-preview landscape" style="background-image: url({{ getImage(getFilePath('item_landscape') . '/' . @$item->image->landscape) }})">
                                            </div>
                                            <div class="image-upload-input-wrapper">
                                                <input type="file" class="image-upload-input" name="landscape" id="profilePicUpload2" accept=".png, .jpg, .jpeg">
                                                <label for="profilePicUpload2" class="bg--primary"><i class="la la-cloud-upload"></i></label>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <small class="mt-3 text-muted"> @lang('Supported Files:') <b>@lang('.png, .jpg, .jpeg')</b></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Title -->
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label>@lang('Title')</label>
                                <input class="form-control" name="title" type="text" value="{{ $item->title }}" placeholder="@lang('Title')">
                            </div>
                        </div>

                        <!-- Version, Rent Options -->
                        @if ($item->item_type == Status::EPISODE_ITEM)
                            <div class="form-group col-md-3 rent-option">
                                <label>@lang('Do you want to add it as rent?')</label>
                                <div class="d-flex gap-3 flex-wrap">
                                    <div class="form-check">
                                        <input class="form-check-input" id="yes" name="version" type="radio" value="2" @checked($item->version == Status::RENT_VERSION)>
                                        <label class="form-check-label" for="yes">@lang('Yes')</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" id="no" name="version" type="radio" value="0" @checked($item->version == Status::FREE_VERSION)>
                                        <label class="form-check-label" for="no">@lang('No')</label>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="form-group col-md-3 version">
                                <label>@lang('Version')</label>
                                <select class="form-control" name="version">
                                    <option value="0">@lang('Free')</option>
                                    <option value="1">@lang('Paid')</option>
                                    <option value="2">@lang('Rent')</option>
                                </select>
                            </div>
                        @endif

                        <!-- Director, Producer, Languages, and Tags -->
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>@lang('Director')</label>
                                <select class="form-control select2-auto-tokenize director-option" name="director[]" multiple="multiple">
                                    @foreach (explode(',', $item->team->director) as $director)
                                        <option value="{{ $director }}" selected>{{ $director }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>@lang('Producer')</label>
                                <select class="form-control select2-auto-tokenize producer-option" name="producer[]" multiple="multiple">
                                    @foreach (explode(',', $item->team->producer) as $producer)
                                        <option value="{{ $producer }}" selected>{{ $producer }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>@lang('Languages')</label>
                                <select class="form-control select2-auto-tokenize language-option" name="language[]" multiple="multiple">
                                    @foreach (explode(',', $item->team->language) as $language)
                                        <option value="{{ $language }}" selected>{{ $language }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Tags -->
                        <div class="form-group col-md-6">
                            <label>@lang('Tags')</label>
                            <small class="text-facebook ml-2 mt-2">@lang('Separate multiple by') <code>,</code> (@lang('comma')) @lang('or') <code>@lang('enter')</code> @lang('key')</small>
                            <select class="form-control select2-auto-tokenize tag-option" name="tags[]" multiple="multiple">
                                @foreach (explode(',', $item->tags) as $tag)
                                    <option value="{{ $tag }}" selected>{{ $tag }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Submit Button -->
                        <div class="card-footer">
                            <button class="btn btn--primary w-100 h-45" type="submit">@lang('Update')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict"

            // Initialize Select2 for tokenized inputs (Director, Producer, Tags, Languages)
            $.each($('.select2-auto-tokenize'), function(index, element) {
                $(element).select2({
                    tags: true,
                    tokenSeparators: [',']
                });
            });

            // Handle dynamic subcategory based on category selection
            $('[name=category]').change(function() {
                var subcategoryOption = '<option>@lang('Select One')</option>';
                var subcategories = $(this).find(':selected').data('subcategories');
                subcategories.forEach(subcategory => {
                    subcategoryOption += `<option value="${subcategory.id}">${subcategory.name}</option>`;
                });
                $('[name=sub_category_id]').html(subcategoryOption);
            });

            // Set initial values for version, category, and subcategory
            $('select[name=category]').val('{{ $item->category->id }}');
            $('select[name=sub_category_id]').val('{{ @$item->sub_category->id }}');
            $('select[name=version]').val('{{ @$item->version }}');

        })(jQuery);

        (function($) {
            "use strict"
            $('[name=category]').change(function() {
                var subcategoryOption = '<option>@lang('Select One')</option>';
                var subcategories = $(this).find(':selected').data('subcategories');

                subcategories.forEach(subcategory => {
                    subcategoryOption += `<option value="${subcategory.id}">${subcategory.name}</option>`;
                });

                $('[name=sub_category_id]').html(subcategoryOption);
            });

            $('select[name=category]').val('{{ $item->category->id }}');
            $('select[name=sub_category_id]').val('{{ @$item->sub_category->id }}');
            $('select[name=version]').val('{{ @$item->version }}');

            let rent = "{{ Status::RENT_VERSION }}";
            let version;
            let rentalArea = $('#rentalArea');
            let currentVersion = "{{ $item->version }}";

            $('#itemForm').on('submit', function(e) {
                version = $('[name=version]').find('option:selected').val();
                if (version == rent) {
                    e.preventDefault();
                    if (!$('[name=rent_price]').val()) {
                        notify('error', 'Rent price field is required');
                        return;
                    }
                    if (!$('[name=rental_period]').val()) {
                        notify('error', 'Rental period field is required');
                        return;
                    }
                    if (!$('[name=exclude_plan]').val()) {
                        notify('error', 'Exclude from plan field is required');
                        return;
                    }
                }
                $(this).off('submit').submit();
            });


            $.each($('.select2-auto-tokenize'), function(index, element) {
                $(element).select2({
                    dropdownParent: $(element).closest('.position-relative'),
                    tags: true,
                    tokenSeparators: [',']
                });
            });


            $('[name=version]').on('change', function(e) {
                version = Number($(this).val())
                if (!version) {
                    version = $('[name=version]:checked').val();
                }
                if (version != undefined) {
                    if (version == rent) {
                        rentalArea.removeClass('d-none');
                    } else {
                        rentalArea.addClass('d-none');
                    }
                }
            }).change();
        })(jQuery);
    </script>
@endpush

@props(['type', 'id', 'reference'])

@extends('admin.layouts.app')

@section('panel')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">@lang('Translate Content')</div>
                <div class="card-body">
                    <form action="{{ route('admin.language.translate2.store') }}" method="post">
                        @csrf
                        <input type="hidden" name="type" value="{{ $type }}">
                        <input type="hidden" name="item_id" value="{{ $id }}">

                        <!-- Display original title from reference table -->
                        <div class="form-group row">
                            <label for="original_title" class="col-md-4 col-form-label text-md-right">@lang('Original Title'): {{ $type }}</label>
                            <div class="col-md-6">
                                <input type="text" id="original_title" name="original_title" value="{{ $reference->title }}" class="form-control" readonly>
                            </div>
                        </div>

                        <!-- Select language code -->
                        <div class="form-group row">
                            <label for="language" class="col-md-4 col-form-label text-md-right">@lang('Language'):</label>
                            <div class="col-md-6">
                                <select name="language" id="language" class="form-control">
                                    <option value="ar">@lang('Arabic')</option>
                                    <option value="en">@lang('English')</option>
                                    <option value="fr">@lang('French')</option>
                                </select>
                            </div>
                        </div>

                        <!-- Translation fields -->
                        <div class="form-group row">
                            <label for="translated_title" class="col-md-4 col-form-label text-md-right">@lang('Translated Title'):</label>
                            <div class="col-md-6">
                                <input type="text" id="translated_title" name="translated_title" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="translated_description" class="col-md-4 col-form-label text-md-right">@lang('Translated Description'):</label>
                            <div class="col-md-6">
                                <textarea id="translated_description" name="translated_description" class="form-control"></textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="translated_tags" class="col-md-4 col-form-label text-md-right">@lang('Translated Tags'):</label>
                            <div class="col-md-6">
                                <select id="translated_tags" name="translated_tags[]" class="form-control select2-tags" multiple="multiple"></select>
                            </div>
                        </div>

                        <!-- Submit button -->
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">@lang('Submit Translation')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Existing Translations Table -->
    <div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>@lang('Existing Translations')</span>
        <span class="badge badge-primary">{{ count($existingTranslations) }} @lang('Translations')</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>@lang('Actions')</th> <!-- Actions header moved to the first column -->
                        <th>@lang('Language')</th>
                        <th>@lang('Translated Title')</th>
                        <th>@lang('Translated Description')</th>
                        <th>@lang('Translated Tags')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($existingTranslations as $translation)
                    <tr>
                        <td> <!-- Actions cell moved to the first column -->
                            <form action="{{ route('admin.language.translate2.delete', $translation->id) }}" method="post" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('@lang('Are you sure you want to delete this translation?')');">@lang('Delete')</button>
                            </form>
                        </td>
                        <td>{{ $translation->language }}</td>
                        <td>{{ $translation->translated_title }}</td>
                        <td>{{ $translation->translated_description }}</td>
                        <td>{{ is_array($translation->translated_tags) ? implode(', ', $translation->translated_tags) : $translation->translated_tags }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

</div>
@endsection

@push('style-lib')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
@endpush

@push('script-lib')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
@endpush

@push('script')
<script>
    (function($) {
        "use strict";

        // Initialize Select2 for tags input fields
        $('.select2-tags').select2({
            tags: true,
            tokenSeparators: [',', ' '],
            width: '100%'
        });
    })(jQuery);
</script>
@endpush

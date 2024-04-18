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
                        <input type="hidden" name="id" value="{{ $id }}">
                        
                        <!-- Display original title from reference table -->
                        <div class="form-group row">
                            <label for="original_title" class="col-md-4 col-form-label text-md-right">Original Title :  {{$type}}</label>
                            <div class="col-md-6">
                                <input type="text" id="original_title" name="original_title" value="{{ $reference->title }}" class="form-control" readonly>
                            </div>
                        </div>
                        
                        <!-- Select language code -->
                        <div class="form-group row">
                            <label for="language" class="col-md-4 col-form-label text-md-right">Language:</label>
                            <div class="col-md-6">
                                <select name="language" id="language" class="form-control">
                                    <option value="ar">Arabic</option>
                                    <option value="en">English</option>
                                    <option value="fr">French</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Translation fields -->
                        <div class="form-group row">
                            <label for="translated_title" class="col-md-4 col-form-label text-md-right">Translated Title:</label>
                            <div class="col-md-6">
                                <input type="text" id="translated_title" name="translated_title" class="form-control">
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="translated_description" class="col-md-4 col-form-label text-md-right">Translated Description:</label>
                            <div class="col-md-6">
                                <textarea id="translated_description" name="translated_description" class="form-control"></textarea>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="translated_tags" class="col-md-4 col-form-label text-md-right">Translated Tags:</label>
                            <div class="col-md-6">
                                <input type="text" id="translated_tags" name="translated_tags" class="form-control select2-tags">
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="translated_keywords" class="col-md-4 col-form-label text-md-right">Translated Keywords:</label>
                            <div class="col-md-6">
                                <input type="text" id="translated_keywords" name="translated_keywords" class="form-control select2-tags">
                            </div>
                        </div>
                        
                        <!-- Submit button -->
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">Submit Translation</button>
                            </div>
                        </div>
                    </form>
                </div>
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
            tokenSeparators: [','],
            width: '100%'
        });
    })(jQuery);
</script>
@endpush

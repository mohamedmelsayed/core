@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <form action="{{ route('admin.defaults.item.defaults.store') }}" method="post" id="defaultsForm">
                @csrf
                <div class="card-body">
                    <h5 class="mb-4">@lang('Configure Default Values for Item Form')</h5>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>@lang('Default Description')</label>
                            <textarea class="form-control" name="default_description" rows="5" placeholder="@lang('Default Description')">{{ old('default_description', $defaults->description ?? '') }}</textarea>
                        </div>
                        <div class="form-group col-md-6">
                            <label>@lang('Default Preview Text')</label>
                            <textarea class="form-control" name="default_preview_text" rows="5" placeholder="@lang('Default Preview Text')">{{ old('default_preview_text', $defaults->preview_text ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-4 position-relative">
                            <label>@lang('Default Directors')</label>
                            <select class="form-control select2-auto-tokenize director-option" name="default_directors[]" multiple="multiple">
                                @foreach (old('default_directors', $defaults->directors ?? []) as $director)
                                    <option value="{{ $director }}" selected>{{ $director }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4 position-relative">
                            <label>@lang('Default Producers')</label>
                            <select class="form-control select2-auto-tokenize producer-option" name="default_producers[]" multiple="multiple">
                                @foreach (old('default_producers', $defaults->producers ?? []) as $producer)
                                    <option value="{{ $producer }}" selected>{{ $producer }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4 position-relative">
                            <label>@lang('Default Genres')</label>
                            <select class="form-control select2-auto-tokenize genres-option" name="default_genres[]" multiple="multiple">
                                @foreach (old('default_genres', $defaults->genres ?? []) as $genre)
                                    <option value="{{ $genre }}" selected>{{ $genre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-4 position-relative">
                            <label>@lang('Default Languages')</label>
                            <select class="form-control select2-auto-tokenize language-option" name="default_languages[]" multiple="multiple">
                                @foreach (old('default_languages', $defaults->languages ?? []) as $language)
                                    <option value="{{ $language }}" selected>{{ $language }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4 position-relative">
                            <label>@lang('Default Casts')</label>
                            <select class="form-control select2-auto-tokenize cast-option" name="default_casts[]" multiple="multiple">
                                @foreach (old('default_casts', $defaults->casts ?? []) as $cast)
                                    <option value="{{ $cast }}" selected>{{ $cast }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4 position-relative">
                            <label>@lang('Default Tags')</label>
                            <select class="form-control select2-auto-tokenize tag-option" name="default_tags[]" multiple="multiple">
                                @foreach (old('default_tags', $defaults->tags ?? []) as $tag)
                                    <option value="{{ $tag }}" selected>{{ $tag }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button class="btn btn--primary h-45 w-100" type="submit">@lang('Save Default Values')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
<x-back route="{{ route('admin.item.index') }}" />
@endpush

@push('script')
<script>
    (function($) {
        "use strict";

        $.each($('.select2-auto-tokenize'), function(index, element) {
            $(element).select2({
                dropdownParent: $(element).closest('.position-relative'),
                tags: true,
                tokenSeparators: [',']
            });
        });
    })(jQuery);
</script>
@endpush

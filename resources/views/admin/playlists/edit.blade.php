@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('admin.playlist.update', $playlist->id) }}" method="POST" enctype="multipart/form-data" id="playlistForm">
                    @csrf
                    @method('POST')
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('Title')</label>
                                <input class="form-control" name="title" type="text" value="{{ old('title', $playlist->title) }}" placeholder="@lang('Title')" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('Type')</label>
                                <select class="form-control" name="type" required>
                                    <option value="audio" @if($playlist->type == 'audio') selected @endif>@lang('Audio')</option>
                                    <option value="video" @if($playlist->type == 'video') selected @endif>@lang('Video')</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('Sub Category')</label>
                                <select class="form-control" name="sub_category_id" required>
                                    <option value="">@lang('Select One')</option>
                                    @foreach ($categories as $category)
                                        <optgroup label="{{ $category->name }}">
                                            @foreach ($category->subCategories as $subCategory)
                                                <option value="{{ $subCategory->id }}" @if($playlist->sub_category_id == $subCategory->id) selected @endif>{{ $subCategory->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Cover Image')</label>
                                    <div class="image--uploader w-100">
                                        <div class="image-upload-wrapper">
                                            <div class="image-upload-preview cover" style="background-image: url({{ getImage('storage/' . $playlist->cover_image) }})">
                                            </div>
                                            <div class="image-upload-input-wrapper">
                                                <input type="file" class="image-upload-input" name="cover_image" id="coverImageInput" accept=".png, .jpg, .jpeg">
                                                <label for="coverImageInput" class="bg--primary"><i class="la la-cloud-upload"></i></label>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <small class="mt-3 text-muted"> @lang('Supported Files:') <b>@lang('.png, .jpg, .jpeg')</b></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>@lang('Description')</label>
                            <textarea class="form-control" name="description" rows="5" placeholder="@lang('Description')" required>{{ old('description', $playlist->description) }}</textarea>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button class="btn btn--primary w-100" type="submit">@lang('Update Playlist')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
    (function($) {
        "use strict";

        // Preview cover image
        $('#coverImageInput').on('change', function() {
            var input = this;
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('.image-upload-preview.cover').css('background-image', 'url(' + e.target.result + ')');
                };
                reader.readAsDataURL(input.files[0]);
            }
        });

    })(jQuery);
</script>
@endpush

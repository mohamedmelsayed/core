@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('admin.playlist.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('Title')</label>
                                <input class="form-control" name="title" type="text" value="{{ old('title') }}" placeholder="@lang('Title')" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('Type')</label>
                                <select class="form-control" name="type" required>
                                    <option value="audio">@lang('Audio')</option>
                                    <option value="video">@lang('Video')</option>
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
                                                <option value="{{ $subCategory->id }}">{{ $subCategory->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label>@lang('Cover Image')</label>
                                <input type="file" class="form-control" name="cover_image" id="coverImageInput" accept="image/*" required>
                                <div class="mt-3">
                                    <img id="coverImagePreview" src="#" alt="Cover Image Preview" style="display:none; max-height: 200px; border: 1px solid #ddd; padding: 5px;">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>@lang('Description')</label>
                            <textarea class="form-control" name="description" rows="5" placeholder="@lang('Description')" required>{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button class="btn btn--primary w-100" type="submit">@lang('Create Playlist')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('script')
    <script>
        document.getElementById('coverImageInput').addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const preview = document.getElementById('coverImagePreview');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
    @endpush
@endsection

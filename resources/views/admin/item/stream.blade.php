@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <h2>Stream Configuration for Item  {{$item->title}}</h2>
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.item.configStream', $item->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="embed_code">@lang('Embed Code')</label>
                        <textarea id="embed_code" name="embed_code" class="form-control" rows="5">{{ old('embed_code', $item->embed_code) }}</textarea>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn--primary">@lang('Save')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
@push('breadcrumb-plugins')
<a href="{{ $prevUrl }}" class="btn btn-sm btn-outline--primary"><i class="la la-undo"></i> @lang('Back')</a>
@endpush
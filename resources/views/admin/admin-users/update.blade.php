@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">
                <form method="post" action="{{ route('admin.admin.update', [$user->id]) }}" enctype="multipart/form-data">

                    @csrf
                    <div class="form-group">
                        <label>@lang('Name')</label>
                        <input type="text" name="name" class="form-control" placeholder="@lang('Name')" required
                            value="{{ $user->name }}">
                        <input type="text" name="id" class="form-control" placeholder="@lang('Name')"
                            hidden="true">
                    </div>
                    <div class="form-group">
                        <label>@lang('Email')</label>
                        <input type="text" name="email" class="form-control" placeholder="@lang('Email')"
                            required value="{{ $user->email }}">
                    </div>




                    <div class="mb-3">
                        @foreach ($roles as $role)
                            <div class="form-check">
                                @if(hasRoleWithId($user->id,$role->role))
                                <input type="checkbox"  name="roles[]" checked
                                     value="{{ $role->role }}" id="{{ $role->role }}">
                                    <label for="{{ $role->role }}" class="form-check-label">{{ $role->role }}</label>
                               
                                @endIf
                                @if(!hasRoleWithId($user->id,$role->role))
                                <input type="checkbox"  name="roles[]" 
                                     value="{{ $role->role }}" id="{{ $role->role }}">
                                    <label for="{{ $role->role }}" class="form-check-label">{{ $role->role }}</label>
                               
                                @endIf
                            </div>
                        @endforeach



                    </div>

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
            </div>
            </form>


        </div>
    </div>
</div>
</div>
@endsection

@push('breadcrumb-plugins')
@endpush

@push('script')

    <script>
        </script>
        @endpush
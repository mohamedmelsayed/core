@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                            <tr>
                                <th>@lang('User')</th>
                                <th>@lang('Email-Phone')</th>
                                <th>@lang('Roles')</th>
                                <th>@lang('Actions')</th>
                             
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($adminUsers as $user)
                            <tr>
                                <td data-label="@lang('User')">
                                    <span class="fw-bold">{{$user->name}}</span>
                                    <br>
                                   
                                </td>

                                <td data-label="@lang('Email')">
                                    {{ $user->email }}
                                </td>

                                <td data-label="@lang('Roles')">
                                    {{ $user->roles }}
                                </td>
                               
                                <td>
                                <form id="formDelete"  
                          enctype="multipart/form-data">
                         @csrf
                         <a class="btn btn-danger deleteBtn btn-sm my-3 confirmationBtn"
                          href="{{ route('admin.admin.delete',$user->id)}}"
                                    ><i class="la la-trash"></i> @lang('Delete')</a>        
                                    <a class="btn btn-outline--primary  btn-sm my-3 confirmationBtn"
                          href="{{ route('admin.admin.updateForm',$user->id)}}"
                                    ><i class="la la-edit"></i> @lang('Update')</a>        

                                    <!-- <button type="button" class="btn btn-sm btn-outline--primary editBtn" data-user="{{ $user }}" data-id="{{ $user->id }}" data-email="{{ $user->email }}"  data-roles[]="{{ $user->roles }}" ><i class="la la-pencil"></i>@lang('Edit')</button> -->
</form>

                                </td>

                             
                            

                            </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse

                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($adminUsers->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($adminUsers) }}
                </div>
                @endif
            </div>
        </div>


    </div>
@endsection



@push('breadcrumb-plugins')

    <button class="btn btn-sm btn-outline--primary addBtn"><i
            class="las la-plus"></i>@lang('Add New')</button>
    <div class="d-flex flex-wrap justify-content-end">
        <form action="" method="GET" class="form-inline">
            <div class="input-group justify-content-end">
                <input type="text" name="search" class="form-control bg--white" placeholder="@lang('Search Username')" value="{{ request()->search }}">
                <button class="btn btn--primary input-group-text" type="submit"><i class="fa fa-search"></i></button>
            </div>
        </form>
    </div>
@endpush



<!-- Modal -->
<div class="modal fade" id="adminUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form  method="post" action="{{ route('admin.admin.create') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>@lang('Name')</label>
                        <input type="text" name="name" class="form-control" placeholder="@lang('Name')" required>
                        <input type="text" name="id" class="form-control" placeholder="@lang('Name')" hidden="true">
                    </div>
                    <div class="form-group">
                        <label>@lang('Email')</label>
                        <input type="text" name="email" class="form-control" placeholder="@lang('Email')" required>
                    </div>
                    <div class="form-group">
                        <label>@lang('username')</label>
                        <input type="text" name="username" class="form-control" placeholder="@lang('Username')" required>
                    </div>
                    <div class="form-group">
                        <label>@lang('password')</label>
                        <input type="password" name="password" class="form-control" placeholder="@lang('Password')" required>
                    </div>

                    
                    
                
                    <div class="mb-3">
                    @foreach($roles as $role)
                    <div class="form-check">
                        <input type="checkbox" name="roles[]"  value="{{$role->role}}" id="{{$role->role}}">
                   <label for="{{$role->role}}" class="form-check-label">{{$role->role}}</label>
                    </div>
                    @endforeach


                    
                    </div>
                    <!-- {{-- <div class="form-group">
                        <label>@lang('Parent Category Name')</label>
                        <select name="category_id" class="form-control">
                            <option value="">-- @lang('Select One') --</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ __($category->name) }}</option>
                            @endforeach
                        </select>
                    </div> --}} -->
                    <!-- {{-- <div class="form-group statusGroup">
                        <label>@lang('Status')</label>
                        <input type="checkbox" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Active')" data-off="@lang('Inactive')" data-width="100%" name="status">
                    </div> --}} -->
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
            </div>
            </form>
        </div>
    </div>
</div>


@push('script')

    <script>
     
        (function ($) {
            "use strict"

            var modal = $('#adminUserModal');

            $('.addBtn').on('click', function(){
                modal.find('.modal-title').text(`@lang('Add Admin User')`);
                modal.find('form').attr('action', `{{ route('admin.admin.create') }}`);
                modal.find('.statusGroup').hide();
                modal.modal('show');
            });
            $('.editBtn').on('click', function(){
            var user = $(this).data('user');
            $('.modal-title').text(`@lang('Update Admin user')`);
            modal.find('input[name=email]').val(user.email);
            modal.find('input[name=id]').val(user.id);
            modal.find('input[name=password]').hide();
            modal.find('input[name=email]').attr("disabled", true) ;
            modal.find('input[name=name]').val(user.name);
            // modal.find('input[name=roles]').val(user.roles);
            modal.find('form').attr('action', `{{ route('admin.admin.update', '') }}/${user.id}`);
            // modal.find('input[name=password]').val("qwqwq");
            modal.modal('show');


            });
            modal.on('hidden.bs.modal', function () {
            modal.find('input[name=email]').attr("disabled", false) ;
            $('#adminUserModal form')[0].reset();
            modal.find('input[name=password]').show();

        });
            $('.deleteBtn').on('click', function(){

                if (!confirm("Do you want to delete user?") == true) {
                    // $('#formDelete').submit();
                    event.preventDefault();

                            }
                 });
        
                 

              

        })(jQuery);

        // function clickRole(event){
           
        //             alert(event);
        //          }  
    </script>
@endpush

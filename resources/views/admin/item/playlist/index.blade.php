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
    							<th scope="col">@lang('Title')</th>
    							<th scope="col">@lang('Status')</th>
                                <th scope="col">@lang('Version')</th>
    							<th scope="col">@lang('Action')</th>
    						</tr>
    					</thead>
    					<tbody>
    						@forelse($episodes as $episode)
    						<tr>
    							<td data-label="@lang('Title')">{{ __($episode->title) }}</td>
    							<td data-label="@lang('Status')">
    								@if($episode->status == 1)
    								<span class="badge badge--success">@lang('Active')</span>
    								@else
    								<span class="badge badge--danger">@lang('Inactive')</span>
    								@endif
    							</td>
                                <td data-label="Version">
                                    <span class="badge badge--success">{{ $episode->planType->name }}</span>

                                    <!-- @if($episode->version == 0)
                                    <span class="badge badge--success">@lang('Free')</span>
                                    @else
                                    <span class="badge badge--primary">@lang('Paid')</span>
                                    @endif -->
                                </td>
    							<td data-label="@lang('Action')">
    								<button class="btn btn-sm btn-outline--primary editBtn" data-title="{{ $episode->title }}" data-version="{{ $episode->version }}" data-image="{{ getImage(getFilePath('episode').'/'.$episode->image) }}" data-episode_id="{{ $episode->id }}" data-status="{{ $episode->status }}" data-toggle="tooltip" title="" data-original-title="Edit">
    									<i class="la la-pencil"></i>@lang('Edit')
    								</button>
    								@if($episode->audio)
    								<a href="{{ route('admin.item.playlist.updateAudio',$episode->id) }}" class="btn btn-sm btn-outline--info">
    									<i class="la la-cloud-upload-alt"></i>@lang('Update Audio')
    								</a>
    								@else
    								<a href="{{ route('admin.item.playlist.addAudio',$episode->id) }}" class="btn btn-sm btn-outline--warning">
    									<i class="la la-cloud-upload-alt"></i>@lang('Upload Audio')
    								</a>
    								@endif
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
    		<div class="card-footer py-4">
    			{{ $episodes->links('admin.partials.paginate') }}
    		</div>
    	</div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="episodeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">@lang('Add New Episode')</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('admin.item.addEpisode',$item->id) }}" method="post" enctype="multipart/form-data">
      	@csrf
	      <div class="modal-body">
	        <div class="form-group">
	        	<label>@lang('Thumbnail Image')</label>
	        	<div class="image-upload">
	        		<div class="thumb">
	        			<div class="avatar-preview">
	        				<div class="profilePicPreview" style="background-image: url({{ getImage('') }})">
	        					<button type="button" class="remove-image"><i class="fa fa-times"></i></button>
	        				</div>
	        			</div>
	        			<div class="avatar-edit">
	        				<input type="file" class="profilePicUpload" name="image" id="profilePicUpload1" accept=".png, .jpg, .jpeg">
	        				<label for="profilePicUpload1" class="bg--success">@lang('Upload Thumbnail Image')</label>
	        			</div>
	        		</div>
	        	</div>
	        </div>
	        <div class="form-group">
	        	<label>@lang('Video Title')</label>
	        	<input type="text" name="title" class="form-control" required>
	        </div>
            <div class="form-group">
                <label>@lang('Version')</label>
                <select class="form-control" name="version" required>
                    <!-- <option value="0">@lang('Free')</option> -->
                    @foreach($types as $type)
					<option value="{{ $type->level }}">{{ __($type->name) }}</option>
					@endforeach
				</select>
            </div>
			<div class="form-group statusGroup">
				<label>@lang('Status')</label>
				<input type="checkbox" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Active')" data-off="@lang('Inactive')" data-width="100%" name="status">
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
<button class="btn btn-sm btn-outline--primary addBtn"><i class="las la-plus"></i>@lang('Add New Episode')</button>
@endpush
@push('script')
<script>
    (function($){
        "use strict"
		var modal = $('#episodeModal');

    	$('.addBtn').click(function(){
			modal.find('form').attr('action', `{{ route('admin.item.addEpisode', $item->id) }}`);
			modal.find('.statusGroup').hide();
    		modal.modal('show');
    	});

    	$('.editBtn').click(function(){
			let data = $(this).data();
    		modal.find('input[name=title]').val(data.title);
    		modal.find('.profilePicPreview').attr('style',`background-image:url(${data.image})`);
            modal.find('select').val(data.version);
			modal.find('.statusGroup').show();

			if(data.status == 1){
				modal.find('input[name=status]').bootstrapToggle('on');
			}else{
				modal.find('input[name=status]').bootstrapToggle('off');
			}

    		modal.find('form').attr('action', `{{ route('admin.item.updateEpisode','') }}/${data.episode_id}`);

    		modal.modal('show');
    	});

		modal.on('hidden.bs.modal', function () {
            modal.find('form')[0].reset();
        });

    })(jQuery);
</script>
@endpush

@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
    	<div class="card">
    		<form action="" method="post" enctype="multipart/form-data" id="upload-audio">
    			@csrf
    			<div class="card-body">
    				<div class="form-row">
	    				<div class="form-group col-md-12">
	    					<label>@lang('audio Type')</label>
	    					<select class="form-control" name="audio_type" required>
	    						<option value="1">@lang('audio')</option>
	    						<option value="0">@lang('Link')</option>
	    					</select>
	    				</div>
	    				<div class="form-group col-md-12">
                            <div class="form-group @if($audio->audio_type != 1) d-none @endif" id="audio">
                                <div class="upload" data-block="audio-drop-zone">
                                    <div>
                                        <svg fill="currentColor" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg" class="feather feather-upload"><path d="M14,13V17H10V13H7L12,8L17,13M19.35,10.03C18.67,6.59 15.64,4 12,4C9.11,4 6.6,5.64 5.35,8.03C2.34,8.36 0,10.9 0,14A6,6 0 0,0 6,20H19A5,5 0 0,0 24,15C24,12.36 21.95,10.22 19.35,10.03Z" /></svg>
                                        <h4> @lang('Darg Drop audio')</h4>
                                        <p>@lang('or Click to choose File')</p>
                                        <button type="button" class="btn btn--primary">@lang('Upload')</button>
                                    </div>
                                </div>
                                <small class="text-facebook">@lang('Only') <strong>@lang('mp4, mkv, 3gp')</strong> @lang('supported')</small>
                                <div class="progress mt-3">
                                    <div class="bar bg--primary"></div >
                                    <div class="percent">0%</div >
                                </div>
                                <input type="file" class="upload-audio-file" name="audio"/>
                            </div>
	    					<div class="form-group @if($audio->audio_type != 0) d-none @endif" id="link">
	    						<label>@lang('Insert Link')</label>
	    						<input type="text" class="form-control" placeholder="@lang('Inert Link')" value="@if($audio->audio_type == 0) {{ $audio->content }} @endif" name="link"/>
	    					</div>
	    				</div>
    				</div>
    			</div>
    			<div class="card-footer">
    				<button class="btn btn--primary w-100 h-45 submitBtn">@lang('Upload audio')</button>
    			</div>
    		</form>
    	</div>
        <div class="card mt-3">
            <div class="card-body">
                <div class="col-md-12 d-flex justify-content-center">
                <audio controls>
                    <source src="{{ $audioFile }}" type="audio/ogg">
                    Your browser does not support the audio tag.
                </audio> 
                    
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('breadcrumb-plugins')
<a href="{{ $prevUrl }}" class="btn btn-sm btn-outline--primary"><i class="la la-undo"></i> @lang('Back')</a>
@endpush
@push('style')
<style type="text/css">
    .progress { position:relative; width:100%; }
        .bar { width:0%; height:20px; }
        .percent { position:absolute; display:inline-block; left:50%;top: 8px; color: #040608;}
        .upload { margin-right: auto; margin-left: auto; width: 100%; height: 200px; margin-top: 20px; border: 3px dashed #929292; line-height: 200px; font-size: 18px; line-height: unset !important; display: table; text-align: center; margin-bottom: 20px; color: #929292; }
        .upload:hover { border: 3px dashed #04abf2; cursor: pointer; color: #04abf2; }
        .upload.hover { border: 3px dashed #04abf2; cursor: pointer; color: #04abf2; }
        .upload > div { display: table-cell; vertical-align: middle; }
        .upload > div h4 { padding: 0; margin: 0; font-size: 25px; font-weight: 700; font-family: Lato, sans-serif; }
        .upload > div p { padding: 0; margin: 0; font-family: Lato, sans-serif; }
        .upload-audio-file{
            opacity: 0;
            position: fixed;
        }
</style>
@endpush
@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.js"></script>
<script>

    "use strict"

	$("#audio_type").change(function(){
        if ($(this).val() == '0') {
            $("#link").removeClass('d-none');
            $("#audio").addClass('d-none');
        }else{
            $("#link").addClass('d-none');
            $("#audio").removeClass('d-none');
        }
    });

    $('select[name=audio_type]').val('{{ $audio->audio_type }}');




    var audio_drop_block = $("[data-block='audio-drop-zone']");

   if (typeof(window.FileReader)){
      audio_drop_block[0].ondragover = function() {
         audio_drop_block.addClass('hover');
         return false;
      };

      audio_drop_block[0].ondragleave = function() {
         audio_drop_block.removeClass('hover');
         return false;
      };

      audio_drop_block[0].ondrop = function(event) {
         event.preventDefault();
         audio_drop_block.removeClass('hover');
         var file = event.dataTransfer.files;
         $('#upload-audio').find('input').prop('files', file);
         $('#upload-audio').submit();
      };
   }


        $(document).on("click", ".upload-audio-file", function (e) {
             e.stopPropagation();
                   //some code
       });
        $(document).on("click", ".upload", function (e) {
            $( '.upload-audio-file' ).trigger("click");
        });

function validate(formData, jqForm, options) {
        var form = jqForm[0];
        if (form.audio_type.value == 0) {
            if (!form.link.value) {
                notify('error','Link field is required');
                return false;
            }
        }else{
            if (!form.audio.value) {
                notify('error','File Not Found');
                return false;
            }
            if (form.audio.files[0].size > 4194304000) {
                notify('error','File size must be lower then 4 gb');
                return false;
            }
            @if(!$audio)
                notify('error','audio not found');
                return false;
            @endif
        }
    }

    $('form').ajaxForm({
        beforeSubmit: validate,
        dataType:'json',
        beforeSend: function() {
            if($('#audio_type').val() == '0'){
                $('form').find('.submitBtn').text('Updating...');
                $('form').find('.submitBtn').attr('disabled','');
            }else{
                $('form').find('.card-footer').addClass('d-none');
            }
            var percentVal = '0%';
            bar.width(percentVal);
            percent.html(percentVal);
        },
        uploadProgress: function(event, position, total, percentComplete) {
            if($('#audio_type').val() == '1'){
                if(percentComplete > 50) {
                    percent.addClass('text-white');
                }
                var percentVal = percentComplete + '%';
                if(percentComplete == 100){
                    $('.percent').attr('style','top:2px');
                    percent.html(`<i class="fas fa-spinner fa-spin"></i> Processing`);
                }else{
                    percent.html(percentVal);
                }
                bar.width(percentVal);
            }
        },
        success: function(data) {
            if(data.demo){
                notify('warning', data.demo);
                percent.removeClass('text-white');
                $('.percent').attr('style','top:8px');
                var percentVal = '0%';
                bar.width(percentVal);
                percent.html(percentVal);
                $('form').find('.card-footer').removeClass('d-none');
                $('form').find('.submitBtn').text('Update audio');
                $('form').find('.submitBtn').removeAttr('disabled');
                $('form').trigger("reset");
            }else if (data.errors) {
                percent.removeClass('text-white');
                $('.percent').attr('style','top:8px');
                var percentVal = '0%';
                bar.width(percentVal);
                percent.html(percentVal);
                $('form').find('.card-footer').removeClass('d-none');
                $('form').find('.submitBtn').text('Update audio');
                $('form').find('.submitBtn').removeAttr('disabled');
                $('form').trigger("reset");
                notify('error', data.errors);
            }
            if(data == 'success') {
                $('.percent').attr('style','top:8px');
                bar.addClass('bg--success');
                percent.html('Success');
                location.reload();
            }
        }
    });

    var bar = $('.bar');
    var percent = $('.percent');

</script>
@endpush

@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
    	<div class="card upload-card">
    		<form action="" method="post" enctype="multipart/form-data" id="upload-audio">
    			@csrf
    			<div class="card-body">
    				<div class="form-row">
	    				<div class="form-group col-md-12">
	    					<label>@lang('Audio Type')</label>
	    					<select class="form-control" name="audio_type" required>
	    						<option value="1">@lang('Audio')</option>
	    						<option value="0">@lang('Link')</option>
	    					</select>
	    				</div>
	    				<div class="form-group col-md-12">
	    					<div class="form-group" id="audio">
                                    <div class="upload" data-block="audio-drop-zone">
                                        <div>
                                            <svg fill="currentColor" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg" class="feather feather-upload"><path d="M14,13V17H10V13H7L12,8L17,13M19.35,10.03C18.67,6.59 15.64,4 12,4C9.11,4 6.6,5.64 5.35,8.03C2.34,8.36 0,10.9 0,14A6,6 0 0,0 6,20H19A5,5 0 0,0 24,15C24,12.36 21.95,10.22 19.35,10.03Z" /></svg>
                                            <h4> @lang('Darg Drop Audio')</h4>
                                            <p>@lang('or Click to choose File')</p>
                                            <button type="button" class="btn btn--primary">@lang('Upload')</button>
                                        </div>
                                    </div>
                                    <small class="text-facebook">@lang('Only') <strong>@lang('mp3')</strong> @lang('supported')</small>
                                    <div class="progress mt-3">
                                        <div class="bar bg--primary"></div >
                                        <div class="percent">0%</div >
                                    </div>
                                    <input type="file" class="upload-audio-file" name="audio" accept="audio/*"/>
	    					</div>
	    					<div class="form-group" id="link">
	    						<label>@lang('Insert Link')</label>
	    						<input type="text" class="form-control" placeholder="@lang('Inert Link')" name="link"/>
	    					</div>
	    				</div>
    				</div>
    			</div>
    			<div class="card-footer">
    				<button class="btn btn--primary w-100 submitButton h-45">@lang('Upload Audio')</button>
    			</div>
    		</form>
    	</div>
    </div>
</div>
@endsection
@push('style')  <style type="text/css">
    .upload {
        margin-right: auto;
        margin-left: auto;
        width: 100%;
        height: 200px;
        margin-top: 20px;
        border: 3px dashed #929292;
        line-height: 200px;
        font-size: 18px;
        line-height: unset !important;
        display: table;
        text-align: center;
        margin-bottom: 20px;
        color: #929292;
    }

    .upload:hover {
        border: 3px dashed #04abf2;
        cursor: pointer;
        color: #04abf2;
    }

    .upload.hover {
        border: 3px dashed #04abf2;
        cursor: pointer;
        color: #04abf2;
    }

    .upload > div {
        display: table-cell;
        vertical-align: middle;
    }

    .upload > div h4 {
        padding: 0;
        margin: 0;
        font-size: 25px;
        font-weight: 700;
        font-family: Lato, sans-serif;
    }

    .upload > div p {
        padding: 0;
        margin: 0;
        font-family: Lato, sans-serif;
    }

    .upload-video-file {
        opacity: 0;
        position: fixed;
    }

    .video-quality .nav-link {
        border: 1px solid #0d6efd;
    }

    .video-quality {
        gap: 10px !important;
    }
</style>
@endpush
@push('breadcrumb-plugins')
<a href="{{ $prevUrl }}" class="btn btn-sm btn-outline--primary"><i class="la la-undo"></i> @lang('Back')</a>
@endpush
@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.js"></script>
<script>


"use strict"


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
            @if($audio)
                notify('error','audio Already Exist');
                return false;
            @endif
        }
    }
var isUploading = false;

$('#uploadForm').on('submit', function (e) {
    e.preventDefault();
    var formData = new FormData($(this)[0]);
    var progressBar = $('.progress');

    isUploading = true;

    $(window).on('beforeunload', function (e) {
        if (isUploading) {
            var confirmationMessage = "An upload is in progress. Are you sure you want to leave this page?";
            (e || window.event).returnValue = confirmationMessage; // For old browsers
            return confirmationMessage;
        }
    });

    var isUploading = false;
    var xhr; // Declare xhr variable globally

    $('#uploadForm').on('submit', function (e) {
        e.preventDefault();
        var formData = new FormData($(this)[0]);
        var progressBar = $('.progress');

        isUploading = true;

        $(window).on('beforeunload', function (e) {
            if (isUploading) {
                var confirmationMessage = "An upload is in progress. Are you sure you want to leave this page?";
                (e || window.event).returnValue = confirmationMessage; // For old browsers
                return confirmationMessage;
            }
        });

        $.ajax({
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
            },
            url: $(this).attr('action'),
            method: "POST",
            data: formData,
            xhr: function () {
                xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function (evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = (evt.loaded / evt.total) * 100;
                        progressBar.show();
                        progressBar.find('.progress-bar').css('width', percentComplete + '%').text(percentComplete.toFixed(2) + '%');
                    }
                }, false);
                xhr.addEventListener("abort", function () {
                    console.log("Upload aborted");
                });
                return xhr;
            },
            processData: false,
            contentType: false,
            success: function (response) {
                isUploading = false;
                $(window).off('beforeunload');
                if (response.error) {
                    notify('error', response.error); // Show error message
                } else {
                    notify('success', response.success); // Show success message
                    setTimeout(function () {
                        window.history.§(); // Replace with the URL of the desired page
                    }, 2000); // Reload the page
                }
            },
            error: function () {
                isUploading = false;
                $(window).off('beforeunload');
                alert('An error occurred while uploading the video.');
            }
        });
    });

    $(window).on('beforeunload', function (e) {
        if (isUploading) {
            var confirmationMessage = "An upload is in progress. Are you sure you want to leave this page?";
            (e || window.event).returnValue = confirmationMessage; // For old browsers
            return confirmationMessage;
        }
    });

    $(window).on('unload', function () {
        if (isUploading && xhr) {
            xhr.abort(); // Abort the upload if the user leaves the page
        }
    });

    $("#audio_type").change(function() {
        if ($(this).val() == '0') {
            $("#link").show();
            $("#audio").hide();
        } else {
            $("#link").hide();
            $("#audio").show();
        }
    }).change();
})(jQuery);

</script>
@endpush

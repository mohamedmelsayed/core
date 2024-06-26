@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="" id="uploadForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        <h5 class="my-4">@lang('audio')</h5>
                        <div class="form-group col-md-12">
                            <label>@lang('Audio Type')</label>
                            <select class="form-control" name="audio_type" required>
                                <option value="1">@lang('Audio')</option>
                                <option value="0">@lang('Link')</option>
                            </select>
                        </div>
                        <div class="form-group" id="three_sixty_video">
                            <div class="upload three-sixty-video" data-block="video-drop-zone">
                                <div>
                                    <svg class="feather feather-upload" fill="currentColor" height="24"
                                         viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M14,13V17H10V13H7L12,8L17,13M19.35,10.03C18.67,6.59 15.64,4 12,4C9.11,4 6.6,5.64 5.35,8.03C2.34,8.36 0,10.9 0,14A6,6 0 0,0 6,20H19A5,5 0 0,0 24,15C24,12.36 21.95,10.22 19.35,10.03Z"/>
                                    </svg>
                                    <h4> @lang('Darg Drop Audio')</h4>
                                    <p>@lang('or Click to choose File')</p>
                                    <button class="btn btn--primary" type="button">@lang('Upload')</button>
                                </div>
                            </div>
                            <input class="upload-video-file three-sixty" name="audio" type="file"
                                   accept="mp3"/>
                        </div>
                        <div class="form-group" id="three_sixty_link">
                            <label>@lang('Insert Link')</label>
                            <input class="form-control" name="link" type="text"
                                   placeholder="@lang('Inert Link')"/>
                        </div>
                        <div class="progress mt-3" style="display: none;">
                            <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated"
                                 role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0"
                                 aria-valuemax="100">0%
                            </div>
                        </div>
                        <div class="">
                            <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('style')
    <style type="text/css">
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
    <a class="btn btn-sm btn-outline--primary" href="{{ $prevUrl }}"><i class="la la-undo"></i> @lang('Back')</a>
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict"
            $(".three-sixty").on("click", function (e) {
                e.stopPropagation();
            });
            $(".three-sixty-video").on("click", function (e) {
                $('.three-sixty').trigger("click");
            });
            $("[name=audio_type]").on('change', function () {
                if ($(this).val() == '0') {
                    $("#three_sixty_link").show();
                    $("#three_sixty_video").hide();
                } else {
                    $("#three_sixty_link").hide();
                    $("#three_sixty_video").show();
                }
            }).change();

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
                                window.history.back(); // Replace with the URL of the desired page
                            }, 2000); // Reload the page
                        }
                    },
                    error: function () {
                        isUploading = false;
                        $(window).off('beforeunload');
                        alert('An error occurred while uploading the audio.');
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
        })(jQuery);
    </script>
@endpush

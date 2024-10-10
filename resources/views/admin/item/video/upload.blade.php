@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ $route }}" id="uploadForm" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="tab-content" id="pills-tabContent">
                            @foreach(['360p', '480p', '720p', '1080p'] as $resolution)
                                <div class="tab-pane fade @if($resolution === '720p') show active @endif" id="pills-{{ $resolution }}" role="tabpanel" aria-labelledby="pills-{{ $resolution }}-tab">
                                    <h5 class="my-4">@lang("Video File $resolution")</h5>
                                    <div class="form-group col-md-12">
                                        <label>@lang('Video Type')</label>
                                        <select class="form-control video-type-selector" name="video_type_{{ strtolower(str_replace('p', '', $resolution)) }}" required>
                                            <option value="1">@lang('Video')</option>
                                            <option value="0">@lang('Link')</option>
                                        </select>
                                    </div>
                                    <div class="form-group" id="{{ strtolower($resolution) }}_video">
                                        <div class="upload video-upload" data-resolution="{{ strtolower($resolution) }}">
                                            <div>
                                                <svg class="feather feather-upload" fill="currentColor" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M14,13V17H10V13H7L12,8L17,13M19.35,10.03C18.67,6.59 15.64,4 12,4C9.11,4 6.6,5.64 5.35,8.03C2.34,8.36 0,10.9 0,14A6,6 0 0,0 6,20H19A5,5 0 0,0 24,15C24,12.36 21.95,10.22 19.35,10.03Z"></path>
                                                </svg>
                                                <h4>@lang('Drag & Drop Video')</h4>
                                                <p>@lang('or Click to choose File')</p>
                                                <button class="btn btn--primary" type="button">@lang('Upload')</button>
                                            </div>
                                        </div>
                                        <input class="upload-video-file" name="{{ strtolower($resolution) }}_video" type="file" accept=".mp4,.mkv,.3gp" />
                                    </div>
                                    <div class="form-group" id="{{ strtolower($resolution) }}_link">
                                        <label>@lang('Insert Link')</label>
                                        <input class="form-control" name="{{ strtolower($resolution) }}_link" type="text" placeholder="@lang('Insert Link')" />
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="progress mt-3" style="display: none;">
                            <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                        </div>

                        <div class="mt-4">
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
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: #929292;
            cursor: pointer;
        }

        .upload:hover {
            border: 3px dashed #04abf2;
            color: #04abf2;
        }

        .upload > div h4, .upload > div p {
            margin: 0;
        }

        .upload-video-file {
            opacity: 0;
            position: absolute;
            z-index: -1;
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
            "use strict";

            // Handle video or link input display based on selected video type
            $('.video-type-selector').on('change', function () {
                let selectedType = $(this).val();
                let resolution = $(this).attr('name').split('_').pop();
                if (selectedType == '0') {
                    $(`#${resolution}_link`).show();
                    $(`#${resolution}_video`).hide();
                } else {
                    $(`#${resolution}_link`).hide();
                    $(`#${resolution}_video`).show();
                }
            }).change();

            // Handle file upload triggering by clicking upload block
            $('.video-upload').on('click', function () {
                let resolution = $(this).data('resolution');
                $(`input[name="${resolution}_video"]`).trigger('click');
            });

            // Form submission and upload progress
            let isUploading = false;
            let xhr;

            $('#uploadForm').on('submit', function (e) {
                e.preventDefault();
                let formData = new FormData($(this)[0]);
                let progressBar = $('.progress');

                isUploading = true;

                $(window).on('beforeunload', function (e) {
                    if (isUploading) {
                        let confirmationMessage = "An upload is in progress. Are you sure you want to leave this page?";
                        (e || window.event).returnValue = confirmationMessage;
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
                    processData: false,
                    contentType: false,
                    xhr: function () {
                        xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function (evt) {
                            if (evt.lengthComputable) {
                                let percentComplete = (evt.loaded / evt.total) * 100;
                                progressBar.show();
                                progressBar.find('.progress-bar').css('width', percentComplete + '%').text(percentComplete.toFixed(2) + '%');
                            }
                        }, false);
                        return xhr;
                    },
                    success: function (response) {
                        isUploading = false;
                        $(window).off('beforeunload');
                        if (response.error) {
                            notify('error', response.error);
                        } else {
                            notify('success', response.success);
                            setTimeout(function () {
                                window.history.back();
                            }, 2000);
                        }
                    },
                    error: function () {
                        isUploading = false;
                        $(window).off('beforeunload');
                        alert('An error occurred while uploading the video.');
                    }
                });
            });

            // Handle window unload and upload cancellation
            $(window).on('unload', function () {
                if (isUploading && xhr) {
                    xhr.abort();
                }
            });

        })(jQuery);
    </script>
@endpush

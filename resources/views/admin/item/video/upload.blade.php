@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card upload-card">
                <form id="upload-video" action="" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label>@lang('Video Type')</label>
                                <select class="form-control" name="video_type" required>
                                    <option value="1">@lang('Video')</option>
                                    <option value="0">@lang('Link')</option>
                                </select>
                            </div>
                            <div class="form-group col-md-12">
                                <div class="form-group" id="video">
                                    <div class="upload" data-block="video-drop-zone">
                                        <div>
                                            <svg class="feather feather-upload" fill="currentColor" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M14,13V17H10V13H7L12,8L17,13M19.35,10.03C18.67,6.59 15.64,4 12,4C9.11,4 6.6,5.64 5.35,8.03C2.34,8.36 0,10.9 0,14A6,6 0 0,0 6,20H19A5,5 0 0,0 24,15C24,12.36 21.95,10.22 19.35,10.03Z" />
                                            </svg>
                                            <h4> @lang('Darg Drop Video')</h4>
                                            <p>@lang('or Click to choose File')</p>
                                            <button class="btn btn--primary" type="button">@lang('Upload')</button>
                                        </div>
                                    </div>
                                    <small class="text-facebook">@lang('Only') <strong>@lang('mp4, mkv, 3gp')</strong> @lang('supported')</small>
                                    <div class="progress mt-3">
                                        <div class="bar bg--primary"></div>
                                        <div class="percent">0%</div>
                                    </div>
                                    <input class="upload-video-file" name="video" type="file" />
                                </div>
                                <div class="form-group" id="link">
                                    <label>@lang('Insert Link')</label>
                                    <input class="form-control" name="link" type="text" placeholder="@lang('Inert Link')" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn--primary w-100 submitButton h-45">@lang('Upload Video')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style type="text/css">
        .progress {
            position: relative;
            width: 100%;
        }

        .bar {
            width: 0%;
            height: 20px;
        }

        .percent {
            position: absolute;
            display: inline-block;
            left: 50%;
            top: 8px;
            color: #040608;
        }

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

        .upload>div {
            display: table-cell;
            vertical-align: middle;
        }

        .upload>div h4 {
            padding: 0;
            margin: 0;
            font-size: 25px;
            font-weight: 700;
            font-family: Lato, sans-serif;
        }

        .upload>div p {
            padding: 0;
            margin: 0;
            font-family: Lato, sans-serif;
        }

        .upload-video-file {
            opacity: 0;
            position: fixed;
        }
    </style>
@endpush
@push('breadcrumb-plugins')
    <a class="btn btn-sm btn-outline--primary" href="{{ $prevUrl }}"><i class="la la-undo"></i> @lang('Back')</a>
@endpush

@push('style-lib')
    <link href="{{ asset('assets/admin/css/bootstrap-clockpicker.min.css') }}" rel="stylesheet">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/bootstrap-clockpicker.min.js') }}"></script>
@endpush
@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.js"></script>
    <script>
        "use strict"


        var video_drop_block = $("[data-block='video-drop-zone']");

        if (typeof(window.FileReader)) {
            video_drop_block[0].ondragover = function() {
                video_drop_block.addClass('hover');
                return false;
            };

            video_drop_block[0].ondragleave = function() {
                video_drop_block.removeClass('hover');
                return false;
            };

            video_drop_block[0].ondrop = function(event) {
                event.preventDefault();
                video_drop_block.removeClass('hover');
                var file = event.dataTransfer.files;
                $('#upload-video').find('input').prop('files', file);
                $('#upload-video').submit();
            };
        }

        $(document).on("click", ".upload-video-file", function(e) {
            e.stopPropagation();
            //some code
        });
        $(document).on("click", ".upload", function(e) {
            $('.upload-video-file').trigger("click");
        });

        function validate(formData, jqForm, options) {
            var form = jqForm[0];
            if (form.video_type.value == 0) {
                if (!form.link.value) {
                    notify('error', 'Link field is required');
                    return false;
                }
            } else {
                if (!form.video.value) {
                    notify('error', 'File Not Found');
                    return false;
                }
                if (form.video.files[0].size > 4194304000) {
                    notify('error', 'File size must be lower then 4 gb');
                    return false;
                }
                @if ($video)
                    notify('error', 'Video Already Exist');
                    return false;
                @endif
            }
        }

        var bar = $('.bar');
        var percent = $('.percent');

        $('form').ajaxForm({
            beforeSubmit: validate,
            dataType: 'json',
            beforeSend: function() {
                if ($('#video_type').val() == '0') {
                    $('form').find('.submitButton').text('Saving...');
                    $('form').find('.submitButton').attr('disabled', '');
                } else {
                    $('form').find('.card-footer').addClass('d-none');
                }
                var percentVal = '0%';
                bar.width(percentVal);
                percent.html(percentVal);
            },
            uploadProgress: function(event, position, total, percentComplete) {
                if ($('#video_type').val() == '1') {
                    if (percentComplete > 50) {
                        percent.addClass('text-white');
                    }
                    var percentVal = percentComplete + '%';
                    if (percentComplete == 100) {
                        $('.percent').attr('style', 'top:2px');
                        percent.html(`<i class="fas fa-spinner fa-spin"></i> Processing`);
                    } else {
                        percent.html(percentVal);
                    }
                    bar.width(percentVal);
                }
            },
            success: function(data) {
                if (data.demo) {
                    notify('warning', data.demo);
                } else if (data.errors) {
                    percent.removeClass('text-white');
                    $('.percent').attr('style', 'top:8px');
                    var percentVal = '0%';
                    bar.width(percentVal);
                    percent.html(percentVal);
                    $('form').find('.card-footer').removeClass('d-none');
                    notify('error', data.errors);
                }
                if (data == 'success') {
                    $('.percent').attr('style', 'top:8px');
                    bar.addClass('bg--success');
                    percent.html('Success');
                    $('form').find('.submitButton').text('Upload Video');
                    $('form').find('.submitButton').removeAttr('disabled');
                    $('form').trigger("reset");
                    notify('success', 'video uploaded');
                    window.location = "{{ route('admin.item.ads.duration', [$item->id, @$episode->id]) }}";
                }
            }
        });

        $("#video_type").change(function() {
            if ($(this).val() == '0') {
                $("#link").show();
                $("#video").hide();
            } else {
                $("#link").hide();
                $("#video").show();
            }
        }).change();



        (function() {
            $('.addBtn').on('click', function() {
                $('.add-timeline-area').append(`<div class="col-md-12 mb-2">
                                                    <div class="input-group clockpicker">
                                                        <input class="form-control single-input" id="single-input" type="text" value="00:5">
                                                    </div>
                                                </div>`);

                initClock()
            });

            function initClock() {
                $('.single-input').clockpicker({
                    placement: 'bottom',
                    align: 'right',
                    autoclose: true,
                    'default': '20:48'
                });
            }
        })(jQuery)
    </script>
@endpush

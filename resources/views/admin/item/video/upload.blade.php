@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card upload-card">
                <form id="upload-video" action="{{ route('upload.video') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label>@lang('Video Type')</label>
                                <select class="form-control" name="video_type" id="video_type" required>
                                    <option value="1">@lang('Video')</option>
                                    <option value="0">@lang('Link')</option>
                                </select>
                            </div>
                            <div class="form-group col-md-12" id="video">
                                <label>@lang('Upload Video')</label>
                                <div class="upload" data-block="video-drop-zone">
                                    <div>
                                        <svg class="feather feather-upload" fill="currentColor" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M14,13V17H10V13H7L12,8L17,13M19.35,10.03C18.67,6.59 15.64,4 12,4C9.11,4 6.6,5.64 5.35,8.03C2.34,8.36 0,10.9 0,14A6,6 0 0,0 6,20H19A5,5 0 0,0 24,15C24,12.36 21.95,10.22 19.35,10.03Z" />
                                        </svg>
                                        <h4>@lang('Darg Drop Video')</h4>
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
                            <div class="form-group col-md-12" id="link" style="display: none;">
                                <label>@lang('Insert Link')</label>
                                <input class="form-control" name="link" type="text" placeholder="@lang('Inert Link')" />
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

@push('scripts')
    <script>
        $(document).ready(function() {
            // Function to perform client-side validations before submitting the form
            $('#upload-video').submit(function(event) {
                // Check if video type is 'Link'
                if ($('#video_type').val() == '0') {
                    // Validate link field
                    var link = $('input[name="link"]').val();
                    if (!link) {
                        alert('Link field is required');
                        event.preventDefault(); // Prevent form submission
                        return false;
                    }
                } else { // Video type is 'Video'
                    // Validate video file
                    var fileInput = $('input[name="video"]');
                    var file = fileInput[0].files[0];
                    if (!file) {
                        alert('File Not Found');
                        event.preventDefault(); // Prevent form submission
                        return false;
                    }
                    // Validate file size (assuming 4 GB limit)
                    var maxSize = 4 * 1024 * 1024 * 1024; // 4 GB in bytes
                    if (file.size > maxSize) {
                        alert('File size must be lower than 4 GB');
                        event.preventDefault(); // Prevent form submission
                        return false;
                    }
                }
                // Form validation successful, allow form submission
                return true;
            });

            // Show/hide link input based on video type selection
            $('#video_type').change(function() {
                if ($(this).val() == '0') {
                    $('#link').show();
                    $('#video').hide();
                } else {
                    $('#link').hide();
                    $('#video').show();
                }
            });
        });
    </script>
@endpush

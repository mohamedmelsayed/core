@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ $route }}" id="uploadForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="tab-pane fade show active" id="pills-720p" role="tabpanel" aria-labelledby="pills-720p-tab" tabindex="0">
                        <!-- Content for 720P video -->
                        <h5 class="my-4">@lang('Video File 720P')</h5>
                        <div class="form-group">
                            <label>@lang('Video Type')</label>
                            <select class="form-control" name="video_type_seven_twenty" required>
                                <option value="1">@lang('Video')</option>
                                <option value="0">@lang('Link')</option>
                            </select>
                        </div>
                        <div class="form-group" id="seven_twenty_video">
                            <div class="upload seven-twenty-video" data-block="video-drop-zone">
                                <div>
                                    <svg class="feather feather-upload" fill="currentColor" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M14,13V17H10V13H7L12,8L17,13M19.35,10.03C18.67,6.59 15.64,4 12,4C9.11,4 6.6,5.64 5.35,8.03C2.34,8.36 0,10.9 0,14A6,6 0 0,0 6,20H19A5,5 0 0,0 24,15C24,12.36 21.95,10.22 19.35,10.03Z" />
                                    </svg>
                                    <h4> @lang('Drag Drop Video')</h4>
                                    <p>@lang('or Click to choose File')</p>
                                    <button class="btn btn--primary" type="button">@lang('Upload')</button>
                                </div>
                            </div>
                            <input class="upload-video-file seven-twenty" name="seven_twenty_video" type="file" accept=".mp4,.mkv,.3gp" />
                        </div>
                        <div class="form-group" id="seven_twenty_link" style="display: none;">
                            <label>@lang('Insert Link')</label>
                            <input class="form-control" name="seven_twenty_link" type="text" placeholder="@lang('Insert Link')" />
                        </div>
                    </div>
                    <div class="progress mt-3" style="display: none;">
                        <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                    </div>
                    <button class="btn btn-primary mt-3" type="submit">@lang('Submit')</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    $(document).ready(function() {
        // Show/Hide link input based on video type selection
        $('[name^="video_type"]').on('change', function() {
            var id = $(this).attr('name').split('_').pop();
            if ($(this).val() == '0') {
                $('#'+id+'_video').hide();
                $('#'+id+'_link').show();
            } else {
                $('#'+id+'_video').show();
                $('#'+id+'_link').hide();
            }
        });

        // Handle form submission with AJAX and show progress bar
        $('#uploadForm').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData($(this)[0]);
            var progressBar = $('.progress');
            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                },
                url: $(this).attr('action'),
                method: "POST",
                data: formData,
                xhr: function() {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = (evt.loaded / evt.total) * 100;
                            progressBar.show();
                            progressBar.find('.progress-bar').css('width', percentComplete + '%').text(percentComplete.toFixed(2) + '%');
                        }
                    }, false);
                    return xhr;
                },
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.error) {
                        alert(response.error); // Show error message
                    } else {
                        alert(response.success); // Show success message
                        window.location.reload(); // Reload the page
                    }
                }
            });
        });
    });
</script>
@endpush

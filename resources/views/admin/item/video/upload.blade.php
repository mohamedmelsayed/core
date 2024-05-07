@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('your.upload.route') }}" id="uploadForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">@lang('Select Video')</label>
                        <input class="form-control" type="file" name="video" accept=".mp4,.mkv,.3gp" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">@lang('Video Type')</label>
                        <select class="form-control" name="video_type" id="video_type" required>
                            <option value="1">@lang('Upload Video')</option>
                            <option value="0">@lang('Insert Link')</option>
                        </select>
                    </div>
                    <div class="mb-3" id="linkInput" style="display: none;">
                        <label class="form-label">@lang('Insert Link')</label>
                        <input class="form-control" type="text" name="link">
                    </div>
                    <div class="progress mt-3" style="display: none;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0;"></div>
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
        $('#video_type').on('change', function() {
            if ($(this).val() == '0') {
                $('#linkInput').show();
            } else {
                $('#linkInput').hide();
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
                            progressBar.find('.progress-bar').css('width', percentComplete + '%');
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
@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('your.upload.route') }}" id="uploadForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">@lang('Select Video')</label>
                        <input class="form-control" type="file" name="video" accept=".mp4,.mkv,.3gp" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">@lang('Video Type')</label>
                        <select class="form-control" name="video_type" id="video_type" required>
                            <option value="1">@lang('Upload Video')</option>
                            <option value="0">@lang('Insert Link')</option>
                        </select>
                    </div>
                    <div class="mb-3" id="linkInput" style="display: none;">
                        <label class="form-label">@lang('Insert Link')</label>
                        <input class="form-control" type="text" name="link">
                    </div>
                    <div class="progress mt-3" style="display: none;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0;"></div>
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
        $('#video_type').on('change', function() {
            if ($(this).val() == '0') {
                $('#linkInput').show();
            } else {
                $('#linkInput').hide();
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
                            progressBar.find('.progress-bar').css('width', percentComplete + '%');
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

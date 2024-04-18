@extends($activeTemplate . 'layouts.' . $layout)

@section('content')
    <div class="card-area pt-80 pb-80">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-10">
                    <div class="card custom--card">
                        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <h4 class="card-title mb-0 d-flex align-items-center gap-2 flex-wrap">
                                @php echo $myTicket->statusBadge; @endphp
                                <span>[@lang('Ticket')#{{ $myTicket->ticket }}] {{ $myTicket->subject }}</span>
                            </h4>
                            @if ($myTicket->status != Status::TICKET_CLOSE && $myTicket->user)
                                <button class="btn--danger btn-sm close-button confirmationBtn" data-question="@lang('Are you sure you want to close this support ticket')?" data-action="{{ route('ticket.close', $myTicket->id) }}" type="button" title="@lang('Close Ticket')"><i class="las la-times-circle"></i>
                                </button>
                            @endif
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ route('ticket.reply', $myTicket->id) }}" enctype="multipart/form-data">
                                @csrf
                                <input name="replayTicket" type="hidden" value="1">
                                <div class="row justify-content-between">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <textarea class="form-control form--control form-control-lg" id="inputMessage" name="message" placeholder="@lang('Your Reply')" rows="4" cols="10"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="text-end">
                                        <button class="cmn-btn btn-sm addFile" type="button">
                                            <i class="las la-plus"></i> @lang('Add New')
                                        </button>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="position-relative">
                                        <input class="form-control form--control custom--file-upload my-1" id="inputAttachments" name="attachments[]" type="file" />
                                    </div>

                                    <div id="fileUploadsContainer"></div>
                                    <p class="ticket-attachments-message text-muted">
                                        @lang('Allowed File Extensions'): .@lang('jpg'),
                                        .@lang('jpeg'), .@lang('png'), .@lang('pdf'), .@lang('doc'),
                                        .@lang('docx')
                                    </p>
                                </div>
                                <div class="form-group">
                                    <button class="cmn-btn custom-success w-100 mt-2" type="submit">
                                        <i class="fa fa-reply"></i> @lang('Reply')
                                    </button>
                                </div>
                            </form>

                            @foreach ($messages as $message)
                                @if ($message->admin_id == 0)
                                    <div class="row border-primary border-radius-3 my-3 mx-2 border py-3">
                                        <div class="col-md-3 border-right text-right">
                                            <h5 class="my-3">{{ $message->ticket->name }}</h5>
                                        </div>
                                        <div class="col-md-9">
                                            <p class="text-muted font-weight-bold my-3">
                                                @lang('Posted on') {{ $message->created_at->format('l, dS F Y @ H:i') }}</p>
                                            <p>{{ $message->message }}</p>
                                            @if ($message->attachments()->count() > 0)
                                                <div class="mt-2">
                                                    @foreach ($message->attachments as $k => $image)
                                                        <a class="mr-3" href="{{ route('ticket.download', encrypt($image->id)) }}"><i class="fa fa-file"></i> @lang('Attachment') {{ ++$k }}
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="row border-warning border-radius-3 my-3 mx-2 border py-3">
                                        <div class="col-md-3 border-right text-right">
                                            <h5 class="my-3">{{ $message->admin->name }}</h5>
                                            <p class="lead text-muted">@lang('Staff')</p>
                                        </div>
                                        <div class="col-md-9">
                                            <p class="text-muted font-weight-bold my-3">
                                                @lang('Posted on') {{ $message->created_at->format('l, dS F Y @ H:i') }}</p>
                                            <p>{{ $message->message }}</p>
                                            @if ($message->attachments()->count() > 0)
                                                <div class="mt-2">
                                                    @foreach ($message->attachments as $k => $image)
                                                        <a class="mr-3" href="{{ route('ticket.download', encrypt($image->id)) }}"><i class="fa fa-file"></i> @lang('Attachment') {{ ++$k }}
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-confirmation-modal closeBtn="btn btn--danger btn-sm" baseBtn="btn btn--base btn-sm" />

@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            var fileAdded = 0;

            $('.delete-message').on('click', function(e) {
                $('.message_id').val($(this).data('id'));
            });
            $('.addFile').on('click', function() {
                if (fileAdded >= 4) {
                    notify('error', 'You\'ve added maximum number of file');
                    return false;
                }
                fileAdded++;
                $("#fileUploadsContainer").append(
                    `<div class="input-group my-2">
                        <input type="file" name="attachments[]" id="inputAttachments" class="form-control form--control"/>
                        <button class="input-group-text remove-btn color--danger" type="button"><i class="las la-times"></i></button>
                    </div>`
                )
            });

            $(document).on('click', '.remove-btn', function() {
                fileAdded--;
                $(this).closest('.input-group').remove();
            });

        })(jQuery);
    </script>
@endpush

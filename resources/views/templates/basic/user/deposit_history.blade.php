@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="card-area section--bg ptb-80">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-10">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>@lang('Transaction ID')</th>
                                <th>@lang('Plan Name')</th>
                                <th>@lang('Gateway')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Time')</th>
                                <th>@lang('MORE')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deposits as $k => $data)
                                <tr>
                                    <td>{{ $data->trx }}</td>
                                    <td>
                                        @if (@$data->subscription->plan)
                                            <span>@lang('Subscription - ') {{ __(@$data->subscription->plan->name) }}</span>
                                        @elseif (@$data->subscription->item)
                                            <a class="text--primary" href="{{ route('watch', @$data->subscription->item->slug) }}">{{ strLimit(@$data->subscription->item->title, 40) }}</a>
                                        @else
                                            <span>@lang('App Purchase')</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($data->method_code == -1)
                                            @lang('Google Pay')
                                        @elseif($data->method_code == -2)
                                            @lang('Apple Pay')
                                        @else
                                            {{ __(@$data->gateway->name) }}
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ getAmount($data->amount) }} {{ $general->cur_text }}</strong>
                                    </td>
                                    <td>
                                        @if ($data->status == 1)
                                            <span class="badge badge--success">@lang('Complete')</span>
                                        @elseif($data->status == 2)
                                            <span class="badge badge--warning">@lang('Pending')</span>
                                        @elseif($data->status == 3)
                                            <span class="badge badge--danger">@lang('Cancel')</span>
                                        @endif

                                        @if ($data->admin_feedback != null)
                                            <button class="btn--info btn-rounded badge detailBtn" data-admin_feedback="{{ $data->admin_feedback }}"><i class="fa fa-info"></i></button>
                                        @endif

                                    </td>
                                    <td>
                                        <i class="fa fa-calendar"></i> {{ showDateTime($data->created_at) }}
                                    </td>

                                    @php
                                        $details = $data->detail != null ? json_encode($data->detail) : null;
                                    @endphp

                                    <td>
                                        <a class="btn btn--default btn-sm approveBtn @if ($data->method_code >= 1000) detailBtn @else disabled @endif" href="javascript:void(0)" @if ($data->method_code >= 1000) data-info="{{ $details }}" @endif @if ($data->status == Status::PAYMENT_REJECT) data-admin_feedback="{{ $data->admin_feedback }}" @endif>
                                            <i class="fa fa-desktop"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="data-not-found" colspan="100">
                                        <div class="data-not-found__text text-center">
                                            <h6 class="empty-table__text mt-1">{{ __($emptyMessage) }} </h6>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $deposits->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Detail MODAL --}}
    <div class="modal fade" id="detailModal" role="dialog" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <span class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <ul class="list-group userData mb-2">
                    </ul>
                    <div class="feedback"></div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-dark btn-sm" data-bs-dismiss="modal" type="button">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.detailBtn').on('click', function() {
                var modal = $('#detailModal');
                var userData = $(this).data('info');
                var html = '';
                if (userData) {
                    userData.forEach(element => {
                        if (element.type != 'file') {
                            html += `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>${element.name}</span>
                                <span">${element.value}</span>
                            </li>`;
                        }
                    });
                }
                modal.find('.userData').html(html);
                if ($(this).data('admin_feedback') != undefined) {
                    var adminFeedback = `
                        <div class="my-3">
                            <strong>@lang('Admin Feedback')</strong>
                            <p>${$(this).data('admin_feedback')}</p>
                        </div>
                    `;
                } else {
                    var adminFeedback = '';
                }
                modal.find('.feedback').html(adminFeedback);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush

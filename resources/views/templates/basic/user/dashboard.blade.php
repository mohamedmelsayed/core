@extends($activeTemplate . 'layouts.master')
@section('content')

    <div class="card-area section--bg pt-80">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6">
                    <div class="card custom--card">
                        <div class="card-body">
                            <div class="join-form-area">
                                <form action="" method="POST" id="partyJoinForm">
                                    <div class="form-group">
                                        <label>@lang('Join Watch Party')</label>
                                        <div class="input-group">
                                            <input type="text" name="party_code" placeholder="Party Code" class="form-control">
                                            <button class="input-group-text" type="submit"><i class="lab la-telegram"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="join-pending d-none">
                                <div class="text-center processing">
                                    <i class="las la-4x la-spinner"></i>
                                    <h5>@lang('Your request is pending. Wait until the host user accepts')</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($user->exp > now())
        <div class="card-area section--bg ptb-80">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-6">
                        <div class="card custom--card">
                            <div class="card-header d-flex align-items-center justify-content-center flex-wrap">
                                <h4 class="card-title mb-0">
                                    @lang('Current subscription plan is ' . @auth()->user()->plan->name)
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="card-body-content text-center">
                                    <h3 class="title">@lang('Subscription will be expired')</h3>
                                </div>
                                <div class="draw-countdown" data-year="{{ \Carbon\Carbon::parse(auth()->user()->exp)->format('Y') }}" data-month="{{ \Carbon\Carbon::parse(auth()->user()->exp)->format('m') }}" data-day="{{ \Carbon\Carbon::parse(auth()->user()->exp)->format('d') }}" data-hour="{{ \Carbon\Carbon::parse(auth()->user()->exp)->format('H') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        @if (auth()->user()->deposits->where('status', 2)->count() > 0)
            <div class="card-area section--bg pt-80">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-xl-6">
                            <div class="card custom--card">
                                <div class="card-body">
                                    <div class="card-body-content text-center">
                                        <h3 class="title">@lang('Your payment is now in pending, please wait for admin response')</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <section class="plan-section section--bg ptb-80">
            <div class="container">
                <div class="row justify-content-center mb-30-none">
                    @foreach ($plans as $plan)
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-30">
                            <div class="plan-item text-center">
                                <div class="plan-icon">
                                    @php echo $plan->icon @endphp
                                </div>
                                <div class="plan-content">
                                    <span class="sub-title">{{ __($plan->name) }}</span>
                                    <h2 class="amount">{{ $general->cur_sym }}{{ getAmount($plan->pricing) }}</h2>
                                    <p>@lang('Get ' . $plan->duration . ' days subscription')</p>
                                    @if ($general->device_limit)
                                        <p>@lang('Connect with ' . $plan->device_limit . ' device')</p>
                                    @endif
                                    <div class="plan-btn mt-30">
                                        @if (auth()->user()->deposits->where('status', 2)->count() > 0)
                                            <button class="btn--base w-100" disabled>@lang('Subscribe Now')</button>
                                        @else
                                            <button class="btn--base w-100 confirmationBtn" data-question="@lang('Are you sure to subscribe this plan')?" data-action="{{ route('user.subscribe.plan', $plan->id) }}">@lang('Subscribe Now')</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
    <x-confirmation-modal closeBtn="btn btn--danger btn-sm" baseBtn="btn btn--base btn-sm" />
@endsection

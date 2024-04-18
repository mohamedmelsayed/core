<?php

namespace App\Http\Controllers\Gateway;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Http\Request;

class PaymentController extends Controller {

    public function deposit() {
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('method_code')->get();
        $pageTitle = 'Payment Methods';

        $subscriptionId = session()->get('subscription_id');
        if (!$subscriptionId) {
            $notify[] = ['error', 'Oops! Session invalid'];
            return redirect()->route('user.home')->withNotify($notify);
        }

        $subscription = Subscription::where('status', 0)->where('id', $subscriptionId)->first();
        if (!$subscription) {
            $notify[] = ['error', 'Oops! Subscription not found'];
            return redirect()->route('user.home')->withNotify($notify);
        }

        $amount = @$subscription->plan->pricing ?? @$subscription->item->rent_price;
        return view($this->activeTemplate . 'user.payment.deposit', compact('gatewayCurrency', 'pageTitle', 'subscription', 'amount'));
    }

    public function depositInsert(Request $request) {

        $request->validate([
            'amount'   => 'required|numeric|gt:0',
            'gateway'  => 'required',
            'currency' => 'required',
        ]);

        $user = auth()->user();
        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->where('method_code', $request->gateway)->where('currency', $request->currency)->first();
        if (!$gate) {
            $notify[] = ['error', 'Invalid gateway'];
            return back()->withNotify($notify);
        }

        $subscriptionId = session()->get('subscription_id');
        if (!$subscriptionId) {
            $notify[] = ['error', 'Oops! Session invalid'];
            return back()->withNotify($notify);
        }

        $subscription = Subscription::inactive()->where('id', $subscriptionId)->first();
        if (!$subscription) {
            $notify[] = ['error', 'Oops! Subscription not found'];
            return back()->withNotify($notify);
        }

        $amount = @$subscription->plan->pricing ?? @$subscription->item->rent_price;

        if ($gate->min_amount > $amount || $gate->max_amount < $amount) {
            $notify[] = ['error', 'Please follow deposit limit'];
            return back()->withNotify($notify);
        }

        $charge      = $gate->fixed_charge + ($amount * $gate->percent_charge / 100);
        $payable     = $amount + $charge;
        $finalAmount = $payable * $gate->rate;

        $data                  = new Deposit();
        $data->user_id         = $user->id;
        $data->subscription_id = $subscription->id;
        $data->method_code     = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount          = $amount;
        $data->charge          = $charge;
        $data->rate            = $gate->rate;
        $data->final_amount    = $finalAmount;
        $data->btc_amount      = 0;
        $data->btc_wallet      = "";
        $data->trx             = getTrx();
        $data->save();
        session()->put('Track', $data->trx);
        session()->forget('subscription_id');
        return to_route('user.deposit.confirm');
    }

    public function appDepositConfirm($hash) {
        try {
            $id = decrypt($hash);
        } catch (\Exception $ex) {
            return "Sorry, invalid URL.";
        }
        $data = Deposit::where('id', $id)->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->firstOrFail();
        $user = User::findOrFail($data->user_id);
        auth()->login($user);
        session()->put('Track', $data->trx);
        return to_route('user.deposit.confirm', 'app');
    }

    public function depositConfirm($type = null) {
        $track   = session()->get('Track');
        $deposit = Deposit::where('trx', $track)->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->with('gateway')->firstOrFail();

        if ($deposit->method_code >= 1000) {
            return redirect()->route('user.deposit.manual.confirm', $type);
        }

        $dirName = $deposit->gateway->alias;
        $new     = __NAMESPACE__ . '\\' . $dirName . '\\ProcessController';

        $data = $new::process($deposit);
        $data = json_decode($data);

        if (isset($data->error)) {
            $notify[] = ['error', $data->message];
            return to_route(gatewayRedirectUrl())->withNotify($notify);
        }
        if (isset($data->redirect)) {
            return redirect($data->redirect_url);
        }

        // for Stripe V3
        if (@$data->session) {
            $deposit->btc_wallet = $data->session->id;
            $deposit->save();
        }

        $pageTitle = 'Payment Confirm';
        return view($this->activeTemplate . $data->view, compact('data', 'pageTitle', 'deposit', 'type'));
    }

    public static function userDataUpdate($deposit, $isManual = null) {
        if ($deposit->status == Status::PAYMENT_INITIATE || $deposit->status == Status::PAYMENT_PENDING) {
            $deposit->status = Status::PAYMENT_SUCCESS;
            $deposit->save();

            $subscription         = Subscription::inactive()->where('id', $deposit->subscription_id)->first();
            $subscription->status = Status::ENABLE;
            $subscription->save();

            $user = User::find($deposit->user_id);
            $plan = $subscription->plan;

            if ($plan) {
                $user->plan_id = $subscription->plan_id;
                $user->exp     = $subscription->expired_date;
                $user->save();

                UserDevice::where('user_id', $user->id)->delete();
                $device            = new UserDevice();
                $device->user_id   = $user->id;
                $device->device_id = md5($_SERVER['HTTP_USER_AGENT']);
                $device->save();

                notify($user, 'SUBSCRIBE_PLAN', [
                    'plan'     => $plan->name,
                    'price'    => showAmount($plan->pricing),
                    'duration' => $plan->duration,
                ]);

                $adminNotifyTitle = $user->username . ' subscribed to ' . @$subscription->plan->name;

            } else {
                notify($user, 'VIDEO_RENT', [
                    'item'     => @$subscription->item->title,
                    'price'    => showAmount(@$subscription->item->rent_price),
                    'duration' => $subscription->expired_date,
                ]);
                $adminNotifyTitle = $user->username . ' rented to ' . @$subscription->item->title;
            }

            if (!$isManual) {
                $adminNotification            = new AdminNotification();
                $adminNotification->user_id   = $user->id;
                $adminNotification->title     = @$adminNotifyTitle;
                $adminNotification->click_url = urlPath('admin.deposit.successful');
                $adminNotification->save();
            }
        }
    }

    public function manualDepositConfirm($type = null) {
        $track = session()->get('Track');
        $data  = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        if (!$data) {
            return to_route(gatewayRedirectUrl());
        }
        if ($data->method_code > 999) {
            $pageTitle = 'Payment Confirm';
            $method    = $data->gatewayCurrency();
            $gateway   = $method->method;
            return view($this->activeTemplate . 'user.payment.manual', compact('data', 'pageTitle', 'method', 'gateway', 'type'));
        }
        abort(404);
    }

    public function manualDepositUpdate(Request $request) {
        $track = session()->get('Track');
        $data  = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        if (!$data) {
            return to_route(gatewayRedirectUrl());
        }
        $gatewayCurrency = $data->gatewayCurrency();
        $gateway         = $gatewayCurrency->method;
        $formData        = $gateway->form->form_data;

        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData = $formProcessor->processFormData($request, $formData);

        $data->detail = $userData;
        $data->status = Status::PAYMENT_PENDING;
        $data->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $data->user->id;
        $adminNotification->title     = 'Payment request from ' . $data->user->username;
        $adminNotification->click_url = urlPath('admin.deposit.details', $data->id);
        $adminNotification->save();

        notify($data->user, 'DEPOSIT_REQUEST', [
            'method_name'     => $data->gatewayCurrency()->name,
            'method_currency' => $data->method_currency,
            'method_amount'   => showAmount($data->final_amount),
            'amount'          => showAmount($data->amount),
            'charge'          => showAmount($data->charge),
            'rate'            => showAmount($data->rate),
            'trx'             => $data->trx,
        ]);

        $notify[] = ['success', 'You have payment request has been taken'];
        return to_route('user.deposit.history')->withNotify($notify);
    }

}

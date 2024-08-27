<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\Item;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserController extends Controller {

   


    public function home() {
        return to_route('home');
    }

    public function depositHistory(Request $request) {
        $pageTitle = 'Payment History';
        $deposits  = auth()->user()->deposits()->searchable(['trx'])->with(['gateway', 'subscription' => function ($query) {
            $query->with('plan', 'item');
        }])->orderBy('id', 'desc')->paginate(getPaginate());
        return view($this->activeTemplate . 'user.deposit_history', compact('pageTitle', 'deposits'));
    }

    public function watchHistory() {
        $pageTitle = 'Watch History';
        $histories = History::where('user_id', auth()->id());
        $total     = $histories->count();
        if (request()->lastId) {
            $histories = $histories->where('id', '<', request()->lastId);
        }
        $histories = $histories->with('item', 'episode.item')->orderBy('id', 'desc')->take(20)->get();
        $lastId    = @$histories->last()->id;

        if (request()->lastId) {
            if ($histories->count()) {
                $data = view($this->activeTemplate . 'user.watch.fetch_history', compact('histories'))->render();
                return response()->json([
                    'data'   => $data,
                    'lastId' => $lastId,
                ]);
            }
            return response()->json([
                'error' => 'History not more yet',
            ]);
        }
        return view($this->activeTemplate . 'user.watch.history', compact('pageTitle', 'histories', 'lastId', 'total'));
    }

    public function removeHistory(Request $request, $id) {
        History::where('id', $id)->where('user_id', auth()->id())->delete();
        $notify[] = ['success', 'Item removed from history list.'];
        return back()->withNotify($notify);
    }

    public function attachmentDownload($fileHash) {
        $filePath  = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $general   = gs();
        $title     = slug($general->site_name) . '- attachments.' . $extension;
        $mimetype  = mime_content_type($filePath);
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }

    public function userData() {
        $user = auth()->user();
        if ($user->profile_complete == Status::YES) {
            return to_route('user.home');
        }
        $pageTitle = 'User Data';
        return view($this->activeTemplate . 'user.user_data', compact('pageTitle', 'user'));
    }
   
    public function userDataSubmit(Request $request) {
        $user = auth()->user();
        if ($user->profile_complete == Status::YES) {
            return to_route('user.home');
        }
        $request->validate([
            'firstname' => 'required',
            'lastname'  => 'required',
        ]);
        $user->firstname = $request->firstname;
        $user->lastname  = $request->lastname;
        $user->address   = [
            'country' => @$user->address->country,
            'address' => $request->address,
            'state'   => $request->state,
            'zip'     => $request->zip,
            'city'    => $request->city,
        ];
        $user->profile_complete = Status::YES;
        $user->save();

        $notify[] = ['success', 'Registration process completed successfully'];
        return to_route('user.home')->withNotify($notify);

    }

    public function subscribePlan($id) {
        $plan = Plan::where('status', 1)->find($id);
        if (!$plan) {
            $notify[] = ['error', 'Plan not found'];
            return back()->withNotify($notify);
        }

        $user = auth()->user();

        if ($user->exp > now()) {
            $notify[] = ['error', 'You have already purchased a plan'];
            return back()->withNotify($notify);
        }

        $subscription = $this->insertSubscription(planId: $plan->id, duration: $plan->duration);
        session()->put('subscription_id', $subscription->id);
        return redirect()->route('user.deposit.index');
    }

    public function subscribeVideo($id) {

        $item = Item::active()->hasVideo()->where('id', $id)->with('episodes')->first();
        if (!$item) {
            $notify[] = ['error', 'Item not found'];
            return back()->withNotify($notify);
        }

        $existItem = Subscription::active()->where('user_id', auth()->id())->where('item_id', $item->id)->whereDate('expired_date', '>', now())->exists();
        if ($existItem) {
            $notify[] = ['error', 'Already rented this item'];
            return back()->withNotify($notify);
        }

        $subscription = $this->insertSubscription(itemId: $item->id, duration: $item->rental_period);
        session()->put('subscription_id', $subscription->id);
        return redirect()->route('user.deposit.index');
    }

    private function insertSubscription($planId = 0, $itemId = 0, $duration = null) {
        $user = auth()->user();

        if ($planId) {
            $pendingPayment = $user->deposits()->where('status', Status::PAYMENT_PENDING)->count();
            if ($pendingPayment > 0) {
                throw ValidationException::withMessages(['error' => 'Already 1 payment in pending. Please Wait']);
            }
        }

        $subscription = Subscription::active()->where('user_id', auth()->id())->where('item_id', $itemId)->first();
        if (!$subscription) {
            $subscription          = new Subscription();
            $subscription->user_id = $user->id;
            $subscription->plan_id = $planId;
            $subscription->item_id = $itemId;
        }
        $subscription->expired_date = now()->addDays($duration);
        $subscription->status       = Status::DISABLE;
        $subscription->save();
        return $subscription;
    }

    public function rentedItem() {
        $pageTitle   = 'Rented Items';
        $rentedItems = Subscription::active()->where('item_id', '!=', 0)->where('user_id', auth()->id());
        $total       = (clone $rentedItems)->count();
        if (request()->lastId) {
            $rentedItems = $rentedItems->where('id', '<', request()->lastId);
        }

        $rentedItems = $rentedItems->with('item')->orderBy('id', 'desc')->take(20)->get();
        $lastId      = @$rentedItems->last()->id;
        if (request()->lastId) {
            if ($rentedItems->count()) {
                $data = view($this->activeTemplate . 'user.rent.fetch_item', compact('rentedItems'))->render();
                return response()->json([
                    'data'   => $data,
                    'lastId' => $lastId,
                ]);
            }
            return response()->json([
                'error' => 'Item not more yet',
            ]);
        }
        return view($this->activeTemplate . 'user.rent.items', compact('pageTitle', 'rentedItems', 'total', 'lastId'));
    }
}

<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class CheckStatus {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $general            = gs();

        if (Auth::check()) {
            $user = auth()->user();
            if ($user->status && $user->ev && ($user->sv&&$general->sv )) {
                return $next($request);
            } else {
                if ($request->is('api/*')) {
                    $notify[] = 'You need to verify your account first.';
                    return response()->json([
                        'remark'  => 'unverified',
                        'status'  => 'error',
                        'message' => ['error' => $notify],
                        'data'    => [
                            'is_ban'          => $user->status,
                            'email_verified'  => $user->ev,
                            'mobile_verified' => $user->sv,
                        ],
                    ]);
                } else {
                    return to_route('user.authorization');
                }
            }
        }
        abort(403);
    }
}

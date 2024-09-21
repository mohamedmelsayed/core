<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotVerified
{

    public $activeTemplate;

    public function __construct() {
            $this->activeTemplate = activeTemplate();
        

        $className = get_called_class();
    }
    public function handle($request, Closure $next)
    {
        // Check if user is logged in
        if (Auth::check()) {
            $user = $request->user();

            // Check if the user must verify their email and hasn't done so
            if ($user instanceof MustVerifyEmail && !$user->hasVerifiedEmail()) {
                $pageTitle = "Must Verify Email";
                
                // Return a specific view with parameters
                return response()->view($this->activeTemplate . 'user.auth.authorization.must_verify_email', compact('user', 'pageTitle'));
            }
        }

        // Proceed with the next request
        return $next($request);
    }
}

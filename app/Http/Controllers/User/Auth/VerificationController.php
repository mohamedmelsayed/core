<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\UserLogin;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller {
    public function verifyUserX($token) {
        // dd($token);
         $user = User::where('verification_token', $token)
                     ->where('verification_token_expires_at', '>', now())  // Check if the token is not expired
                     ->first();
  
         if (!$user) {
             $notify[] = ['success', 'Invalid or expired verification token.'];
             return to_route('user.login')->withNotify($notify);
             return redirect()->route('login')->withErrors(['message' => 'Invalid or expired verification token.']);
         }
     
         $user->status = 1;  // Mark the user as verified
         $user->ev = 1;  // Mark the user as verified
         $user->verification_token = null;  // Remove the token
         $user->verification_token_expires_at = null;  // Clear the expiration timestamp
         $user->save();
     
         auth()->login($user);
     
         return redirect()->route('user.home')->with('success', 'Your account has been verified and you are now logged in.');
     }

   
}
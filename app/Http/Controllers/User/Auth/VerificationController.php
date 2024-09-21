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


     /**
     * Get the post-verification redirect path.
     *
     * @return string
     */
    protected function redirectTo()
    {

        
        $user = auth()->user();
        // Redirect to different routes based on user roles or other conditions
        if ($user->is_admin) {
            return '/admin/dashboard';
        }

        if (!($user->verification_token_expires_at && $user->verification_token_expires_at>now())) {
            $user->verification_token = Str::random(60);  // Generate a new verification token
            $user->verification_token_expires_at = now()->addHours(6);  // Set token expiration time
            $user->save();  
        }
        
        $verificationUrl = route('verify.mail', ['token' => $user->verification_token]);
        // dd($verificationUrl);
        notify($user, $notifyTemplate, [
            'link' => $verificationUrl,
        ], [$type]);
                    $pageTitle      = 'Verify Email';

        return view($this->activeTemplate . 'user.auth.authorization.verify_email', compact('user', 'pageTitle'));


    }


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
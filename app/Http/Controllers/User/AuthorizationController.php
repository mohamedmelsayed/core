<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthorizationController extends Controller {
    protected function checkCodeValidity($user, $addMin = 2) {
        if (!$user->ver_code_send_at) {
            return false;
        }
        if ($user->ver_code_send_at->addMinutes($addMin) < Carbon::now()) {
            return false;
        }
        return true;
    }
    public function addMobile(Request $request)
    {

        $request->validate([
            'country' => 'required|string',
            'mobile' => 'required|numeric|unique:users,mobile',
            'mobile_code' => 'required|string',
            'country_code' => 'required|string',
        ]);

        $user = auth()->user();
        $user->mobile = $request->mobile;
        $user->country_code = $request->country_code;
        $user->save();
        $notify[] = ['success', 'Mobile number added successfully. Please verify it.'];

        return to_route('user.home')->withNotify($notify);
    }

    public function authorizeForm() {
        $user = auth()->user();
        // Check if the user is banned
        if (!$user->status&&$user->ev) {
            $pageTitle = 'Banned';
            $type      = 'ban';
        } 
        // Check if the email is not verified
        elseif (!$user->ev) {
            $type           = 'email';
            $pageTitle      = 'Verify Email';
            $notifyTemplate = 'EVER_LINK';
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
            $type='verify_email';
        return view($this->activeTemplate . 'user.auth.authorization.' . $type, compact('user', 'pageTitle'));

        } 
  //      Check if the user has a registered mobile number
        elseif (!$user->mobile) {
       

            // Redirect to mobile verification page or allow skip
            $pageTitle  = "Add Mobile Number";
            $info       = json_decode(json_encode(getIpInfo()), true);
            $mobileCode = @implode(',', $info['code']);
            $countries  = json_decode(file_get_contents(resource_path('views/partials/country.json')));
           return view($this->activeTemplate . 'user.auth.mobile_verification', compact('user', 'mobileCode', 'countries','pageTitle'));
        } 
        // Check if the mobile number is verified
        elseif (!$user->sv) {
            $type           = 'sms';
            $pageTitle      = 'Verify Mobile Number';
            $notifyTemplate = 'SVER_CODE';
        } 
        else {
            
            return redirect()->route('user.home');

        }
    
        // Generate a verification code if not banned and the verification is required  
        if (!($this->checkCodeValidity($user) && ($type != 'ban')&&$user->mobile!=null)) {
            $user->ver_code         = verificationCode(6);
            $user->ver_code_send_at = Carbon::now();
            $user->save();
            notify($user, $notifyTemplate, [
                'code' => $user->ver_code,
            ], [$type]);
        }
        
        return view($this->activeTemplate . 'user.auth.authorization.' . $type, compact('user', 'pageTitle'));
    }
    
    public function sendVerifyCode($type) {
        $user = auth()->user();

        if ($this->checkCodeValidity($user)) {
            $targetTime = $user->ver_code_send_at->addMinutes(2)->timestamp;
            $delay      = $targetTime - time();
            throw ValidationException::withMessages(['resend' => 'Please try after ' . $delay . ' seconds']);
        }

        $user->ver_code         = verificationCode(6);
        $user->ver_code_send_at = Carbon::now();
        $user->save();

        if ($type == 'email') {
            $type           = 'email';
            $notifyTemplate = 'EVER_CODE';
        } else {
            $type           = 'sms';
            $notifyTemplate = 'SVER_CODE';
        }

        notify($user, $notifyTemplate, [
            'code' => $user->ver_code,
        ], [$type]);

        $notify[] = ['success', 'Verification code sent successfully'];
        return back()->withNotify($notify);
    }

    public function emailVerification(Request $request) {
        $request->validate([
            'code' => 'required',
        ]);

        $user = auth()->user();

        if ($user->ver_code == $request->code) {
            $user->ev               = Status::VERIFIED;
            $user->ver_code         = null;
            $user->ver_code_send_at = null;
            $user->save();
            return to_route('user.home');
        }
        throw ValidationException::withMessages(['code' => 'Verification code didn\'t match!']);
    }

    public function mobileVerification(Request $request) {
        $request->validate([
            'code' => 'required',
        ]);

        $user = auth()->user();
        if ($user->ver_code == $request->code) {
            $user->sv               = Status::VERIFIED;
            $user->ver_code         = null;
            $user->ver_code_send_at = null;
            $user->save();
            return to_route('user.home');
        }
        throw ValidationException::withMessages(['code' => 'Verification code didn\'t match!']);
    }

}

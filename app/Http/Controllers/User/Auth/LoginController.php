<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use App\Models\UserDevice;
use App\Models\UserLogin;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller {

    use AuthenticatesUsers;

    protected $username;

    public function __construct() {
        parent::__construct();
        $this->middleware('guest')->except('logout');
        $this->username = $this->findUsername();
    }

    public function showLoginForm() {
        $pageTitle = "Login";
        return view($this->activeTemplate . 'user.auth.login', compact('pageTitle'));
    }

    public function login(Request $request) {
        $notifyTemplate = 'EVER_LINK';

        $this->validateLogin($request);
    
        $request->session()->regenerateToken();
    
        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }
    
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }
    
        if ($this->attemptLogin($request)) {
            $user = $request->user();
    
            if (!$user->ev) {

                if ($user->verification_token_expires_at && $user->verification_token_expires_at>now()) {
                    $verificationUrl = route('verify.mail', ['token' => $user->verification_token]);
                    // dd($verificationUrl);
                    notify($user, $notifyTemplate, [
                        'link' => $verificationUrl,
                    ], ['email']);
    
                    Auth::logout();
                    $pageTitle="Must verify Email";
                    return view($this->activeTemplate . 'user.auth.authorization.verify_email' , compact('user', 'pageTitle'));
                } else {
                    // Regenerate the activation token if the previous one has expired
                    $user->status = 1;  // Set the status to 0 (not verified)
                    $user->verification_token = Str::random(60);  // Generate a new verification token
                    $user->verification_token_expires_at = now()->addHours(6);  // Set token expiration time
                    $user->save();

                    $verificationUrl = route('verify.mail', ['token' => $user->verification_token]);
                    // dd($verificationUrl);
                    notify($user, $notifyTemplate, [
                        'link' => $verificationUrl,
                    ], ['email']);
    
                    Auth::logout();
                    $pageTitle="Must verify Email";
                    return view($this->activeTemplate . 'user.auth.authorization.verify_email' , compact('user', 'pageTitle'));

                    // Optional: Send verification code via SMS if enabled
                    if ($user->mobile && env('SEND_SMS')) {
                        $user->ver_code = verificationCode(6);
                        $user->ver_code_send_at = Carbon::now();
                        $user->save();
                        sendSms($user->mobile, $user->ver_code);
                    }
    
                    Auth::logout();
                    $notify[] = 'A new verification link has been sent to your email. Please verify your account.';
                    return back()->withNotify($notify);
                }
            }
    
            return $this->sendLoginResponse($request);
        }
    
        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);
    }
    
    public function findUsername() {
        $login = request()->input('username');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$fieldType => $login]);
        return $fieldType;
    }

    public function username() {
        return $this->username;
    }

    protected function validateLogin(Request $request) {

        $request->validate([
            $this->username() => 'required|string',
            'password'        => 'required|string',
        ]);
    }

    public function logout() {
        $this->guard()->logout();

        request()->session()->invalidate();

        $notify[] = ['success', 'You have been logged out.'];
        return to_route('user.login')->withNotify($notify);
    }

    public function authenticated(Request $request, $user) {
        $ip      = getRealIP();
        $general = gs();
        if ($general->device_limit && $user->plan) {
            $userDevices     = UserDevice::where('user_id', $user->id)->distinct()->pluck('device_id')->toArray();
            $currentDeviceId = md5($_SERVER['HTTP_USER_AGENT']);

            if (count($userDevices) == @$user->plan->device_limit && !in_array($currentDeviceId, $userDevices)) {
                session()->flush();
                Auth::logout();
                $notify[] = ['error', 'Device limit is over'];
                return to_route('user.login')->withNotify($notify);
            }
            $existDevice = UserDevice::where('user_id', $user->id)->where('device_id', $currentDeviceId)->exists();
            if (!$existDevice) {
                $device            = new UserDevice();
                $device->user_id   = $user->id;
                $device->device_id = $currentDeviceId;
                $device->save();
            }
        }

        $exist     = UserLogin::where('user_ip', $ip)->first();
        $userLogin = new UserLogin();
        if ($exist) {
            $userLogin->longitude    = $exist->longitude;
            $userLogin->latitude     = $exist->latitude;
            $userLogin->city         = $exist->city;
            $userLogin->country_code = $exist->country_code;
            $userLogin->country      = $exist->country;
        } else {
            $info                    = json_decode(json_encode(getIpInfo()), true);
            $userLogin->longitude    = @implode(',', $info['long']);
            $userLogin->latitude     = @implode(',', $info['lat']);
            $userLogin->city         = @implode(',', $info['city']);
            $userLogin->country_code = @implode(',', $info['code']);
            $userLogin->country      = @implode(',', $info['country']);
        }

        $userAgent          = osBrowser();
        $userLogin->user_id = $user->id;
        $userLogin->user_ip = $ip;

        $userLogin->browser = @$userAgent['browser'];
        $userLogin->os      = @$userAgent['os_platform'];
        $userLogin->save();

        if (session()->has('device_token')) {
            $deviceToken = session()->get('device_token');
            $token       = DeviceToken::where('token', $deviceToken)->first();
            if ($token) {
                $token->user_id = auth()->id();
                $token->save();
            }
            session()->forget('device_token');
        }
        return to_route('user.home');
    }

}

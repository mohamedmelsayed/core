<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserLogin;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\UserDevice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
     */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */

    protected $username;

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct() {
        parent::__construct();
        $this->username = $this->findUsername();
    }
    public function login(Request $request) {
        $validator = $this->validateLogin($request);
    
        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }
    
        $credentials = request([$this->username, 'password']);
        if (!Auth::attempt($credentials)) {
            $response[] = 'Unauthorized user';
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $response],
            ]);
        }
    
        $user = $request->user();
    
        // Check if user is not verified
        if (!$user->ev) {
            // Regenerate the activation token
            $user->activation_token = Str::random(60);
            $user->verification_token_expires_at = now()->addHours(6);  // Set token expiration time

            $user->save();
    
            // Send verification email with the new activation token
            Mail::send('emails.verify', ['token' => $user->verification_token, 'user' => $user], function($message) use ($user) {
                $message->to($user->email);
                $message->subject('Account Verification');
            });
    
            // Optional: Send verification code via SMS
            if ($user->mobile && env('SEND_SMS')) {
                $user->ver_code = verificationCode(6);
                $user->ver_code_send_at = Carbon::now();
                $user->save();
                sendSms($user->mobile, $user->ver_code);
            }
    
            Auth::logout();
            $response[] = 'Account not verified. A new verification email has been sent.';
            return response()->json([
                'remark'  => 'not_verified',
                'status'  => 'error',
                'message' => ['error' => $response],
            ]);
        }
    
        $tokenResult = $user->createToken('auth_token')->plainTextToken;
    
        $this->authenticated($request, $user);
        $response[] = 'Login Successful';
        return response()->json([
            'remark'  => 'login_success',
            'status'  => 'success',
            'message' => ['success' => $response],
            'data'    => [
                'user'         => auth()->user(),
                'access_token' => $tokenResult,
                'token_type'   => 'Bearer',
            ],
        ]);
    }
    

    public function loginOld(Request $request) {
        $validator = $this->validateLogin($request);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $credentials = request([$this->username, 'password']);
        if (!Auth::attempt($credentials)) {
            $response[] = 'Unauthorized user';
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $response],
            ]);
        }

        $user        = $request->user();
        $tokenResult = $user->createToken('auth_token')->plainTextToken;
        
        $general = gs();
        if ($general->device_limit && $user->plan) {
            $userDevices     = UserDevice::where('user_id', $user->id)->distinct()->pluck('device_id')->toArray();
            $currentDeviceId = md5($_SERVER['HTTP_USER_AGENT']);
            if (count($userDevices) == @$user->plan->device_limit && !in_array($currentDeviceId, $userDevices)) {
                session()->flush();
                Auth::logout();
                $response = 'Device limit is over';
                return response()->json([
                    'remark'  => 'device_limit_error',
                    'status'  => 'error',
                    'message' => ['error' => $response],
                ]);
            }
            $device            = new UserDevice();
            $device->user_id   = $user->id;
            $device->device_id = $currentDeviceId;
            $device->save();
        }
        
        $this->authenticated($request, $user);
        $response[] = 'Login Successful';
        return response()->json([
            'remark'  => 'login_success',
            'status'  => 'success',
            'message' => ['success' => $response],
            'data'    => [
                'user'         => auth()->user(),
                'access_token' => $tokenResult,
                'token_type'   => 'Bearer',
            ],
        ]);

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
        $validation_rule = [
            $this->username() => 'required|string',
            'password'        => 'required|string',
        ];

        $validate = Validator::make($request->all(), $validation_rule);
        return $validate;

    }

    public function logout() {
        auth()->user()->tokens()->delete();

        $notify[] = 'Logout Successful';
        return response()->json([
            'remark'  => 'logout',
            'status'  => 'ok',
            'message' => ['success' => $notify],
        ]);
    }

    public function authenticated(Request $request, $user) {
        $ip        = getRealIP();
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
    }

}

<?php

namespace App\Http\Controllers\User\Auth;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\DeviceToken;
use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller {

    use RegistersUsers;

    public function __construct() {
        parent::__construct();
        $this->middleware('guest');
        $this->middleware('registration.status')->except('registrationNotAllowed');
    }

    public function showRegistrationForm() {
        $pageTitle  = "Register";
        $info       = json_decode(json_encode(getIpInfo()), true);
        $mobileCode = @implode(',', $info['code']);
        $countries  = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        return view($this->activeTemplate . 'user.auth.register', compact('pageTitle', 'mobileCode', 'countries'));
    }

    protected function validator(array $data) {
        $general            = gs();
        $passwordValidation = Password::min(6);
        if ($general->secure_password) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }
        $agree = 'nullable';
        if ($general->agree) {
            $agree = 'required';
        }
        $countryData  = (array) json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes  = implode(',', array_column($countryData, 'dial_code'));
        $countries    = implode(',', array_column($countryData, 'country'));
        $validate     = Validator::make($data, [
            'email'        => 'required|string|email|unique:users',
            'password'     => ['required', 'confirmed', $passwordValidation],
            'username'     => 'required|unique:users|min:6',
            'captcha'      => 'sometimes|required',
            // 'mobile'       => 'nullable|regex:/^([0-9]*)$/', // Optional mobile field
            // 'mobile_code'  => 'nullable|in:' . $mobileCodes, // Optional mobile code field
            // 'country_code' => 'nullable|in:' . $countryCodes, // Optional country code field
            // 'country'      => 'nullable|in:' . $countries, // Optional country field
            'agree'        => $agree,
        ]);
        return $validate;
    }
    

    public function register(Request $request) {
        $this->validator($request->all())->validate();

        $request->session()->regenerateToken();

        if (preg_match("/[^a-z0-9_]/", trim($request->username))) {
            $notify[] = ['info', 'Username can contain only small letters, numbers and underscore.'];
            $notify[] = ['error', 'No special character, space or capital letters in username.'];
            return back()->withNotify($notify)->withInput($request->all());
        }

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        // $exist = User::where('mobile', $request->mobile_code . $request->mobile)->first();
        // if ($exist) {
        //     $notify[] = ['error', 'The mobile number already exists'];
        //     return back()->withNotify($notify)->withInput();
        // }

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        return $this->registered($request, $user)
        ?: redirect($this->redirectPath());
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return \App\User
     */
    protected function create(array $data) {
        $general = gs();
    
        // User Create
        $user               = new User();
        $user->email        = strtolower($data['email']);
        $user->password     = Hash::make($data['password']);
        $user->username     = $data['username'];
        // $user->country_code = isset($data['country_code']) ? $data['country_code'] : null; // Optional country code
        // $user->mobile       = isset($data['mobile_code']) && isset($data['mobile']) ? $data['mobile_code'] . $data['mobile'] : null; // Optional mobile
        $user->address      = [
            'address' => '',
            'state'   => '',
            'zip'     => '',
            'country' => isset($data['country']) ? $data['country'] : null, // Optional country
            'city'    => '',
        ];
        $user->status = 0;  // Initially set the status to 0 (not verified)
        $user->verification_token = Str::random(60);  // Generate verification token
        $user->save();
    
        // Send verification email
        Mail::send('emails.verify', ['token' => $user->verification_token], function($message) use ($user) {
            $message->to($user->email);
            $message->subject('Account Verification');
        });
    
        return $user;
    }
    
    public function checkUser(Request $request) {
        $exist['data'] = false;
        $exist['type'] = null;
        if ($request->email) {
            $exist['data'] = User::where('email', $request->email)->exists();
            $exist['type'] = 'email';
        }
        if ($request->username) {
            $exist['data'] = User::where('username', $request->username)->exists();
            $exist['type'] = 'username';
        }
        return response($exist);
    }
    
    public function registered() {
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

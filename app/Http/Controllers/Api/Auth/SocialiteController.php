<?php

namespace App\Http\Controllers\Api\Auth;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SocialiteController extends Controller {

    public function socialLogin(Request $request) {

        $userData = User::where('username', $request->id)->first();
        if (!$userData) {
            $emailExists = User::where('email', @$request->email)->exists();
            $oldUserData = User::where('email', @$request->email)->first();
             $tokenResult = $oldUserData->createToken('auth_token')->plainTextToken;

            if ($emailExists) {
                $response[] = 'login_success';
                return response()->json([
                    'remark'  => 'login_success',
                    'status'  => 'success',
                    'message' => ['success' => $response],
                    'data'    => [
                        'user'         => $oldUserData,
                        'access_token' => $tokenResult,
                        'token_type'   => 'Bearer',
                    ],
                ]);
            }
            $userData = $this->createUser($request);
        }

        $tokenResult = $userData->createToken('auth_token')->plainTextToken;
        $this->loginLog($userData);
        $response[] = 'Login Successful';
        return response()->json([
            'remark'  => 'login_success',
            'status'  => 'success',
            'message' => ['success' => $response],
            'data'    => [
                'user'         => $userData,
                'access_token' => $tokenResult,
                'token_type'   => 'Bearer',
            ],
        ]);
    }

    private function createUser($request) {
        $general  = gs();
        $password = getTrx(8);

        $firstName = preg_replace('/\W\w+\s*(\W*)$/', '$1', $request->name);

        $pieces   = explode(' ', $request->name);
        $lastName = array_pop($pieces);

        if ($pieces) {
            $firstName = $pieces[0];
        }
        if ($pieces) {
            $lastName = $lastName;
        }

        $newUser           = new User();
        $newUser->username = $request->id;

        $newUser->email = $request->email;

        $newUser->password  = Hash::make($password);
        $newUser->firstname = $firstName;
        $newUser->lastname  = $lastName;

        $newUser->address = [
            'address' => '',
            'state'   => '',
            'zip'     => '',
            'country' => '',
            'city'    => '',
        ];

        $newUser->status   = Status::VERIFIED;
        $newUser->ev       = 1;
        $newUser->sv       = 1;
        $newUser->login_by = $request->provider;

        $newUser->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $newUser->id;
        $adminNotification->title     = 'New member registered';
        $adminNotification->click_url = urlPath('admin.users.detail', $newUser->id);
        $adminNotification->save();

        return $newUser;
    }

    private function loginLog($user) {
        //Login Log Create
        $ip        = getRealIP();
        $exist     = UserLogin::where('user_ip', $ip)->first();
        $userLogin = new UserLogin();

        //Check exist or not
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

<?php

use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::namespace('Api')->name('api.')->group(function () {

    Route::get('general-setting', function () {
        $general  = gs();
        $notify[] = 'General setting data';

        $timezones       = json_decode(file_get_contents(resource_path('views/admin/partials/timezone.json')));
        $currentTimezone = array_search(config('app.timezone'), $timezones);
        $timezone        = @$timezones[$currentTimezone] ?? 'UTC';

        return response()->json([
            'remark'  => 'general_setting',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'general_setting' => $general,
                'timezone'        => $timezone,
            ],
        ]);
    });

    Route::get('get-countries', function () {
        $c        = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $notify[] = 'General setting data';
        foreach ($c as $k => $country) {
            $countries[] = [
                'country'      => $country->country,
                'dial_code'    => $country->dial_code,
                'country_code' => $k,
            ];
        }
        return response()->json([
            'remark'  => 'country_data',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'countries' => $countries,
            ],
        ]);
    });

    Route::namespace('Auth')->group(function () {
        Route::post('login', 'LoginController@login');
        Route::post('register', 'RegisterController@register');
        Route::post('social-login', 'SocialiteController@socialLogin');

        Route::controller('ForgotPasswordController')->group(function () {
            Route::post('password/email', 'sendResetCodeEmail')->name('password.email');
            Route::post('password/verify-code', 'verifyCode')->name('password.verify.code');
            Route::post('password/reset', 'reset')->name('password.update');
        });
    });

    Route::get('dashboard', 'UserController@dashboard');

    Route::middleware('auth:sanctum')->group(function () {

        //authorization
        Route::controller('AuthorizationController')->group(function () {
            Route::get('authorization', 'authorization')->name('authorization');
            Route::get('resend-verify/{type}', 'sendVerifyCode')->name('send.verify.code');
            Route::post('verify-email', 'emailVerification')->name('verify.email');
            Route::post('verify-mobile', 'mobileVerification')->name('verify.mobile');
            Route::post('verify-g2fa', 'g2faVerification')->name('go2fa.verify');
        });

        Route::middleware(['check.status'])->group(function () {
            Route::post('user-data-submit', 'UserController@userDataSubmit')->name('data.submit');
            Route::post('get/device/token', 'UserController@getDeviceToken')->name('get.device.token');

            Route::middleware('registration.complete')->group(function () {
                Route::controller('UserController')->group(function () {
                    Route::get('user-info', 'userInfo');
                    Route::any('deposit/history', 'depositHistory')->name('deposit.history');
                    Route::get('rented/items', 'rentedItem');
                });

                //Profile setting
                Route::controller('UserController')->group(function () {
                     Route::get('lang/{lang}', 'changeLanguage')->name('lang');
                    Route::post('profile-setting', 'submitProfile');
                    Route::post('change-password', 'submitPassword');
                    Route::get('validate-purchase', 'validatePurchase');
                });

                Route::controller('PusherController')->group(function () {
                    Route::post('authenticationApp', 'authenticationApp');
                });

                Route::controller('WatchPartyController')->prefix('party')->group(function () {
                    Route::post('create', 'create');
                    Route::get('room/{code}/{guestId?}', 'room');
                    Route::post('join/request', 'joinRequest');
                    Route::post('request/accept/{id?}', 'requestAccept');
                    Route::post('request/reject/{id?}', 'requestReject');
                    Route::post('send/message', 'sendMessage');
                    Route::post('player/setting', 'playerSetting');
                    Route::post('status/{id}', 'status');
                    Route::post('cancel/{id}', 'cancel');
                    Route::post('leave/{id}/{user_id}', 'leave');
                    Route::post('disabled/{id}', 'disabled');
                    Route::get('history', 'history');
                    Route::post('reload', 'reload');
                });

                // Payment
                Route::controller('PaymentController')->group(function () {
                    Route::get('deposit/methods', 'methods')->name('deposit');
                    Route::post('deposit/insert', 'depositInsert')->name('deposit.insert');
                    Route::get('deposit/confirm', 'depositConfirm')->name('deposit.confirm');
                    Route::get('deposit/manual', 'manualDepositConfirm')->name('deposit.manual.confirm');
                    Route::post('deposit/manual', 'manualDepositUpdate')->name('deposit.manual.update');
                });

                //UserController
                Route::controller('UserController')->group(function () {
                    Route::get('subscribe', 'subscribe');
                    Route::get('plans/{type?}', 'plans');

                    Route::post('subscribe-plan', 'subscribePlan');
                    Route::post('purchase-plan', 'purchasePlan');
                    Route::post('purchase-app', 'purchaseFromApp');

                    Route::post('add-wishlist', 'addWishlist');
                    Route::post('remove-wishlist', 'removeWishlist');
                    Route::get('check-wishlist', 'checkWishlist');
                    Route::get('wishlists', 'wishlists');
                    Route::get('history', 'history');
                    Route::get('watch', 'watchVideo');
                    Route::get('play', 'playVideo');
                    Route::post('status', 'status');
                });

            });
        });

        Route::get('logout', 'Auth\LoginController@logout');
    });

    Route::controller('FrontendController')->group(function () {
        Route::get('logo', 'logo');
        Route::get('welcome-info', 'welcomeInfo');
        Route::get('sliders', 'sliders');
        Route::get('live-television', 'liveTelevision');
        Route::get('live-tv/{id?}', 'watchTelevision');

        Route::get('section/featured', 'featured');
        Route::get('section/recent', 'recentlyAdded');
        Route::get('section/latest', 'latestSeries');
        Route::get('section/single', 'single');
        Route::get('section/trailer', 'trailer');
        Route::get('section/free-zone', 'freeZone');
        Route::get('section/rent', 'rent');

        Route::get('movies', 'movies');
        Route::get('episodes', 'episodes');

        Route::get('categories', 'categories');
        Route::get('subcategories', 'subcategories');
        Route::get('sub-category/{id}', 'subCategory')->name('subCategory');

        Route::get('search', 'search');

        Route::get('watch-video', 'watchVideo');
        Route::get('view-audio', 'viewAudio');
        Route::get('stream-list', 'streamlist');
        Route::get('watch-stream', 'watchStream');
        Route::get('list-audios', 'audios');
        Route::get('list-videos', 'listVideo');
        Route::get('play-video', 'playVideo');
        Route::get('play-audio', 'playAudio');
        Route::get('policy-pages', 'policyPages');
        Route::get('language/{code?}', 'language');
        Route::get('pop-up/ads', 'popUpAds');
        Route::get('playlists', 'playlists');
        Route::get('playlist/{id}', 'playlist');



    });

    Route::controller('MediaMetadataController')->group(function () {
        Route::get('/media/metadata', 'getMediaMetadata');

    });





});

<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\User\Auth\VerificationController;
use Illuminate\Support\Facades\Route;
Route::get('verify-email/{token}', [VerificationController::class, 'verifyUserX'])->name('verify.mail');

Route::namespace('User\Auth')->name('user.')->group(function () {


    Route::controller('SocialiteController')->group(function () {
        Route::get('social-login/{provider}', 'socialLogin')->name('social.login');
        Route::get('login/callback/{provider}', 'callback')->name('social.login.callback');
    });
    Route::controller('LoginController')->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
        Route::get('logout', 'logout')->middleware('auth')->name('logout');
    });
    Route::controller('RegisterController')->group(function () {

        Route::get('register', 'showRegistrationForm')->name('register');
        Route::post('register', 'register')->middleware('registration.status');
        Route::post('check-mail', 'checkUser')->name('checkUser');

    });

    Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
        Route::get('reset', 'showLinkRequestForm')->name('request');
        Route::post('email', 'sendResetCodeEmail')->name('email');
        Route::get('code-verify', 'codeVerify')->name('code.verify');
        Route::post('verify-code', 'verifyCode')->name('verify.code');
    });
    Route::controller('ResetPasswordController')->group(function () {
        Route::post('password/reset', 'reset')->name('password.update');
        Route::get('password/reset/{token}', 'showResetForm')->name('password.reset');
    });
});

Route::middleware('auth')->name('user.')->group(function () {
    Route::namespace('User')->controller('AuthorizationController')->group(function () {
        Route::get('authorization', 'authorizeForm')->name('authorization');
        Route::get('resend-verify/{type}', 'sendVerifyCode')->name('send.verify.code');
        Route::post('verify-email', 'emailVerification')->name('verify.email');
        Route::post('verify-mobile', 'mobileVerification')->name('verify.mobile');
        Route::post('add-mobile', 'addMobile')->name('add.mobile');

        
    });
    Route::middleware(['check.status'])->group(function () {
        Route::get('user-data', 'User\UserController@userData')->name('data');
        Route::post('user-data-submit', 'User\UserController@userDataSubmit')->name('data.submit');
        Route::middleware('registration.complete')->namespace('User')->group(function () {
            Route::controller('UserController')->group(function () {
                Route::get('dashboard', 'home')->name('home');
                Route::any('payment/history', 'depositHistory')->name('deposit.history');
                Route::get('watch/history', 'watchHistory')->name('watch.history');
                Route::post('remove/history/{id}', 'removeHistory')->name('remove.history');
                Route::get('attachment-download/{fil_hash}', 'attachmentDownload')->name('attachment.download');

                Route::post('subscribe/plan/{id}', 'subscribePlan')->name('subscribe.plan');
                Route::post('subscribe/video/{id}', 'subscribeVideo')->name('subscribe.video');
                Route::get('rented/items', 'rentedItem')->name('rented.item');
            });
            Route::controller('ProfileController')->group(function () {
                Route::get('profile-setting', 'profile')->name('profile.setting');
                Route::post('profile-setting', 'submitProfile');
                Route::get('change-password', 'changePassword')->name('change.password');
                Route::post('change-password', 'submitPassword');
            });
            Route::controller('WishlistController')->name('wishlist.')->group(function () {
                Route::get('wishlist', 'wishlist')->name('index');
                Route::post('wishlist/remove/{id}', 'wishlistRemove')->name('remove');
            });

            Route::middleware('watch.party')->controller('WatchPartyController')->prefix('watch/party')->name('watch.party.')->group(function () {
                Route::post('create', 'create')->name('create');
                Route::get('room/{code}/{guestId?}', 'room')->name('room');
                Route::post('join/request', 'joinRequest')->name('join.request');
                Route::post('request/accept/{id?}', 'requestAccept')->name('request.accept');
                Route::post('request/reject/{id?}', 'requestReject')->name('request.reject');
                Route::post('send/message', 'sendMessage')->name('send.message');
                Route::post('player/setting', 'playerSetting')->name('player.setting');
                Route::post('status/{id}', 'status')->name('status');
                Route::post('cancel/{id}', 'cancel')->name('cancel');
                Route::post('leave/{id}/{user_id}', 'leave')->name('leave');
                Route::post('disabled/{id}', 'disabled')->name('disabled');
                Route::get('history', 'history')->name('history');
                Route::post('reload', 'reload')->name('reload');

            });
        });
        Route::middleware('registration.complete')->prefix('payment')->name('deposit.')->controller('Gateway\PaymentController')->group(function () {
            Route::any('/', 'deposit')->name('index');
            Route::post('insert', 'depositInsert')->name('insert');
            Route::get('confirm/{type?}', 'depositConfirm')->name('confirm');
            Route::get('manual/{type?}', 'manualDepositConfirm')->name('manual.confirm');
            Route::post('manual', 'manualDepositUpdate')->name('manual.update');
        });
    });
});

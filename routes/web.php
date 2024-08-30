<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\User\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::post('pusher/auth/{socketId}/{channelName}', 'SiteController@pusher')->name('pusher');

Route::get('/clear', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});

Route::get('lang/{lang}', [UserController::class,'changeLanguage'])->name('change-Lang');

// User Support Ticket
Route::controller('TicketController')->prefix('ticket')->name('ticket.')->group(function () {
    Route::get('/', 'supportTicket')->name('index');
    Route::get('new', 'openSupportTicket')->name('open');
    Route::post('create', 'storeSupportTicket')->name('store');
    Route::get('view/{ticket}', 'viewTicket')->name('view');
    Route::post('reply/{ticket}', 'replyTicket')->name('reply');
    Route::post('close/{ticket}', 'closeTicket')->name('close');
    Route::get('download/{ticket}', 'ticketDownload')->name('download');
});

Route::get('app/deposit/confirm/{hash}', 'Gateway\PaymentController@appDepositConfirm')->name('deposit.app.confirm');

//Wishlist
Route::controller('SiteController')->name('wishlist.')->prefix('wishlist')->group(function () {
    Route::post('add', 'addWishlist')->name('add');
    Route::post('remove', 'removeWishlist')->name('remove');
});

Route::controller('SiteController')->group(function () {
    Route::get('cron', 'cron')->name('cron');

    Route::get('live-stream', 'watchLive')->name('watch.live');
    Route::get('live-tv', 'liveTelevision')->name('live.tv');
    Route::get('live-tv/{id?}', 'watchTelevision')->name('watch.tv');

    Route::get('/get/section', 'getSection')->name('get.section');
    Route::get('watch-video/{slug}/{episode_id?}', 'watchVideo')->name('watch');
    Route::get('preview-audio/{slug}/{episode_id?}', 'previewAudio')->name('preview.audio');


    Route::get('category/{id}', 'category')->name('category');
    Route::get('sub-category/{id}', 'subCategory')->name('subCategory');
    Route::get('search', 'search')->name('search');

    Route::get('load-more', 'loadMore')->name('loadmore.load_data');

    Route::get('company-policy/{id}/{slug}', 'policy')->name('policies');
    Route::get('links/{id}/{slug}', 'links')->name('links');
    Route::post('add-click', 'addClick')->name('add.click');
    Route::post('subscribe', 'subscribe')->name('subscribe');

    Route::get('subscribe', 'subscription')->name('subscription');

    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'contactSubmit');
    Route::get('/change/{lang?}', 'changeLanguage')->name('lang');

    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');

    Route::get('/cookie/accept', 'cookieAccept')->name('cookie.accept');

    Route::get('placeholder-image/{size}', 'placeholderImage')->name('placeholder.image');

    Route::post('/device/token', 'storeDeviceToken')->name('store.device.token');

    Route::get('/', 'index')->name('home');
});


Route::get('verify/{token}', [RegisterController::class, 'verifyUser'])->name('user.verify');
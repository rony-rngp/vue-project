<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

//Route::get('/', function () {
//    return view('welcome');
//});

Route::inertia('/', 'frontend/Home');
Route::inertia('/about', 'About');
Route::inertia('/contact', 'Contact');

Route::match(['get', 'post'],'/login', [\App\Http\Controllers\Frontend\AuthController::class, 'login'])->name('login');
Route::match(['get', 'post'],'/register', [\App\Http\Controllers\Frontend\AuthController::class, 'register'])->name('register');
Route::match(['get', 'post'], 'email-verify', [\App\Http\Controllers\Frontend\AuthController::class, 'email_verify'])->name('email_verify');
Route::match(['get', 'post'], 'phone-verify', [\App\Http\Controllers\Frontend\AuthController::class, 'phone_verify'])->name('phone_verify');
Route::post('logout', [\App\Http\Controllers\Frontend\AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::post('resend-otp', [\App\Http\Controllers\Frontend\AuthController::class, 'resend_otp'])->name('resend_otp');

//users
Route::group(['prefix' => 'user', 'as' => 'user.', 'middleware' => ['auth', 'user']], function (){
    Route::get('dashboard', [\App\Http\Controllers\Frontend\UserController::class, 'dashboard'])->name('dashboard');
});

//admin
Route::group(['prefix' => 'admin', 'as' => 'admin.'], function (){

    Route::middleware(['auth', 'admin'])->group(function (){
      Route::get('dashboard', [\App\Http\Controllers\Backend\AuthController::class, 'dashboard'])->name('dashboard');

      Route::get('sip', [\App\Http\Controllers\Backend\AuthController::class, 'sip'])->name('sip');

      //user routes
      Route::resource('users', \App\Http\Controllers\Backend\UserController::class);

      //number list
        Route::resource('number-list', \App\Http\Controllers\Backend\NumberListController::class);

        //number list
        Route::resource('voice-file', \App\Http\Controllers\Backend\VoiceFileController::class);
        Route::post('voice-file/{id}', [\App\Http\Controllers\Backend\VoiceFileController::class, 'update'])->name('admin.voice-file.update');

        //campaigns code
        Route::resource('campaigns', \App\Http\Controllers\Backend\CampaignController::class);

        Route::post('campaigns/{campaign}/launch', [\App\Http\Controllers\Backend\CampaignController::class, 'launch'])->name('campaigns.launch');
        Route::post('campaigns/{campaign}/pause', [\App\Http\Controllers\Backend\CampaignController::class, 'pause'])->name('campaigns.pause');
        Route::post('campaigns/{campaign}/resume', [\App\Http\Controllers\Backend\CampaignController::class, 'resume'])->name('campaigns.resume');

        Route::controller(\App\Http\Controllers\Backend\ContactController::class)->group(function (){
            Route::get('contacts-info-api/{caller}', 'contact_info_api')->name('contacts.caller_api');
            Route::post('contacts-store-api', 'contact_store_api')->name('contacts.caller_store_api');
        });

      //settings route
        Route::match(['get', 'post'], '/settings', [\App\Http\Controllers\Backend\SettingController::class, 'settings'])->name('settings');

    });

});

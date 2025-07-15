<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

//Route::get('/', function () {
//    return view('welcome');
//});

Route::inertia('/', 'frontend/Home')->name('home_page');
Route::inertia('/about', 'About')->name('about');
Route::inertia('/contact', 'Contact')->name('contact');

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

      Route::get('call-history', [\App\Http\Controllers\Backend\AuthController::class, 'sip'])->name('call_history');

      Route::get('search-caller', [\App\Http\Controllers\Backend\AuthController::class, 'search_caller'])->name('search_caller');

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

        //contact api
        Route::controller(\App\Http\Controllers\Backend\ContactController::class)->group(function (){
            Route::get('contacts-info-api/{caller}', 'contact_info_api')->name('contacts.caller_api');
            Route::post('contacts-store-api', 'contact_store_api')->name('contacts.caller_store_api');
        });

        Route::get('/current-caller-info', function (\Illuminate\Http\Request $request) {
            if (!$request->has('callerId')) {
                return redirect('/');
            }
            return inertia('admin/global/CallPage', [
                'callerId' => $request->query('callerId')
            ]);
        })->name('current_caller_info');

        //ticket routes
        Route::get('tickets-by-caller/{id}', [\App\Http\Controllers\Backend\TicketController::class, 'getCallerTicketList'])->name('getCallerTicketList');
        Route::get('tickets-details/{id}', [\App\Http\Controllers\Backend\TicketController::class, 'getTicketDetails'])->name('getTicketDetails');
        Route::post('tickets-store', [\App\Http\Controllers\Backend\TicketController::class, 'ticketStore'])->name('ticketStore');
        Route::post('tickets-status-update', [\App\Http\Controllers\Backend\TicketController::class, 'updateTicketStatus'])->name('updateTicketStatus');
        Route::get('tickets-assign/{id}', [\App\Http\Controllers\Backend\TicketController::class, 'assignTicket'])->name('assignTicket');
        Route::resource('tickets', \App\Http\Controllers\Backend\TicketController::class);

        //airtable lead
        Route::get('leads', [\App\Http\Controllers\Backend\LeadController::class, 'index'])->name('leads.index');

        Route::controller(\App\Http\Controllers\Backend\ConversationController::class)->group(function (){
           Route::get('conversations', 'index')->name('conversation.index');
        });

        //conversation
        Route::controller(\App\Http\Controllers\Backend\ConversationController::class)->group(function (){
            Route::get('conversations', 'index')->name('conversations.index');
            Route::get('conversations/show/{id}', 'show')->name('conversations.show');
            Route::delete('conversations/destroy/{id}', 'destroy')->name('conversations.destroy');
        });

        Route::controller(\App\Http\Controllers\Backend\ContactController::class)->group(function (){
            Route::get('contacts', 'index')->name('contacts.index');
            Route::get('contacts/create', 'create')->name('contacts.create');
            Route::post('contacts/create', 'store')->name('contacts.store');
            Route::get('contacts/edit/{id}', 'edit')->name('contacts.edit');
            Route::post('contacts/update/{id}', 'update')->name('contacts.update');
            Route::delete('contacts/destroy/{id}', 'destroy')->name('contacts.destroy');
        });


        //settings route
        Route::match(['get', 'post'], '/settings', [\App\Http\Controllers\Backend\SettingController::class, 'settings'])->name('settings');

    });

});


//endpoint
Route::post('handel-conversation', [\App\Http\Controllers\Backend\ConversationController::class, 'handel_conversation']);

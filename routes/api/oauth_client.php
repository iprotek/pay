<?php


use iProtek\Pay\Http\Controllers\Api\AuthController;
use iProtek\Pay\Http\Controllers\AppUserAccountRegistrationController;
use iProtek\Pay\Http\Controllers\AppUserAccountRecoveryController;


Route::middleware(['oauth.client'])->group(function(){        
    Route::middleware(['throttle:3,1'])->post('/login',[AuthController::class, 'app_user_login'])->name('.login');

    Route::get('/client-info',[AuthController::class, 'get_client_info'])->name('.client-info');
    
    Route::get('/client-users',[AuthController::class, 'get_users_by_client'])->name('.get-client-users');

    Route::get('/client-user/{id}',[AuthController::class, 'get_user_by_client'])->name('.get-client-user');

    //This should be emailed first before registered.
    //Route::post('register-app-user',[AuthController::class, 'register_app_user_account'])->name('register-app-user');
    //App Registration
    Route::post('/send-registration', [AppUserAccountRegistrationController::class, 'send_registration'])->name('send-registration');

    //SEND RECOVERY
    Route::post('/send-recovery', [AppUserAccountRecoveryController::class, 'send_recovery'])->name('.send-recovery');

});
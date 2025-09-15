<?php

use Illuminate\Support\Facades\Route; 
use iProtek\Apps\Http\Controllers\AppsController;
use Illuminate\Http\Request;
use iProtek\Dbm\Http\Controllers\DbmController;

include(__DIR__.'/api.php');

Route::middleware(['web'])->group(function(){
     
    Route::prefix('/manage')->middleware(['auth'])->name('manage')->group(function(){
        
        //Route::get('/', [ iProtek\Pay\Http\Controllers\Manage\DashboardController::class, 'index' ]);
        
        //DASHBOARD
        include(__DIR__.'/manage/dashboard.php');

        //DASHBOARD
        include(__DIR__.'/manage/customer.php');

    });

    Route::middleware(['signed'])->group(function(){

        //Registration
        Route::post('/app-user-account-registration', [iProtek\Pay\Http\Controllers\AppUserAccountRegistrationController::class, 'post_app_user_account_registration' ])->name('post-app-user-account-registration');
        Route::get('/app-user-account-registration', [iProtek\Pay\Http\Controllers\AppUserAccountRegistrationController::class, 'get_app_user_account_registration' ])->name('get-app-user-account-registration');

        //Invitation 
        Route::post('/app-user-account-invitation', [iProtek\Pay\Http\Controllers\AppUserAccountInvitationController::class, 'post_app_user_account_invitation' ])->name('post-app-user-account-invitation');
        Route::get('/app-user-account-invitation', [iProtek\Pay\Http\Controllers\AppUserAccountInvitationController::class, 'get_app_user_account_invitation' ])->name('get-app-user-account-invitation');

        //Recovery
        Route::post('/app-user-account-recovery', [iProtek\Pay\Http\Controllers\AppUserAccountRecoveryController::class, 'post_app_user_account_recovery' ])->name('post-app-user-account-recovery');
        Route::get('/app-user-account-recovery', [iProtek\Pay\Http\Controllers\AppUserAccountRecoveryController::class, 'get_app_user_account_recovery' ])->name('get-app-user-account-recovery');

    });
  
});
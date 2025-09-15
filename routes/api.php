<?php

use Illuminate\Support\Facades\Route; 
use iProtek\Core\Http\Controllers\Manage\FileUploadController; 
use iProtek\Core\Http\Controllers\AppVariableController;
use Illuminate\Http\Request;

Route::prefix('api')->middleware('api')->name('api')->group(function(){ 
    
    Route::post('/app-user-register', [AuthController::class, 'app_user_register'])->name('.app-user-login');
    
    //Route::middleware(['oauth.client'])->post('/login', [AuthController::class, 'app_user_login'])->name('.login');
    //Route::middleware('auth:api')->get('/user', function (Request $request) {
        //return $request->user();    
    
    //CLIENT OAUTH
    include(__DIR__.'/api/oauth_client.php');

    //APP USERS
    include(__DIR__.'/api/app_user.php');

    //APP ADMINS
    include(__DIR__.'/api/user_admin.php');

    //APP ACCOUNTS
    include(__DIR__.'/api/app_user_account.php');
 
}); 

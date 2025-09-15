<?php

use Illuminate\Support\Facades\Route;

/**
  * /manage
*/

Route::get('/', [ iProtek\Pay\Http\Controllers\Manage\DashboardController::class, 'index' ]);

Route::prefix('dashboard')->name('.dashboard')->group(function(){
  
  Route::get('/', [ iProtek\Pay\Http\Controllers\Manage\DashboardController::class, 'index' ])->name('.dashboard');
  Route::prefix('client')->name('.clients')->group(function(){

    Route::get('/my-list', [ iProtek\Pay\Http\Controllers\Manage\DashboardController::class, 'passport_client_list' ])->name('.my-list');
    Route::post('/add', [ iProtek\Pay\Http\Controllers\Manage\DashboardController::class, 'add_client' ])->name('.add-client');
    Route::post('/update/{id}', [ iProtek\Pay\Http\Controllers\Manage\DashboardController::class, 'update_client' ])->name('.update-client');
    Route::get('/get/{id}', [ iProtek\Pay\Http\Controllers\Manage\DashboardController::class, 'get_client' ])->name('.get');

    Route::post('/renew-secret/{id}', [ iProtek\Pay\Http\Controllers\Manage\DashboardController::class, 'renewSecreteClient' ])->name('.renew-secret');
    Route::get('/get-secret/{id}', [ iProtek\Pay\Http\Controllers\Manage\DashboardController::class, 'get_client_secret' ])->name('.get-secret');

  });
  
}); 
<?php
use iProtek\Pay\Http\Controllers\Manage\AppUserAccountController;

Route::prefix('customer-accounts')->name('.customer')->group(function(){
    Route::get('/list', [ AppUserAccountController::class, 'list' ])->name('.list');
    Route::get('/client-list-selection', [ AppUserAccountController::class, 'list_client_selection' ])->name('.list-client-selection');
    Route::get('/get-client-user-app-account/{client_id}/{id}', [ AppUserAccountController::class, 'get_client_app_user_account' ])->name('.get-client-user-app-account');
    
    Route::get('/', [ AppUserAccountController::class, 'index' ])->name('.dashboard');
    Route::post('/add-app-user-account/{client_id}', [AppUserAccountController::class, 'add_app_user_account'])->name('.add-app-user-account');

    //Route::post('/add-customer')


    /*
    Route::get('/sales-overview', [ iProtek\Pay\Http\Controllers\Manage\DashboardController::class, 'sales_overview' ])->name('.sales-overview'); 
    Route::get('/items-overview', [ iProtek\Pay\Http\Controllers\Manage\DashboardController::class, 'items_overview' ])->name('.items-overview'); 
    Route::prefix('inventory')->name('.inventory')->group(function(){            
        Route::get('/get-list', [ iProtek\Pay\Http\Controllers\Manage\ItemController::class ,'list'])->name('.get-list');
    });
    */   
  }); 

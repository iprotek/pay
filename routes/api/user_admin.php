<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use iProtek\Pay\Http\Controllers\Api\AuthController;


    Route::middleware(['auth:api_admin'])->prefix('user-admin')->name('.user-admin')->group(function () {
        Route::get('/', [AuthController::class, 'app_user']);
        Route::post('/user-admin-logout', [AuthController::class, 'app_user_logout'])->name('.logout');
    });
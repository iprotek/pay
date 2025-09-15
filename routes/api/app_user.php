<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use iProtek\Pay\Http\Controllers\Api\AuthController;

Route::middleware(['auth:api'])->prefix('app-user')->name('.app-user')->group(function () {
    Route::get('/app-user', [AuthController::class, 'app_user']);
    Route::post('/app-user-logout', [AuthController::class, 'app_user_logout'])->name('.logout');
});
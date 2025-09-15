<?php

use iProtek\Pay\Http\Controllers\Api\AuthAppUserAccountController ;
use iProtek\Pay\Http\Controllers\AppUserAccountInvitationController;
use iProtek\Pay\Http\Controllers\Manage\AppUserAccountGroupController;
use iProtek\Pay\Http\Controllers\Api\XracBranchController;
use iProtek\Pay\Http\Controllers\Api\XracRoleController;

Route::middleware(['auth:api_app_user_account'])->prefix('app-user-account')->name('.app-user-account')->group(function () {

    Route::get('/', [AuthAppUserAccountController::class, 'app_user']);
    Route::get('/logout', [AuthAppUserAccountController::class, 'logout'])->name('.logout');
    Route::get('/logout-all', [AuthAppUserAccountController::class, 'logout_all'])->name('.logout-all');
    Route::get('/list', [AuthAppUserAccountController::class, 'list'])->name('.list');
    
    Route::post('/update-password',[AuthAppUserAccountController::class, 'update_password'])->name('.update-password');
    Route::post('/update-my-name',[AuthAppUserAccountController::class, 'update_my_name'])->name('.update-my-name');
    Route::post('/update-group-name',[AuthAppUserAccountController::class, 'update_group_name'])->name('.update-group-name');
    Route::post('/update-email',[AuthAppUserAccountController::class, 'update_pseudo_email'])->name('.update-email');

    //Share Groups & WORKSPACE
    //Route::post('/share-group',[AuthAppUserAccountController::class, 'share_group_app_user_account'])->name('.share-group');
    Route::get('/share-group-list', [AuthAppUserAccountController::class, 'share_group_list'])->name('.share-group-list');
    Route::post('/send-invitation', [AppUserAccountInvitationController::class, 'send_invitation'])->name('.send-invitation');
    

    //ALLOWING GROUP
    Route::middleware(['pay-api-requestor'])->get('/group/{group_access_id}', [AppUserAccountGroupController::class, 'group_access'])->name('.group-access');

    Route::prefix('/xrac')->name('.xrac')->group(function(){

        //Branches
        Route::prefix('branches')->name('.branches')->group(function(){
            //GET
            Route::get('list', [XracBranchController::class, 'list'])->name('.list');
            Route::post('add-update', [XracBranchController::class, 'add_update'])->name('.add-update');
        });

        //Domains
        Route::prefix('domains')->name('.domains')->group(function(){
            //GET
        });

        //Role
        Route::prefix('roles')->name('.roles')->group(function(){
            //GET specific
            Route::get('list', [XracRoleController::class, 'list'])->name('.list');
            //UPDATE specific
            Route::post('add-update', [XracRoleController::class, 'add_update'])->name('.add-update');
        });

        //Updatelogs
        Route::prefix('logs')->name('.logs')->group(function(){
            //GET specific app_user_account
        });

        //UserRole
        Route::prefix('user-roles')->name('.user-roles')->group(function(){
            //GET SPECIFIC including

            //UPDATE / ADD - payload should include domaininfo, roleinfo and branchinfo

        });



    });

});
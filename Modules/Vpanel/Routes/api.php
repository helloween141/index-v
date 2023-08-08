<?php

use Illuminate\Support\Facades\Route;
use Modules\Vpanel\Http\Controllers\MainRequestController;
use Modules\Vpanel\Http\Controllers\RoleRequestController;
use Modules\Vpanel\Http\Controllers\UserController;

Route::group(['prefix' => 'vpanel', 'middleware' => 'auth'], function () {
    Route::get('/menu', [MainRequestController::class, 'getMenu']);
    Route::get('/{moduleName}/{modelName}/interface/{id?}', [MainRequestController::class, 'getInterface']);
    Route::get('/{moduleName}/{modelName?}/list', [MainRequestController::class, 'getList']);
    Route::get('/{moduleName}/{modelName?}/pointer', [MainRequestController::class, 'getPointer']);
    Route::get('/{moduleName}/{modelName}/record/{id?}', [MainRequestController::class, 'getRecord']);
    Route::delete('/{moduleName}/{modelName}/delete/{id}', [MainRequestController::class, 'deleteRecord']);
    Route::patch('/{moduleName}/{modelName}/restore/{id}', [MainRequestController::class, 'restoreRecord']);
    Route::post('/{moduleName}/{modelName}/save/{id?}', [MainRequestController::class, 'saveRecord']);
    Route::post('/{moduleName}/{modelName}/sort', [MainRequestController::class, 'sortList']);

    Route::get('/get-permission-list/{role_id}', [RoleRequestController::class, 'getPermissionList']);
    Route::get('/get-widget-list/{role_id}', [RoleRequestController::class, 'getWidgetList']);

    Route::post('/save-permission-list/{role_id}', [RoleRequestController::class, 'savePermissionList']);
    Route::post('/save-widget-list/{role_id}', [RoleRequestController::class, 'saveWidgetList']);

    Route::group(['prefix' => 'user'], function () {
        Route::get('/widgets', [UserController::class, 'getWidgets']);
        Route::get('/info', [UserController::class, 'getInfo']);
        Route::post('/update', [UserController::class, 'update']);
        Route::post('/auth-as-user', [UserController::class, 'authAsUser']);
        Route::post('/auth-as-admin', [UserController::class, 'authAsAdmin']);
    });
});
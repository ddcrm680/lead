<?php

use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::prefix('settings')->middleware('auth')->group(function () {

    Route::get('/', [SettingsController::class, 'index'])
        ->name('settings');

    Route::post('/general', [SettingsController::class, 'updateGeneral'])
            ->middleware('permission:settings.update')
            ->name('updateGeneral');

    Route::put('/roles/{role}/permissions', [SettingsController::class, 'updateRolePermissions'])
            ->middleware('permission:permissions.assign')
            ->name('updateRolePermissions');

});
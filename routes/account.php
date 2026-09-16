<?php

use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Route;

Route::prefix('account')->middleware('auth')->group(function () {

    Route::get('/profile', [AccountController::class, 'profile'])
        ->name('profile');

    Route::post('/update', [AccountController::class, 'updateProfile'])
        ->name('updateProfile');

    Route::post('/change-password', [AccountController::class, 'changePassword'])
        ->middleware('auth.session')
        ->name('changePassword');

});
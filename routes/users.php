<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->middleware('auth')->group(function () {

    Route::get('/', [UserController::class, 'index'])
        ->middleware('permission:users.view')
        ->name('users');

    Route::get('/data', [UserController::class, 'data'])
        ->middleware('permission:users.view')
        ->name('usersData');

    Route::post('/store', [UserController::class, 'store'])
        ->middleware('permission:users.create')
        ->name('storeUser');

    Route::get('/{user}/view', [UserController::class, 'show'])
        ->middleware('permission:users.view')
        ->name('viewUser');

    Route::get('/{user}/edit', [UserController::class, 'edit'])
        ->middleware('permission:users.update')
        ->name('editUser');

    Route::put('/{user}/update', [UserController::class, 'update'])
        ->middleware('permission:users.update')
        ->name('updateUser');

    Route::patch('/{user}/status', [UserController::class, 'toggleStatus'])
        ->middleware('permission:users.toggle-status')
        ->name('toggleUserStatus');

    Route::delete('/{user}/delete', [UserController::class, 'destroy'])
        ->middleware('permission:users.delete')
        ->name('deleteUser');

    Route::get('/export', [UserController::class, 'export'])
        ->middleware('permission:users.export')
        ->name('exportUsers');

});
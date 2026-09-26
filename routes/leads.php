<?php

use App\Http\Controllers\LeadController;
use Illuminate\Support\Facades\Route;

Route::prefix('leads')->middleware('auth')->group(function () {

    Route::get('/', [LeadController::class, 'index'])
        ->middleware('permission:leads.view')
        ->name('leads');

    Route::get('/data', [LeadController::class, 'data'])
        ->middleware('permission:leads.view')
        ->name('leadsData');


    Route::get('/create', [LeadController::class, 'create'])
        ->middleware('permission:leads.create')
        ->name('createLead');

    Route::post('/store', [LeadController::class, 'store'])
        ->middleware('permission:leads.create')
        ->name('storeLead');

    Route::get('/{lead}/view', [LeadController::class, 'show'])
        ->middleware('permission:leads.view')
        ->name('viewLead');

    Route::get('/{lead}/edit', [LeadController::class, 'edit'])
        ->middleware('permission:leads.update')
        ->name('editLead');

    Route::put('/{lead}/update', [LeadController::class, 'update'])
        ->middleware('permission:leads.update')
        ->name('updateLead');

    Route::get('/{lead}/follow-ups/create', [LeadController::class, 'createFollowUp'])
        ->middleware('permission:leads.update')
        ->name('createLeadFollowUp');

    Route::post('/{lead}/follow-ups', [LeadController::class, 'storeFollowUp'])
        ->middleware('permission:leads.update')
        ->name('storeLeadFollowUp');

    Route::get('/{lead}/tags/options', [LeadController::class, 'tagOptions'])
        ->middleware('permission:leads.update')
        ->name('leadTagOptions');

    Route::patch('/{lead}/tags', [LeadController::class, 'updateTags'])
        ->middleware('permission:leads.update')
        ->name('updateLeadTags');

    Route::patch('/{lead}/status', [LeadController::class, 'toggleStatus'])
        ->middleware('permission:leads.toggle-status')
        ->name('toggleLeadStatus');

    Route::delete('/{lead}/delete', [LeadController::class, 'destroy'])
        ->middleware('permission:leads.delete')
        ->name('deleteLead');

    Route::get('/export', [LeadController::class, 'export'])
        ->middleware('permission:leads.export')
        ->name('exportLead');

});

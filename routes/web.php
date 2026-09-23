<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Main application route file.
| Module-specific routes are loaded from their respective route files.
|
*/

require __DIR__.'/auth.php';

require __DIR__.'/dashboard.php';

require __DIR__.'/account.php';

require __DIR__.'/settings.php';

require __DIR__.'/users.php';

require __DIR__ . '/leads.php';

require __DIR__.'/deploy.php';

Route::get('/permission-test', function () {
    return 'Permission works!';
})
    ->middleware(['auth', 'permission:users.view'])
    ->name('permissionTest');

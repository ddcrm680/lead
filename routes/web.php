<?php

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Artisan;
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

Route::get('/permission-test', function () {
    return 'Permission works!';
})
    ->middleware(['auth', 'permission:users.view'])
    ->name('permissionTest');


// Route::get('/deploy-setup', function (Request $request) {
//     // $token = env('DEPLOY_TOKEN');

//     // abort_unless(
//     //     is_string($token)
//     //     && $token !== ''
//     //     && hash_equals($token, (string) $request->query('token')),
//     //     403,
//     //     'Unauthorized'
//     // );

//     try {
//         Artisan::call('migrate:fresh', [ '--force' => true] );

//         $freshOutput = Artisan::output();

//         Artisan::call('app:setup', [ '--force' => true,'--with-lead-defaults' => true ]);

//         $setupOutput = Artisan::output();

//         return response()->json([
//             'status' => 'success',
//             'migrate_fresh' => $freshOutput,
//             'app_setup' => $setupOutput,
//         ]);
//     } catch (\Throwable $e) {
//         return response()->json([
//             'status' => 'error',
//             'message' => $e->getMessage(),
//         ], 500);
//     }
// });

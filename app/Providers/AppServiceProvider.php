<?php

namespace App\Providers;

use App\Services\SettingsService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(SettingsService $settingsService): void
    {
        View::composer('*', function ($view) use ($settingsService) {
            $generalSettings = Schema::hasTable('settings')
                ? $settingsService->getGroup('general')
                : [];

            $view->with(compact(
                'generalSettings'
            ));
        });
    }
}
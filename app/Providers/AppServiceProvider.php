<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
    public function boot(): void
    {
        $forwardedProto = (string) request()->header('x-forwarded-proto', '');
        $appUrl = (string) config('app.url', '');

        if ($forwardedProto === 'https' || str_starts_with($appUrl, 'https://') || app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}

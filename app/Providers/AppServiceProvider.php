<?php

namespace App\Providers;

use App\Auth\PmrUserProvider;
use App\Models\Member;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
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
    public function boot(): void
    {
        if (config('app.env') === 'production' || app()->environment('production')) {
            URL::forceScheme('https');
        }

        Auth::provider('pmr', function ($app, array $config) {
            return new PmrUserProvider(
                $app['hash'],
                $config['model'] ?? Member::class
            );
        });
    }
}

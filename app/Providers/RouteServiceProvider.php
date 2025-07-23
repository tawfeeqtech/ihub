<?php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('auth', function ($request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('public-api', function ($request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        RateLimiter::for('user-api', function ($request) {
            return Limit::perMinute(100)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}

<?php

namespace App\Providers;

use App\Models\Booking;
use App\Observers\BookingObserver;
use App\Services\NotificationService;
use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Contract\Messaging;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Messaging::class, function ($app) {
            $factory = (new \Kreait\Firebase\Factory())
                ->withServiceAccount(base_path() . '\storage\app\firebase\firebase-credentials.json');
            return $factory->createMessaging();
        });

        $this->app->singleton(NotificationService::class, function ($app) {
            return new NotificationService($app->make(Messaging::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Booking::observe(BookingObserver::class);
    }
}

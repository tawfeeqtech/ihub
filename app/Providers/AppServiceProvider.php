<?php

namespace App\Providers;

use App\Models\Booking;
use App\Observers\BookingObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
        Booking::observe(BookingObserver::class);

        // Storage::disk('custom_public')->setVisibility('uploads/logo.png', 'public');

        // Log::info('File upload attempted', [
        //     'disk' => 'custom_public',
        //     'path' => 'uploads/logo.png',
        // ]);
    }
}

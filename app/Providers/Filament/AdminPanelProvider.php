<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            // ->viteTheme('resources/js/app.js')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
                // \App\Filament\Widgets\UnreadMessagesWidget::class,

            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->databaseNotifications(true) // أو فقط ->databaseNotifications();
            ->databaseNotificationsPolling('15s')
            ->authMiddleware([
                Authenticate::class,
                \App\Http\Middleware\EnsureUserIsAdmin::class,
            ]);
        // ->renderHook(
        //     'panels::body.end',
        //     fn(): string => <<<HTML
        //     <script>
        //         console.log('panels::body.end');

        //         document.addEventListener('livewire:initialized', function () {
        //         console.log('livewire:initialized');
        //         Livewire.hook('message.processed', (message, component) => {
        //             console.log('message.processed');
        //             if (message.updateQueue.some(update => 
        //                 update.type === 'callMethod' && 
        //                 update.payload.method === 'dispatchBrowserEvent' && 
        //                 update.payload.params[0] === 'notificationReceived'
        //             )) {
        //                 const notificationTitle = document.querySelector('.fi-no-notification .fi-no-title')?.innerText;
        //                 if (notificationTitle && notificationTitle.includes('There is a service request')) {
        //                     console.log('Playing bell sound for:', notificationTitle);
        //                     const audio = new Audio('/sounds/bell.mp3');
        //                     audio.play().catch(error => console.error('Audio playback failed:', error));
        //                 }
        //             }
        //         });
        //     });
        //     </script>
        //     HTML
        // );
    }
}

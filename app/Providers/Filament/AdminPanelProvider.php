<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\AdminBookingsByStatusChart;
use Filament\Actions\Action;
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
use Kenepa\TranslationManager\TranslationManagerPlugin;

use App\Filament\Widgets\AdminBookingsChart;
use App\Filament\Widgets\AdminConversationsActivityChart;
use App\Filament\Widgets\AdminPackagesByWorkspaceChart;
use App\Filament\Widgets\AdminServiceRequestsByStatusChart;
use App\Filament\Widgets\AdminServicesByWorkspaceChart;
use App\Filament\Widgets\AdminUsersGrowthChart;
use App\Filament\Widgets\AdminWorkspacesTrendChart;
use App\Filament\Widgets\SecretaryBookingsChart;
use App\Filament\Widgets\SecretaryConversationsActivityChart;
use App\Filament\Widgets\SecretaryServiceRequestsByStatusChart;
use App\Filament\Widgets\SecretaryServicesOverTimeChart;

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
                'primary' => Color::Hex('#4CAF50'), // لون أخضر فاتح (يمكن تغييره حسب الذوق)
            ])

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // AdminStatsOverview::class,
                AdminBookingsChart::class,
                AdminUsersGrowthChart::class,
                AdminWorkspacesTrendChart::class,
                AdminBookingsByStatusChart::class,
                AdminConversationsActivityChart::class,
                AdminPackagesByWorkspaceChart::class,
                AdminServiceRequestsByStatusChart::class,
                AdminServicesByWorkspaceChart::class,
                SecretaryBookingsChart::class,
                SecretaryConversationsActivityChart::class,
                SecretaryServiceRequestsByStatusChart::class,
                SecretaryServicesOverTimeChart::class,

                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
                // \App\Filament\Widgets\UnreadMessagesWidget::class,
            ])
            // ->defaultLocale(fn() => auth()->user()?->locale ?? 'en')
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
                // \App\Http\Middleware\SyncUserLocaleWithFilament::class,
            ])
            ->databaseNotifications(true)
            ->databaseNotificationsPolling('15s')
            ->authMiddleware([
                Authenticate::class,
                \App\Http\Middleware\EnsureUserIsAdmin::class,
            ])->plugin(TranslationManagerPlugin::make());
    }
}

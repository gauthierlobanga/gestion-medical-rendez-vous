<?php

namespace App\Providers\Filament;

use App\Filament\Pages\DashboardDisponibilite;
use App\Filament\Pages\DashboardMedecin;
use App\Filament\Pages\DashboardNotification;
use App\Filament\Pages\DashboardUser;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Navigation\NavigationGroup;
use Filament\Widgets\FilamentInfoWidget;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use App\Http\Middleware\AdminPanelAuthentificationMiddleware;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->profile()
            ->id('admin')
            ->path('admin')
            ->login()
            ->registration()
            ->colors([
                'danger' => Color::Rose,
                'gray' => Color::Gray,
                'info' => Color::Blue,
                'primary' => Color::Indigo,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                DashboardUser::class,
                DashboardMedecin::class,
                DashboardNotification::class,
                DashboardDisponibilite::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Gestion des patients'),
                NavigationGroup::make()
                    ->label('Gestion du personnel'),
                NavigationGroup::make()
                    ->label('Gestion des rendez-vous'),
                NavigationGroup::make()
                    ->label('Gestion Service'),
                NavigationGroup::make()
                    ->label('Security'),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                AdminPanelAuthentificationMiddleware::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])->multiFactorAuthentication([
                EmailAuthentication::make(),
                AppAuthentication::make()
                    ->recoverable()
                    ->brandName('Filament RDV'),
            ])
            ->databaseNotifications()
            ->resourceCreatePageRedirect('index')
            ->resourceEditPageRedirect('index')
            ->viteTheme('resources/css/filament/admin/theme.css');
    }
}

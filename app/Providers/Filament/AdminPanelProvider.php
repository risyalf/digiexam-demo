<?php

namespace App\Providers\Filament;

use App\Enum\Menu;
use Filament\Http\Middleware\Authenticate;
use App\Filament\Auth\Login;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id("admin")
            ->path("admin")
            ->brandName('')
            ->favicon(asset("images/logo_smk.png"))
            ->renderHook(
                PanelsRenderHook::TOPBAR_START,
                fn (): string => Blade::render('
                    <style>
                        .fi-topbar .fi-logo {
                            margin-right:5rem;
                            margin-top:1rem;
                            margin-bottom:1rem;
                        }

                        .logo-light { display: block; }
                        .logo-dark { display: none; }

                        .dark .logo-light { display: none; }
                        .dark .logo-dark { display: block; }
                        
                        @media (max-width: 1023px) {
                            .fi-topbar .fi-logo {
                                margin-right:0;
                                display:none;
                            }
                        }
                    </style>
                    <img alt="Logo SMK Swadaya" src="/images/logo_light.png" class="logo-light h-10 fi-logo" style="height: 4rem;" alt="Logo Light">
                    <img alt="Logo SMK Swadaya" src="/images/logo_dark.png" class="logo-dark h-10 fi-logo" style="height: 4rem;" alt="Logo Dark">
                ')
            )
            ->authGuard("web")
            ->login(Login::class)
            ->colors([
                "primary" => Color::Blue,
            ])
            ->spa()
            ->discoverResources(
                in: app_path("Filament/Resources"),
                for: "App\Filament\Resources",
            )
            ->discoverPages(
                in: app_path("Filament/Pages"),
                for: "App\Filament\Pages",
            )
            ->pages([Dashboard::class])
            ->discoverWidgets(
                in: app_path("Filament/Widgets"),
                for: "App\Filament\Widgets",
            )
            ->widgets([AccountWidget::class])
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
            ->plugins([FilamentShieldPlugin::make()])
            ->databaseNotifications()
            ->authMiddleware([Authenticate::class])
            ->navigationGroups([
                Menu::DATA_MODUL->value,
                Menu::DATA_GURU->value,
                Menu::DATA_PESERTA->value,
                Menu::DATA_TES->value,
                Menu::ADMIN->value
            ])
            ->sidebarCollapsibleOnDesktop()
            ->sidebarFullyCollapsibleOnDesktop(false);
    }
}

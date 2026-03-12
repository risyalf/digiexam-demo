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
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                fn() => Blade::render('
                    <div style="display:flex;justify-content:center;margin-bottom:1rem">
                        <img src="/images/logo_smk.png" style="height:7rem">
                    </div>
                ')
            )
            ->brandName('DigiExam CBT')
            ->brandLogoHeight('3rem')
            ->brandLogo(asset('images/logo_smk.png'))
            ->favicon(asset("images/logo_smk.png"))
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

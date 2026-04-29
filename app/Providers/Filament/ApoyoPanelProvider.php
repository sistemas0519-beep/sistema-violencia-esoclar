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

class ApoyoPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('apoyo')
            ->path('apoyo')
            ->colors([
                'primary' => Color::Teal,
                'danger'  => Color::Rose,
                'warning' => Color::Amber,
                'success' => Color::Emerald,
                'info'    => Color::Sky,
            ])
            ->brandName('Panel de Apoyo')
            ->favicon(asset('favicon.ico'))
            ->discoverResources(in: app_path('Filament/Apoyo/Resources'), for: 'App\\Filament\\Apoyo\\Resources')
            ->discoverPages(in: app_path('Filament/Apoyo/Pages'), for: 'App\\Filament\\Apoyo\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Apoyo/Widgets'), for: 'App\\Filament\\Apoyo\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->authMiddleware([])
            ->navigationGroups([
                'Casos Sensibles',
                'Asesoría y Apoyo',
                'Comunicación',
                'Recursos',
                'Métricas',
            ])
            ->sidebarCollapsibleOnDesktop()
            ->login()
            ->authMiddleware([
                Authenticate::class,
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
            ]);
    }
}

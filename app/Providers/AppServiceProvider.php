<?php

namespace App\Providers;

use App\Models\Asignacion;
use App\Models\Caso;
use App\Observers\AsignacionObserver;
use App\Policies\CasoSensiblePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use App\Http\Responses\LogoutResponse;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LogoutResponseContract::class, LogoutResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Asignacion::observe(AsignacionObserver::class);

        Gate::policy(Caso::class, CasoSensiblePolicy::class);
    }
}

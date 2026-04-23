<?php

namespace App\Filament\Apoyo\Widgets;

use App\Models\Caso;
use App\Models\Intervencion;
use App\Models\Mensaje;
use App\Models\Sesion;
use App\Models\SolicitudAsesoria;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ApoyoStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $userId = auth()->id();

        $casosActivos = Caso::where('asignado_a', $userId)->activos()->count();
        $casosSensibles = Caso::where('asignado_a', $userId)->sensibles()->activos()->count();
        $sesionesHoy = Sesion::delProfesional($userId)->hoy()->count();
        $sesionesProximas = Sesion::delProfesional($userId)->proximas()->count();
        $solicitudesPendientes = SolicitudAsesoria::pendientes()->count();
        $intervencionesActivas = Intervencion::delProfesional($userId)->activas()->count();
        $mensajesNoLeidos = Mensaje::noLeidos($userId)->count();
        $pendientesSeguimiento = Intervencion::delProfesional($userId)->pendientesSeguimiento()->count();

        return [
            Stat::make('Casos Activos', $casosActivos)
                ->description($casosSensibles . ' sensibles')
                ->descriptionIcon('heroicon-o-shield-exclamation')
                ->color($casosSensibles > 0 ? 'danger' : 'success')
                ->chart([7, 3, 4, 5, 6, 3, 5]),

            Stat::make('Sesiones Hoy', $sesionesHoy)
                ->description($sesionesProximas . ' próximas programadas')
                ->descriptionIcon('heroicon-o-calendar-days')
                ->color('info')
                ->chart([2, 3, 4, 2, 3, 4, 2]),

            Stat::make('Solicitudes Pendientes', $solicitudesPendientes)
                ->description('Requieren atención')
                ->descriptionIcon('heroicon-o-clipboard-document-check')
                ->color($solicitudesPendientes > 5 ? 'danger' : ($solicitudesPendientes > 0 ? 'warning' : 'success'))
                ->chart([4, 3, 5, 4, 3, 2, 4]),

            Stat::make('Intervenciones Activas', $intervencionesActivas)
                ->description($pendientesSeguimiento . ' pendientes de seguimiento')
                ->descriptionIcon('heroicon-o-hand-raised')
                ->color($pendientesSeguimiento > 0 ? 'warning' : 'success')
                ->chart([3, 4, 5, 3, 4, 5, 3]),

            Stat::make('Mensajes No Leídos', $mensajesNoLeidos)
                ->description('Bandeja de entrada')
                ->descriptionIcon('heroicon-o-envelope')
                ->color($mensajesNoLeidos > 0 ? 'danger' : 'success'),
        ];
    }
}

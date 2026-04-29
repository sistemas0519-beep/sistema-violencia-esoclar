<?php

namespace App\Filament\Apoyo\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ApoyoStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';
    protected static bool $isLazy = true;

    protected function getStats(): array
    {
        $userId = auth()->id();

        // Cache per-user for 120 seconds - all 8 values from 3 SQL queries
        $data = Cache::remember("apoyo_stats:{$userId}", 120, function () use ($userId) {
            $today = today()->toDateString();

            // Query 1: casos activos y sensibles del profesional
            $casosRow = DB::selectOne(
                "SELECT
                    SUM(estado NOT IN ('cerrado')) AS activos,
                    SUM(estado NOT IN ('cerrado') AND es_sensible = 1) AS sensibles
                 FROM casos WHERE asignado_a = ?",
                [$userId]
            );

            // Query 2: sesiones de hoy y proximas
            $sesionesRow = DB::selectOne(
                "SELECT
                    SUM(DATE(fecha) = ?) AS hoy,
                    SUM(fecha >= ? AND estado IN ('programada', 'confirmada')) AS proximas
                 FROM sesiones WHERE profesional_id = ?",
                [$today, $today, $userId]
            );

            // Query 3: intervenciones activas y pendientes de seguimiento
            $intervRow = DB::selectOne(
                "SELECT
                    SUM(estado IN ('planificada', 'en_curso')) AS activas,
                    SUM(requiere_seguimiento = 1 AND proximo_seguimiento IS NOT NULL AND proximo_seguimiento <= ?) AS pendientes_seguimiento
                 FROM intervenciones WHERE profesional_id = ?",
                [$today, $userId]
            );

            // Query 4: solicitudes pendientes (global) y Mensajes No Leídos
            $otrosRow = DB::selectOne(
                "SELECT
                    (SELECT COUNT(*) FROM solicitudes_asesoria WHERE estado = 'pendiente') AS solicitudes,
                    (SELECT COUNT(*) FROM mensajes WHERE destinatario_id = ? AND leido_en IS NULL) AS mensajes",
                [$userId]
            );

            return [
                'casos_activos'              => (int) ($casosRow->activos ?? 0),
                'casos_sensibles'            => (int) ($casosRow->sensibles ?? 0),
                'sesiones_hoy'               => (int) ($sesionesRow->hoy ?? 0),
                'sesiones_proximas'          => (int) ($sesionesRow->proximas ?? 0),
                'intervenciones_activas'     => (int) ($intervRow->activas ?? 0),
                'pendientes_seguimiento'     => (int) ($intervRow->pendientes_seguimiento ?? 0),
                'solicitudes_pendientes'     => (int) ($otrosRow->solicitudes ?? 0),
                'mensajes_no_leidos'         => (int) ($otrosRow->mensajes ?? 0),
            ];
        });

        return [
            Stat::make('Casos Activos', $data['casos_activos'])
                ->description($data['casos_sensibles'] . ' sensibles')
                ->descriptionIcon('heroicon-o-shield-exclamation')
                ->color($data['casos_sensibles'] > 0 ? 'danger' : 'success')
                ->chart([7, 3, 4, 5, 6, 3, 5]),

            Stat::make('Sesiones Hoy', $data['sesiones_hoy'])
                ->description($data['sesiones_proximas'] . ' próximas programadas')
                ->descriptionIcon('heroicon-o-calendar-days')
                ->color('info')
                ->chart([2, 3, 4, 2, 3, 4, 2]),

            Stat::make('Solicitudes Pendientes', $data['solicitudes_pendientes'])
                ->description('Requieren atención')
                ->descriptionIcon('heroicon-o-clipboard-document-check')
                ->color($data['solicitudes_pendientes'] > 5 ? 'danger' : ($data['solicitudes_pendientes'] > 0 ? 'warning' : 'success'))
                ->chart([4, 3, 5, 4, 3, 2, 4]),

            Stat::make('Intervenciones Activas', $data['intervenciones_activas'])
                ->description($data['pendientes_seguimiento'] . ' pendientes de seguimiento')
                ->descriptionIcon('heroicon-o-hand-raised')
                ->color($data['pendientes_seguimiento'] > 0 ? 'warning' : 'success')
                ->chart([3, 4, 5, 3, 4, 5, 3]),

            Stat::make('Mensajes No Leídos', $data['mensajes_no_leidos'])
                ->description('Bandeja de entrada')
                ->descriptionIcon('heroicon-o-envelope')
                ->color($data['mensajes_no_leidos'] > 0 ? 'danger' : 'success'),
        ];
    }
}

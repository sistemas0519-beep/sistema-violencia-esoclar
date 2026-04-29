<?php

namespace App\Filament\Widgets;

use App\Models\Documento;
use App\Services\ReportesService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = -2;
    protected static bool $isLazy = true;

    protected function getStats(): array
    {
        $stats = ReportesService::getEstadisticasGenerales();
        $casos = $stats['casos'];
        $asignaciones = $stats['asignaciones'];
        $usuarios = $stats['usuarios'];

        $chartData = Cache::remember('stats:chart_7dias', 300, function () {
            $desde = now()->subDays(6)->startOfDay();

            $totalesPorDia = DB::table('casos')
                ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
                ->where('created_at', '>=', $desde)
                ->groupByRaw('DATE(created_at)')
                ->pluck('total', 'fecha')
                ->toArray();

            return collect(range(6, 0))
                ->map(function (int $days) use ($totalesPorDia) {
                    $fecha = now()->subDays($days)->toDateString();

                    return (int) ($totalesPorDia[$fecha] ?? 0);
                })
                ->values()
                ->all();
        });

        $documentosTotal = Cache::remember('stats:documentos_total', 300, fn (): int => Documento::count());

        return [
            Stat::make('Total de Casos', $casos['total'])
                ->description($casos['hoy'] . ' nuevos hoy')
                ->descriptionIcon('heroicon-m-shield-exclamation')
                ->color('primary')
                ->chart($chartData),

            Stat::make('Casos Pendientes', $casos['pendiente'])
                ->description('Requieren atención')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->url('/admin/casos'),

            Stat::make('Casos Urgentes', $casos['urgente'])
                ->description('Prioridad máxima')
                ->descriptionIcon('heroicon-m-fire')
                ->color('danger')
                ->url('/admin/casos'),

            Stat::make('Casos Resueltos', $casos['resuelto'])
                ->description('Tasa resolución: ' . $this->getTasaResolucion($casos) . '%')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->url('/admin/casos'),

            Stat::make('Asignaciones Activas', $asignaciones['activa'])
                ->description('Psicólogos atendiendo')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info')
                ->url('/admin/asignaciones'),

            Stat::make('Documentos', $documentosTotal)
                ->description('Archivos en el sistema')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('gray'),

            Stat::make('SLA Vencido', $casos['sla_vencido'])
                ->description('Casos fuera de plazo')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($casos['sla_vencido'] > 0 ? 'danger' : 'success'),

            Stat::make('Psicólogos Disponibles', $usuarios['psicologos_disponibles'])
                ->description('De ' . $usuarios['psicologos'] . ' psicólogos')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color($usuarios['psicologos_disponibles'] > 0 ? 'success' : 'warning'),
        ];
    }

    private function getTasaResolucion(array $casos): string
    {
        $total = $casos['total'] ?: 1;
        $resueltos = $casos['resuelto'] + $casos['cerrado'];
        return round(($resueltos / $total) * 100, 1);
    }
}

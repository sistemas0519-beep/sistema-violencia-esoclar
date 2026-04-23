<?php

namespace App\Filament\Apoyo\Pages;

use App\Models\Caso;
use App\Models\Intervencion;
use App\Models\Sesion;
use App\Models\SolicitudAsesoria;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class MetricasReportes extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Métricas';
    protected static ?string $navigationLabel = 'Métricas y Reportes';
    protected static ?string $title = 'Métricas y Reportes';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.apoyo.pages.metricas-reportes';

    public string $periodo = 'mes';

    private function hoursDiffExpression(string $from, string $to): string
    {
        return match (DB::connection()->getDriverName()) {
            'mysql' => "TIMESTAMPDIFF(HOUR, {$from}, {$to})",
            'pgsql' => "EXTRACT(EPOCH FROM ({$to} - {$from})) / 3600",
            'sqlite' => "(julianday({$to}) - julianday({$from})) * 24",
            default => "TIMESTAMPDIFF(HOUR, {$from}, {$to})",
        };
    }

    public function getMetricas(): array
    {
        $userId = auth()->id();
        $desde = match ($this->periodo) {
            'semana' => now()->startOfWeek(),
            'mes'    => now()->startOfMonth(),
            'anio'   => now()->startOfYear(),
            default  => now()->startOfMonth(),
        };

        // ─── Casos atendidos ──────────────────────────────────────────────────
        $casosAtendidos = Caso::where('asignado_a', $userId)
            ->where('updated_at', '>=', $desde)
            ->count();

        $casosPorEstado = Caso::where('asignado_a', $userId)
            ->where('updated_at', '>=', $desde)
            ->select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado')
            ->toArray();

        // ─── Distribución por tipo de problemática ────────────────────────────
        $distribucionTipo = Caso::where('asignado_a', $userId)
            ->where('updated_at', '>=', $desde)
            ->select('tipo_violencia', DB::raw('count(*) as total'))
            ->groupBy('tipo_violencia')
            ->pluck('total', 'tipo_violencia')
            ->toArray();

        $distribucionArea = Caso::where('asignado_a', $userId)
            ->whereNotNull('area_tematica')
            ->where('updated_at', '>=', $desde)
            ->select('area_tematica', DB::raw('count(*) as total'))
            ->groupBy('area_tematica')
            ->pluck('total', 'area_tematica')
            ->toArray();

        // ─── Sesiones ─────────────────────────────────────────────────────────
        $sesionesCompletadas = Sesion::where('profesional_id', $userId)
            ->where('estado', 'completada')
            ->where('fecha', '>=', $desde)
            ->count();

        $duracionPromedio = Sesion::where('profesional_id', $userId)
            ->where('estado', 'completada')
            ->where('fecha', '>=', $desde)
            ->avg('duracion_real_minutos');

        $sesionesNoAsistio = Sesion::where('profesional_id', $userId)
            ->where('estado', 'no_asistio')
            ->where('fecha', '>=', $desde)
            ->count();

        $totalSesiones = Sesion::where('profesional_id', $userId)
            ->where('fecha', '>=', $desde)
            ->count();

        $tasaAsistencia = $totalSesiones > 0
            ? round(($sesionesCompletadas / $totalSesiones) * 100, 1)
            : 0;

        // ─── Intervenciones ───────────────────────────────────────────────────
        $intervencionesTotal = Intervencion::where('profesional_id', $userId)
            ->where('fecha_inicio', '>=', $desde)
            ->count();

        $efectividadData = Intervencion::where('profesional_id', $userId)
            ->where('estado', 'completada')
            ->where('fecha_inicio', '>=', $desde)
            ->select('efectividad', DB::raw('count(*) as total'))
            ->groupBy('efectividad')
            ->pluck('total', 'efectividad')
            ->toArray();

        $intervencionesEfectivas = ($efectividadData['muy_efectiva'] ?? 0) + ($efectividadData['efectiva'] ?? 0);
        $intervencionesCompletadas = array_sum($efectividadData);
        $tasaEfectividad = $intervencionesCompletadas > 0
            ? round(($intervencionesEfectivas / $intervencionesCompletadas) * 100, 1)
            : 0;

        // ─── Solicitudes: tiempos de respuesta ───────────────────────────────
        $tiempoPromedioRespuesta = SolicitudAsesoria::where('atendido_por', $userId)
            ->whereNotNull('fecha_asignacion')
            ->where('fecha_solicitud', '>=', $desde)
            ->selectRaw('AVG(' . $this->hoursDiffExpression('fecha_solicitud', 'fecha_asignacion') . ') as promedio')
            ->value('promedio');

        $tiempoPromedioResolucion = SolicitudAsesoria::where('atendido_por', $userId)
            ->whereNotNull('fecha_resolucion')
            ->where('fecha_solicitud', '>=', $desde)
            ->selectRaw('AVG(' . $this->hoursDiffExpression('fecha_solicitud', 'fecha_resolucion') . ') as promedio')
            ->value('promedio');

        $solicitudesAtendidas = SolicitudAsesoria::where('atendido_por', $userId)
            ->where('estado', 'completada')
            ->where('fecha_solicitud', '>=', $desde)
            ->count();

        // ─── Tendencia semanal (últimas 8 semanas) ───────────────────────────
        $tendencia = [];
        for ($i = 7; $i >= 0; $i--) {
            $semanaInicio = now()->subWeeks($i)->startOfWeek();
            $semanaFin = now()->subWeeks($i)->endOfWeek();

            $tendencia[] = [
                'semana'     => $semanaInicio->format('d/m'),
                'casos'      => Caso::where('asignado_a', $userId)->whereBetween('updated_at', [$semanaInicio, $semanaFin])->count(),
                'sesiones'   => Sesion::where('profesional_id', $userId)->where('estado', 'completada')->whereBetween('fecha', [$semanaInicio, $semanaFin])->count(),
            ];
        }

        return [
            'casosAtendidos'           => $casosAtendidos,
            'casosPorEstado'           => $casosPorEstado,
            'distribucionTipo'         => $distribucionTipo,
            'distribucionArea'         => $distribucionArea,
            'sesionesCompletadas'      => $sesionesCompletadas,
            'duracionPromedio'         => round($duracionPromedio ?? 0),
            'tasaAsistencia'           => $tasaAsistencia,
            'sesionesNoAsistio'        => $sesionesNoAsistio,
            'intervencionesTotal'      => $intervencionesTotal,
            'efectividadData'          => $efectividadData,
            'tasaEfectividad'          => $tasaEfectividad,
            'tiempoPromedioRespuesta'  => round($tiempoPromedioRespuesta ?? 0, 1),
            'tiempoPromedioResolucion' => round($tiempoPromedioResolucion ?? 0, 1),
            'solicitudesAtendidas'     => $solicitudesAtendidas,
            'tendencia'                => $tendencia,
        ];
    }
}

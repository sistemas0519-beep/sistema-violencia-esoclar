<?php

namespace App\Filament\Apoyo\Pages;

use App\Models\Caso;
use App\Models\Intervencion;
use App\Models\Sesion;
use App\Models\SolicitudAsesoria;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MetricasReportes extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Métricas';
    protected static ?string $navigationLabel = 'Métricas y Reportes';
    protected static ?string $title           = 'Métricas y Reportes';
    protected static ?int    $navigationSort  = 1;

    protected static string $view = 'filament.apoyo.pages.metricas-reportes';

    public string $periodo = 'mes';

    private function hoursDiffExpression(string $from, string $to): string
    {
        return match (DB::connection()->getDriverName()) {
            'mysql'  => "TIMESTAMPDIFF(HOUR, {$from}, {$to})",
            'pgsql'  => "EXTRACT(EPOCH FROM ({$to} - {$from})) / 3600",
            'sqlite' => "(julianday({$to}) - julianday({$from})) * 24",
            default  => "TIMESTAMPDIFF(HOUR, {$from}, {$to})",
        };
    }

    public function getMetricas(): array
    {
        $userId = auth()->id();
        $desde  = match ($this->periodo) {
            'semana' => now()->startOfWeek(),
            'anio'   => now()->startOfYear(),
            default  => now()->startOfMonth(),
        };

        $cacheKey = "metricas:{$userId}:{$this->periodo}:" . $desde->toDateString();

        return Cache::remember($cacheKey, 300, function () use ($userId, $desde) {
            // Casos: una sola consulta para el total
            $casosRow = DB::selectOne(
                'SELECT COUNT(*) AS total FROM casos WHERE asignado_a = ? AND updated_at >= ?',
                [$userId, $desde]
            );

            $casosPorEstado = Caso::where('asignado_a', $userId)
                ->where('updated_at', '>=', $desde)
                ->selectRaw('estado, COUNT(*) as total')
                ->groupBy('estado')
                ->pluck('total', 'estado')
                ->toArray();

            $distribucionTipo = Caso::where('asignado_a', $userId)
                ->where('updated_at', '>=', $desde)
                ->selectRaw('tipo_violencia, COUNT(*) as total')
                ->groupBy('tipo_violencia')
                ->pluck('total', 'tipo_violencia')
                ->toArray();

            $distribucionArea = Caso::where('asignado_a', $userId)
                ->whereNotNull('area_tematica')
                ->where('updated_at', '>=', $desde)
                ->selectRaw('area_tematica, COUNT(*) as total')
                ->groupBy('area_tematica')
                ->pluck('total', 'area_tematica')
                ->toArray();

            // Sesiones: una sola consulta con SUM condicionales
            $sesRow = DB::selectOne(
                "SELECT
                    COUNT(*) AS total,
                    SUM(estado = 'completada') AS completadas,
                    SUM(estado = 'no_asistio') AS no_asistio,
                    AVG(CASE WHEN estado = 'completada' THEN duracion_real_minutos END) AS duracion_promedio
                 FROM sesiones
                 WHERE profesional_id = ? AND fecha >= ?",
                [$userId, $desde->toDateString()]
            );

            $totalSesiones       = (int) ($sesRow->total ?? 0);
            $sesionesCompletadas = (int) ($sesRow->completadas ?? 0);
            $sesionesNoAsistio   = (int) ($sesRow->no_asistio ?? 0);
            $duracionPromedio    = round((float) ($sesRow->duracion_promedio ?? 0), 1);
            $tasaAsistencia      = $totalSesiones > 0
                ? round(($sesionesCompletadas / $totalSesiones) * 100, 1)
                : 0;

            // Intervenciones
            $intervencionesTotal = Intervencion::where('profesional_id', $userId)
                ->where('fecha_inicio', '>=', $desde)
                ->count();

            $efectividadData = Intervencion::where('profesional_id', $userId)
                ->where('estado', 'completada')
                ->where('fecha_inicio', '>=', $desde)
                ->selectRaw('efectividad, COUNT(*) as total')
                ->groupBy('efectividad')
                ->pluck('total', 'efectividad')
                ->toArray();

            $intervencionesEfectivas   = ($efectividadData['muy_efectiva'] ?? 0) + ($efectividadData['efectiva'] ?? 0);
            $intervencionesCompletadas = array_sum($efectividadData);
            $tasaEfectividad           = $intervencionesCompletadas > 0
                ? round(($intervencionesEfectivas / $intervencionesCompletadas) * 100, 1)
                : 0;

            // Solicitudes: una sola consulta con AVG condicionales
            $solRow = DB::selectOne(
                "SELECT
                    AVG(" . $this->hoursDiffExpression('fecha_solicitud', 'fecha_asignacion') . ") AS promedio_respuesta,
                    AVG(" . $this->hoursDiffExpression('fecha_solicitud', 'fecha_resolucion') . ")  AS promedio_resolucion,
                    SUM(estado = 'completada') AS atendidas
                 FROM solicitudes_asesoria
                 WHERE atendido_por = ? AND fecha_solicitud >= ?",
                [$userId, $desde]
            );

            // Tendencia: 2 consultas agrupadas en lugar de 16 individuales
            $tendenciaCasos = Caso::where('asignado_a', $userId)
                ->where('updated_at', '>=', now()->subWeeks(8)->startOfWeek())
                ->selectRaw('YEARWEEK(updated_at, 1) AS semana_key, COUNT(*) AS total')
                ->groupBy(DB::raw('YEARWEEK(updated_at, 1)'))
                ->pluck('total', 'semana_key')
                ->toArray();

            $tendenciaSesiones = Sesion::where('profesional_id', $userId)
                ->where('estado', 'completada')
                ->where('fecha', '>=', now()->subWeeks(8)->startOfWeek()->toDateString())
                ->selectRaw('YEARWEEK(fecha, 1) AS semana_key, COUNT(*) AS total')
                ->groupBy(DB::raw('YEARWEEK(fecha, 1)'))
                ->pluck('total', 'semana_key')
                ->toArray();

            $tendencia = [];
            for ($i = 7; $i >= 0; $i--) {
                $semanaInicio = now()->subWeeks($i)->startOfWeek();
                $key          = (int) $semanaInicio->format('oW');
                $tendencia[]  = [
                    'semana'   => $semanaInicio->format('d/m'),
                    'casos'    => $tendenciaCasos[$key] ?? 0,
                    'sesiones' => $tendenciaSesiones[$key] ?? 0,
                ];
            }

            return [
                'casosAtendidos'           => (int) ($casosRow->total ?? 0),
                'casosPorEstado'           => $casosPorEstado,
                'distribucionTipo'         => $distribucionTipo,
                'distribucionArea'         => $distribucionArea,
                'sesionesCompletadas'      => $sesionesCompletadas,
                'duracionPromedio'         => $duracionPromedio,
                'tasaAsistencia'           => $tasaAsistencia,
                'sesionesNoAsistio'        => $sesionesNoAsistio,
                'intervencionesTotal'      => $intervencionesTotal,
                'efectividadData'          => $efectividadData,
                'tasaEfectividad'          => $tasaEfectividad,
                'tiempoPromedioRespuesta'  => round((float) ($solRow->promedio_respuesta ?? 0), 1),
                'tiempoPromedioResolucion' => round((float) ($solRow->promedio_resolucion ?? 0), 1),
                'solicitudesAtendidas'     => (int) ($solRow->atendidas ?? 0),
                'tendencia'                => $tendencia,
            ];
        });
    }
}

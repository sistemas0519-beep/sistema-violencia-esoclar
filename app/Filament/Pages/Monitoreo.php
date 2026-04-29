<?php

namespace App\Filament\Pages;

use App\Models\Asignacion;
use App\Models\AuditLog;
use App\Models\Caso;
use App\Models\Sesion;
use App\Models\User;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Monitoreo extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-computer-desktop';
    protected static ?string $navigationGroup = 'Administración';
    protected static ?string $navigationLabel = 'Monitoreo';
    protected static ?string $title           = 'Monitoreo del Sistema';
    protected static ?int    $navigationSort  = 4;

    protected static string $view = 'filament.pages.monitoreo';

    // ─── Métricas principales (caché 60s) ────────────────────────────────────

    public function getMetricasProperty(): array
    {
        return Cache::remember('monitoreo:metricas', 60, function () {
            $hoy        = now()->toDateString();
            $semana     = now()->subDays(7)->toDateTimeString();
            $inicioSemana = now()->startOfWeek()->toDateString();
            $finSemana    = now()->endOfWeek()->toDateString();

            $usuariosRow = DB::selectOne(
                "SELECT
                    COUNT(*) AS total,
                    SUM(activo = 1) AS activos,
                    SUM(activo = 0) AS inactivos,
                    SUM(DATE(ultimo_acceso) = ?) AS hoy,
                    SUM(ultimo_acceso >= ?) AS activos_semana,
                    SUM(ultimo_acceso >= ? AND ultimo_acceso < ?) AS activos_semana_anterior,
                    SUM(rol = 'psicologo' AND disponibilidad = 'disponible' AND activo = 1) AS psicologos_disponibles,
                    SUM(rol = 'psicologo' AND disponibilidad = 'ocupado' AND activo = 1) AS psicologos_ocupados,
                    SUM(rol = 'psicologo' AND activo = 1) AS psicologos_total
                 FROM users",
                [$hoy, $semana, now()->subDays(14)->toDateTimeString(), $semana]
            );

            $casosRow = DB::selectOne(
                "SELECT
                    COUNT(*) AS total,
                    SUM(DATE(created_at) = ?) AS hoy,
                    SUM(created_at >= ?) AS semana,
                    SUM(estado = 'pendiente') AS pendientes,
                    SUM(estado = 'en_proceso') AS en_proceso,
                    SUM(estado IN ('resuelto', 'cerrado')) AS resueltos,
                    SUM(prioridad = 'urgente' AND estado != 'cerrado') AS urgentes,
                    SUM(sla_vencido = 1 AND estado != 'cerrado') AS sla_vencido,
                    SUM(escalado = 1 AND estado != 'cerrado') AS escalados,
                    SUM(es_sensible = 1 AND estado != 'cerrado') AS sensibles,
                    SUM(estado = 'pendiente' AND asignado_a IS NULL) AS sin_asignar,
                    SUM(sla_limite IS NOT NULL) AS sla_total,
                    SUM(sla_limite IS NOT NULL AND sla_vencido = 0) AS sla_cumplido,
                    SUM(created_at >= ?) AS casos_semana_actual,
                    SUM(created_at >= ? AND created_at < ?) AS casos_semana_anterior
                 FROM casos",
                [$hoy, $semana, $semana, now()->subDays(14)->toDateTimeString(), $semana]
            );

            $asignacionesRow = DB::selectOne(
                "SELECT
                    SUM(estado = 'activa') AS activas,
                    SUM(estado = 'completada') AS completadas
                 FROM asignaciones"
            );

            $sesionesRow = DB::selectOne(
                "SELECT
                    SUM(DATE(fecha) = ?) AS hoy,
                    SUM(DATE(fecha) = ? AND estado = 'completada') AS completadas_hoy,
                    SUM(fecha BETWEEN ? AND ?) AS semana
                 FROM sesiones",
                [$hoy, $hoy, $inicioSemana, $finSemana]
            );

            $casosTotales   = (int) ($casosRow->total ?? 0);
            $casosResueltos = (int) ($casosRow->resueltos ?? 0);
            $tasaResolucion = $casosTotales > 0 ? round(($casosResueltos / $casosTotales) * 100, 1) : 0;

            $slaTotal = (int) ($casosRow->sla_total ?? 0);
            $slaCumplido = (int) ($casosRow->sla_cumplido ?? 0);
            $slaPct = $slaTotal > 0 ? round(($slaCumplido / $slaTotal) * 100, 1) : 100;

            return [
                'usuarios_totales'        => (int) ($usuariosRow->total ?? 0),
                'usuarios_activos'        => (int) ($usuariosRow->activos ?? 0),
                'usuarios_inactivos'      => (int) ($usuariosRow->inactivos ?? 0),
                'usuarios_hoy'            => (int) ($usuariosRow->hoy ?? 0),

                'casos_totales'           => $casosTotales,
                'casos_hoy'               => (int) ($casosRow->hoy ?? 0),
                'casos_semana'            => (int) ($casosRow->semana ?? 0),
                'casos_pendientes'        => (int) ($casosRow->pendientes ?? 0),
                'casos_en_proceso'        => (int) ($casosRow->en_proceso ?? 0),
                'casos_resueltos'         => $casosResueltos,
                'casos_urgentes'          => (int) ($casosRow->urgentes ?? 0),
                'casos_sla_vencido'       => (int) ($casosRow->sla_vencido ?? 0),
                'casos_escalados'         => (int) ($casosRow->escalados ?? 0),
                'casos_sensibles'         => (int) ($casosRow->sensibles ?? 0),
                'casos_sin_asignar'       => (int) ($casosRow->sin_asignar ?? 0),
                'tasa_resolucion'         => $tasaResolucion,

                'sla_total'               => $slaTotal,
                'sla_cumplido_pct'        => $slaPct,

                'asignaciones_activas'    => (int) ($asignacionesRow->activas ?? 0),
                'asignaciones_completadas'=> (int) ($asignacionesRow->completadas ?? 0),

                'psicologos_disponibles'  => (int) ($usuariosRow->psicologos_disponibles ?? 0),
                'psicologos_ocupados'     => (int) ($usuariosRow->psicologos_ocupados ?? 0),
                'psicologos_total'        => (int) ($usuariosRow->psicologos_total ?? 0),

                'sesiones_hoy'            => (int) ($sesionesRow->hoy ?? 0),
                'sesiones_completadas_hoy'=> (int) ($sesionesRow->completadas_hoy ?? 0),
                'sesiones_semana'         => (int) ($sesionesRow->semana ?? 0),
            ];
        });
    }

    // ─── Tendencias semana vs semana anterior ────────────────────────────────

    public function getTendenciasProperty(): array
    {
        return Cache::remember('monitoreo:tendencias', 120, function () {
            $semanaActual  = now()->subDays(7);
            $semanaAnterior = now()->subDays(14);

            $casosRow = DB::selectOne(
                'SELECT SUM(created_at >= ?) AS actual, SUM(created_at >= ? AND created_at < ?) AS anterior FROM casos',
                [$semanaActual, $semanaAnterior, $semanaActual]
            );

            $usersRow = DB::selectOne(
                'SELECT SUM(ultimo_acceso >= ?) AS actual, SUM(ultimo_acceso >= ? AND ultimo_acceso < ?) AS anterior FROM users',
                [$semanaActual, $semanaAnterior, $semanaActual]
            );

            $casosActual   = (int) ($casosRow->actual ?? 0);
            $casosAnterior = (int) ($casosRow->anterior ?? 0);

            $usersActual   = (int) ($usersRow->actual ?? 0);
            $usersAnterior = (int) ($usersRow->anterior ?? 0);

            $calcTrend = fn ($actual, $anterior) => $anterior > 0
                ? round((($actual - $anterior) / $anterior) * 100, 1)
                : ($actual > 0 ? 100 : 0);

            return [
                'casos'    => ['actual' => $casosActual,  'pct' => $calcTrend($casosActual, $casosAnterior)],
                'usuarios' => ['actual' => $usersActual,  'pct' => $calcTrend($usersActual, $usersAnterior)],
            ];
        });
    }

    // ─── Actividad reciente (caché 60s) ──────────────────────────────────────

    public function getActividadRecienteProperty(): array
    {
        return Cache::remember('monitoreo:actividad_reciente', 60, function () {
            return AuditLog::with(['user:id,name,rol'])
                ->orderByDesc('created_at')
                ->limit(15)
                ->get(['id', 'user_id', 'accion', 'modulo', 'descripcion', 'ip_address', 'created_at'])
                ->map(fn ($log) => [
                    'id'          => $log->id,
                    'usuario'     => $log->user?->name ?? 'Sistema',
                    'rol'         => $log->user?->rol ?? '',
                    'accion'      => $log->accion,
                    'modulo'      => $log->modulo,
                    'descripcion' => $log->descripcion,
                    'ip'          => $log->ip_address,
                    'fecha'       => $log->created_at->diffForHumans(),
                    'fecha_full'  => $log->created_at->format('d/m/Y H:i:s'),
                ])
                ->toArray();
        });
    }

    // ─── Alertas ─────────────────────────────────────────────────────────────

    public function getAlertasProperty(): array
    {
        $alertas = [];
        $m       = $this->metricas;

        if ($m['casos_sla_vencido'] > 0) {
            $alertas[] = ['nivel' => 'danger',  'mensaje' => "{$m['casos_sla_vencido']} caso(s) con SLA vencido requieren atención inmediata",    'icono' => 'heroicon-o-exclamation-triangle'];
        }
        if ($m['casos_urgentes'] > 0) {
            $alertas[] = ['nivel' => 'warning', 'mensaje' => "{$m['casos_urgentes']} caso(s) urgentes activos",                                   'icono' => 'heroicon-o-fire'];
        }
        if ($m['casos_escalados'] > 0) {
            $alertas[] = ['nivel' => 'warning', 'mensaje' => "{$m['casos_escalados']} caso(s) escalados pendientes de resolución",                'icono' => 'heroicon-o-arrow-trending-up'];
        }
        if ($m['casos_sensibles'] > 0) {
            $alertas[] = ['nivel' => 'warning', 'mensaje' => "{$m['casos_sensibles']} caso(s) sensibles activos requieren manejo especial",       'icono' => 'heroicon-o-shield-exclamation'];
        }
        if ($m['psicologos_disponibles'] === 0 && $m['psicologos_total'] > 0) {
            $alertas[] = ['nivel' => 'danger',  'mensaje' => 'No hay psicólogos disponibles en este momento',                                     'icono' => 'heroicon-o-user-minus'];
        }
        if ($m['casos_sin_asignar'] > 5) {
            $alertas[] = ['nivel' => 'warning', 'mensaje' => "{$m['casos_sin_asignar']} casos pendientes sin asignar",                           'icono' => 'heroicon-o-clipboard-document'];
        }
        if ($m['sla_cumplido_pct'] < 70) {
            $alertas[] = ['nivel' => 'danger',  'mensaje' => "Cumplimiento SLA crítico: {$m['sla_cumplido_pct']}% — revisar plazos de atención",  'icono' => 'heroicon-o-clock'];
        }

        if (empty($alertas)) {
            $alertas[] = ['nivel' => 'success', 'mensaje' => 'Todo funciona correctamente. No hay alertas activas.',                              'icono' => 'heroicon-o-check-circle'];
        }

        return $alertas;
    }

    // ─── Gráfico: casos por día (14 días) ────────────────────────────────────

    public function getCasosPorDiaProperty(): array
    {
        return Cache::remember('monitoreo:casos_por_dia', 300, function () {
            $rawData = Caso::select(
                    DB::raw('DATE(created_at) as fecha'),
                    DB::raw('COUNT(*) as total')
                )
                ->where('created_at', '>=', now()->subDays(14))
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy(DB::raw('DATE(created_at)'))
                ->pluck('total', 'fecha')
                ->toArray();

            $result = [];
            for ($i = 13; $i >= 0; $i--) {
                $date   = now()->subDays($i)->format('Y-m-d');
                $label  = now()->subDays($i)->format('d/m');
                $result[] = ['fecha' => $label, 'total' => $rawData[$date] ?? 0];
            }
            return $result;
        });
    }

    // ─── Gráfico: distribución por rol ───────────────────────────────────────

    public function getDistribucionRolesProperty(): array
    {
        return Cache::remember('monitoreo:roles', 300, function () {
            return User::select('rol', DB::raw('COUNT(*) as total'))
                ->where('activo', true)
                ->groupBy('rol')
                ->orderByDesc('total')
                ->get()
                ->map(fn ($r) => ['rol' => $r->rol, 'total' => $r->total])
                ->toArray();
        });
    }

    // ─── Gráfico: casos por tipo de violencia ────────────────────────────────

    public function getCasosPorTipoProperty(): array
    {
        return Cache::remember('monitoreo:casos_tipo', 300, function () {
            return Caso::select('tipo_violencia', DB::raw('COUNT(*) as total'))
                ->whereNotNull('tipo_violencia')
                ->groupBy('tipo_violencia')
                ->orderByDesc('total')
                ->get()
                ->map(fn ($r) => ['tipo' => $r->tipo_violencia, 'total' => $r->total])
                ->toArray();
        });
    }

    // ─── Top regiones con más casos ──────────────────────────────────────────

    public function getCasosPorRegionProperty(): array
    {
        return Cache::remember('monitoreo:casos_region', 300, function () {
            return Caso::select('region', DB::raw('COUNT(*) as total'))
                ->whereNotNull('region')
                ->where('region', '!=', '')
                ->groupBy('region')
                ->orderByDesc('total')
                ->limit(8)
                ->get()
                ->map(fn ($r) => ['region' => $r->region, 'total' => $r->total])
                ->toArray();
        });
    }

    // ─── Salud del sistema ────────────────────────────────────────────────────

    public function getSaludSistemaProperty(): array
    {
        $dbMs = null;
        $dbStatus = 'ok';
        try {
            $t0   = microtime(true);
            DB::select('SELECT 1');
            $dbMs = round((microtime(true) - $t0) * 1000, 1);
            $dbStatus = $dbMs > 300 ? 'lento' : 'ok';
        } catch (\Throwable) {
            $dbStatus = 'error';
        }

        $memUsed   = round(memory_get_usage(true) / 1048576, 1);   // MB
        $memPeak   = round(memory_get_peak_usage(true) / 1048576, 1);
        $cacheOk   = true;
        try { Cache::put('_ping', 1, 5); Cache::get('_ping'); } catch (\Throwable) { $cacheOk = false; }

        $logsHoy   = AuditLog::whereDate('created_at', today())->count();
        $erroresBd = Caso::where('updated_at', '>=', now()->subMinutes(5))->count(); // actividad reciente

        return [
            'db_status'   => $dbStatus,
            'db_ms'       => $dbMs,
            'cache_ok'    => $cacheOk,
            'mem_used_mb' => $memUsed,
            'mem_peak_mb' => $memPeak,
            'logs_hoy'    => $logsHoy,
            'actividad_5m'=> $erroresBd,
            'uptime_php'  => PHP_VERSION,
        ];
    }

    // ─── Resumen SLA por tipo ────────────────────────────────────────────────

    public function getResumenSlaProperty(): array
    {
        return Cache::remember('monitoreo:sla', 120, function () {
            return Caso::select(
                    'tipo_violencia',
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(CASE WHEN sla_vencido = 1 THEN 1 ELSE 0 END) as vencidos')
                )
                ->whereNotNull('tipo_violencia')
                ->groupBy('tipo_violencia')
                ->orderByDesc('total')
                ->limit(6)
                ->get()
                ->map(fn ($r) => [
                    'tipo'       => $r->tipo_violencia,
                    'total'      => $r->total,
                    'vencidos'   => $r->vencidos,
                    'pct_cumpl'  => $r->total > 0 ? round((($r->total - $r->vencidos) / $r->total) * 100) : 100,
                ])
                ->toArray();
        });
    }
}

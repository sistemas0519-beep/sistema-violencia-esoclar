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
            $hoy   = now()->startOfDay();
            $semana = now()->subDays(7);

            $casosTotales   = Caso::count();
            $casosResueltos = Caso::whereIn('estado', ['resuelto', 'cerrado'])->count();
            $tasaResolucion = $casosTotales > 0 ? round(($casosResueltos / $casosTotales) * 100, 1) : 0;

            $slaTotal    = Caso::whereNotNull('sla_limite')->count();
            $slaCumplido = Caso::whereNotNull('sla_limite')->where('sla_vencido', false)->count();
            $slaPct      = $slaTotal > 0 ? round(($slaCumplido / $slaTotal) * 100, 1) : 100;

            return [
                'usuarios_totales'        => User::count(),
                'usuarios_activos'        => User::where('activo', true)->count(),
                'usuarios_inactivos'      => User::where('activo', false)->count(),
                'usuarios_hoy'            => User::whereDate('ultimo_acceso', $hoy)->count(),

                'casos_totales'           => $casosTotales,
                'casos_hoy'               => Caso::whereDate('created_at', $hoy)->count(),
                'casos_semana'            => Caso::where('created_at', '>=', $semana)->count(),
                'casos_pendientes'        => Caso::where('estado', 'pendiente')->count(),
                'casos_en_proceso'        => Caso::where('estado', 'en_proceso')->count(),
                'casos_resueltos'         => $casosResueltos,
                'casos_urgentes'          => Caso::where('prioridad', 'urgente')->where('estado', '!=', 'cerrado')->count(),
                'casos_sla_vencido'       => Caso::where('sla_vencido', true)->where('estado', '!=', 'cerrado')->count(),
                'casos_escalados'         => Caso::where('escalado', true)->where('estado', '!=', 'cerrado')->count(),
                'casos_sensibles'         => Caso::where('es_sensible', true)->where('estado', '!=', 'cerrado')->count(),
                'casos_sin_asignar'       => Caso::where('estado', 'pendiente')->whereNull('asignado_a')->count(),
                'tasa_resolucion'         => $tasaResolucion,

                'sla_total'               => $slaTotal,
                'sla_cumplido_pct'        => $slaPct,

                'asignaciones_activas'    => Asignacion::where('estado', 'activa')->count(),
                'asignaciones_completadas'=> Asignacion::where('estado', 'completada')->count(),

                'psicologos_disponibles'  => User::where('rol', 'psicologo')->where('disponibilidad', 'disponible')->where('activo', true)->count(),
                'psicologos_ocupados'     => User::where('rol', 'psicologo')->where('disponibilidad', 'ocupado')->where('activo', true)->count(),
                'psicologos_total'        => User::where('rol', 'psicologo')->where('activo', true)->count(),

                'sesiones_hoy'            => Sesion::whereDate('fecha', today())->count(),
                'sesiones_completadas_hoy'=> Sesion::whereDate('fecha', today())->where('estado', 'completada')->count(),
                'sesiones_semana'         => Sesion::whereBetween('fecha', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            ];
        });
    }

    // ─── Tendencias semana vs semana anterior ────────────────────────────────

    public function getTendenciasProperty(): array
    {
        return Cache::remember('monitoreo:tendencias', 120, function () {
            $semanaActual  = now()->subDays(7);
            $semanaAnterior = now()->subDays(14);

            $casosActual   = Caso::where('created_at', '>=', $semanaActual)->count();
            $casosAnterior = Caso::whereBetween('created_at', [$semanaAnterior, $semanaActual])->count();

            $usersActual   = User::where('ultimo_acceso', '>=', $semanaActual)->count();
            $usersAnterior = User::whereBetween('ultimo_acceso', [$semanaAnterior, $semanaActual])->count();

            $calcTrend = fn ($actual, $anterior) => $anterior > 0
                ? round((($actual - $anterior) / $anterior) * 100, 1)
                : ($actual > 0 ? 100 : 0);

            return [
                'casos'    => ['actual' => $casosActual,  'pct' => $calcTrend($casosActual, $casosAnterior)],
                'usuarios' => ['actual' => $usersActual,  'pct' => $calcTrend($usersActual, $usersAnterior)],
            ];
        });
    }

    // ─── Actividad reciente ───────────────────────────────────────────────────

    public function getActividadRecienteProperty(): array
    {
        return AuditLog::with('user')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
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

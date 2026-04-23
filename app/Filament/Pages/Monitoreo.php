<?php

namespace App\Filament\Pages;

use App\Models\ActividadSistema;
use App\Models\Asignacion;
use App\Models\AuditLog;
use App\Models\Caso;
use App\Models\User;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class Monitoreo extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-computer-desktop';
    protected static ?string $navigationGroup = 'Administración';
    protected static ?string $navigationLabel = 'Monitoreo';
    protected static ?string $title           = 'Monitoreo del Sistema';
    protected static ?int    $navigationSort  = 4;

    protected static string $view = 'filament.pages.monitoreo';

    // ─── Métricas en tiempo real ──────────────────────────────────────────────

    public function getMetricasProperty(): array
    {
        $hoy = now()->startOfDay();
        $semana = now()->subDays(7);

        return [
            'usuarios_totales'       => User::count(),
            'usuarios_activos'       => User::where('activo', true)->count(),
            'usuarios_inactivos'     => User::where('activo', false)->count(),
            'usuarios_hoy'           => User::whereDate('ultimo_acceso', $hoy)->count(),

            'casos_totales'          => Caso::count(),
            'casos_hoy'             => Caso::whereDate('created_at', $hoy)->count(),
            'casos_semana'          => Caso::where('created_at', '>=', $semana)->count(),
            'casos_pendientes'      => Caso::where('estado', 'pendiente')->count(),
            'casos_en_proceso'      => Caso::where('estado', 'en_proceso')->count(),
            'casos_urgentes'        => Caso::where('prioridad', 'urgente')->where('estado', '!=', 'cerrado')->count(),
            'casos_sla_vencido'     => Caso::where('sla_vencido', true)->where('estado', '!=', 'cerrado')->count(),
            'casos_escalados'       => Caso::where('escalado', true)->where('estado', '!=', 'cerrado')->count(),

            'asignaciones_activas'  => Asignacion::where('estado', 'activa')->count(),
            'psicologos_disponibles' => User::where('rol', 'psicologo')
                ->where('disponibilidad', 'disponible')
                ->where('activo', true)
                ->count(),
            'psicologos_total'      => User::where('rol', 'psicologo')->where('activo', true)->count(),
        ];
    }

    public function getActividadRecienteProperty(): array
    {
        return AuditLog::with('user')
            ->orderByDesc('created_at')
            ->limit(15)
            ->get()
            ->map(fn ($log) => [
                'id'          => $log->id,
                'usuario'     => $log->user?->name ?? 'Sistema',
                'accion'      => $log->accion,
                'modulo'      => $log->modulo,
                'descripcion' => $log->descripcion,
                'ip'          => $log->ip_address,
                'fecha'       => $log->created_at->diffForHumans(),
                'fecha_full'  => $log->created_at->format('d/m/Y H:i:s'),
            ])
            ->toArray();
    }

    public function getAlertasProperty(): array
    {
        $alertas = [];

        $m = $this->metricas;

        if ($m['casos_sla_vencido'] > 0) {
            $alertas[] = [
                'nivel'   => 'danger',
                'mensaje' => "{$m['casos_sla_vencido']} caso(s) con SLA vencido requieren atención inmediata",
                'icono'   => 'heroicon-o-exclamation-triangle',
            ];
        }

        if ($m['casos_urgentes'] > 0) {
            $alertas[] = [
                'nivel'   => 'warning',
                'mensaje' => "{$m['casos_urgentes']} caso(s) marcados como urgentes están abiertos",
                'icono'   => 'heroicon-o-fire',
            ];
        }

        if ($m['casos_escalados'] > 0) {
            $alertas[] = [
                'nivel'   => 'warning',
                'mensaje' => "{$m['casos_escalados']} caso(s) han sido escalados",
                'icono'   => 'heroicon-o-arrow-trending-up',
            ];
        }

        if ($m['psicologos_disponibles'] === 0 && $m['psicologos_total'] > 0) {
            $alertas[] = [
                'nivel'   => 'danger',
                'mensaje' => 'No hay psicólogos disponibles en este momento',
                'icono'   => 'heroicon-o-user-minus',
            ];
        }

        $sinAsignar = Caso::where('estado', 'pendiente')->whereNull('asignado_a')->count();
        if ($sinAsignar > 5) {
            $alertas[] = [
                'nivel'   => 'warning',
                'mensaje' => "{$sinAsignar} casos pendientes sin asignar",
                'icono'   => 'heroicon-o-clipboard-document',
            ];
        }

        if (empty($alertas)) {
            $alertas[] = [
                'nivel'   => 'success',
                'mensaje' => 'Todo funciona correctamente. No hay alertas activas.',
                'icono'   => 'heroicon-o-check-circle',
            ];
        }

        return $alertas;
    }

    public function getCasosPorDiaProperty(): array
    {
        return Caso::select(
                DB::raw('DATE(created_at) as fecha'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', now()->subDays(14))
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get()
            ->map(fn ($r) => [
                'fecha' => \Carbon\Carbon::parse($r->fecha)->format('d/m'),
                'total' => $r->total,
            ])
            ->toArray();
    }

    public function getDistribucionRolesProperty(): array
    {
        return User::select('rol', DB::raw('COUNT(*) as total'))
            ->where('activo', true)
            ->groupBy('rol')
            ->get()
            ->map(fn ($r) => [
                'rol'   => $r->rol,
                'total' => $r->total,
            ])
            ->toArray();
    }
}

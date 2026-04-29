<?php

namespace App\Filament\Widgets;

use App\Models\Caso;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;

/**
 * Widget de alertas de casos criticos en tiempo real.
 */
class AlertasCriticasWidget extends Widget
{
    protected static ?int $sort = -1;
    protected static bool $isLazy = true;
    protected static string $view = 'filament.widgets.alertas-criticas';
    protected static ?string $pollingInterval = '90s';
    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        $slaVencidos = Caso::where('sla_vencido', true)
            ->whereNotIn('estado', ['cerrado', 'resuelto'])
            ->count();

        $urgentes = Caso::where('prioridad', 'urgente')
            ->whereNotIn('estado', ['cerrado', 'resuelto'])
            ->count();

        $escalados = Caso::where('escalado', true)
            ->whereNotIn('estado', ['cerrado'])
            ->count();

        $sinAsignar = Caso::whereNull('asignado_a')
            ->where('estado', 'pendiente')
            ->count();

        $severidadAlta = Caso::where('nivel_severidad', '>=', 4)
            ->whereNotIn('estado', ['cerrado', 'resuelto'])
            ->count();

        $nuevosHoy = Caso::whereDate('created_at', today())->count();

        return [
            'slaVencidos'   => $slaVencidos,
            'urgentes'      => $urgentes,
            'escalados'     => $escalados,
            'sinAsignar'    => $sinAsignar,
            'severidadAlta' => $severidadAlta,
            'nuevosHoy'     => $nuevosHoy,
            'tieneAlertas'  => ($slaVencidos + $urgentes + $escalados) > 0,
        ];
    }
}



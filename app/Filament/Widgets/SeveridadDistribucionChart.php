<?php

namespace App\Filament\Widgets;

use App\Models\Caso;
use Filament\Widgets\ChartWidget;

/**
 * Gráfico donut: Distribución por nivel de severidad.
 */
class SeveridadDistribucionChart extends ChartWidget
{
    protected static ?string $heading = 'Distribución por Severidad';
    protected static string $color    = 'warning';
    protected static ?int $sort       = 4;
    protected static ?string $pollingInterval = '120s';

    protected function getData(): array
    {
        $niveles = Caso::selectRaw('nivel_severidad, COUNT(*) as total')
            ->whereNotNull('nivel_severidad')
            ->groupBy('nivel_severidad')
            ->orderBy('nivel_severidad')
            ->pluck('total', 'nivel_severidad')
            ->toArray();

        $labels = [];
        $data   = [];
        $colors = [];

        $colorMap = [
            1 => '#6b7280', // gray
            2 => '#10b981', // green
            3 => '#3b82f6', // blue
            4 => '#f59e0b', // amber
            5 => '#ef4444', // red
        ];

        $labelMap = [
            1 => 'Niv. 1 – Muy leve',
            2 => 'Niv. 2 – Leve',
            3 => 'Niv. 3 – Moderado',
            4 => 'Niv. 4 – Grave',
            5 => 'Niv. 5 – Muy grave',
        ];

        for ($i = 1; $i <= 5; $i++) {
            if (isset($niveles[$i]) && $niveles[$i] > 0) {
                $labels[] = $labelMap[$i];
                $data[]   = $niveles[$i];
                $colors[] = $colorMap[$i];
            }
        }

        return [
            'labels'   => $labels,
            'datasets' => [
                [
                    'data'            => $data,
                    'backgroundColor' => $colors,
                    'borderWidth'     => 0,
                    'hoverOffset'     => 6,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'cutout'  => '72%',
            'plugins' => [
                'legend' => ['position' => 'right', 'labels' => ['padding' => 16]],
                'tooltip' => [
                    'backgroundColor' => 'rgba(17,24,39,.9)',
                    'padding'         => 12,
                    'cornerRadius'    => 10,
                ],
            ],
        ];
    }
}

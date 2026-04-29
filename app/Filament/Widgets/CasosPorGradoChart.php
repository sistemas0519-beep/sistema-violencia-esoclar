<?php

namespace App\Filament\Widgets;

use App\Models\Caso;
use Filament\Widgets\ChartWidget;

/**
 * Gráfico de barras horizontales: Casos por grado y grupo escolar.
 */
class CasosPorGradoChart extends ChartWidget
{
    protected static ?string $heading = 'Casos por Grado/Grupo Escolar';
    protected static string $color    = 'info';
    protected static ?int $sort       = 5;
    protected static ?string $pollingInterval = '300s';

    protected function getData(): array
    {
        $data = Caso::selectRaw('grado_grupo, COUNT(*) as total')
            ->whereNotNull('grado_grupo')
            ->where('grado_grupo', '!=', '')
            ->groupBy('grado_grupo')
            ->orderByDesc('total')
            ->limit(10)
            ->pluck('total', 'grado_grupo')
            ->toArray();

        return [
            'labels'   => array_keys($data),
            'datasets' => [
                [
                    'label'           => 'Casos',
                    'data'            => array_values($data),
                    'backgroundColor' => 'rgba(59,130,246,.7)',
                    'borderColor'     => '#3b82f6',
                    'borderWidth'     => 2,
                    'borderRadius'    => 6,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins'   => [
                'legend' => ['display' => false],
                'tooltip' => [
                    'backgroundColor' => 'rgba(17,24,39,.9)',
                    'padding'         => 12,
                    'cornerRadius'    => 10,
                ],
            ],
            'scales' => [
                'x' => ['beginAtZero' => true, 'ticks' => ['stepSize' => 1]],
                'y' => ['grid' => ['display' => false]],
            ],
        ];
    }
}

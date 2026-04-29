<?php

namespace App\Filament\Widgets;

use App\Models\Caso;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

/**
 * Gráfico de líneas: Tendencia de casos de los últimos 12 meses.
 */
class TendenciaMensualChart extends ChartWidget
{
    protected static ?string $heading = 'Tendencia Mensual de Casos';
    protected static string $color    = 'primary';
    protected static ?int $sort       = 3;
    protected static ?string $pollingInterval = '300s';

    public ?string $filter = '12';

    protected function getFilters(): ?array
    {
        return [
            '3'  => 'Últimos 3 meses',
            '6'  => 'Últimos 6 meses',
            '12' => 'Últimos 12 meses',
        ];
    }

    protected function getData(): array
    {
        $meses = (int) $this->filter;

        $labels       = [];
        $casosTotales = [];
        $casosResueltos = [];

        for ($i = $meses - 1; $i >= 0; $i--) {
            $fecha  = Carbon::now()->subMonths($i);
            $labels[] = $fecha->translatedFormat('M Y');

            $casosTotales[] = Caso::whereYear('created_at', $fecha->year)
                ->whereMonth('created_at', $fecha->month)
                ->count();

            $casosResueltos[] = Caso::whereYear('created_at', $fecha->year)
                ->whereMonth('created_at', $fecha->month)
                ->where('estado', 'resuelto')
                ->count();
        }

        return [
            'labels'   => $labels,
            'datasets' => [
                [
                    'label'           => 'Total Casos',
                    'data'            => $casosTotales,
                    'borderColor'     => '#3b82f6',
                    'backgroundColor' => 'rgba(59,130,246,.15)',
                    'fill'            => true,
                    'tension'         => 0.4,
                    'pointRadius'     => 5,
                    'pointHoverRadius' => 8,
                ],
                [
                    'label'           => 'Resueltos',
                    'data'            => $casosResueltos,
                    'borderColor'     => '#10b981',
                    'backgroundColor' => 'rgba(16,185,129,.1)',
                    'fill'            => true,
                    'tension'         => 0.4,
                    'pointRadius'     => 4,
                    'pointHoverRadius' => 7,
                    'borderDash'      => [5, 5],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['position' => 'top'],
                'tooltip' => [
                    'backgroundColor' => 'rgba(17,24,39,.9)',
                    'padding'         => 12,
                    'cornerRadius'    => 10,
                ],
            ],
            'scales' => [
                'y' => ['beginAtZero' => true, 'ticks' => ['stepSize' => 1]],
            ],
        ];
    }
}

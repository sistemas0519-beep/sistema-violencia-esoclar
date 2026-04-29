<?php

namespace App\Filament\Widgets;

use App\Models\Caso;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

/**
 * Grafico de barras: Casos por tipo de violencia (ultimos 30 dias vs anterior).
 */
class CasosPorTipoChart extends ChartWidget
{
    protected static ?string $heading = 'Casos por Tipo de Violencia';
    protected static string $color    = 'danger';
    protected static ?int $sort       = 2;
    protected static bool $isLazy = true;
    protected static ?string $pollingInterval = '120s';

    public ?string $filter = 'all';

    protected function getFilters(): ?array
    {
        return [
            'all'     => 'Todo el periodo',
            'month'   => 'Este mes',
            'week'    => 'Esta semana',
            'year'    => 'Este ano',
        ];
    }

    protected function getData(): array
    {
        $tipos = [
            'fisica'         => 'Fisica',
            'verbal'         => 'Verbal',
            'psicologica'    => 'Psicologica',
            'bullying'       => 'Bullying',
            'cyberbullying'  => 'Cyberbullying',
            'discriminacion' => 'Discriminacion',
            'sexual'         => 'Sexual',
            'ciberacoso'     => 'Ciberacoso',
            'otro'           => 'Otro',
        ];

        $colores = [
            'fisica'         => '#ef4444',
            'verbal'         => '#f97316',
            'psicologica'    => '#eab308',
            'bullying'       => '#dc2626',
            'cyberbullying'  => '#3b82f6',
            'discriminacion' => '#8b5cf6',
            'sexual'         => '#ec4899',
            'ciberacoso'     => '#0ea5e9',
            'otro'           => '#6b7280',
        ];

        $query = Caso::query();
        match ($this->filter) {
            'month' => $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
            'week'  => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
            'year'  => $query->whereYear('created_at', now()->year),
            default => null,
        };

        $data = $query->selectRaw('tipo_violencia, COUNT(*) as total')
            ->groupBy('tipo_violencia')
            ->pluck('total', 'tipo_violencia')
            ->toArray();

        $labels = [];
        $values = [];
        $bgColors = [];

        foreach ($tipos as $key => $label) {
            $labels[]   = $label;
            $values[]   = $data[$key] ?? 0;
            $bgColors[] = ($colores[$key] ?? '#6b7280') . 'cc';
        }

        return [
            'labels'   => $labels,
            'datasets' => [
                [
                    'label'           => 'Casos',
                    'data'            => $values,
                    'backgroundColor' => $bgColors,
                    'borderColor'     => array_map(fn ($c) => substr($c, 0, 7), $bgColors),
                    'borderWidth'     => 2,
                    'borderRadius'    => 8,
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
            'plugins' => [
                'legend' => ['display' => false],
                'tooltip' => [
                    'backgroundColor' => 'rgba(17,24,39,.9)',
                    'padding'         => 12,
                    'cornerRadius'    => 10,
                ],
            ],
            'scales' => [
                'y' => ['beginAtZero' => true, 'ticks' => ['stepSize' => 1]],
                'x' => ['grid' => ['display' => false]],
            ],
        ];
    }
}


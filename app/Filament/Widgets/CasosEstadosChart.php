<?php

namespace App\Filament\Widgets;

use App\Models\Caso;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class CasosEstadosChart extends ChartWidget
{
    protected static ?string $heading = 'Distribucion de Casos por Estado';
    protected static string $color = 'info';
    protected static ?int $sort = 0;
    protected static bool $isLazy = true;

    protected function getStats(): array | null
    {
        return null;
    }

    protected function getData(): array
    {
        $data = Cache::remember('chart:casos_estados', 300, function () {
            $casos = Caso::select('estado')
                ->selectRaw('COUNT(*) as cantidad')
                ->groupBy('estado')
                ->pluck('cantidad', 'estado')
                ->toArray();

            return [
                'labels' => array_map(fn ($e) => ucfirst(str_replace('_', ' ', $e)), array_keys($casos)),
                'datasets' => [
                    [
                        'label' => 'Casos',
                        'data' => array_values($casos),
                        'backgroundColor' => [
                            '#FCD34D', // Amarillo (pendiente)
                            '#60A5FA', // Azul (en_proceso)
                            '#34D399', // Verde (resuelto)
                            '#9CA3AF', // Gris (cerrado)
                        ],
                        'borderColor' => '#fff',
                        'borderWidth' => 2,
                    ],
                ],
            ];
        });

        return $data;
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}


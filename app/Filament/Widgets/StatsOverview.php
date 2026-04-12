<?php

namespace App\Filament\Widgets;

use App\Models\Caso;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total de Casos', Caso::count())
                ->description('Casos registrados en el sistema')
                ->descriptionIcon('heroicon-m-shield-exclamation')
                ->color('primary'),

            Stat::make('Casos Pendientes', Caso::where('estado', 'pendiente')->count())
                ->description('Requieren atención inmediata')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('En Proceso', Caso::where('estado', 'en_proceso')->count())
                ->description('Bajo seguimiento activo')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('info'),

            Stat::make('Casos Resueltos', Caso::where('estado', 'resuelto')->count())
                ->description('Atendidos satisfactoriamente')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Usuarios Registrados', User::count())
                ->description('Estudiantes, docentes y personal')
                ->descriptionIcon('heroicon-m-users')
                ->color('gray'),
        ];
    }
}

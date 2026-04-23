<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class CargaTrabajoPsicologos extends BaseWidget
{
    protected static ?string $heading = 'Carga de Trabajo - Equipo Psicológico';
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = '120s';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->where('rol', 'psicologo')
                    ->where('activo', true)
                    ->withCount([
                        'asignacionesComoPsicologo as asignaciones_activas_count' => fn ($q) => $q->where('estado', 'activa'),
                        'casosAsignados as casos_activos_count'                   => fn ($q) => $q->where('estado', '!=', 'cerrado'),
                    ])
                    ->orderByDesc('asignaciones_activas_count')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Psicólogo')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('especialidad')
                    ->label('Especialidad')
                    ->limit(30)
                    ->default('—'),

                Tables\Columns\TextColumn::make('disponibilidad')
                    ->label('Disponibilidad')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'disponible'   => 'success',
                        'ocupado'      => 'warning',
                        'no_disponible' => 'danger',
                        default        => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', $state ?? ''))),

                Tables\Columns\TextColumn::make('asignaciones_activas_count')
                    ->label('Asignaciones Activas')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 8 => 'danger',
                        $state >= 5 => 'warning',
                        default     => 'success',
                    }),

                Tables\Columns\TextColumn::make('casos_activos_count')
                    ->label('Casos Activos')
                    ->badge()
                    ->color('info'),
            ])
            ->paginated(false)
            ->striped();
    }
}

<?php

namespace App\Filament\Apoyo\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class CargaTrabajoWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected static bool $isLazy = true;
    protected int|string|array $columnSpan = '1';
    protected static ?string $heading = 'Carga de Trabajo del Equipo';
    protected static ?string $pollingInterval = '180s';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->whereIn('rol', ['psicologo', 'asistente'])
                    ->where('activo', true)
                    ->withCount([
                        'asignacionesComoPsicologo as asignaciones_activas_count' => fn ($q) => $q->where('estado', 'activa'),
                        'sesionesComoProfesional as sesiones_semana_count'        => fn ($q) => $q->whereBetween('fecha', [now()->startOfWeek(), now()->endOfWeek()]),
                        'solicitudesAtendidas as solicitudes_pendientes_count'    => fn ($q) => $q->where('estado', 'en_proceso'),
                    ])
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Profesional')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('rol')
                    ->badge()
                    ->colors([
                        'info'    => 'psicologo',
                        'warning' => 'asistente',
                    ]),

                Tables\Columns\TextColumn::make('disponibilidad')
                    ->badge()
                    ->colors([
                        'success' => 'disponible',
                        'warning' => 'ocupado',
                        'danger'  => 'no_disponible',
                    ]),

                Tables\Columns\TextColumn::make('asignaciones_activas_count')
                    ->label('Asignaciones')
                    ->badge()
                    ->color(fn ($state) => $state > 8 ? 'danger' : ($state > 5 ? 'warning' : 'success')),

                Tables\Columns\TextColumn::make('sesiones_semana_count')
                    ->label('Sesiones/Sem')
                    ->badge()
                    ->color('info'),
            ])
            ->paginated(false)
            ->striped();
    }
}



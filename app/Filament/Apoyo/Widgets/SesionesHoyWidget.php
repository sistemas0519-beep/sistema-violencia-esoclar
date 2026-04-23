<?php

namespace App\Filament\Apoyo\Widgets;

use App\Models\Sesion;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class SesionesHoyWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = 'Sesiones de Hoy';
    protected static ?string $pollingInterval = '60s';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Sesion::query()
                    ->where('profesional_id', auth()->id())
                    ->whereDate('fecha', today())
                    ->orderBy('hora_inicio')
            )
            ->columns([
                Tables\Columns\TextColumn::make('hora_inicio')
                    ->label('Hora')
                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('H:i'))
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('hora_fin')
                    ->label('Hasta')
                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('H:i')),

                Tables\Columns\TextColumn::make('paciente.name')
                    ->label('Paciente'),

                Tables\Columns\TextColumn::make('tipo_sesion')
                    ->label('Tipo')
                    ->formatStateUsing(fn (string $state) => str_replace('_', ' ', ucfirst($state)))
                    ->badge()
                    ->colors([
                        'info'    => 'seguimiento',
                        'primary' => 'evaluacion_inicial',
                        'warning' => 'intervencion',
                        'success' => 'cierre',
                        'danger'  => 'emergencia',
                        'gray'    => 'grupal',
                    ]),

                Tables\Columns\TextColumn::make('modalidad')
                    ->badge(),

                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->colors([
                        'gray'    => 'programada',
                        'info'    => 'confirmada',
                        'warning' => 'en_curso',
                        'success' => 'completada',
                        'danger'  => fn ($state) => in_array($state, ['cancelada', 'no_asistio']),
                    ]),

                Tables\Columns\TextColumn::make('lugar')
                    ->default('—')
                    ->toggleable(),
            ])
            ->paginated(false)
            ->emptyStateHeading('Sin sesiones programadas para hoy')
            ->emptyStateIcon('heroicon-o-calendar')
            ->striped();
    }
}

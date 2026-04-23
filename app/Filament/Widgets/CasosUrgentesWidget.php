<?php

namespace App\Filament\Widgets;

use App\Models\Caso;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class CasosUrgentesWidget extends BaseWidget
{
    protected static ?string $heading = 'Casos Urgentes y con SLA Vencido';
    protected static ?int $sort = 2;
    protected static ?string $pollingInterval = '60s';
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Caso::query()
                    ->where(function ($q) {
                        $q->where('prioridad', 'urgente')
                            ->orWhere('sla_vencido', true)
                            ->orWhere('escalado', true);
                    })
                    ->where('estado', '!=', 'cerrado')
                    ->with(['denunciante:id,name', 'asignado:id,name'])
                    ->orderByRaw('CASE WHEN sla_vencido = 1 THEN 1 WHEN escalado = 1 THEN 2 WHEN prioridad = "urgente" THEN 3 ELSE 4 END')
                    ->orderByDesc('created_at')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('codigo_caso')
                    ->label('Codigo')
                    ->copyable()
                    ->weight('bold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('tipo_violencia')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'fisica'         => 'danger',
                        'psicologica'    => 'warning',
                        'verbal'         => 'warning',
                        'sexual'         => 'danger',
                        'ciberacoso'     => 'info',
                        'discriminacion' => 'gray',
                        default          => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', $state))),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pendiente'  => 'warning',
                        'en_proceso' => 'info',
                        'resuelto'   => 'success',
                        'cerrado'    => 'gray',
                        default      => 'gray',
                    }),

                Tables\Columns\TextColumn::make('prioridad')
                    ->label('Prioridad')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'urgente' => 'danger',
                        'alta'    => 'warning',
                        'media'   => 'info',
                        'baja'    => 'gray',
                        default   => 'gray',
                    }),

                Tables\Columns\IconColumn::make('sla_vencido')
                    ->label('SLA')
                    ->boolean()
                    ->trueIcon('heroicon-s-exclamation-triangle')
                    ->trueColor('danger')
                    ->falseIcon('heroicon-o-clock')
                    ->falseColor('success'),

                Tables\Columns\TextColumn::make('asignado.name')
                    ->label('Asignado a')
                    ->default('Sin asignar')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->since()
                    ->sortable(),
            ])
            ->paginated(false)
            ->striped()
            ->emptyStateHeading('Sin casos urgentes')
            ->emptyStateDescription('Todos los casos estan siendo atendidos adecuadamente')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
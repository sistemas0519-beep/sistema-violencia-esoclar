<?php

namespace App\Filament\Apoyo\Widgets;

use App\Models\Caso;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class CasosUrgentesWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected static bool $isLazy = true;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = 'Casos Urgentes y Sensibles';
    protected static ?string $pollingInterval = '120s';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Caso::query()
                    ->where(function ($q) {
                        $q->where('asignado_a', auth()->id())
                            ->orWhere('estado', 'pendiente');
                    })
                    ->where(function ($q) {
                        $q->whereIn('nivel_urgencia', ['inmediata', 'critico', 'alta'])
                            ->orWhere('prioridad', 'urgente')
                            ->orWhere('es_sensible', true)
                            ->orWhere('sla_vencido', true)
                            ->orWhere('escalado', true);
                    })
                    ->activos()
                    ->orderByRaw("FIELD(nivel_urgencia, 'inmediata', 'critico', 'alta', 'media', 'medio', 'baja')")
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('codigo_caso')
                    ->label('Codigo')
                    ->weight('bold')
                    ->copyable(),

                Tables\Columns\TextColumn::make('nivel_urgencia')
                    ->label('Urgencia')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => ucfirst(str_replace('_', ' ', (string) $state)))
                    ->colors([
                        'danger'  => fn (?string $state) => in_array($state, ['inmediata', 'critico'], true),
                        'warning' => 'alta',
                        'info'    => fn (?string $state) => in_array($state, ['media', 'medio'], true),
                        'gray'    => 'baja',
                    ]),

                Tables\Columns\IconColumn::make('es_sensible')
                    ->label('Sens.')
                    ->boolean()
                    ->trueIcon('heroicon-s-shield-exclamation')
                    ->trueColor('danger'),

                Tables\Columns\TextColumn::make('tipo_violencia')
                    ->label('Tipo')
                    ->formatStateUsing(fn (string $state) => ucfirst($state)),

                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->colors([
                        'warning' => 'pendiente',
                        'info'    => fn (?string $state) => in_array($state, ['en_proceso', 'en_seguimiento'], true),
                        'success' => 'resuelto',
                    ]),

                Tables\Columns\IconColumn::make('sla_vencido')
                    ->label('SLA')
                    ->boolean()
                    ->trueIcon('heroicon-s-exclamation-triangle')
                    ->trueColor('danger')
                    ->falseIcon('heroicon-o-clock')
                    ->falseColor('success'),

                Tables\Columns\TextColumn::make('asignado.name')
                    ->label('Asignado')
                    ->default('Sin asignar'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->since(),
            ])
            ->paginated(false)
            ->emptyStateHeading('Sin casos urgentes pendientes')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->striped();
    }
}



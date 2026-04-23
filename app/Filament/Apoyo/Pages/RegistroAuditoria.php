<?php

namespace App\Filament\Apoyo\Pages;

use App\Models\AuditLog;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RegistroAuditoria extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-finger-print';
    protected static ?string $navigationGroup = 'Métricas';
    protected static ?string $navigationLabel = 'Registro de Auditoría';
    protected static ?string $title = 'Registro de Auditoría';
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.apoyo.pages.registro-auditoria';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                AuditLog::query()
                    ->where('user_id', auth()->id())
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('accion')
                    ->badge()
                    ->colors([
                        'success' => 'crear',
                        'info'    => 'lectura',
                        'warning' => 'actualizar',
                        'danger'  => fn ($state) => in_array($state, ['eliminar', 'cancelar']),
                        'primary' => fn ($state) => in_array($state, ['asignar', 'completar']),
                    ]),

                Tables\Columns\TextColumn::make('modulo')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('descripcion')
                    ->limit(60)
                    ->wrap(),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha y Hora')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('modulo')
                    ->options([
                        'casos_sensibles'      => 'Casos Sensibles',
                        'notas_confidenciales' => 'Notas Confidenciales',
                        'solicitudes_asesoria' => 'Solicitudes de Asesoría',
                        'sesiones'             => 'Sesiones',
                        'intervenciones'       => 'Intervenciones',
                    ]),
                Tables\Filters\SelectFilter::make('accion')
                    ->options([
                        'crear'      => 'Crear',
                        'lectura'    => 'Lectura',
                        'actualizar' => 'Actualizar',
                        'eliminar'   => 'Eliminar',
                        'asignar'    => 'Asignar',
                        'completar'  => 'Completar',
                        'cancelar'   => 'Cancelar',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50]);
    }
}

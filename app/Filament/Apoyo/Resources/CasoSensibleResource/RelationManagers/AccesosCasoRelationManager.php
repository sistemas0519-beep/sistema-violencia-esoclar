<?php

namespace App\Filament\Apoyo\Resources\CasoSensibleResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AccesosCasoRelationManager extends RelationManager
{
    protected static string $relationship = 'accesos';
    protected static ?string $title = 'Registro de Accesos';
    protected static ?string $icon = 'heroicon-o-eye';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->weight('bold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.rol')
                    ->label('Rol')
                    ->badge()
                    ->colors([
                        'info'    => 'psicologo',
                        'warning' => 'asistente',
                        'danger'  => 'admin',
                    ]),

                Tables\Columns\TextColumn::make('tipo_acceso')
                    ->label('Tipo')
                    ->badge()
                    ->colors([
                        'info'    => 'lectura',
                        'warning' => 'escritura',
                        'danger'  => 'descarga',
                        'gray'    => 'impresion',
                    ]),

                Tables\Columns\TextColumn::make('seccion_accedida')
                    ->label('Sección')
                    ->default('—'),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha y Hora')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}

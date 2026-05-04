<?php

namespace App\Filament\Apoyo\Resources\CasoSensibleResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SeguimientosRelationManager extends RelationManager
{
    protected static string $relationship = 'seguimientos';
    protected static ?string $title = 'Seguimientos';
    protected static ?string $icon = 'heroicon-o-clipboard-document-list';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('responsable.name')
                    ->label('Responsable')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('accion')
                    ->label('Acción')
                    ->limit(50),

                Tables\Columns\TextColumn::make('notas')
                    ->label('Notas')
                    ->limit(60)
                    ->wrap(),

                Tables\Columns\TextColumn::make('fecha_seguimiento')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('fecha_seguimiento', 'desc')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Nuevo Seguimiento')
                    ->form([
                        Forms\Components\Select::make('accion')
                            ->label('Tipo de Acción')
                            ->options([
                                'llamada'      => '📞 Llamada telefónica',
                                'reunion'      => '🤝 Reunión presencial',
                                'intervencion' => '🎯 Intervención directa',
                                'derivacion'   => '↗️ Derivación a especialista',
                                'cierre'       => '✅ Cierre de caso',
                                'otro'         => '📋 Otro',
                            ])
                            ->required()
                            ->native(false)
                            ->default('otro'),
                        Forms\Components\Textarea::make('notas')
                            ->label('Descripción')
                            ->rows(3)
                            ->required(),
                        Forms\Components\DateTimePicker::make('fecha_seguimiento')
                            ->label('Fecha y Hora')
                            ->default(now())
                            ->required(),
                        Forms\Components\Hidden::make('responsable_id')
                            ->default(fn () => auth()->id()),
                    ]),
            ]);
    }
}

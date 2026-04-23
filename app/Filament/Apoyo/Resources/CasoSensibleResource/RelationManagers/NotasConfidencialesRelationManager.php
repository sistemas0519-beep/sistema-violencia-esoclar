<?php

namespace App\Filament\Apoyo\Resources\CasoSensibleResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NotasConfidencialesRelationManager extends RelationManager
{
    protected static string $relationship = 'notasConfidenciales';
    protected static ?string $title = 'Notas Confidenciales';
    protected static ?string $icon = 'heroicon-o-lock-closed';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->visiblesPara(auth()->user())
            )
            ->columns([
                Tables\Columns\TextColumn::make('autor.name')
                    ->label('Autor')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('contenido')
                    ->limit(80)
                    ->wrap(),

                Tables\Columns\TextColumn::make('visibilidad')
                    ->badge()
                    ->colors([
                        'gray'    => 'solo_autor',
                        'info'    => 'psicologos',
                        'success' => 'equipo_apoyo',
                    ])
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'solo_autor'   => 'Solo autor',
                        'psicologos'   => 'Psicólogos',
                        'equipo_apoyo' => 'Equipo',
                        default        => $state,
                    }),

                Tables\Columns\IconColumn::make('es_critica')
                    ->label('Crítica')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->trueColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Agregar Nota')
                    ->form([
                        Forms\Components\Textarea::make('contenido')
                            ->required()
                            ->rows(4),
                        Forms\Components\Select::make('visibilidad')
                            ->options([
                                'solo_autor'   => 'Solo yo',
                                'psicologos'   => 'Psicólogos',
                                'equipo_apoyo' => 'Todo el equipo',
                            ])
                            ->default('equipo_apoyo')
                            ->required(),
                        Forms\Components\Toggle::make('es_critica')
                            ->label('¿Es nota crítica?'),
                    ])
                    ->mutateFormDataBeforeCreate(function (array $data): array {
                        $data['autor_id'] = auth()->id();
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => $record->autor_id === auth()->id()),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn ($record) => $record->autor_id === auth()->id()),
            ]);
    }
}

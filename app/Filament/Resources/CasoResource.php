<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CasoResource\Pages;
use App\Models\Caso;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CasoResource extends Resource
{
    protected static ?string $model = Caso::class;

    protected static ?string $navigationIcon    = 'heroicon-o-shield-exclamation';
    protected static ?string $navigationGroup   = 'Gestión de Casos';
    protected static ?string $navigationLabel   = 'Casos';
    protected static ?string $modelLabel        = 'Caso';
    protected static ?string $pluralModelLabel  = 'Casos';
    protected static ?int    $navigationSort    = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Información del Caso')
                ->schema([
                    Forms\Components\TextInput::make('codigo_caso')
                        ->label('Código del Caso')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->placeholder('VIO-2026-001'),

                    Forms\Components\Select::make('tipo_violencia')
                        ->label('Tipo de Violencia')
                        ->required()
                        ->options([
                            'fisica'         => 'Física',
                            'psicologica'    => 'Psicológica',
                            'verbal'         => 'Verbal',
                            'sexual'         => 'Sexual',
                            'ciberacoso'     => 'Ciberacoso',
                            'discriminacion' => 'Discriminación',
                            'otro'           => 'Otro',
                        ]),

                    Forms\Components\Select::make('estado')
                        ->label('Estado')
                        ->required()
                        ->options([
                            'pendiente'  => 'Pendiente',
                            'en_proceso' => 'En Proceso',
                            'resuelto'   => 'Resuelto',
                            'cerrado'    => 'Cerrado',
                        ])
                        ->default('pendiente'),

                    Forms\Components\DateTimePicker::make('fecha_incidente')
                        ->label('Fecha del Incidente')
                        ->nullable(),
                ])->columns(2),

            Forms\Components\Section::make('Descripción')
                ->schema([
                    Forms\Components\Textarea::make('descripcion')
                        ->label('Descripción del Caso')
                        ->required()
                        ->rows(4)
                        ->columnSpanFull(),

                    Forms\Components\Toggle::make('es_anonimo')
                        ->label('Denuncia Anónima')
                        ->default(false),
                ]),

            Forms\Components\Section::make('Personas Involucradas')
                ->schema([
                    Forms\Components\Select::make('denunciante_id')
                        ->label('Denunciante')
                        ->relationship('denunciante', 'name')
                        ->searchable()
                        ->nullable()
                        ->preload(),

                    Forms\Components\Select::make('asignado_a')
                        ->label('Asignado a (Psicólogo)')
                        ->options(
                            User::where('rol', 'psicologo')->pluck('name', 'id')
                        )
                        ->searchable()
                        ->nullable(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo_caso')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('tipo_violencia')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'fisica'         => 'danger',
                        'psicologica'    => 'warning',
                        'verbal'         => 'warning',
                        'ciberacoso'     => 'info',
                        'sexual'         => 'primary',
                        'discriminacion' => 'gray',
                        default          => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'fisica'         => 'Física',
                        'psicologica'    => 'Psicológica',
                        'verbal'         => 'Verbal',
                        'sexual'         => 'Sexual',
                        'ciberacoso'     => 'Ciberacoso',
                        'discriminacion' => 'Discriminación',
                        default          => 'Otro',
                    }),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'pendiente'  => 'warning',
                        'en_proceso' => 'primary',
                        'resuelto'   => 'success',
                        'cerrado'    => 'gray',
                        default      => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'pendiente'  => 'Pendiente',
                        'en_proceso' => 'En Proceso',
                        'resuelto'   => 'Resuelto',
                        'cerrado'    => 'Cerrado',
                        default      => $state,
                    }),

                Tables\Columns\IconColumn::make('es_anonimo')
                    ->label('Anónimo')
                    ->boolean(),

                Tables\Columns\TextColumn::make('denunciante.name')
                    ->label('Denunciante')
                    ->default('Anónimo')
                    ->searchable(),

                Tables\Columns\TextColumn::make('asignado.name')
                    ->label('Asignado a')
                    ->default('—')
                    ->searchable(),

                Tables\Columns\TextColumn::make('fecha_incidente')
                    ->label('Fecha Incidente')
                    ->dateTime('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo_violencia')
                    ->label('Tipo de Violencia')
                    ->options([
                        'fisica'         => 'Física',
                        'psicologica'    => 'Psicológica',
                        'verbal'         => 'Verbal',
                        'sexual'         => 'Sexual',
                        'ciberacoso'     => 'Ciberacoso',
                        'discriminacion' => 'Discriminación',
                        'otro'           => 'Otro',
                    ]),
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'pendiente'  => 'Pendiente',
                        'en_proceso' => 'En Proceso',
                        'resuelto'   => 'Resuelto',
                        'cerrado'    => 'Cerrado',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCasos::route('/'),
            'create' => Pages\CreateCaso::route('/create'),
            'edit'   => Pages\EditCaso::route('/{record}/edit'),
        ];
    }
}

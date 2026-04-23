<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AsignacionResource\Pages;
use App\Models\Asignacion;
use App\Models\Caso;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AsignacionResource extends Resource
{
    protected static ?string $model = Asignacion::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Gestión';

    protected static ?string $navigationLabel = 'Asignaciones';

    protected static ?string $label = 'Asignación';

    protected static ?string $pluralLabel = 'Asignaciones';

    protected static ?int    $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Asignación')
                    ->schema([
                        Forms\Components\Select::make('psicologo_id')
                            ->label('Psicólogo')
                            ->options(fn () => User::where('rol', 'psicologo')
                                ->where('disponibilidad', '!=', 'no_disponible')
                                ->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('Solo psicólogos disponibles'),

                        Forms\Components\Select::make('paciente_id')
                            ->label('Alumno')
                            ->options(fn () => User::where('rol', 'alumno')
                                ->orderBy('name')
                                ->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('Alumno que recibirá la asignación'),

                        Forms\Components\Select::make('caso_id')
                            ->label('Caso (opcional)')
                            ->options(fn () => Caso::where('estado', '!=', 'cerrado')
                                ->pluck('codigo_caso', 'id'))
                            ->nullable()
                            ->searchable()
                            ->preload()
                            ->helperText('Vincular a un caso existente'),

                        Forms\Components\Textarea::make('notas')
                            ->label('Notas')
                            ->placeholder('Notas o motivo de la asignación')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Horario de Atención')
                    ->schema([
                        Forms\Components\DatePicker::make('fecha_inicio')
                            ->label('Fecha de Inicio')
                            ->required()
                            ->default(now()),

                        Forms\Components\DatePicker::make('fecha_fin')
                            ->label('Fecha de Fin')
                            ->nullable()
                            ->helperText('Dejar vacío si la asignación está activa'),

                        Forms\Components\Select::make('frecuencia_atencion')
                            ->label('Frecuencia de Atención')
                            ->options([
                                'semanal' => 'Semanal',
                                'quincenal' => 'Quincenal',
                                'mensual' => 'Mensual',
                            ])
                            ->required()
                            ->default('semanal'),

                        Forms\Components\Select::make('dia_atencion')
                            ->label('Día de Atención')
                            ->options([
                                'lunes' => 'Lunes',
                                'martes' => 'Martes',
                                'miercoles' => 'Miércoles',
                                'jueves' => 'Jueves',
                                'viernes' => 'Viernes',
                            ])
                            ->nullable(),

                        Forms\Components\TimePicker::make('hora_atencion')
                            ->label('Hora de Atención')
                            ->nullable()
                            ->seconds(false),
                    ])->columns(3),

                Forms\Components\Section::make('Estado')
                    ->schema([
                        Forms\Components\Select::make('estado')
                            ->label('Estado')
                            ->options([
                                'activa' => 'Activa',
                                'finalizada' => 'Finalizada',
                                'cancelada' => 'Cancelada',
                            ])
                            ->required()
                            ->default('activa'),

                        Forms\Components\Textarea::make('motivo_fin')
                            ->label('Motivo de Finalización')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('paciente.name')
                    ->label('Paciente')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('psicologo.name')
                    ->label('Psicólogo')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('caso.codigo_caso')
                    ->label('Caso')
                    ->placeholder('Sin caso'),

                Tables\Columns\TextColumn::make('fecha_inicio')
                    ->label('Fecha Inicio')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('frecuencia_atencion')
                    ->label('Frecuencia')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'semanal' => 'Semanal',
                        'quincenal' => 'Quincenal',
                        'mensual' => 'Mensual',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('dia_atencion')
                    ->label('Día')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('hora_atencion')
                    ->label('Hora')
                    ->placeholder('-'),

                Tables\Columns\BadgeColumn::make('estado')
                    ->colors([
                        'success' => 'activa',
                        'warning' => 'finalizada',
                        'danger' => 'cancelada',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'activa' => 'Activa',
                        'finalizada' => 'Finalizada',
                        'cancelada' => 'Cancelada',
                        default => $state,
                    }),

                Tables\Columns\IconColumn::make('solicitud_cambio')
                    ->label('Cambio')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-circle')
                    ->falseIcon(''),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'activa' => 'Activa',
                        'finalizada' => 'Finalizada',
                        'cancelada' => 'Cancelada',
                    ]),

                Tables\Filters\SelectFilter::make('psicologo_id')
                    ->label('Psicólogo')
                    ->options(fn () => User::where('rol', 'psicologo')->pluck('name', 'id')),

                Tables\Filters\SelectFilter::make('paciente_id')
                    ->label('Alumno')
                    ->options(fn () => User::where('rol', 'alumno')
                        ->orderBy('name')
                        ->pluck('name', 'id')),

                Tables\Filters\SelectFilter::make('frecuencia_atencion')
                    ->label('Frecuencia')
                    ->options([
                        'semanal' => 'Semanal',
                        'quincenal' => 'Quincenal',
                        'mensual' => 'Mensual',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('finalizar')
                    ->label('Finalizar')
                    ->icon('heroicon-o-check-circle')
                    ->color('warning')
                    ->visible(fn (Asignacion $record) => $record->estado === 'activa')
                    ->form([
                        Forms\Components\Textarea::make('motivo')
                            ->label('Motivo de finalización')
                            ->required(),
                    ])
                    ->action(function (Asignacion $record, array $data) {
                        $user = auth()->user();
                        $record->finalizar($data['motivo'], $user?->id);
                    }),

                Tables\Actions\Action::make('ver_historial')
                    ->label('Historial')
                    ->icon('heroicon-o-clock')
                    ->url(fn (Asignacion $record) => static::getUrl('index', [
                        'tableFilters' => [
                            'paciente_id' => [
                                'value' => (string) $record->paciente_id,
                            ],
                        ],
                    ])),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAsignaciones::route('/'),
            'create' => Pages\CreateAsignacion::route('/create'),
            'edit' => Pages\EditAsignacion::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('estado', 'activa')->count() ?: null;
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();

        return $user && $user->esAdmin();
    }
}

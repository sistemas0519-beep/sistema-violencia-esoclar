<?php

namespace App\Filament\Apoyo\Resources;

use App\Filament\Apoyo\Resources\IntervencionResource\Pages;
use App\Models\AuditLog;
use App\Models\Intervencion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class IntervencionResource extends Resource
{
    protected static ?string $model = Intervencion::class;

    protected static ?string $navigationIcon = 'heroicon-o-hand-raised';
    protected static ?string $navigationGroup = 'Asesoría y Apoyo';
    protected static ?string $navigationLabel = 'Intervenciones';
    protected static ?string $modelLabel = 'Intervención';
    protected static ?string $pluralModelLabel = 'Intervenciones';
    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        return (string) Intervencion::activas()->delProfesional(auth()->id())->count();
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        return parent::getEloquentQuery()
            ->when($user->esPsicologo() || $user->esAsistente(), function ($q) use ($user) {
                $q->where('profesional_id', $user->id);
            });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Datos de la Intervención')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('codigo')
                            ->default(fn () => Intervencion::generarCodigo())
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\Select::make('tipo_intervencion')
                            ->label('Tipo de Intervención')
                            ->options([
                                'contencion_emocional'    => 'Contención Emocional',
                                'orientacion_individual'  => 'Orientación Individual',
                                'orientacion_familiar'    => 'Orientación Familiar',
                                'derivacion_externa'      => 'Derivación Externa',
                                'mediacion'               => 'Mediación',
                                'plan_seguridad'          => 'Plan de Seguridad',
                                'intervencion_crisis'     => 'Intervención de Crisis',
                                'taller_grupal'           => 'Taller Grupal',
                                'acompanamiento'          => 'Acompañamiento',
                                'otro'                    => 'Otro',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('caso_id')
                            ->label('Caso')
                            ->relationship('caso', 'codigo_caso')
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('profesional_id')
                            ->label('Profesional')
                            ->relationship('profesional', 'name', fn ($query) => $query->whereIn('rol', ['psicologo', 'asistente']))
                            ->default(auth()->id())
                            ->required(),

                        Forms\Components\Select::make('sesion_id')
                            ->label('Sesión Vinculada')
                            ->relationship('sesion', 'id', fn ($query) => $query->delProfesional(auth()->id()))
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->fecha->format('d/m/Y') . ' — ' . $record->paciente->name)
                            ->nullable(),

                        Forms\Components\Select::make('estado')
                            ->options([
                                'planificada' => 'Planificada',
                                'en_curso'    => 'En Curso',
                                'completada'  => 'Completada',
                                'suspendida'  => 'Suspendida',
                            ])
                            ->default('planificada')
                            ->required()
                            ->native(false),

                        Forms\Components\DatePicker::make('fecha_inicio')
                            ->default(today())
                            ->required(),

                        Forms\Components\DatePicker::make('fecha_fin')
                            ->nullable(),
                    ]),

                Forms\Components\Section::make('Descripción y Resultados')
                    ->schema([
                        Forms\Components\Textarea::make('descripcion')
                            ->required()
                            ->rows(3),

                        Forms\Components\Textarea::make('acciones_realizadas')
                            ->label('Acciones Realizadas')
                            ->rows(3),

                        Forms\Components\Textarea::make('resultados_observados')
                            ->label('Resultados Observados')
                            ->rows(3),

                        Forms\Components\Textarea::make('recomendaciones')
                            ->rows(3),

                        Forms\Components\Select::make('efectividad')
                            ->options([
                                'muy_efectiva'           => '⭐ Muy Efectiva',
                                'efectiva'               => '✅ Efectiva',
                                'parcial'                => '⚠️ Parcial',
                                'sin_efecto'             => '❌ Sin Efecto',
                                'pendiente_evaluacion'   => '⏳ Pendiente de Evaluación',
                            ])
                            ->default('pendiente_evaluacion')
                            ->native(false),
                    ]),

                Forms\Components\Section::make('Seguimiento')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Toggle::make('requiere_seguimiento')
                            ->default(true)
                            ->reactive(),

                        Forms\Components\DatePicker::make('proximo_seguimiento')
                            ->label('Próximo Seguimiento')
                            ->visible(fn (Forms\Get $get) => $get('requiere_seguimiento')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),

                Tables\Columns\TextColumn::make('tipo_intervencion')
                    ->label('Tipo')
                    ->formatStateUsing(fn (string $state) => str_replace('_', ' ', ucfirst($state)))
                    ->wrap(),

                Tables\Columns\TextColumn::make('caso.codigo_caso')
                    ->label('Caso')
                    ->searchable(),

                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->colors([
                        'gray'    => 'planificada',
                        'info'    => 'en_curso',
                        'success' => 'completada',
                        'danger'  => 'suspendida',
                    ]),

                Tables\Columns\TextColumn::make('efectividad')
                    ->badge()
                    ->colors([
                        'success' => fn ($state) => in_array($state, ['muy_efectiva', 'efectiva']),
                        'warning' => 'parcial',
                        'danger'  => 'sin_efecto',
                        'gray'    => 'pendiente_evaluacion',
                    ])
                    ->formatStateUsing(fn (string $state) => str_replace('_', ' ', ucfirst($state))),

                Tables\Columns\IconColumn::make('requiere_seguimiento')
                    ->label('Seg.')
                    ->boolean(),

                Tables\Columns\TextColumn::make('fecha_inicio')
                    ->label('Inicio')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('proximo_seguimiento')
                    ->label('Próx. Seguimiento')
                    ->date('d/m/Y')
                    ->color(fn (?string $state) => $state && now()->gt($state) ? 'danger' : null),
            ])
            ->defaultSort('fecha_inicio', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'planificada' => 'Planificada',
                        'en_curso'    => 'En Curso',
                        'completada'  => 'Completada',
                        'suspendida'  => 'Suspendida',
                    ]),
                Tables\Filters\SelectFilter::make('tipo_intervencion')
                    ->label('Tipo')
                    ->options([
                        'contencion_emocional'    => 'Contención Emocional',
                        'orientacion_individual'  => 'Orientación Individual',
                        'orientacion_familiar'    => 'Orientación Familiar',
                        'derivacion_externa'      => 'Derivación Externa',
                        'mediacion'               => 'Mediación',
                        'plan_seguridad'          => 'Plan de Seguridad',
                        'intervencion_crisis'     => 'Intervención de Crisis',
                        'taller_grupal'           => 'Taller Grupal',
                        'acompanamiento'          => 'Acompañamiento',
                        'otro'                    => 'Otro',
                    ]),
                Tables\Filters\Filter::make('pendiente_seguimiento')
                    ->label('Pendientes de Seguimiento')
                    ->query(fn (Builder $query) => $query->pendientesSeguimiento()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('completar')
                    ->label('Completar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Intervencion $record) => in_array($record->estado, ['planificada', 'en_curso']))
                    ->form([
                        Forms\Components\Textarea::make('resultados')
                            ->label('Resultados Observados')
                            ->required()
                            ->rows(3),
                        Forms\Components\Select::make('efectividad')
                            ->options([
                                'muy_efectiva' => 'Muy Efectiva',
                                'efectiva'     => 'Efectiva',
                                'parcial'      => 'Parcial',
                                'sin_efecto'   => 'Sin Efecto',
                            ])
                            ->required()
                            ->native(false),
                    ])
                    ->action(function (Intervencion $record, array $data) {
                        $record->completar($data['resultados'], $data['efectividad']);
                        AuditLog::registrar('completar', 'intervenciones', "Completó intervención {$record->codigo}", $record);
                    }),
            ])
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListIntervenciones::route('/'),
            'create' => Pages\CreateIntervencion::route('/create'),
            'view'   => Pages\ViewIntervencion::route('/{record}'),
            'edit'   => Pages\EditIntervencion::route('/{record}/edit'),
        ];
    }
}

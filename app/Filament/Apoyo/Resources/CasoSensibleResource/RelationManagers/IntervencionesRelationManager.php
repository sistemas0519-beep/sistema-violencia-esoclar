<?php

namespace App\Filament\Apoyo\Resources\CasoSensibleResource\RelationManagers;

use App\Models\Intervencion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class IntervencionesRelationManager extends RelationManager
{
    protected static string $relationship = 'intervenciones';
    protected static ?string $title = 'Intervenciones';
    protected static ?string $icon = 'heroicon-o-hand-raised';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->weight('bold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('tipo_intervencion')
                    ->label('Tipo')
                    ->formatStateUsing(fn (string $state) => str_replace('_', ' ', ucfirst($state))),

                Tables\Columns\TextColumn::make('profesional.name')
                    ->label('Profesional'),

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

                Tables\Columns\TextColumn::make('fecha_inicio')
                    ->label('Inicio')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('fecha_inicio', 'desc')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Nueva Intervención')
                    ->form([
                        Forms\Components\Select::make('tipo_intervencion')
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
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\Textarea::make('descripcion')
                            ->required()
                            ->rows(3),
                        Forms\Components\DatePicker::make('fecha_inicio')
                            ->default(today())
                            ->required(),
                        Forms\Components\Toggle::make('requiere_seguimiento')
                            ->default(true),
                        Forms\Components\DatePicker::make('proximo_seguimiento')
                            ->visible(fn (Forms\Get $get) => $get('requiere_seguimiento')),
                    ])
                    ->mutateFormDataBeforeCreate(function (array $data): array {
                        $data['profesional_id'] = auth()->id();
                        $data['codigo'] = Intervencion::generarCodigo();
                        return $data;
                    }),
            ]);
    }
}

<?php

namespace App\Filament\Apoyo\Resources;

use App\Filament\Apoyo\Resources\SesionResource\Pages;
use App\Models\AuditLog;
use App\Models\Sesion;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SesionResource extends Resource
{
    protected static ?string $model = Sesion::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Asesoría y Apoyo';
    protected static ?string $navigationLabel = 'Sesiones Programadas';
    protected static ?string $modelLabel = 'Sesión';
    protected static ?string $pluralModelLabel = 'Sesiones';
    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return (string) Sesion::hoy()->delProfesional(auth()->id())->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'info';
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
                Forms\Components\Section::make('Datos de la Sesión')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Select::make('paciente_id')
                            ->label('Estudiante')
                            ->relationship(
                                name: 'paciente',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn ($query) => $query
                                    ->where('rol', 'alumno')
                                    ->orderBy('name')
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(),

                        Forms\Components\Select::make('profesional_id')
                            ->label('Profesional')
                            ->options(fn () => User::whereIn('rol', ['psicologo', 'asistente'])->pluck('name', 'id'))
                            ->default(auth()->id())
                            ->required(),

                        Forms\Components\Select::make('caso_id')
                            ->label('Caso Relacionado')
                            ->relationship(
                                name: 'caso',
                                titleAttribute: 'codigo_caso',
                                modifyQueryUsing: fn ($query, Get $get) => $query
                                    ->when(
                                        $get('paciente_id'),
                                        fn ($q, $pacienteId) => $q->where('denunciante_id', $pacienteId)
                                    )
                            )
                            ->searchable()
                            ->nullable()
                            ->placeholder('Seleccione primero un estudiante'),


                        Forms\Components\Select::make('asignacion_id')
                            ->label('Asignación')
                            ->relationship('asignacion', 'id', fn ($query) => $query->activas())
                            ->nullable(),

                        Forms\Components\Select::make('tipo_sesion')
                            ->label('Tipo de Sesión')
                            ->options([
                                'evaluacion_inicial' => 'Evaluación Inicial',
                                'seguimiento'        => 'Seguimiento',
                                'intervencion'       => 'Intervención',
                                'cierre'             => 'Cierre',
                                'emergencia'         => '🚨 Emergencia',
                                'grupal'             => 'Grupal',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('modalidad')
                            ->options([
                                'presencial'  => '🏫 Presencial',
                                'virtual'     => '💻 Virtual',
                                'telefonica'  => '📞 Telefónica',
                            ])
                            ->default('presencial')
                            ->required()
                            ->native(false),
                    ]),

                Forms\Components\Section::make('Horario')
                    ->columns(4)
                    ->schema([
                        Forms\Components\DatePicker::make('fecha')
                            ->required()
                            ->default(today()),

                        Forms\Components\TimePicker::make('hora_inicio')
                            ->label('Hora Inicio')
                            ->required()
                            ->seconds(false),

                        Forms\Components\TimePicker::make('hora_fin')
                            ->label('Hora Fin')
                            ->required()
                            ->seconds(false),

                        Forms\Components\Select::make('estado')
                            ->options([
                                'programada'    => 'Programada',
                                'confirmada'    => 'Confirmada',
                                'en_curso'      => 'En Curso',
                                'completada'    => 'Completada',
                                'cancelada'     => 'Cancelada',
                                'no_asistio'    => 'No Asistió',
                                'reprogramada'  => 'Reprogramada',
                            ])
                            ->default('programada')
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('lugar')
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('duracion_real_minutos')
                            ->label('Duración Real (min)')
                            ->numeric()
                            ->visible(fn (?Sesion $record) => $record && $record->estado === 'completada'),
                    ]),

                Forms\Components\Section::make('Notas y Resumen')
                    ->schema([
                        Forms\Components\Textarea::make('objetivo')
                            ->rows(2),

                        Forms\Components\Textarea::make('notas_preparacion')
                            ->label('Notas de Preparación')
                            ->rows(2),

                        Forms\Components\Textarea::make('resumen_sesion')
                            ->label('Resumen de la Sesión')
                            ->rows(4)
                            ->visible(fn (?Sesion $record) => $record && in_array($record->estado, ['completada', 'no_asistio'])),

                        Forms\Components\Textarea::make('motivo_cancelacion')
                            ->label('Motivo de Cancelación')
                            ->rows(2)
                            ->visible(fn (?Sesion $record) => $record && $record->estado === 'cancelada'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha')
                    ->date('d/m/Y')
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('horario_formateado')
                    ->label('Horario'),

                Tables\Columns\TextColumn::make('paciente.name')
                    ->label('Paciente')
                    ->searchable(),

                Tables\Columns\TextColumn::make('tipo_sesion')
                    ->label('Tipo')
                    ->formatStateUsing(fn (string $state) => str_replace('_', ' ', ucfirst($state)))
                    ->badge()
                    ->colors([
                        'info'    => 'seguimiento',
                        'primary' => 'evaluacion_inicial',
                        'warning' => 'intervencion',
                        'success' => 'cierre',
                        'danger'  => 'emergencia',
                        'gray'    => 'grupal',
                    ]),

                Tables\Columns\TextColumn::make('modalidad')
                    ->badge()
                    ->colors([
                        'success' => 'presencial',
                        'info'    => 'virtual',
                        'warning' => 'telefonica',
                    ]),

                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->colors([
                        'gray'    => 'programada',
                        'info'    => 'confirmada',
                        'warning' => 'en_curso',
                        'success' => 'completada',
                        'danger'  => fn ($state) => in_array($state, ['cancelada', 'no_asistio']),
                        'primary' => 'reprogramada',
                    ])
                    ->formatStateUsing(fn (string $state) => str_replace('_', ' ', ucfirst($state))),

                Tables\Columns\TextColumn::make('caso.codigo_caso')
                    ->label('Caso')
                    ->default('—')
                    ->toggleable(),
            ])
            ->defaultSort('fecha', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'programada'   => 'Programada',
                        'confirmada'   => 'Confirmada',
                        'en_curso'     => 'En Curso',
                        'completada'   => 'Completada',
                        'cancelada'    => 'Cancelada',
                        'no_asistio'   => 'No Asistió',
                        'reprogramada' => 'Reprogramada',
                    ]),
                Tables\Filters\SelectFilter::make('tipo_sesion')
                    ->label('Tipo')
                    ->options([
                        'evaluacion_inicial' => 'Evaluación Inicial',
                        'seguimiento'        => 'Seguimiento',
                        'intervencion'       => 'Intervención',
                        'cierre'             => 'Cierre',
                        'emergencia'         => 'Emergencia',
                        'grupal'             => 'Grupal',
                    ]),
                Tables\Filters\Filter::make('hoy')
                    ->label('Hoy')
                    ->query(fn (Builder $query) => $query->whereDate('fecha', today())),
                Tables\Filters\Filter::make('esta_semana')
                    ->label('Esta Semana')
                    ->query(fn (Builder $query) => $query->semanaActual()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('completar_sesion')
                    ->label('Completar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Sesion $record) => in_array($record->estado, ['programada', 'confirmada', 'en_curso']))
                    ->form([
                        Forms\Components\Textarea::make('resumen')
                            ->label('Resumen de la Sesión')
                            ->required()
                            ->rows(4),
                        Forms\Components\TextInput::make('duracion')
                            ->label('Duración Real (minutos)')
                            ->numeric()
                            ->required()
                            ->default(45),
                    ])
                    ->action(function (Sesion $record, array $data) {
                        $record->completar($data['resumen'], (int) $data['duracion']);
                        AuditLog::registrar('completar', 'sesiones', "Completó sesión del " . $record->fecha->format('d/m/Y'), $record);
                    }),
                Tables\Actions\Action::make('cancelar_sesion')
                    ->label('Cancelar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Sesion $record) => in_array($record->estado, ['programada', 'confirmada']))
                    ->form([
                        Forms\Components\Textarea::make('motivo')
                            ->label('Motivo de Cancelación')
                            ->required()
                            ->rows(2),
                    ])
                    ->requiresConfirmation()
                    ->action(function (Sesion $record, array $data) {
                        $record->cancelar($data['motivo']);
                        AuditLog::registrar('cancelar', 'sesiones', "Canceló sesión del " . $record->fecha->format('d/m/Y'), $record);
                    }),
            ])
            ->striped()
            ->poll('60s');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSesiones::route('/'),
            'create' => Pages\CreateSesion::route('/create'),
            'view'   => Pages\ViewSesion::route('/{record}'),
            'edit'   => Pages\EditSesion::route('/{record}/edit'),
        ];
    }
}

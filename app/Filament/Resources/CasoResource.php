<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CasoResource\Pages;
use App\Models\AuditLog;
use App\Models\Caso;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;

class CasoResource extends Resource
{
    protected static ?string $model = Caso::class;

    protected static ?string $navigationIcon    = 'heroicon-o-shield-exclamation';
    protected static ?string $navigationGroup   = 'Gestión';
    protected static ?string $navigationLabel   = 'Casos';
    protected static ?string $modelLabel        = 'Caso';
    protected static ?string $pluralModelLabel  = 'Casos';
    protected static ?int    $navigationSort    = 1;

    public static function getNavigationBadge(): ?string
    {
        return (string) Caso::where('estado', 'pendiente')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return Caso::where('estado', 'pendiente')->count() > 0 ? 'warning' : 'gray';
    }

    public static function tiposViolenciaOpciones(): array
    {
        return [
            'fisica'         => 'Física',
            'verbal'         => 'Verbal',
            'psicologica'    => 'Psicológica',
            'bullying'       => 'Bullying',
            'cyberbullying'  => 'Cyberbullying',
            'discriminacion' => 'Discriminación',
            'sexual'         => 'Sexual',
            'ciberacoso'     => 'Ciberacoso',
            'otro'           => 'Otro',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Información del Caso')
                ->icon('heroicon-o-document-text')
                ->description('Datos básicos de identificación del incidente')
                ->schema([
                    Forms\Components\TextInput::make('codigo_caso')
                        ->label('Código del Caso')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->placeholder('VIO-2026-001')
                        ->prefixIcon('heroicon-m-hashtag'),

                    Forms\Components\Select::make('tipo_violencia')
                        ->label('Tipo de Violencia')
                        ->required()
                        ->options(self::tiposViolenciaOpciones())
                        ->native(false)
                        ->prefixIcon('heroicon-m-exclamation-triangle'),

                    Forms\Components\Select::make('estado')
                        ->label('Estado del Caso')
                        ->required()
                        ->options([
                            'pendiente'  => 'Pendiente',
                            'en_proceso' => 'En Proceso',
                            'resuelto'   => 'Resuelto',
                            'cerrado'    => 'Cerrado',
                            'escalado'   => 'Escalado',
                        ])
                        ->default('pendiente')
                        ->native(false),

                    Forms\Components\Select::make('prioridad')
                        ->label('Prioridad')
                        ->required()
                        ->options([
                            'baja'    => 'Baja',
                            'media'   => 'Media',
                            'alta'    => 'Alta',
                            'urgente' => 'Urgente',
                        ])
                        ->default('media')
                        ->native(false),

                    Forms\Components\DateTimePicker::make('fecha_incidente')
                        ->label('Fecha y Hora del Incidente')
                        ->nullable()
                        ->maxDate(now())
                        ->native(false)
                        ->displayFormat('d/m/Y H:i'),

                    Forms\Components\Select::make('nivel_severidad')
                        ->label('Nivel de Severidad')
                        ->options([
                            1 => '1 – Muy leve',
                            2 => '2 – Leve',
                            3 => '3 – Moderado',
                            4 => '4 – Grave',
                            5 => '5 – Muy grave',
                        ])
                        ->default(3)
                        ->required()
                        ->native(false),
                ])->columns(3),

            Forms\Components\Section::make('Descripción del Incidente')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->schema([
                    Forms\Components\Textarea::make('descripcion')
                        ->label('Descripción Detallada')
                        ->required()
                        ->rows(5)
                        ->placeholder('Describa detalladamente lo ocurrido...')
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('acciones_tomadas')
                        ->label('Acciones Tomadas')
                        ->rows(3)
                        ->placeholder('Acciones inmediatas tomadas por el personal...')
                        ->columnSpanFull(),

                    Forms\Components\TagsInput::make('etiquetas')
                        ->label('Etiquetas')
                        ->placeholder('Agregar etiqueta')
                        ->columnSpanFull(),

                    Forms\Components\Toggle::make('es_anonimo')
                        ->label('Denuncia Anónima')
                        ->default(false),

                    Forms\Components\Toggle::make('es_sensible')
                        ->label('Caso Sensible')
                        ->default(false),
                ])->columns(2),

            Forms\Components\Section::make('Ubicación del Incidente')
                ->icon('heroicon-o-map-pin')
                ->schema([
                    Forms\Components\Select::make('ubicacion_exacta')
                        ->label('Ubicación Exacta en la Escuela')
                        ->options([
                            'aula'             => 'Aula / Salón de clases',
                            'patio'            => 'Patio / Recreo',
                            'bano'             => 'Baños',
                            'pasillo'          => 'Pasillo / Corredor',
                            'cafeteria'        => 'Cafetería / Comedor',
                            'biblioteca'       => 'Biblioteca',
                            'laboratorio'      => 'Laboratorio',
                            'educacion_fisica' => 'Cancha / Educación física',
                            'entrada_salida'   => 'Entrada / Salida',
                            'virtual'          => 'Entorno Virtual / Online',
                            'fuera_escuela'    => 'Fuera de la escuela',
                            'otro'             => 'Otro',
                        ])
                        ->native(false)
                        ->searchable(),

                    Forms\Components\TextInput::make('grado_grupo')
                        ->label('Grado y Grupo Involucrado')
                        ->placeholder('Ej: 3° A')
                        ->maxLength(50),

                    Forms\Components\TextInput::make('categoria')
                        ->label('Categoría del Caso')
                        ->maxLength(100),

                    Forms\Components\TextInput::make('escuela_nombre')
                        ->label('Nombre de la Institución')
                        ->maxLength(200),

                    Forms\Components\TextInput::make('region')
                        ->label('Región')
                        ->maxLength(100),

                    Forms\Components\TextInput::make('provincia')
                        ->label('Provincia')
                        ->maxLength(100),

                    Forms\Components\TextInput::make('distrito')
                        ->label('Distrito')
                        ->maxLength(100),

                    Forms\Components\TextInput::make('codigo_modular')
                        ->label('Código Modular')
                        ->maxLength(20),
                ])->columns(4)
                ->collapsible(),

            Forms\Components\Section::make('Personas Involucradas')
                ->icon('heroicon-o-user-group')
                ->schema([
                    Forms\Components\Fieldset::make('Agresor / Acosador')
                        ->schema([
                            Forms\Components\TextInput::make('agresor_nombre')
                                ->label('Nombre del Agresor')
                                ->placeholder('Nombre completo o apodo')
                                ->maxLength(255),

                            Forms\Components\Select::make('agresor_rol')
                                ->label('Rol del Agresor')
                                ->options([
                                    'alumno'      => 'Alumno/a',
                                    'docente'     => 'Docente',
                                    'personal'    => 'Personal Administrativo',
                                    'padre'       => 'Padre/Madre de familia',
                                    'externo'     => 'Persona externa',
                                    'desconocido' => 'Desconocido',
                                ])
                                ->native(false),

                            Forms\Components\TextInput::make('agresor_grado_grupo')
                                ->label('Grado/Grupo del Agresor')
                                ->placeholder('Ej: 2° B')
                                ->maxLength(50),
                        ])->columns(3),

                    Forms\Components\Fieldset::make('Víctima / Afectado')
                        ->schema([
                            Forms\Components\TextInput::make('victima_nombre')
                                ->label('Nombre de la Víctima')
                                ->placeholder('Nombre completo')
                                ->maxLength(255),

                            Forms\Components\Select::make('victima_rol')
                                ->label('Rol de la Víctima')
                                ->options([
                                    'alumno'   => 'Alumno/a',
                                    'docente'  => 'Docente',
                                    'personal' => 'Personal Administrativo',
                                    'padre'    => 'Padre/Madre de familia',
                                    'externo'  => 'Persona externa',
                                ])
                                ->native(false),

                            Forms\Components\TextInput::make('victima_grado_grupo')
                                ->label('Grado/Grupo de la Víctima')
                                ->placeholder('Ej: 2° B')
                                ->maxLength(50),
                        ])->columns(3),

                    Forms\Components\Textarea::make('testigos')
                        ->label('Testigos del Incidente')
                        ->placeholder('Nombres y datos de las personas que presenciaron el incidente...')
                        ->rows(3)
                        ->columnSpanFull(),
                ])->columns(1),

            Forms\Components\Section::make('Asignación y Responsables')
                ->icon('heroicon-o-user-circle')
                ->schema([
                    Forms\Components\Select::make('denunciante_id')
                        ->label('Denunciante (Reportó el caso)')
                        ->relationship('denunciante', 'name')
                        ->searchable()
                        ->nullable()
                        ->preload()
                        ->visible(fn (Get $get) => !$get('es_anonimo')),

                    Forms\Components\Select::make('asignado_a')
                        ->label('Psicólogo Asignado')
                        ->options(
                            User::where('rol', 'psicologo')->where('activo', true)->pluck('name', 'id')
                        )
                        ->searchable()
                        ->nullable(),

                    Forms\Components\Select::make('docente_responsable_id')
                        ->label('Docente Responsable')
                        ->options(
                            User::where('rol', 'docente')->where('activo', true)->pluck('name', 'id')
                        )
                        ->searchable()
                        ->nullable(),
                ])->columns(3),

            Forms\Components\Section::make('SLA y Control de Plazos')
                ->icon('heroicon-o-clock')
                ->schema([
                    Forms\Components\DateTimePicker::make('sla_limite')
                        ->label('Fecha Límite SLA')
                        ->native(false)
                        ->displayFormat('d/m/Y H:i'),

                    Forms\Components\DateTimePicker::make('fecha_primera_atencion')
                        ->label('Primera Atención')
                        ->native(false)
                        ->displayFormat('d/m/Y H:i'),

                    Forms\Components\Toggle::make('sla_vencido')
                        ->label('SLA Vencido')
                        ->disabled()
                        ->dehydrated(false),

                    Forms\Components\Toggle::make('escalado')
                        ->label('Caso Escalado'),

                    Forms\Components\DateTimePicker::make('fecha_escalamiento')
                        ->label('Fecha de Escalamiento')
                        ->native(false)
                        ->visible(fn (Get $get) => $get('escalado')),
                ])->columns(3)
                ->collapsible()
                ->collapsed(),

            Forms\Components\Section::make('Notas Internas Confidenciales')
                ->icon('heroicon-o-lock-closed')
                ->schema([
                    Forms\Components\Textarea::make('notas_internas')
                        ->label('Notas Internas (solo visible para administradores)')
                        ->rows(4)
                        ->columnSpanFull(),
                ])
                ->collapsible()
                ->collapsed(),
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
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Código copiado'),

                Tables\Columns\TextColumn::make('tipo_violencia')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'fisica'         => 'danger',
                        'psicologica'    => 'warning',
                        'verbal'         => 'warning',
                        'ciberacoso'     => 'info',
                        'cyberbullying'  => 'info',
                        'bullying'       => 'danger',
                        'sexual'         => 'primary',
                        'discriminacion' => 'gray',
                        default          => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => self::tiposViolenciaOpciones()[$state] ?? ucfirst($state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'pendiente'  => 'warning',
                        'en_proceso' => 'primary',
                        'resuelto'   => 'success',
                        'cerrado'    => 'gray',
                        'escalado'   => 'danger',
                        default      => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'pendiente'  => 'Pendiente',
                        'en_proceso' => 'En Proceso',
                        'resuelto'   => 'Resuelto',
                        'cerrado'    => 'Cerrado',
                        'escalado'   => 'Escalado',
                        default      => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('prioridad')
                    ->label('Prioridad')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'urgente' => 'danger',
                        'alta'    => 'warning',
                        'media'   => 'info',
                        'baja'    => 'gray',
                        default   => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => ucfirst($state))
                    ->icon(fn (string $state) => match ($state) {
                        'urgente' => 'heroicon-m-fire',
                        'alta'    => 'heroicon-m-arrow-up',
                        'media'   => 'heroicon-m-minus',
                        'baja'    => 'heroicon-m-arrow-down',
                        default   => null,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('nivel_severidad')
                    ->label('Severidad')
                    ->badge()
                    ->color(fn ($state) => match ((int) $state) {
                        5 => 'danger', 4 => 'warning', 3 => 'info', 2 => 'success', default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => "Niv. {$state}")
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('grado_grupo')
                    ->label('Grado/Grupo')
                    ->default('—')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('ubicacion_exacta')
                    ->label('Ubicación')
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'aula' => 'Aula', 'patio' => 'Patio', 'bano' => 'Baños',
                        'pasillo' => 'Pasillo', 'cafeteria' => 'Cafetería',
                        'virtual' => 'Virtual', null => '—', default => ucfirst((string) $state),
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('sla_vencido')
                    ->label('SLA')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('escalado')
                    ->label('Escalado')
                    ->boolean()
                    ->trueIcon('heroicon-o-arrow-trending-up')
                    ->trueColor('warning')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('denunciante.name')
                    ->label('Denunciante')
                    ->default('Anónimo')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('asignado.name')
                    ->label('Psicólogo')
                    ->default('Sin asignar')
                    ->searchable(),

                Tables\Columns\TextColumn::make('docenteResponsable.name')
                    ->label('Docente')
                    ->default('—')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('fecha_incidente')
                    ->label('Fecha Incidente')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('tipo_violencia')
                    ->label('Tipo de Violencia')
                    ->options(self::tiposViolenciaOpciones())
                    ->multiple(),

                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'pendiente'  => 'Pendiente',
                        'en_proceso' => 'En Proceso',
                        'resuelto'   => 'Resuelto',
                        'cerrado'    => 'Cerrado',
                        'escalado'   => 'Escalado',
                    ])
                    ->multiple(),

                SelectFilter::make('prioridad')
                    ->label('Prioridad')
                    ->options([
                        'baja'    => 'Baja',
                        'media'   => 'Media',
                        'alta'    => 'Alta',
                        'urgente' => 'Urgente',
                    ])
                    ->multiple(),

                SelectFilter::make('nivel_severidad')
                    ->label('Nivel de Severidad')
                    ->options([
                        1 => 'Nivel 1 – Muy leve',
                        2 => 'Nivel 2 – Leve',
                        3 => 'Nivel 3 – Moderado',
                        4 => 'Nivel 4 – Grave',
                        5 => 'Nivel 5 – Muy grave',
                    ]),

                SelectFilter::make('ubicacion_exacta')
                    ->label('Ubicación')
                    ->options([
                        'aula'          => 'Aula',
                        'patio'         => 'Patio',
                        'bano'          => 'Baños',
                        'pasillo'       => 'Pasillo',
                        'cafeteria'     => 'Cafetería',
                        'virtual'       => 'Virtual',
                        'fuera_escuela' => 'Fuera de la escuela',
                    ]),

                SelectFilter::make('docente_responsable_id')
                    ->label('Docente Responsable')
                    ->relationship('docenteResponsable', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('asignado_a')
                    ->label('Psicólogo Asignado')
                    ->relationship('asignado', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('fecha_incidente_rango')
                    ->label('Rango de Fecha')
                    ->form([
                        Forms\Components\DatePicker::make('desde')
                            ->label('Desde')
                            ->native(false),
                        Forms\Components\DatePicker::make('hasta')
                            ->label('Hasta')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['desde'] ?? null, fn ($q) => $q->whereDate('fecha_incidente', '>=', $data['desde']))
                            ->when($data['hasta'] ?? null, fn ($q) => $q->whereDate('fecha_incidente', '<=', $data['hasta']));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['desde'] ?? null) $indicators[] = 'Desde: ' . $data['desde'];
                        if ($data['hasta'] ?? null) $indicators[] = 'Hasta: ' . $data['hasta'];
                        return $indicators;
                    }),

                TernaryFilter::make('sla_vencido')
                    ->label('SLA Vencido')
                    ->trueLabel('Vencidos')
                    ->falseLabel('Vigentes')
                    ->placeholder('Todos'),

                TernaryFilter::make('escalado')
                    ->label('Escalado')
                    ->trueLabel('Escalados')
                    ->falseLabel('No escalados')
                    ->placeholder('Todos'),

                TernaryFilter::make('es_sensible')
                    ->label('Caso Sensible')
                    ->trueLabel('Sensibles')
                    ->falseLabel('No sensibles')
                    ->placeholder('Todos'),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),

                    Tables\Actions\Action::make('escalar')
                        ->label('Escalar Caso')
                        ->icon('heroicon-o-arrow-trending-up')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->visible(fn (Caso $record) => !$record->escalado && $record->estado !== 'cerrado')
                        ->action(function (Caso $record) {
                            $record->update([
                                'escalado'           => true,
                                'fecha_escalamiento' => now(),
                                'prioridad'          => 'urgente',
                            ]);
                            AuditLog::registrar('escalar', 'casos', "Escaló el caso {$record->codigo_caso}", $record);
                            Notification::make()->title('Caso escalado')->warning()->send();
                        }),

                    Tables\Actions\Action::make('cambiarEstado')
                        ->label('Cambiar Estado')
                        ->icon('heroicon-o-arrow-path')
                        ->color('primary')
                        ->form([
                            Forms\Components\Select::make('estado')
                                ->label('Nuevo Estado')
                                ->required()
                                ->options(['pendiente' => 'Pendiente', 'en_proceso' => 'En Proceso', 'resuelto' => 'Resuelto', 'cerrado' => 'Cerrado'])
                                ->native(false),
                        ])
                        ->action(function (Caso $record, array $data) {
                            $anterior = $record->estado;
                            $record->update(['estado' => $data['estado']]);
                            AuditLog::registrar('editar', 'casos', "Estado {$record->codigo_caso}: {$anterior} -> {$data['estado']}", $record);
                            Notification::make()->title('Estado actualizado')->success()->send();
                        }),

                    Tables\Actions\Action::make('cambiarPrioridad')
                        ->label('Cambiar Prioridad')
                        ->icon('heroicon-o-flag')
                        ->color('info')
                        ->form([
                            Forms\Components\Select::make('prioridad')
                                ->label('Nueva Prioridad')
                                ->required()
                                ->options(['baja' => 'Baja', 'media' => 'Media', 'alta' => 'Alta', 'urgente' => 'Urgente'])
                                ->native(false),
                        ])
                        ->action(function (Caso $record, array $data) {
                            $anterior = $record->prioridad;
                            $record->update(['prioridad' => $data['prioridad']]);
                            AuditLog::registrar('editar', 'casos', "Prioridad {$record->codigo_caso}: {$anterior} -> {$data['prioridad']}", $record);
                            Notification::make()->title('Prioridad actualizada')->success()->send();
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('marcarUrgente')
                        ->label('Marcar como Urgente')
                        ->icon('heroicon-o-fire')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(fn ($r) => $r->update(['prioridad' => 'urgente']));
                            Notification::make()->title('Casos marcados como urgente')->warning()->send();
                        }),

                    Tables\Actions\BulkAction::make('cambiarEstadoMasivo')
                        ->label('Cambiar Estado')
                        ->icon('heroicon-o-arrow-path')
                        ->color('primary')
                        ->form([
                            Forms\Components\Select::make('estado')
                                ->label('Nuevo Estado')
                                ->required()
                                ->options(['pendiente' => 'Pendiente', 'en_proceso' => 'En Proceso', 'resuelto' => 'Resuelto', 'cerrado' => 'Cerrado'])
                                ->native(false),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each(fn ($r) => $r->update(['estado' => $data['estado']]));
                            Notification::make()->title('Estado actualizado masivamente')->success()->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->persistFiltersInSession()
            ->persistSortInSession();
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

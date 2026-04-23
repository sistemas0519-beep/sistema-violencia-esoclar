<?php

namespace App\Filament\Apoyo\Resources;

use App\Filament\Apoyo\Resources\CasoSensibleResource\Pages;
use App\Filament\Apoyo\Resources\CasoSensibleResource\RelationManagers;
use App\Models\AccesoCaso;
use App\Models\AuditLog;
use App\Models\Caso;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CasoSensibleResource extends Resource
{
    protected static ?string $model = Caso::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';
    protected static ?string $navigationGroup = 'Casos Sensibles';
    protected static ?string $navigationLabel = 'Gestión de Casos';
    protected static ?string $modelLabel = 'Caso Sensible';
    protected static ?string $pluralModelLabel = 'Casos Sensibles';
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return (string) Caso::sensibles()
            ->activos()
            ->when(auth()->user()->esAsistente(), fn ($q) => $q->where('nivel_sensibilidad', '!=', 'altamente_confidencial'))
            ->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'danger';
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        return parent::getEloquentQuery()
            ->when($user->esAsistente(), function ($q) {
                $q->where('nivel_sensibilidad', '!=', 'altamente_confidencial');
            })
            ->when($user->esPsicologo(), function ($q) use ($user) {
                $q->where(fn ($sub) =>
                    $sub->where('asignado_a', $user->id)
                        ->orWhere('es_sensible', true)
                );
            });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Caso')
                    ->icon('heroicon-o-document-text')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('codigo_caso')
                            ->label('Código')
                            ->disabled()
                            ->columnSpan(1),

                        Forms\Components\Select::make('estado')
                            ->options([
                                'pendiente'      => 'Pendiente',
                                'en_proceso'     => 'En Proceso',
                                'en_seguimiento' => 'En Seguimiento',
                                'cerrado'        => 'Cerrado',
                                'derivado'       => 'Derivado',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('tipo_violencia')
                            ->label('Tipo de Violencia')
                            ->options([
                                'fisica'       => 'Física',
                                'psicologica'  => 'Psicológica',
                                'sexual'       => 'Sexual',
                                'ciberacoso'   => 'Ciberacoso',
                                'negligencia'  => 'Negligencia',
                            ])
                            ->disabled(),
                    ]),

                Forms\Components\Section::make('Clasificación de Sensibilidad')
                    ->icon('heroicon-o-lock-closed')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Toggle::make('es_sensible')
                            ->label('¿Es caso sensible?')
                            ->reactive()
                            ->columnSpanFull(),

                        Forms\Components\Select::make('nivel_sensibilidad')
                            ->label('Nivel de Sensibilidad')
                            ->options([
                                'normal'                  => '🟢 Normal',
                                'sensible'                => '🟡 Sensible',
                                'altamente_confidencial'  => '🔴 Altamente Confidencial',
                            ])
                            ->visible(fn (Forms\Get $get) => $get('es_sensible'))
                            ->required(fn (Forms\Get $get) => $get('es_sensible'))
                            ->native(false)
                            ->disabled(fn () => auth()->user()->esAsistente()),

                        Forms\Components\Select::make('area_tematica')
                            ->label('Área Temática')
                            ->options([
                                'acoso_escolar'          => 'Acoso Escolar',
                                'violencia_fisica'       => 'Violencia Física',
                                'violencia_psicologica'  => 'Violencia Psicológica',
                                'violencia_sexual'       => 'Violencia Sexual',
                                'autolesion'             => 'Autolesión',
                                'consumo_sustancias'     => 'Consumo de Sustancias',
                                'violencia_familiar'     => 'Violencia Familiar',
                                'discriminacion'         => 'Discriminación',
                                'ciberacoso'             => 'Ciberacoso',
                                'otro'                   => 'Otro',
                            ])
                            ->native(false),

                        Forms\Components\Select::make('nivel_urgencia')
                            ->label('Nivel de Urgencia')
                            ->options([
                                'inmediata' => '🚨 Inmediata',
                                'alta'      => '🔴 Alta',
                                'media'     => '🟡 Media',
                                'baja'      => '🟢 Baja',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('prioridad')
                            ->options([
                                'urgente' => '🚨 Urgente',
                                'alta'    => '🔴 Alta',
                                'media'   => '🟡 Media',
                                'baja'    => '🟢 Baja',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\Textarea::make('motivo_sensibilidad')
                            ->label('Motivo de Clasificación Sensible')
                            ->visible(fn (Forms\Get $get) => $get('es_sensible'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Descripción y Notas')
                    ->icon('heroicon-o-pencil-square')
                    ->schema([
                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción del Caso')
                            ->rows(4)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('notas_internas')
                            ->label('Notas Internas')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Solo visible para el personal de apoyo'),
                    ]),
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
                    ->copyable(),

                Tables\Columns\IconColumn::make('es_sensible')
                    ->label('Sensible')
                    ->boolean()
                    ->trueIcon('heroicon-o-shield-exclamation')
                    ->falseIcon('heroicon-o-shield-check')
                    ->trueColor('danger')
                    ->falseColor('success'),

                Tables\Columns\TextColumn::make('nivel_sensibilidad')
                    ->label('Nivel')
                    ->badge()
                    ->colors([
                        'success'  => 'normal',
                        'warning'  => 'sensible',
                        'danger'   => 'altamente_confidencial',
                    ])
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'normal'                 => 'Normal',
                        'sensible'               => 'Sensible',
                        'altamente_confidencial' => 'Alt. Confidencial',
                        default                  => $state,
                    }),

                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->colors([
                        'warning' => 'pendiente',
                        'info'    => 'en_proceso',
                        'primary' => 'en_seguimiento',
                        'success' => 'cerrado',
                        'gray'    => 'derivado',
                    ]),

                Tables\Columns\TextColumn::make('nivel_urgencia')
                    ->label('Urgencia')
                    ->badge()
                    ->colors([
                        'danger'  => 'inmediata',
                        'warning' => 'alta',
                        'info'    => 'media',
                        'gray'    => 'baja',
                    ]),

                Tables\Columns\TextColumn::make('area_tematica')
                    ->label('Área')
                    ->formatStateUsing(fn (?string $state) => $state ? str_replace('_', ' ', ucfirst($state)) : '—')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('tipo_violencia')
                    ->label('Tipo')
                    ->formatStateUsing(fn (string $state) => ucfirst($state))
                    ->toggleable(),

                Tables\Columns\TextColumn::make('asignado.name')
                    ->label('Asignado')
                    ->default('Sin asignar')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'pendiente'      => 'Pendiente',
                        'en_proceso'     => 'En Proceso',
                        'en_seguimiento' => 'En Seguimiento',
                        'cerrado'        => 'Cerrado',
                        'derivado'       => 'Derivado',
                    ]),

                Tables\Filters\SelectFilter::make('nivel_sensibilidad')
                    ->label('Sensibilidad')
                    ->options([
                        'normal'                  => 'Normal',
                        'sensible'                => 'Sensible',
                        'altamente_confidencial'  => 'Altamente Confidencial',
                    ]),

                Tables\Filters\SelectFilter::make('area_tematica')
                    ->label('Área Temática')
                    ->options([
                        'acoso_escolar'          => 'Acoso Escolar',
                        'violencia_fisica'       => 'Violencia Física',
                        'violencia_psicologica'  => 'Violencia Psicológica',
                        'violencia_sexual'       => 'Violencia Sexual',
                        'autolesion'             => 'Autolesión',
                        'consumo_sustancias'     => 'Consumo de Sustancias',
                        'violencia_familiar'     => 'Violencia Familiar',
                        'discriminacion'         => 'Discriminación',
                        'ciberacoso'             => 'Ciberacoso',
                        'otro'                   => 'Otro',
                    ]),

                Tables\Filters\SelectFilter::make('nivel_urgencia')
                    ->label('Urgencia')
                    ->options([
                        'inmediata' => 'Inmediata',
                        'alta'      => 'Alta',
                        'media'     => 'Media',
                        'baja'      => 'Baja',
                    ]),

                Tables\Filters\TernaryFilter::make('es_sensible')
                    ->label('Solo sensibles'),

                Tables\Filters\Filter::make('fecha')
                    ->form([
                        Forms\Components\DatePicker::make('desde'),
                        Forms\Components\DatePicker::make('hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['desde'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['hasta'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->after(function (Caso $record) {
                        AccesoCaso::registrar($record->id, 'lectura', 'vista_detalle');
                    }),
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()->esPsicologo()),
                Tables\Actions\Action::make('registrar_nota')
                    ->label('Nota')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('warning')
                    ->form([
                        Forms\Components\Textarea::make('contenido')
                            ->label('Nota Confidencial')
                            ->required()
                            ->rows(4),
                        Forms\Components\Select::make('visibilidad')
                            ->options([
                                'solo_autor'    => 'Solo yo',
                                'psicologos'    => 'Psicólogos',
                                'equipo_apoyo'  => 'Todo el equipo',
                            ])
                            ->default('equipo_apoyo')
                            ->required(),
                        Forms\Components\Toggle::make('es_critica')
                            ->label('¿Es nota crítica?'),
                    ])
                    ->action(function (Caso $record, array $data) {
                        $record->notasConfidenciales()->create([
                            'autor_id'     => auth()->id(),
                            'contenido'    => $data['contenido'],
                            'visibilidad'  => $data['visibilidad'],
                            'es_critica'   => $data['es_critica'] ?? false,
                        ]);

                        AuditLog::registrar(
                            'crear',
                            'notas_confidenciales',
                            "Nota confidencial agregada al caso {$record->codigo_caso}",
                            $record,
                        );
                    }),
            ])
            ->bulkActions([])
            ->recordUrl(null)
            ->striped()
            ->poll('60s');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\NotasConfidencialesRelationManager::class,
            RelationManagers\AccesosCasoRelationManager::class,
            RelationManagers\SeguimientosRelationManager::class,
            RelationManagers\IntervencionesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCasosSensibles::route('/'),
            'view'   => Pages\ViewCasoSensible::route('/{record}'),
            'edit'   => Pages\EditCasoSensible::route('/{record}/edit'),
        ];
    }
}

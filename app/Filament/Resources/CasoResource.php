<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CasoResource\Pages;
use App\Models\AuditLog;
use App\Models\Caso;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

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

                    Forms\Components\Select::make('prioridad')
                        ->label('Prioridad')
                        ->required()
                        ->options([
                            'baja'    => 'Baja',
                            'media'   => 'Media',
                            'alta'    => 'Alta',
                            'urgente' => 'Urgente',
                        ])
                        ->default('media'),

                    Forms\Components\DateTimePicker::make('fecha_incidente')
                        ->label('Fecha del Incidente')
                        ->nullable(),

                    Forms\Components\TextInput::make('categoria')
                        ->label('Categoría')
                        ->maxLength(100)
                        ->placeholder('Ej: Aula, Patio, Virtual'),
                ])->columns(3),

            Forms\Components\Section::make('Descripción')
                ->schema([
                    Forms\Components\Textarea::make('descripcion')
                        ->label('Descripción del Caso')
                        ->required()
                        ->rows(4)
                        ->columnSpanFull(),

                    Forms\Components\TagsInput::make('etiquetas')
                        ->label('Etiquetas')
                        ->placeholder('Agregar etiqueta')
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
                            User::where('rol', 'psicologo')->where('activo', true)->pluck('name', 'id')
                        )
                        ->searchable()
                        ->nullable(),
                ])->columns(2),

            Forms\Components\Section::make('SLA y Escalamiento')
                ->schema([
                    Forms\Components\DateTimePicker::make('sla_limite')
                        ->label('Límite SLA')
                        ->helperText('Fecha límite para primera atención'),

                    Forms\Components\DateTimePicker::make('fecha_primera_atencion')
                        ->label('Primera Atención')
                        ->helperText('Fecha en que se atendió por primera vez'),

                    Forms\Components\Toggle::make('sla_vencido')
                        ->label('SLA Vencido')
                        ->disabled()
                        ->dehydrated(false),

                    Forms\Components\Toggle::make('escalado')
                        ->label('Caso Escalado'),

                    Forms\Components\DateTimePicker::make('fecha_escalamiento')
                        ->label('Fecha de Escalamiento')
                        ->visible(fn (Forms\Get $get) => $get('escalado')),
                ])->columns(2)
                ->collapsible()
                ->collapsed(),

            Forms\Components\Section::make('Notas Internas')
                ->schema([
                    Forms\Components\Textarea::make('notas_internas')
                        ->label('Notas internas (solo admin)')
                        ->rows(3)
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
                    }),

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

                Tables\Columns\IconColumn::make('es_anonimo')
                    ->label('Anónimo')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('denunciante.name')
                    ->label('Denunciante')
                    ->default('Anónimo')
                    ->searchable(),

                Tables\Columns\TextColumn::make('asignado.name')
                    ->label('Asignado')
                    ->default('Sin asignar')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y')
                    ->sortable(),
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
                Tables\Filters\SelectFilter::make('prioridad')
                    ->label('Prioridad')
                    ->options([
                        'baja'    => 'Baja',
                        'media'   => 'Media',
                        'alta'    => 'Alta',
                        'urgente' => 'Urgente',
                    ]),
                Tables\Filters\TernaryFilter::make('sla_vencido')
                    ->label('SLA Vencido')
                    ->trueLabel('Vencidos')
                    ->falseLabel('Vigentes')
                    ->placeholder('Todos'),
                Tables\Filters\TernaryFilter::make('escalado')
                    ->label('Escalado')
                    ->trueLabel('Escalados')
                    ->falseLabel('No escalados')
                    ->placeholder('Todos'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),

                    Tables\Actions\Action::make('escalar')
                        ->label('Escalar Caso')
                        ->icon('heroicon-o-arrow-trending-up')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Escalar Caso')
                        ->modalDescription(fn (Caso $record) => "¿Escalar el caso {$record->codigo_caso}? Se marcará como urgente.")
                        ->visible(fn (Caso $record) => !$record->escalado && $record->estado !== 'cerrado')
                        ->action(function (Caso $record) {
                            $record->update([
                                'escalado'           => true,
                                'fecha_escalamiento' => now(),
                                'prioridad'          => 'urgente',
                            ]);

                            AuditLog::registrar(
                                'escalar',
                                'casos',
                                "Escaló el caso {$record->codigo_caso}",
                                $record,
                            );

                            Notification::make()
                                ->title('Caso escalado')
                                ->body("El caso {$record->codigo_caso} ha sido escalado a urgente")
                                ->warning()
                                ->send();
                        }),

                    Tables\Actions\Action::make('cambiarPrioridad')
                        ->label('Cambiar Prioridad')
                        ->icon('heroicon-o-flag')
                        ->color('info')
                        ->form([
                            Forms\Components\Select::make('prioridad')
                                ->label('Nueva Prioridad')
                                ->required()
                                ->options([
                                    'baja'    => 'Baja',
                                    'media'   => 'Media',
                                    'alta'    => 'Alta',
                                    'urgente' => 'Urgente',
                                ]),
                        ])
                        ->action(function (Caso $record, array $data) {
                            $anterior = $record->prioridad;
                            $record->update(['prioridad' => $data['prioridad']]);

                            AuditLog::registrar(
                                'editar',
                                'casos',
                                "Cambió prioridad de {$record->codigo_caso}: {$anterior} → {$data['prioridad']}",
                                $record,
                                ['prioridad' => $anterior],
                                ['prioridad' => $data['prioridad']],
                            );

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

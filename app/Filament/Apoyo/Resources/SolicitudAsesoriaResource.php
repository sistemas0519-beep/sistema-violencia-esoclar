<?php

namespace App\Filament\Apoyo\Resources;

use App\Filament\Apoyo\Resources\SolicitudAsesoriaResource\Pages;
use App\Models\AuditLog;
use App\Models\SolicitudAsesoria;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SolicitudAsesoriaResource extends Resource
{
    protected static ?string $model = SolicitudAsesoria::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Asesoría y Apoyo';
    protected static ?string $navigationLabel = 'Solicitudes de Asesoría';
    protected static ?string $modelLabel = 'Solicitud';
    protected static ?string $pluralModelLabel = 'Solicitudes de Asesoría';
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return (string) SolicitudAsesoria::pendientes()->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        $count = SolicitudAsesoria::pendientes()->count();
        return $count > 5 ? 'danger' : ($count > 0 ? 'warning' : 'success');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Solicitud')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('codigo')
                            ->default(fn () => SolicitudAsesoria::generarCodigo())
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\Select::make('tipo')
                            ->options([
                                'orientacion'   => 'Orientación',
                                'intervencion'  => 'Intervención',
                                'derivacion'    => 'Derivación',
                                'seguimiento'   => 'Seguimiento',
                                'emergencia'    => '🚨 Emergencia',
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
                            ->default('media')
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('estado')
                            ->options([
                                'pendiente'  => 'Pendiente',
                                'en_proceso' => 'En Proceso',
                                'completada' => 'Completada',
                                'cancelada'  => 'Cancelada',
                                'derivada'   => 'Derivada',
                            ])
                            ->default('pendiente')
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('solicitante_id')
                            ->label('Solicitante')
                            ->relationship('solicitante', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('atendido_por')
                            ->label('Atendido por')
                            ->options(fn () => User::whereIn('rol', ['psicologo', 'asistente'])->pluck('name', 'id'))
                            ->searchable()
                            ->nullable(),

                        Forms\Components\Select::make('caso_id')
                            ->label('Caso Relacionado')
                            ->relationship('caso', 'codigo_caso')
                            ->searchable()
                            ->nullable(),
                    ]),

                Forms\Components\Section::make('Detalle')
                    ->schema([
                        Forms\Components\TextInput::make('motivo')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('descripcion')
                            ->rows(4),

                        Forms\Components\Textarea::make('observaciones_resolucion')
                            ->label('Observaciones de Resolución')
                            ->rows(3)
                            ->visible(fn (?SolicitudAsesoria $record) => $record && in_array($record->estado, ['completada', 'derivada'])),
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

                Tables\Columns\TextColumn::make('tipo')
                    ->badge()
                    ->colors([
                        'info'    => 'orientacion',
                        'primary' => 'intervencion',
                        'warning' => 'derivacion',
                        'gray'    => 'seguimiento',
                        'danger'  => 'emergencia',
                    ])
                    ->formatStateUsing(fn (string $state) => ucfirst($state)),

                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->colors([
                        'warning' => 'pendiente',
                        'info'    => 'en_proceso',
                        'success' => 'completada',
                        'danger'  => 'cancelada',
                        'gray'    => 'derivada',
                    ])
                    ->formatStateUsing(fn (string $state) => str_replace('_', ' ', ucfirst($state))),

                Tables\Columns\TextColumn::make('prioridad')
                    ->badge()
                    ->colors([
                        'danger'  => 'urgente',
                        'warning' => 'alta',
                        'info'    => 'media',
                        'gray'    => 'baja',
                    ]),

                Tables\Columns\TextColumn::make('solicitante.name')
                    ->label('Solicitante')
                    ->searchable(),

                Tables\Columns\TextColumn::make('atendidoPor.name')
                    ->label('Atendido por')
                    ->default('Sin asignar'),

                Tables\Columns\TextColumn::make('motivo')
                    ->limit(40)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('fecha_solicitud')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('fecha_solicitud', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'pendiente'  => 'Pendiente',
                        'en_proceso' => 'En Proceso',
                        'completada' => 'Completada',
                        'cancelada'  => 'Cancelada',
                        'derivada'   => 'Derivada',
                    ]),
                Tables\Filters\SelectFilter::make('tipo')
                    ->options([
                        'orientacion'  => 'Orientación',
                        'intervencion' => 'Intervención',
                        'derivacion'   => 'Derivación',
                        'seguimiento'  => 'Seguimiento',
                        'emergencia'   => 'Emergencia',
                    ]),
                Tables\Filters\SelectFilter::make('prioridad')
                    ->options([
                        'urgente' => 'Urgente',
                        'alta'    => 'Alta',
                        'media'   => 'Media',
                        'baja'    => 'Baja',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('tomar')
                    ->label('Tomar Solicitud')
                    ->icon('heroicon-o-hand-raised')
                    ->color('success')
                    ->visible(fn (SolicitudAsesoria $record) => $record->estado === 'pendiente')
                    ->requiresConfirmation()
                    ->action(function (SolicitudAsesoria $record) {
                        $record->asignar(auth()->id());
                        AuditLog::registrar('asignar', 'solicitudes_asesoria', "Tomó solicitud {$record->codigo}", $record);
                    }),
                Tables\Actions\Action::make('completar')
                    ->label('Completar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (SolicitudAsesoria $record) => $record->estado === 'en_proceso' && $record->atendido_por === auth()->id())
                    ->form([
                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones de Resolución')
                            ->required()
                            ->rows(4),
                    ])
                    ->action(function (SolicitudAsesoria $record, array $data) {
                        $record->completar($data['observaciones']);
                        AuditLog::registrar('completar', 'solicitudes_asesoria', "Completó solicitud {$record->codigo}", $record);
                    }),
            ])
            ->bulkActions([])
            ->striped()
            ->poll('30s');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSolicitudesAsesoria::route('/'),
            'create' => Pages\CreateSolicitudAsesoria::route('/create'),
            'view'   => Pages\ViewSolicitudAsesoria::route('/{record}'),
            'edit'   => Pages\EditSolicitudAsesoria::route('/{record}/edit'),
        ];
    }
}

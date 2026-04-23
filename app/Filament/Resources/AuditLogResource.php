<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static ?string $navigationIcon   = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup  = 'Administración';
    protected static ?string $navigationLabel  = 'Auditoría';
    protected static ?string $modelLabel       = 'Registro de Auditoría';
    protected static ?string $pluralModelLabel = 'Registros de Auditoría';
    protected static ?int    $navigationSort   = 6;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Detalle del Registro')
                ->schema([
                    Forms\Components\TextInput::make('accion')->label('Acción')->disabled(),
                    Forms\Components\TextInput::make('modulo')->label('Módulo')->disabled(),
                    Forms\Components\Textarea::make('descripcion')->label('Descripción')->disabled()->columnSpanFull(),
                    Forms\Components\TextInput::make('ip_address')->label('IP')->disabled(),
                    Forms\Components\Textarea::make('user_agent')->label('User Agent')->disabled()->columnSpanFull(),
                    Forms\Components\KeyValue::make('datos_anteriores')->label('Datos Anteriores')->disabled()->columnSpanFull(),
                    Forms\Components\KeyValue::make('datos_nuevos')->label('Datos Nuevos')->disabled()->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->placeholder('Sistema'),

                Tables\Columns\TextColumn::make('accion')
                    ->label('Acción')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'crear'           => 'success',
                        'editar'          => 'info',
                        'eliminar'        => 'danger',
                        'activar'         => 'success',
                        'desactivar'      => 'warning',
                        'reset_password'  => 'warning',
                        'login'           => 'gray',
                        'logout'          => 'gray',
                        default           => 'gray',
                    }),

                Tables\Columns\TextColumn::make('modulo')
                    ->label('Módulo')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(60)
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('accion')
                    ->label('Acción')
                    ->options([
                        'crear'          => 'Crear',
                        'editar'         => 'Editar',
                        'eliminar'       => 'Eliminar',
                        'activar'        => 'Activar',
                        'desactivar'     => 'Desactivar',
                        'reset_password' => 'Reset Contraseña',
                        'login'          => 'Login',
                        'logout'         => 'Logout',
                    ]),
                Tables\Filters\SelectFilter::make('modulo')
                    ->label('Módulo')
                    ->options([
                        'usuarios'       => 'Usuarios',
                        'casos'          => 'Casos',
                        'asignaciones'   => 'Asignaciones',
                        'documentos'     => 'Documentos',
                        'configuracion'  => 'Configuración',
                        'sistema'        => 'Sistema',
                    ]),
                Tables\Filters\Filter::make('fecha')
                    ->form([
                        Forms\Components\DatePicker::make('desde')
                            ->label('Desde')
                            ->native(false),
                        Forms\Components\DatePicker::make('hasta')
                            ->label('Hasta')
                            ->native(false),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['desde'], fn ($q) => $q->whereDate('created_at', '>=', $data['desde']))
                            ->when($data['hasta'], fn ($q) => $q->whereDate('created_at', '<=', $data['hasta']));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
            'view'  => Pages\ViewAuditLog::route('/{record}'),
        ];
    }
}

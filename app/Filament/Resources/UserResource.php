<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\AuditLog;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon   = 'heroicon-o-users';
    protected static ?string $navigationGroup  = 'Administración';
    protected static ?string $navigationLabel  = 'Usuarios';
    protected static ?string $modelLabel       = 'Usuario';
    protected static ?string $pluralModelLabel = 'Usuarios';
    protected static ?int    $navigationSort   = 2;

    public static function getNavigationBadge(): ?string
    {
        return (string) User::count();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Datos del Usuario')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nombre Completo')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('email')
                        ->label('Correo Electrónico')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    Forms\Components\Select::make('rol')
                        ->label('Rol')
                        ->required()
                        ->options([
                            'admin'     => 'Administrador',
                            'psicologo' => 'Psicólogo/a',
                            'docente'   => 'Docente',
                            'alumno'    => 'Alumno',
                        ])
                        ->default('alumno')
                        ->reactive(),

                    Forms\Components\TextInput::make('password')
                        ->label('Contraseña')
                        ->password()
                        ->required(fn ($livewire) => $livewire instanceof Pages\CreateUser)
                        ->dehydrated(fn ($state) => filled($state))
                        ->maxLength(255)
                        ->helperText('Dejar en blanco para no cambiar (solo en edición).'),

                    Forms\Components\Toggle::make('activo')
                        ->label('Usuario Activo')
                        ->helperText('Desactivar para impedir el acceso al sistema')
                        ->default(true)
                        ->columnSpanFull(),
                ])->columns(2),

            Forms\Components\Section::make('Datos de Psicólogo')
                ->schema([
                    Forms\Components\TextInput::make('especialidad')
                        ->label('Especialidad')
                        ->maxLength(255),

                    Forms\Components\Select::make('disponibilidad')
                        ->label('Disponibilidad')
                        ->options([
                            'disponible'    => 'Disponible',
                            'ocupado'       => 'Ocupado',
                            'no_disponible' => 'No Disponible',
                        ])
                        ->default('disponible'),
                ])
                ->columns(2)
                ->visible(fn (Forms\Get $get) => $get('rol') === 'psicologo'),

            Forms\Components\Section::make('Notas Administrativas')
                ->schema([
                    Forms\Components\Textarea::make('notas_admin')
                        ->label('Notas internas')
                        ->helperText('Solo visible para administradores')
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
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->icon('heroicon-m-envelope')
                    ->iconColor('gray'),

                Tables\Columns\TextColumn::make('rol')
                    ->label('Rol')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'admin'     => 'danger',
                        'psicologo' => 'success',
                        'docente'   => 'info',
                        'alumno'    => 'gray',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'admin'     => 'Administrador',
                        'psicologo' => 'Psicólogo',
                        'docente'   => 'Docente',
                        'alumno'    => 'Alumno',
                        default     => $state,
                    }),

                Tables\Columns\IconColumn::make('activo')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                Tables\Columns\TextColumn::make('ultimo_acceso')
                    ->label('Último Acceso')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Nunca')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('rol')
                    ->label('Rol')
                    ->options([
                        'admin'     => 'Administrador',
                        'psicologo' => 'Psicólogo',
                        'docente'   => 'Docente',
                        'alumno'    => 'Alumno',
                    ]),
                Tables\Filters\TernaryFilter::make('activo')
                    ->label('Estado')
                    ->trueLabel('Activos')
                    ->falseLabel('Inactivos')
                    ->placeholder('Todos'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),

                    Tables\Actions\Action::make('toggleActivo')
                        ->label(fn (User $record) => $record->activo ? 'Desactivar' : 'Activar')
                        ->icon(fn (User $record) => $record->activo ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                        ->color(fn (User $record) => $record->activo ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->modalHeading(fn (User $record) => $record->activo ? 'Desactivar Usuario' : 'Activar Usuario')
                        ->modalDescription(fn (User $record) => $record->activo
                            ? "¿Está seguro de desactivar a {$record->name}? No podrá acceder al sistema."
                            : "¿Reactivar el acceso de {$record->name}?")
                        ->action(function (User $record) {
                            $anterior = $record->activo;
                            $record->update(['activo' => !$record->activo]);

                            AuditLog::registrar(
                                $record->activo ? 'activar' : 'desactivar',
                                'usuarios',
                                ($record->activo ? 'Activó' : 'Desactivó') . " al usuario {$record->name}",
                                $record,
                                ['activo' => $anterior],
                                ['activo' => $record->activo],
                            );

                            Notification::make()
                                ->title($record->activo ? 'Usuario activado' : 'Usuario desactivado')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\Action::make('resetPassword')
                        ->label('Restablecer Contraseña')
                        ->icon('heroicon-o-key')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Restablecer Contraseña')
                        ->modalDescription(fn (User $record) => "Se generará una nueva contraseña temporal para {$record->name}")
                        ->form([
                            Forms\Components\TextInput::make('nueva_password')
                                ->label('Nueva Contraseña')
                                ->password()
                                ->required()
                                ->minLength(8)
                                ->default(fn () => Str::random(12)),
                        ])
                        ->action(function (User $record, array $data) {
                            $record->update(['password' => Hash::make($data['nueva_password'])]);

                            AuditLog::registrar(
                                'reset_password',
                                'usuarios',
                                "Restableció la contraseña de {$record->name}",
                                $record,
                            );

                            Notification::make()
                                ->title('Contraseña restablecida')
                                ->body("Nueva contraseña: {$data['nueva_password']}")
                                ->warning()
                                ->persistent()
                                ->send();
                        }),

                    Tables\Actions\DeleteAction::make()
                        ->before(function (User $record) {
                            AuditLog::registrar(
                                'eliminar',
                                'usuarios',
                                "Eliminó al usuario {$record->name} ({$record->email})",
                                $record,
                                $record->toArray(),
                            );
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('activar')
                        ->label('Activar seleccionados')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(fn ($r) => $r->update(['activo' => true]));
                            Notification::make()->title('Usuarios activados')->success()->send();
                        }),

                    Tables\Actions\BulkAction::make('desactivar')
                        ->label('Desactivar seleccionados')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(fn ($r) => $r->update(['activo' => false]));
                            Notification::make()->title('Usuarios desactivados')->success()->send();
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
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

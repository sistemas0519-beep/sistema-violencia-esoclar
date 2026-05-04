<?php

namespace App\Filament\Apoyo\Resources;

use App\Filament\Apoyo\Resources\SeguimientoResource\Pages;
use App\Models\AuditLog;
use App\Models\Caso;
use App\Models\Seguimiento;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SeguimientoResource extends Resource
{
    protected static ?string $model = Seguimiento::class;

    protected static ?string $navigationIcon    = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup   = 'Casos Sensibles';
    protected static ?string $navigationLabel   = 'Seguimientos';
    protected static ?string $modelLabel        = 'Seguimiento';
    protected static ?string $pluralModelLabel  = 'Seguimientos';
    protected static ?int    $navigationSort    = 3;

    // ── Badge: cantidad de seguimientos propios del mes en curso ─────────────

    public static function getNavigationBadge(): ?string
    {
        $count = Seguimiento::where('responsable_id', auth()->id())
            ->whereMonth('fecha_seguimiento', now()->month)
            ->whereYear('fecha_seguimiento', now()->year)
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'info';
    }

    // ── Scope: psicólogo/asistente sólo ve sus propios seguimientos ──────────

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        return parent::getEloquentQuery()
            ->with(['caso', 'responsable'])
            ->when(
                in_array($user->rol, ['psicologo', 'asistente']),
                fn (Builder $q) => $q->where('responsable_id', $user->id)
            );
    }

    // ── Formulario ───────────────────────────────────────────────────────────

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Seguimiento')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('caso_id')
                            ->label('Caso')
                            ->options(
                                fn () => Caso::whereNotIn('estado', ['cerrado'])
                                    ->orderBy('codigo_caso')
                                    ->pluck('codigo_caso', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Select::make('accion')
                            ->label('Tipo de Acción')
                            ->options([
                                'llamada'      => '📞 Llamada telefónica',
                                'reunion'      => '🤝 Reunión presencial',
                                'intervencion' => '🎯 Intervención directa',
                                'derivacion'   => '↗️ Derivación a especialista',
                                'cierre'       => '✅ Cierre de caso',
                                'otro'         => '📋 Otro',
                            ])
                            ->required()
                            ->native(false)
                            ->default('otro'),

                        Forms\Components\DateTimePicker::make('fecha_seguimiento')
                            ->label('Fecha y Hora')
                            ->required()
                            ->default(now())
                            ->maxDate(now()->addDay()),

                        Forms\Components\Hidden::make('responsable_id')
                            ->default(fn () => auth()->id()),
                    ]),

                Forms\Components\Section::make('Notas')
                    ->icon('heroicon-o-pencil-square')
                    ->schema([
                        Forms\Components\Textarea::make('notas')
                            ->label('Descripción del seguimiento')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull()
                            ->placeholder('Describe las acciones realizadas, observaciones y próximos pasos...'),
                    ]),
            ]);
    }

    // ── Tabla ────────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('caso.codigo_caso')
                    ->label('Caso')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('accion')
                    ->label('Acción')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'llamada'      => 'info',
                        'reunion'      => 'success',
                        'intervencion' => 'warning',
                        'derivacion'   => 'primary',
                        'cierre'       => 'success',
                        default        => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'llamada'      => 'Llamada',
                        'reunion'      => 'Reunión',
                        'intervencion' => 'Intervención',
                        'derivacion'   => 'Derivación',
                        'cierre'       => 'Cierre',
                        default        => 'Otro',
                    }),

                Tables\Columns\TextColumn::make('notas')
                    ->label('Notas')
                    ->limit(80)
                    ->wrap()
                    ->tooltip(fn ($record) => $record->notas),

                Tables\Columns\TextColumn::make('responsable.name')
                    ->label('Responsable')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('fecha_seguimiento')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->since()
                    ->tooltip(fn ($record) => $record->fecha_seguimiento?->format('d/m/Y H:i')),
            ])
            ->defaultSort('fecha_seguimiento', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('accion')
                    ->label('Tipo de Acción')
                    ->options([
                        'llamada'      => 'Llamada',
                        'reunion'      => 'Reunión',
                        'intervencion' => 'Intervención',
                        'derivacion'   => 'Derivación',
                        'cierre'       => 'Cierre',
                        'otro'         => 'Otro',
                    ]),

                Tables\Filters\Filter::make('este_mes')
                    ->label('Este mes')
                    ->query(fn (Builder $query) => $query
                        ->whereMonth('fecha_seguimiento', now()->month)
                        ->whereYear('fecha_seguimiento', now()->year)
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn (Seguimiento $record) => $record->responsable_id === auth()->id()),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (Seguimiento $record) => $record->responsable_id === auth()->id()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->striped()
            ->emptyStateHeading('Sin seguimientos registrados')
            ->emptyStateDescription('Crea el primer seguimiento usando el botón superior.')
            ->emptyStateIcon('heroicon-o-clipboard-document-list');
    }

    // ── Páginas ──────────────────────────────────────────────────────────────

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSeguimientos::route('/'),
            'create' => Pages\CreateSeguimiento::route('/create'),
            'view'   => Pages\ViewSeguimiento::route('/{record}'),
            'edit'   => Pages\EditSeguimiento::route('/{record}/edit'),
        ];
    }
}

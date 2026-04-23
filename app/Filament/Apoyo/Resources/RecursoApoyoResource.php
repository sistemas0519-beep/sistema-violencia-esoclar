<?php

namespace App\Filament\Apoyo\Resources;

use App\Filament\Apoyo\Resources\RecursoApoyoResource\Pages;
use App\Models\RecursoApoyo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RecursoApoyoResource extends Resource
{
    protected static ?string $model = RecursoApoyo::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Recursos';
    protected static ?string $navigationLabel = 'Base de Conocimientos';
    protected static ?string $modelLabel = 'Recurso';
    protected static ?string $pluralModelLabel = 'Recursos de Apoyo';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Recurso')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('titulo')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('categoria')
                            ->options([
                                'protocolo'                => '📋 Protocolo',
                                'guia_intervencion'        => '📖 Guía de Intervención',
                                'normativa'                => '⚖️ Normativa',
                                'recurso_externo'          => '🔗 Recurso Externo',
                                'formato'                  => '📄 Formato',
                                'material_psicoeducativo'  => '🧠 Material Psicoeducativo',
                                'otro'                     => '📌 Otro',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\TagsInput::make('etiquetas')
                            ->separator(','),

                        Forms\Components\RichEditor::make('contenido')
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('archivo_adjunto')
                            ->label('Archivo Adjunto')
                            ->directory('recursos-apoyo')
                            ->maxSize(10240)
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('es_publico')
                            ->label('¿Visible para todos?')
                            ->default(false),

                        Forms\Components\Toggle::make('destacado')
                            ->label('¿Recurso destacado?')
                            ->default(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('destacado')
                    ->label('')
                    ->boolean()
                    ->trueIcon('heroicon-s-star')
                    ->trueColor('warning')
                    ->falseIcon('heroicon-o-star'),

                Tables\Columns\TextColumn::make('titulo')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(60),

                Tables\Columns\TextColumn::make('categoria')
                    ->badge()
                    ->colors([
                        'primary' => 'protocolo',
                        'success' => 'guia_intervencion',
                        'warning' => 'normativa',
                        'info'    => 'recurso_externo',
                        'gray'    => fn ($state) => in_array($state, ['formato', 'otro']),
                        'danger'  => 'material_psicoeducativo',
                    ])
                    ->formatStateUsing(fn (string $state) => str_replace('_', ' ', ucfirst($state))),

                Tables\Columns\TextColumn::make('creador.name')
                    ->label('Creado por'),

                Tables\Columns\TextColumn::make('visitas')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\IconColumn::make('es_publico')
                    ->label('Público')
                    ->boolean(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('destacado', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('categoria')
                    ->options([
                        'protocolo'                => 'Protocolo',
                        'guia_intervencion'        => 'Guía de Intervención',
                        'normativa'                => 'Normativa',
                        'recurso_externo'          => 'Recurso Externo',
                        'formato'                  => 'Formato',
                        'material_psicoeducativo'  => 'Material Psicoeducativo',
                        'otro'                     => 'Otro',
                    ]),
                Tables\Filters\TernaryFilter::make('destacado')
                    ->label('Destacados'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->after(fn (RecursoApoyo $record) => $record->incrementarVisitas()),
                Tables\Actions\EditAction::make()
                    ->visible(fn (RecursoApoyo $record) => $record->creado_por === auth()->id() || auth()->user()->esPsicologo()),
            ])
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRecursosApoyo::route('/'),
            'create' => Pages\CreateRecursoApoyo::route('/create'),
            'view'   => Pages\ViewRecursoApoyo::route('/{record}'),
            'edit'   => Pages\EditRecursoApoyo::route('/{record}/edit'),
        ];
    }
}

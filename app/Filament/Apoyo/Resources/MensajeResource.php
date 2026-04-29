<?php

namespace App\Filament\Apoyo\Resources;

use App\Filament\Apoyo\Resources\MensajeResource\Pages;
use App\Models\Mensaje;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class MensajeResource extends Resource
{
    protected static ?string $model = Mensaje::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = 'Comunicación';
    protected static ?string $navigationLabel = 'Mensajería Interna';
    protected static ?string $modelLabel = 'Mensaje';
    protected static ?string $pluralModelLabel = 'Mensajes';
    protected static ?int $navigationSort = 1;

    protected static function getNavigationBadgeCount(): int
    {
        $userId = auth()->id();

        return Cache::remember("nav_badge:mensajes_no_leidos:{$userId}", 60, fn (): int => (int) Mensaje::noLeidos($userId)->count());
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getNavigationBadgeCount();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        $count = static::getNavigationBadgeCount();
        return $count > 0 ? 'danger' : 'success';
    }

    public static function getEloquentQuery(): Builder
    {
        $userId = auth()->id();

        return parent::getEloquentQuery()
            ->where(fn ($q) =>
                $q->where('destinatario_id', $userId)
                    ->orWhere('remitente_id', $userId)
            )
            ->principales();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Nuevo Mensaje')
                    ->schema([
                        Forms\Components\Select::make('destinatario_id')
                            ->label('Destinatario')
                            ->options(fn () => User::whereIn('rol', ['psicologo', 'asistente', 'admin'])
                                ->where('id', '!=', auth()->id())
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('caso_id')
                            ->label('Caso Relacionado (opcional)')
                            ->relationship('caso', 'codigo_caso')
                            ->searchable()
                            ->nullable(),

                        Forms\Components\TextInput::make('asunto')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('prioridad')
                            ->options([
                                'urgente' => '🚨 Urgente',
                                'alta'    => '🔴 Alta',
                                'normal'  => '🟢 Normal',
                                'baja'    => '⚪ Baja',
                            ])
                            ->default('normal')
                            ->required()
                            ->native(false),

                        Forms\Components\Toggle::make('es_confidencial')
                            ->label('Mensaje confidencial'),

                        Forms\Components\RichEditor::make('contenido')
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('leido_en')
                    ->label('')
                    ->icon(fn ($record) => $record->destinatario_id === auth()->id() && !$record->leido_en
                        ? 'heroicon-s-envelope'
                        : 'heroicon-o-envelope-open')
                    ->color(fn ($record) => $record->destinatario_id === auth()->id() && !$record->leido_en
                        ? 'danger'
                        : 'gray')
                    ->size(Tables\Columns\IconColumn\IconColumnSize::Small),

                Tables\Columns\TextColumn::make('asunto')
                    ->searchable()
                    ->weight(fn ($record) => $record->destinatario_id === auth()->id() && !$record->leido_en ? 'bold' : 'normal')
                    ->limit(50),

                Tables\Columns\TextColumn::make('remitente.name')
                    ->label('De'),

                Tables\Columns\TextColumn::make('destinatario.name')
                    ->label('Para'),

                Tables\Columns\TextColumn::make('prioridad')
                    ->badge()
                    ->colors([
                        'danger'  => 'urgente',
                        'warning' => 'alta',
                        'success' => 'normal',
                        'gray'    => 'baja',
                    ]),

                Tables\Columns\IconColumn::make('es_confidencial')
                    ->label('🔒')
                    ->boolean()
                    ->trueIcon('heroicon-s-lock-closed')
                    ->trueColor('warning'),

                Tables\Columns\TextColumn::make('caso.codigo_caso')
                    ->label('Caso')
                    ->default('—')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('respuestas_count')
                    ->label('Resp.')
                    ->counts('respuestas')
                    ->badge(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\Filter::make('no_leidos')
                    ->label('No leídos')
                    ->query(fn (Builder $query) => $query->noLeidos(auth()->id())),
                Tables\Filters\SelectFilter::make('prioridad')
                    ->options([
                        'urgente' => 'Urgente',
                        'alta'    => 'Alta',
                        'normal'  => 'Normal',
                        'baja'    => 'Baja',
                    ]),
                Tables\Filters\TernaryFilter::make('es_confidencial')
                    ->label('Confidencial'),
                Tables\Filters\Filter::make('recibidos')
                    ->label('Solo recibidos')
                    ->query(fn (Builder $query) => $query->where('destinatario_id', auth()->id())),
                Tables\Filters\Filter::make('enviados')
                    ->label('Solo enviados')
                    ->query(fn (Builder $query) => $query->where('remitente_id', auth()->id())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->after(function (Mensaje $record) {
                        if ($record->destinatario_id === auth()->id()) {
                            $record->marcarLeido();
                        }
                    }),
                Tables\Actions\Action::make('responder')
                    ->label('Responder')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('info')
                    ->form([
                        Forms\Components\RichEditor::make('contenido')
                            ->label('Respuesta')
                            ->required(),
                    ])
                    ->action(function (Mensaje $record, array $data) {
                        Mensaje::create([
                            'remitente_id'     => auth()->id(),
                            'destinatario_id'  => $record->remitente_id === auth()->id()
                                ? $record->destinatario_id
                                : $record->remitente_id,
                            'caso_id'          => $record->caso_id,
                            'mensaje_padre_id' => $record->id,
                            'asunto'           => 'RE: ' . $record->asunto,
                            'contenido'        => $data['contenido'],
                            'prioridad'        => $record->prioridad,
                            'es_confidencial'  => $record->es_confidencial,
                        ]);
                    }),
                Tables\Actions\Action::make('archivar')
                    ->label('Archivar')
                    ->icon('heroicon-o-archive-box')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->action(fn (Mensaje $record) => $record->archivarPara(auth()->id())),
            ])
            ->striped()
            ->poll('15s');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMensajes::route('/'),
            'create' => Pages\CreateMensaje::route('/create'),
            'view'   => Pages\ViewMensaje::route('/{record}'),
        ];
    }
}

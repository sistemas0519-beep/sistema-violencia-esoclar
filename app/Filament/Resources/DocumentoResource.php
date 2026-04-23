<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentoResource\Pages;
use App\Models\AuditLog;
use App\Models\Caso;
use App\Models\Documento;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class DocumentoResource extends Resource
{
    protected static ?string $model = Documento::class;

    protected static ?string $navigationIcon   = 'heroicon-o-document-text';
    protected static ?string $navigationGroup  = 'Gestión';
    protected static ?string $navigationLabel  = 'Documentos';
    protected static ?string $modelLabel       = 'Documento';
    protected static ?string $pluralModelLabel = 'Documentos';
    protected static ?int    $navigationSort   = 4;

    public static function getNavigationBadge(): ?string
    {
        return (string) Documento::count();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Información del Documento')
                ->schema([
                    Forms\Components\FileUpload::make('ruta')
                        ->label('Archivo')
                        ->required()
                        ->disk('local')
                        ->directory('documentos')
                        ->acceptedFileTypes([
                            'application/pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ])
                        ->maxSize(10240) // 10MB
                        ->columnSpanFull()
                        ->helperText('Formatos: PDF, Word, Excel, imágenes. Máx: 10MB'),

                    Forms\Components\TextInput::make('nombre')
                        ->label('Nombre del Documento')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Select::make('categoria')
                        ->label('Categoría')
                        ->required()
                        ->options([
                            'evidencia'                => 'Evidencia',
                            'informe'                  => 'Informe',
                            'evaluacion_psicologica'   => 'Evaluación Psicológica',
                            'acta'                     => 'Acta',
                            'consentimiento'           => 'Consentimiento',
                            'derivacion'               => 'Derivación',
                            'otro'                     => 'Otro',
                        ])
                        ->default('otro'),

                    Forms\Components\Select::make('caso_id')
                        ->label('Caso Asociado')
                        ->relationship('caso', 'codigo_caso')
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->placeholder('Sin caso asociado'),

                    Forms\Components\Select::make('acceso')
                        ->label('Nivel de Acceso')
                        ->required()
                        ->options([
                            'publico'       => 'Público - Visible para todos',
                            'privado'       => 'Privado - Solo personal autorizado',
                            'confidencial'  => 'Confidencial - Solo administradores',
                        ])
                        ->default('privado'),

                    Forms\Components\Textarea::make('descripcion')
                        ->label('Descripción')
                        ->rows(3)
                        ->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(40),

                Tables\Columns\TextColumn::make('categoria')
                    ->label('Categoría')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'evidencia'              => 'danger',
                        'informe'                => 'info',
                        'evaluacion_psicologica' => 'success',
                        'acta'                   => 'warning',
                        'consentimiento'         => 'gray',
                        'derivacion'             => 'primary',
                        default                  => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'evidencia'              => 'Evidencia',
                        'informe'                => 'Informe',
                        'evaluacion_psicologica' => 'Eval. Psicológica',
                        'acta'                   => 'Acta',
                        'consentimiento'         => 'Consentimiento',
                        'derivacion'             => 'Derivación',
                        default                  => 'Otro',
                    }),

                Tables\Columns\TextColumn::make('caso.codigo_caso')
                    ->label('Caso')
                    ->placeholder('—')
                    ->searchable(),

                Tables\Columns\TextColumn::make('acceso')
                    ->label('Acceso')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'publico'       => 'success',
                        'privado'       => 'warning',
                        'confidencial'  => 'danger',
                    })
                    ->formatStateUsing(fn (string $state) => ucfirst($state)),

                Tables\Columns\TextColumn::make('tipo_archivo')
                    ->label('Tipo')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('tamaño_formateado')
                    ->label('Tamaño'),

                Tables\Columns\TextColumn::make('autor.name')
                    ->label('Subido por')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('version')
                    ->label('V.')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('categoria')
                    ->label('Categoría')
                    ->options([
                        'evidencia'              => 'Evidencia',
                        'informe'                => 'Informe',
                        'evaluacion_psicologica' => 'Eval. Psicológica',
                        'acta'                   => 'Acta',
                        'consentimiento'         => 'Consentimiento',
                        'derivacion'             => 'Derivación',
                        'otro'                   => 'Otro',
                    ]),
                Tables\Filters\SelectFilter::make('acceso')
                    ->label('Nivel de Acceso')
                    ->options([
                        'publico'       => 'Público',
                        'privado'       => 'Privado',
                        'confidencial'  => 'Confidencial',
                    ]),
                Tables\Filters\SelectFilter::make('caso_id')
                    ->label('Caso')
                    ->relationship('caso', 'codigo_caso')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('descargar')
                    ->label('Descargar')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->action(function (Documento $record) {
                        if (Storage::disk('local')->exists($record->ruta)) {
                            AuditLog::registrar(
                                'descargar',
                                'documentos',
                                "Descargó el documento: {$record->nombre}",
                                $record,
                            );
                            return Storage::disk('local')->download($record->ruta, $record->nombre_original);
                        }
                        Notification::make()->title('Archivo no encontrado')->danger()->send();
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Documento $record) {
                        AuditLog::registrar(
                            'eliminar',
                            'documentos',
                            "Eliminó el documento: {$record->nombre}",
                            $record,
                        );
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index'  => Pages\ListDocumentos::route('/'),
            'create' => Pages\CreateDocumento::route('/create'),
            'edit'   => Pages\EditDocumento::route('/{record}/edit'),
        ];
    }
}

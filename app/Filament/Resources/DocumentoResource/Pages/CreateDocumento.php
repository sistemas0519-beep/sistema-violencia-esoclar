<?php

namespace App\Filament\Resources\DocumentoResource\Pages;

use App\Filament\Resources\DocumentoResource;
use App\Models\AuditLog;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateDocumento extends CreateRecord
{
    protected static string $resource = DocumentoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['subido_por'] = auth()->id();

        // Extraer metadatos del archivo subido
        if (!empty($data['ruta'])) {
            $path = $data['ruta'];
            if (Storage::disk('local')->exists($path)) {
                $data['nombre_original'] = basename($path);
                $data['tipo_archivo'] = pathinfo($path, PATHINFO_EXTENSION);
                $data['mime_type'] = Storage::disk('local')->mimeType($path) ?? 'application/octet-stream';
                $data['tamaño'] = Storage::disk('local')->size($path);
            }
        }

        if (empty($data['nombre_original'])) {
            $data['nombre_original'] = $data['nombre'] ?? 'documento';
        }
        if (empty($data['tipo_archivo'])) {
            $data['tipo_archivo'] = 'unknown';
        }
        if (empty($data['mime_type'])) {
            $data['mime_type'] = 'application/octet-stream';
        }
        if (empty($data['tamaño'])) {
            $data['tamaño'] = 0;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        AuditLog::registrar(
            'crear',
            'documentos',
            "Subió el documento: {$this->record->nombre}",
            $this->record,
        );
    }
}

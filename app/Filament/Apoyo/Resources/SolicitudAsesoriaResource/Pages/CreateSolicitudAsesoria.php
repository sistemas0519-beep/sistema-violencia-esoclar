<?php

namespace App\Filament\Apoyo\Resources\SolicitudAsesoriaResource\Pages;

use App\Filament\Apoyo\Resources\SolicitudAsesoriaResource;
use App\Models\AuditLog;
use Filament\Resources\Pages\CreateRecord;

class CreateSolicitudAsesoria extends CreateRecord
{
    protected static string $resource = SolicitudAsesoriaResource::class;

    protected function afterCreate(): void
    {
        AuditLog::registrar(
            'crear',
            'solicitudes_asesoria',
            "Creó solicitud de asesoría {$this->record->codigo}",
            $this->record,
        );
    }
}

<?php

namespace App\Filament\Apoyo\Resources\SesionResource\Pages;

use App\Filament\Apoyo\Resources\SesionResource;
use App\Models\AuditLog;
use Filament\Resources\Pages\CreateRecord;

class CreateSesion extends CreateRecord
{
    protected static string $resource = SesionResource::class;

    protected function afterCreate(): void
    {
        AuditLog::registrar(
            'crear',
            'sesiones',
            "Programó sesión para el " . $this->record->fecha->format('d/m/Y'),
            $this->record,
        );
    }
}

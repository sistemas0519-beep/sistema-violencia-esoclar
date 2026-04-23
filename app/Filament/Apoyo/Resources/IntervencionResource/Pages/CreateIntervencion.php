<?php

namespace App\Filament\Apoyo\Resources\IntervencionResource\Pages;

use App\Filament\Apoyo\Resources\IntervencionResource;
use App\Models\AuditLog;
use Filament\Resources\Pages\CreateRecord;

class CreateIntervencion extends CreateRecord
{
    protected static string $resource = IntervencionResource::class;

    protected function afterCreate(): void
    {
        AuditLog::registrar(
            'crear',
            'intervenciones',
            "Creó intervención {$this->record->codigo}",
            $this->record,
        );
    }
}

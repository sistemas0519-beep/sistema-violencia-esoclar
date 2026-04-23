<?php

namespace App\Filament\Apoyo\Resources\CasoSensibleResource\Pages;

use App\Filament\Apoyo\Resources\CasoSensibleResource;
use App\Models\AccesoCaso;
use App\Models\AuditLog;
use Filament\Resources\Pages\ViewRecord;

class ViewCasoSensible extends ViewRecord
{
    protected static string $resource = CasoSensibleResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        AccesoCaso::registrar($this->record->id, 'lectura', 'vista_completa');

        AuditLog::registrar(
            'lectura',
            'casos_sensibles',
            "Accedió al caso sensible {$this->record->codigo_caso}",
            $this->record,
        );

        return $data;
    }
}

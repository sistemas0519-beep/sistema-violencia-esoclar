<?php

namespace App\Filament\Apoyo\Resources\CasoSensibleResource\Pages;

use App\Filament\Apoyo\Resources\CasoSensibleResource;
use App\Models\AccesoCaso;
use App\Models\AuditLog;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCasoSensible extends EditRecord
{
    protected static string $resource = CasoSensibleResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        AccesoCaso::registrar($this->record->id, 'escritura', 'edicion_caso');
        return $data;
    }

    protected function afterSave(): void
    {
        AuditLog::registrar(
            'actualizar',
            'casos_sensibles',
            "Actualizó caso sensible {$this->record->codigo_caso}",
            $this->record,
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }
}

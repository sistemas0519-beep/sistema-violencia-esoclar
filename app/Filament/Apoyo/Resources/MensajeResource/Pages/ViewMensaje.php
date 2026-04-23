<?php

namespace App\Filament\Apoyo\Resources\MensajeResource\Pages;

use App\Filament\Apoyo\Resources\MensajeResource;
use Filament\Resources\Pages\ViewRecord;

class ViewMensaje extends ViewRecord
{
    protected static string $resource = MensajeResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if ($this->record->destinatario_id === auth()->id()) {
            $this->record->marcarLeido();
        }
        return $data;
    }
}

<?php

namespace App\Filament\Apoyo\Resources\MensajeResource\Pages;

use App\Filament\Apoyo\Resources\MensajeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMensaje extends CreateRecord
{
    protected static string $resource = MensajeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['remitente_id'] = auth()->id();
        return $data;
    }
}

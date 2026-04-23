<?php

namespace App\Filament\Apoyo\Resources\SolicitudAsesoriaResource\Pages;

use App\Filament\Apoyo\Resources\SolicitudAsesoriaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSolicitudAsesoria extends EditRecord
{
    protected static string $resource = SolicitudAsesoriaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }
}

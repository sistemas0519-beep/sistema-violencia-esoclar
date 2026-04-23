<?php

namespace App\Filament\Apoyo\Resources\SolicitudAsesoriaResource\Pages;

use App\Filament\Apoyo\Resources\SolicitudAsesoriaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSolicitudesAsesoria extends ListRecords
{
    protected static string $resource = SolicitudAsesoriaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

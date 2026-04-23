<?php

namespace App\Filament\Apoyo\Resources\CasoSensibleResource\Pages;

use App\Filament\Apoyo\Resources\CasoSensibleResource;
use Filament\Resources\Pages\ListRecords;

class ListCasosSensibles extends ListRecords
{
    protected static string $resource = CasoSensibleResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}

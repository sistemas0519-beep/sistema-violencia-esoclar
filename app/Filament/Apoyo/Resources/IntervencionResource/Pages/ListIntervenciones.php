<?php

namespace App\Filament\Apoyo\Resources\IntervencionResource\Pages;

use App\Filament\Apoyo\Resources\IntervencionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIntervenciones extends ListRecords
{
    protected static string $resource = IntervencionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Apoyo\Resources\SesionResource\Pages;

use App\Filament\Apoyo\Resources\SesionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSesiones extends ListRecords
{
    protected static string $resource = SesionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

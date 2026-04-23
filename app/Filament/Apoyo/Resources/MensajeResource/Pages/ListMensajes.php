<?php

namespace App\Filament\Apoyo\Resources\MensajeResource\Pages;

use App\Filament\Apoyo\Resources\MensajeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMensajes extends ListRecords
{
    protected static string $resource = MensajeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Mensaje'),
        ];
    }
}

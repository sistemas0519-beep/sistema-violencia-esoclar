<?php

namespace App\Filament\Apoyo\Resources\RecursoApoyoResource\Pages;

use App\Filament\Apoyo\Resources\RecursoApoyoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRecursosApoyo extends ListRecords
{
    protected static string $resource = RecursoApoyoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

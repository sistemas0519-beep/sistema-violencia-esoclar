<?php

namespace App\Filament\Apoyo\Resources\IntervencionResource\Pages;

use App\Filament\Apoyo\Resources\IntervencionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIntervencion extends EditRecord
{
    protected static string $resource = IntervencionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Apoyo\Resources\SeguimientoResource\Pages;

use App\Filament\Apoyo\Resources\SeguimientoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSeguimiento extends ViewRecord
{
    protected static string $resource = SeguimientoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn () => $this->record->responsable_id === auth()->id()),
        ];
    }
}

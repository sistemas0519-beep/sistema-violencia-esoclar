<?php

namespace App\Filament\Apoyo\Resources\SeguimientoResource\Pages;

use App\Filament\Apoyo\Resources\SeguimientoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSeguimientos extends ListRecords
{
    protected static string $resource = SeguimientoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Seguimiento')
                ->icon('heroicon-o-plus'),
        ];
    }
}

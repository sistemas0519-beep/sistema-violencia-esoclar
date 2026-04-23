<?php

namespace App\Filament\Apoyo\Resources\RecursoApoyoResource\Pages;

use App\Filament\Apoyo\Resources\RecursoApoyoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRecursoApoyo extends CreateRecord
{
    protected static string $resource = RecursoApoyoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['creado_por'] = auth()->id();
        return $data;
    }
}

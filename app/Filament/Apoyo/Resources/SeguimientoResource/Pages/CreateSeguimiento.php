<?php

namespace App\Filament\Apoyo\Resources\SeguimientoResource\Pages;

use App\Filament\Apoyo\Resources\SeguimientoResource;
use App\Models\AuditLog;
use Filament\Resources\Pages\CreateRecord;

class CreateSeguimiento extends CreateRecord
{
    protected static string $resource = SeguimientoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['responsable_id'] = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        AuditLog::registrar(
            'crear',
            'seguimientos',
            'Registró seguimiento de tipo "' . $this->record->accion . '" para el caso ' . $this->record->caso?->codigo_caso,
            $this->record,
        );
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

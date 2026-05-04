<?php

namespace App\Filament\Apoyo\Resources\SeguimientoResource\Pages;

use App\Filament\Apoyo\Resources\SeguimientoResource;
use App\Models\AuditLog;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSeguimiento extends EditRecord
{
    protected static string $resource = SeguimientoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        AuditLog::registrar(
            'editar',
            'seguimientos',
            'Editó seguimiento del caso ' . $this->record->caso?->codigo_caso,
            $this->record,
        );
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

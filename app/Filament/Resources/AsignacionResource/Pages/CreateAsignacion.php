<?php

namespace App\Filament\Resources\AsignacionResource\Pages;

use App\Filament\Resources\AsignacionResource;
use App\Models\Asignacion;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateAsignacion extends CreateRecord
{
    protected static string $resource = AsignacionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();
        $data['created_by'] = $user?->id;

        $asignacionActiva = Asignacion::where('paciente_id', $data['paciente_id'])
            ->where('estado', 'activa')
            ->exists();

        if ($asignacionActiva) {
            Notification::make()
                ->danger()
                ->title('El paciente ya tiene una asignacion activa.')
                ->send();
            $this->halt();
        }

        $psicologo = User::find($data['psicologo_id']);
        if ($psicologo && $psicologo->disponibilidad === 'no_disponible') {
            Notification::make()
                ->danger()
                ->title('El psicologo no esta disponible.')
                ->send();
            $this->halt();
        }

        return $data;
    }
}

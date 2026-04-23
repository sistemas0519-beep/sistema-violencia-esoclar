<?php

namespace App\Observers;

use App\Models\Asignacion;
use App\Notifications\AsignacionCanceladaNotification;
use App\Notifications\AsignacionFinalizadaNotification;
use App\Notifications\NuevaAsignacionNotification;
use App\Notifications\PsicologoAsignadoNotification;
use Illuminate\Support\Facades\Notification;

class AsignacionObserver
{
    public function created(Asignacion $asignacion): void
    {
        $psicologo = $asignacion->psicologo;
        $paciente = $asignacion->paciente;

        if ($psicologo) {
            Notification::send($psicologo, new NuevaAsignacionNotification($asignacion));
        }

        if ($paciente) {
            Notification::send($paciente, new PsicologoAsignadoNotification($asignacion));
        }
    }

    public function updated(Asignacion $asignacion): void
    {
        if ($asignacion->isDirty('estado')) {
            $estadoAnterior = $asignacion->getOriginal('estado');
            $psicologo = $asignacion->psicologo;
            $paciente = $asignacion->paciente;

            if ($asignacion->estado === 'finalizada' && $estadoAnterior === 'activa') {
                if ($psicologo) {
                    Notification::send($psicologo, new AsignacionFinalizadaNotification($asignacion));
                }
                if ($paciente) {
                    Notification::send($paciente, new AsignacionFinalizadaNotification($asignacion));
                }
            }

            if ($asignacion->estado === 'cancelada' && $estadoAnterior === 'activa') {
                if ($psicologo) {
                    Notification::send($psicologo, new AsignacionCanceladaNotification($asignacion));
                }
                if ($paciente) {
                    Notification::send($paciente, new AsignacionCanceladaNotification($asignacion));
                }
            }
        }
    }

    public function deleted(Asignacion $asignacion): void
    {
        //
    }
}

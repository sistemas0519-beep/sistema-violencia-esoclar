<?php

namespace App\Notifications;

use App\Filament\Resources\AsignacionResource;
use App\Models\Asignacion;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PsicologoAsignadoNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Asignacion $asignacion
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Se le ha asignado un Psicólogo')
            ->greeting('Hola '.$this->asignacion->paciente->name)
            ->line('Se le ha asignado un psicólogo para su atención.')
            ->line('**Psicólogo:** '.$this->asignacion->psicologo->name)
            ->line('**Especialidad:** '.($this->asignacion->psicologo->especialidad ?? 'No especificada'))
            ->line('**Fecha de Inicio:** '.$this->asignacion->fecha_inicio->format('d/m/Y'))
            ->line('**Frecuencia:** '.ucfirst($this->asignacion->frecuencia_atencion))
            ->when($this->asignacion->dia_atencion, fn ($m) => $m
                ->line('**Día de Atención:** '.ucfirst($this->asignacion->dia_atencion)))
            ->when($this->asignacion->hora_atencion, fn ($m) => $m
                ->line('**Hora:** '.$this->asignacion->hora_atencion->format('H:i')))
            ->action('Ver Mi Asignación', AsignacionResource::getUrl('edit', ['record' => $this->asignacion]))
            ->line('Si tiene alguna consulta, puede comunicarse con el administrador.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Psicólogo Asignado',
            'message' => 'Se le ha asignado el psicólogo: '.$this->asignacion->psicologo->name,
            'asignacion_id' => $this->asignacion->id,
            'url' => AsignacionResource::getUrl('edit', ['record' => $this->asignacion]),
        ];
    }
}

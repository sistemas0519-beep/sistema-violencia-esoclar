<?php

namespace App\Notifications;

use App\Filament\Resources\AsignacionResource;
use App\Models\Asignacion;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NuevaAsignacionNotification extends Notification
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
            ->subject('Nueva Asignación de Paciente')
            ->greeting('Hola '.$this->asignacion->psicologo->name)
            ->line('Se le ha asignado un nuevo paciente.')
            ->line('**Paciente:** '.$this->asignacion->paciente->name)
            ->line('**Fecha de Inicio:** '.$this->asignacion->fecha_inicio->format('d/m/Y'))
            ->line('**Frecuencia:** '.ucfirst($this->asignacion->frecuencia_atencion))
            ->when($this->asignacion->dia_atencion, fn ($m) => $m
                ->line('**Día:** '.ucfirst($this->asignacion->dia_atencion)))
            ->when($this->asignacion->hora_atencion, fn ($m) => $m
                ->line('**Hora:** '.$this->asignacion->hora_atencion->format('H:i')))
            ->when($this->asignacion->notas, fn ($m) => $m
                ->line('**Notas:** '.$this->asignacion->notas))
            ->action('Ver Asignación', AsignacionResource::getUrl('edit', ['record' => $this->asignacion]))
            ->line('Gracias por usar nuestro sistema.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Nueva Asignación',
            'message' => 'Se le ha asignado al paciente: '.$this->asignacion->paciente->name,
            'asignacion_id' => $this->asignacion->id,
            'url' => AsignacionResource::getUrl('edit', ['record' => $this->asignacion]),
        ];
    }
}

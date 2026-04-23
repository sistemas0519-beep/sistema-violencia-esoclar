<?php

namespace App\Notifications;

use App\Filament\Resources\AsignacionResource;
use App\Models\Asignacion;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AsignacionCanceladaNotification extends Notification
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
            ->subject('Asignación Cancelada')
            ->greeting('Hola '.$notifiable->name)
            ->line('La asignación ha sido cancelada.')
            ->line('**Paciente:** '.$this->asignacion->paciente->name)
            ->line('**Psicólogo:** '.$this->asignacion->psicologo->name)
            ->line('**Fecha de inicio:** '.$this->asignacion->fecha_inicio->format('d/m/Y'))
            ->when($this->asignacion->motivo_fin, fn ($m) => $m
                ->line('**Motivo:** '.$this->asignacion->motivo_fin))
            ->line('Si necesita más información, contacte al administrador.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Asignación Cancelada',
            'message' => 'La asignación con '.$this->asignacion->paciente->name.' ha sido cancelada.',
            'asignacion_id' => $this->asignacion->id,
            'url' => AsignacionResource::getUrl('edit', ['record' => $this->asignacion]),
        ];
    }
}

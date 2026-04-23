<?php

namespace App\Policies;

use App\Models\Mensaje;
use App\Models\User;

class MensajePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->esPersonalApoyo();
    }

    public function view(User $user, Mensaje $mensaje): bool
    {
        return $mensaje->remitente_id === $user->id || $mensaje->destinatario_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->esPersonalApoyo();
    }

    public function update(User $user, Mensaje $mensaje): bool
    {
        return false;
    }

    public function delete(User $user, Mensaje $mensaje): bool
    {
        return false;
    }
}

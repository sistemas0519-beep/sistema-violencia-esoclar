<?php

namespace App\Policies;

use App\Models\NotaConfidencial;
use App\Models\User;

class NotaConfidencialPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->esPersonalApoyo();
    }

    public function view(User $user, NotaConfidencial $nota): bool
    {
        if ($nota->visibilidad === 'equipo_apoyo') {
            return $user->esPersonalApoyo();
        }

        if ($nota->visibilidad === 'psicologos') {
            return $user->esPsicologo();
        }

        // solo_autor
        return $nota->autor_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->esPersonalApoyo();
    }

    public function update(User $user, NotaConfidencial $nota): bool
    {
        return $nota->autor_id === $user->id;
    }

    public function delete(User $user, NotaConfidencial $nota): bool
    {
        return $nota->autor_id === $user->id;
    }
}

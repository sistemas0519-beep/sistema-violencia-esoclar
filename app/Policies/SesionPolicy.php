<?php

namespace App\Policies;

use App\Models\Sesion;
use App\Models\User;

class SesionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->esPersonalApoyo();
    }

    public function view(User $user, Sesion $sesion): bool
    {
        return $user->esPersonalApoyo() && $sesion->profesional_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->esPersonalApoyo();
    }

    public function update(User $user, Sesion $sesion): bool
    {
        return $sesion->profesional_id === $user->id;
    }

    public function delete(User $user, Sesion $sesion): bool
    {
        return $user->esPsicologo() && $sesion->profesional_id === $user->id;
    }
}

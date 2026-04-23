<?php

namespace App\Policies;

use App\Models\Intervencion;
use App\Models\User;

class IntervencionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->esPersonalApoyo();
    }

    public function view(User $user, Intervencion $intervencion): bool
    {
        return $user->esPersonalApoyo();
    }

    public function create(User $user): bool
    {
        return $user->esPersonalApoyo();
    }

    public function update(User $user, Intervencion $intervencion): bool
    {
        return $intervencion->profesional_id === $user->id;
    }

    public function delete(User $user, Intervencion $intervencion): bool
    {
        return $user->esPsicologo() && $intervencion->profesional_id === $user->id;
    }
}

<?php

namespace App\Policies;

use App\Models\Caso;
use App\Models\User;

class CasoSensiblePolicy
{
    /**
     * Psicólogos y asistentes pueden ver la lista de casos.
     */
    public function viewAny(User $user): bool
    {
        return $user->esPersonalApoyo();
    }

    /**
     * Psicólogos ven todos los casos. Asistentes no ven altamente confidenciales.
     */
    public function view(User $user, Caso $caso): bool
    {
        if (!$user->esPersonalApoyo()) {
            return false;
        }

        if ($user->esAsistente() && $caso->nivel_sensibilidad === 'altamente_confidencial') {
            return false;
        }

        return true;
    }

    /**
     * Solo psicólogos pueden editar casos sensibles.
     */
    public function update(User $user, Caso $caso): bool
    {
        return $user->esPsicologo();
    }

    /**
     * No se permite crear casos desde el panel de apoyo.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * No se permite eliminar casos.
     */
    public function delete(User $user, Caso $caso): bool
    {
        return false;
    }
}

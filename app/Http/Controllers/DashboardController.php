<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Redirige al dashboard correcto según el rol del usuario.
     */
    public function index()
    {
        $user = auth()->user();

        return match ($user->rol) {
            'psicologo' => redirect()->route('psicologo.dashboard'),
            'docente'   => redirect()->route('docente.dashboard'),
            'alumno'    => redirect()->route('alumno.dashboard'),
            'admin'     => redirect('/admin'),
            default     => redirect()->route('alumno.dashboard'),
        };
    }
}

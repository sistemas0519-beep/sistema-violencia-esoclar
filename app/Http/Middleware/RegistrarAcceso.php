<?php

namespace App\Http\Middleware;

use App\Models\ActividadSistema;
use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RegistrarAcceso
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (auth()->check()) {
            $user = auth()->user();

            // Actualizar último acceso (max cada 5 minutos)
            if (!$user->ultimo_acceso || $user->ultimo_acceso->diffInMinutes(now()) >= 5) {
                $user->timestamps = false;
                $user->update(['ultimo_acceso' => now()]);
                $user->timestamps = true;
            }
        }

        return $response;
    }
}

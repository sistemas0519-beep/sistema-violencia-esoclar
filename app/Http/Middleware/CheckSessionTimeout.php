<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionTimeout
{
    /**
     * Minutos de inactividad permitidos antes de cerrar sesión.
     */
    private const TIMEOUT_MINUTES = 30;

    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $lastActivity = $request->session()->get('last_activity_at');

        if ($lastActivity !== null) {
            $elapsed = now()->diffInMinutes(\Carbon\Carbon::createFromTimestamp($lastActivity));

            if ($elapsed >= self::TIMEOUT_MINUTES) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/')
                    ->with('timeout_warning', 'Tu sesión expiró por inactividad. Por favor, inicia sesión nuevamente.');
            }
        }

        // Actualizar marca de última actividad
        $request->session()->put('last_activity_at', now()->timestamp);

        return $next($request);
    }
}

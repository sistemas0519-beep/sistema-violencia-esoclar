<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Cache;

class OptimizedAuthenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return route('login');
        }
    }

    /**
     * Handle the request - optimizado con caché
     */
    public function handle($request, ...$guards)
    {
        // Cargar datos de usuario en caché para reducir queries
        if ($request->user()) {
            $this->cacheUserData($request->user());
        }

        return parent::handle($request, ...$guards);
    }

    /**
     * Cachear datos del usuario para evitar queries innecesarias
     */
    private function cacheUserData($user): void
    {
        $cacheKey = 'user_cached:' . $user->id;

        Cache::remember($cacheKey, 3600, function () use ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'rol' => $user->rol,
                'activo' => $user->activo,
                'disponibilidad' => $user->disponibilidad,
                'especialidad' => $user->especialidad,
            ];
        });
    }
}

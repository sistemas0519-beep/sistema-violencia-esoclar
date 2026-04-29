<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutoLoginApoyo
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            $user = User::whereIn('rol', ['psicologo', 'asistente'])
                ->where('activo', true)
                ->first()
                ?? User::whereIn('rol', ['psicologo', 'asistente'])->first();

            if ($user) {
                Auth::login($user, remember: true);
                $request->session()->regenerate();
            }
        } elseif (!in_array(Auth::user()?->rol, ['psicologo', 'asistente'])) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $user = User::whereIn('rol', ['psicologo', 'asistente'])
                ->where('activo', true)
                ->first()
                ?? User::whereIn('rol', ['psicologo', 'asistente'])->first();

            if ($user) {
                Auth::login($user, remember: true);
                $request->session()->regenerate();
            }
        }

        return $next($request);
    }
}

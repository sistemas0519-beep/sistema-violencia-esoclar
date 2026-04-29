<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutoLoginAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            $user = User::where('rol', 'admin')->where('activo', true)->first()
                ?? User::where('rol', 'admin')->first();

            if ($user) {
                Auth::login($user, remember: true);
                $request->session()->regenerate();
            }
        } elseif (Auth::user()?->rol !== 'admin') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $user = User::where('rol', 'admin')->where('activo', true)->first()
                ?? User::where('rol', 'admin')->first();

            if ($user) {
                Auth::login($user, remember: true);
                $request->session()->regenerate();
            }
        }

        return $next($request);
    }
}

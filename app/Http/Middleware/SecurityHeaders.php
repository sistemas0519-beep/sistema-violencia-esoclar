<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Evitar clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Evitar MIME-sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Protección básica XSS en navegadores legacy
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Controlar información del referrer
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Limitar acceso a funciones del navegador
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        // Eliminar cabecera que revela la tecnología del servidor
        $response->headers->remove('X-Powered-By');
        $response->headers->set('Server', '');

        // Content Security Policy básico
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://fonts.bunny.net",
            "style-src 'self' 'unsafe-inline' https://fonts.bunny.net",
            "font-src 'self' https://fonts.bunny.net",
            "img-src 'self' data:",
            "connect-src 'self'",
            "frame-ancestors 'self'",
            "base-uri 'self'",
            "form-action 'self'",
        ]);
        $response->headers->set('Content-Security-Policy', $csp);

        // HSTS: solo en producción con HTTPS
        if (app()->isProduction()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}

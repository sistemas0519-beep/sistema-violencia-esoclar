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

        // Detectar si estamos en Codespaces / entorno de preview con iframe
        $isCodespaces = str_contains(config('app.url', ''), '.app.github.dev');

        // Evitar clickjacking (relajado en Codespaces para permitir preview iframe)
        if (! $isCodespaces) {
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        }

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

        // frame-ancestors: permite Codespaces en dev, solo 'self' en producción
        $frameAncestors = $isCodespaces
            ? "frame-ancestors 'self' https://*.app.github.dev"
            : "frame-ancestors 'self'";

        // Detectar entorno local para permitir el servidor de Vite (HMR)
        $isLocal = app()->environment('local');

        // Leer la URL real del servidor Vite desde public/hot (creado por laravel-vite-plugin)
        // Esto resuelve el problema cuando Vite usa un puerto distinto al 5173 (ej. 5174)
        $viteOrigin = '';
        if ($isLocal) {
            $hotFile = public_path('hot');
            if (file_exists($hotFile)) {
                $hotUrl = rtrim(file_get_contents($hotFile), "\n");
                // Convertir http:// a ws:// para WebSockets
                $wsUrl = str_replace(['https://', 'http://'], ['wss://', 'ws://'], $hotUrl);
                $viteOrigin = " {$hotUrl} {$wsUrl}";
            } else {
                // Fallback: permitir los puertos más comunes de Vite
                $viteOrigin = ' http://localhost:5173 ws://localhost:5173 http://localhost:5174 ws://localhost:5174';
            }
        }

        // connect-src: permite Codespaces en dev + Vite HMR en local
        $connectSrcExtras = '';
        if ($isCodespaces) {
            $connectSrcExtras = ' https://*.app.github.dev wss://*.app.github.dev';
        }
        if ($isLocal) {
            $connectSrcExtras .= $viteOrigin;
        }
        $connectSrc = "connect-src 'self'{$connectSrcExtras}";

        // Content Security Policy
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://fonts.bunny.net{$viteOrigin}",
            "style-src 'self' 'unsafe-inline' https://fonts.bunny.net{$viteOrigin}",
            "font-src 'self' https://fonts.bunny.net{$viteOrigin}",
            "img-src 'self' data: https:",
            $connectSrc,
            $frameAncestors,
            "base-uri 'self'",
            "form-action 'self'",
        ]);
        $response->headers->set('Content-Security-Policy', $csp);

        // HSTS: solo en producción con HTTPS
        if (app()->isProduction()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
            // Certificate Transparency (deprecated pero aún útil como señal)
            $response->headers->set('Expect-CT', 'max-age=86400, enforce');
        }

        return $response;
    }
}

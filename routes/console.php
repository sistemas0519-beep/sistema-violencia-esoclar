<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ─── Tareas programadas ───────────────────────────────────────────────────────

// Retención de datos RGPD: diario a las 3 AM en producción
Schedule::command('gdpr:retener-datos --force')
    ->dailyAt('03:00')
    ->environments(['production'])
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/gdpr-retencion.log'));

// Limpiar caché de estadísticas a medianoche para datos frescos
Schedule::command('cache:forget estadisticas:generales')
    ->dailyAt('00:05')
    ->withoutOverlapping();

// Eliminar sesiones expiradas
Schedule::command('session:gc')
    ->hourly()
    ->withoutOverlapping();

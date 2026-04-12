<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ControladorDenuncias;
use App\Http\Controllers\ControladorSeguimiento;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ─── Página de bienvenida ──────────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
});

// ─── Dashboard dinámico (redirige según rol) ─────────────────────────────────
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ─── Rutas autenticadas ────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // Perfil (todos los usuarios)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ─── ALUMNO / DOCENTE: Reportar incidentes ────────────────────────────────
    Route::middleware('rol:alumno,docente')->group(function () {
        Route::get('/alumno/dashboard', function () {
            return view('alumno.dashboard');
        })->name('alumno.dashboard');

        Route::get('/alumno/denuncia', [ControladorDenuncias::class, 'index'])
            ->name('alumno.denuncia');

        Route::post('/alumno/denuncia', [ControladorDenuncias::class, 'store'])
            ->name('denuncia.store');

        Route::get('/alumno/mis-casos', [ControladorDenuncias::class, 'misCasos'])
            ->name('alumno.mis-casos');
    });

    // ─── DOCENTE ──────────────────────────────────────────────────────────────
    Route::middleware('rol:docente')->group(function () {
        Route::get('/docente/dashboard', function () {
            return view('docente.dashboard');
        })->name('docente.dashboard');
    });

    // ─── PSICÓLOGO ────────────────────────────────────────────────────────────
    Route::middleware('rol:psicologo')->group(function () {
        Route::get('/psicologo/dashboard', [ControladorSeguimiento::class, 'index'])
            ->name('psicologo.dashboard');

        Route::get('/psicologo/caso/{caso}', [ControladorSeguimiento::class, 'show'])
            ->name('psicologo.caso');

        Route::post('/psicologo/caso/{caso}/seguimiento', [ControladorSeguimiento::class, 'registrarSeguimiento'])
            ->name('seguimiento.store');

        Route::post('/psicologo/caso/{caso}/asignar', [ControladorSeguimiento::class, 'asignar'])
            ->name('caso.asignar');
    });
});

require __DIR__ . '/auth.php';

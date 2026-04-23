<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ControladorDenuncias;
use App\Http\Controllers\ControladorSeguimiento;
use App\Http\Controllers\ProfileController;
use App\Models\Caso;
use Illuminate\Support\Facades\Route;

// ─── Página de bienvenida ──────────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
});

// ─── Consulta pública de expedientes ──────────────────────────────────────────
Route::get('/consultar-expediente', [ControladorDenuncias::class, 'consultarExpediente'])
    ->name('consultar.expediente');

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
            $userId = auth()->id();
            $casos = Caso::where('denunciante_id', $userId)->get();
            $totalCasos = $casos->count();
            $pendientes = $casos->where('estado', 'pendiente')->count();
            $enProceso = $casos->where('estado', 'en_proceso')->count();
            $resueltos = $casos->where('estado', 'resuelto')->count();
            $ultimosCasos = Caso::with('asignado')
                ->where('denunciante_id', $userId)
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();

            return view('alumno.dashboard', compact(
                'totalCasos', 'pendientes', 'enProceso', 'resueltos', 'ultimosCasos'
            ));
        })->name('alumno.dashboard');

        Route::get('/alumno/denuncia', [ControladorDenuncias::class, 'index'])
            ->name('alumno.denuncia');

        Route::post('/alumno/denuncia', [ControladorDenuncias::class, 'store'])
            ->name('denuncia.store');

        Route::get('/alumno/catalogos/regiones', [ControladorDenuncias::class, 'regiones'])
            ->name('catalogos.regiones');

        Route::get('/alumno/catalogos/provincias', [ControladorDenuncias::class, 'provincias'])
            ->name('catalogos.provincias');

        Route::get('/alumno/catalogos/distritos', [ControladorDenuncias::class, 'distritos'])
            ->name('catalogos.distritos');

        Route::get('/alumno/catalogos/escuelas', [ControladorDenuncias::class, 'escuelas'])
            ->name('catalogos.escuelas');

        Route::get('/alumno/mis-casos', [ControladorDenuncias::class, 'misCasos'])
            ->name('alumno.mis-casos');
    });

    // ─── DOCENTE ──────────────────────────────────────────────────────────────
    Route::middleware('rol:docente')->group(function () {
        Route::get('/docente/dashboard', function () {
            $userId = auth()->id();
            $casos = Caso::where('denunciante_id', $userId)->get();
            $totalReportes = $casos->count();
            $pendientes = $casos->where('estado', 'pendiente')->count();
            $enProceso = $casos->where('estado', 'en_proceso')->count();
            $resueltos = $casos->where('estado', 'resuelto')->count();
            $ultimosReportes = Caso::with('asignado')
                ->where('denunciante_id', $userId)
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();

            return view('docente.dashboard', compact(
                'totalReportes', 'pendientes', 'enProceso', 'resueltos', 'ultimosReportes'
            ));
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

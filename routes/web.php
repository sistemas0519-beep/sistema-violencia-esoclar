<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ControladorDenuncias;
use App\Http\Controllers\ControladorSeguimiento;
use App\Http\Controllers\ProfileController;
use App\Models\Caso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ─── Página de bienvenida (con consulta de expediente inline) ─────────────────
Route::get('/', function (Request $request) {
    $resultados = null;
    $busqueda   = null;
    $tipo       = null;
    $buscado    = false;

    if ($request->filled('busqueda')) {
        $validated = $request->validate([
            'busqueda' => 'required|string|min:2|max:100',
            'tipo'     => 'required|in:codigo,nombre',
        ]);
        $busqueda = trim($validated['busqueda']);
        $tipo     = $validated['tipo'];
        $buscado  = true;

        $query = Caso::select([
            'id', 'codigo_caso', 'tipo_violencia', 'estado', 'prioridad',
            'es_anonimo', 'escuela_nombre', 'distrito', 'provincia', 'region',
            'created_at', 'updated_at',
        ]);

        if ($tipo === 'codigo') {
            $query->where('codigo_caso', strtoupper($busqueda));
        } else {
            $query->whereHas('denunciante', function ($q) use ($busqueda) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($busqueda) . '%']);
            })->where('es_anonimo', false)->orderByDesc('created_at');
        }

        $resultados = $query->paginate(5)->withQueryString();
    }

    return view('welcome', compact('resultados', 'busqueda', 'tipo', 'buscado'));
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
            // Una sola consulta con agregados en lugar de cargar todos los registros en PHP
            $stats = Caso::where('denunciante_id', $userId)
                ->selectRaw("
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado = 'en_proceso' THEN 1 ELSE 0 END) as en_proceso,
                    SUM(CASE WHEN estado = 'resuelto' THEN 1 ELSE 0 END) as resueltos
                ")->first();

            $ultimosCasos = Caso::with('asignado:id,name,rol')
                ->where('denunciante_id', $userId)
                ->select(['id','codigo_caso','tipo_violencia','estado','prioridad','created_at','asignado_a'])
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();

            return view('alumno.dashboard', [
                'totalCasos' => $stats->total,
                'pendientes' => $stats->pendientes,
                'enProceso'  => $stats->en_proceso,
                'resueltos'  => $stats->resueltos,
                'ultimosCasos' => $ultimosCasos,
            ]);
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
            $stats = Caso::where('denunciante_id', $userId)
                ->selectRaw("
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado = 'en_proceso' THEN 1 ELSE 0 END) as en_proceso,
                    SUM(CASE WHEN estado = 'resuelto' THEN 1 ELSE 0 END) as resueltos
                ")->first();

            $ultimosReportes = Caso::with('asignado:id,name,rol')
                ->where('denunciante_id', $userId)
                ->select(['id','codigo_caso','tipo_violencia','estado','prioridad','created_at','asignado_a'])
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();

            return view('docente.dashboard', [
                'totalReportes' => $stats->total,
                'pendientes'    => $stats->pendientes,
                'enProceso'     => $stats->en_proceso,
                'resueltos'     => $stats->resueltos,
                'ultimosReportes' => $ultimosReportes,
            ]);
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

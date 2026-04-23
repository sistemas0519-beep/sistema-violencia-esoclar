<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Caso;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class ControladorDenuncias extends Controller
{
    private const MINEDU_BASE_URL = 'https://sigmed.minedu.gob.pe/servicios/rest/service/restsig.svc';

    public function index()
    {
        return view('alumno.denuncia');
    }

    public function store(Request $request)
    {
        // Rate limit: max 3 reports per hour per user/IP
        $throttleKey = 'denuncia.' . (auth()->check() ? 'user.' . auth()->id() : 'ip.' . $request->ip());
        if (Cache::get($throttleKey, 0) >= 3) {
            return back()
                ->withInput()
                ->withErrors(['_throttle' => 'Has enviado demasiados reportes en poco tiempo. Intenta nuevamente en una hora.']);
        }

        $validated = $request->validate([
            'tipo_violencia' => 'required|in:fisica,psicologica,verbal,sexual,ciberacoso,discriminacion,otro',
            'descripcion'    => 'required|min:10|max:3000',
            'es_anonimo'     => 'nullable|boolean',
            'fecha_incidente'=> 'nullable|date|before_or_equal:today',
            'region'         => 'required|string|max:120',
            'provincia'      => 'required|string|max:120',
            'distrito'       => 'required|string|max:120',
            'escuela_nombre' => 'required|string|max:255',
            'codigo_modular' => 'nullable|string|max:20',
        ]);

        $esAnonimo  = (bool) $request->input('es_anonimo', false);
        $prioridad  = $this->calcularPrioridad($validated['tipo_violencia']);
        $slaLimite  = $this->calcularSla($prioridad);
        $codigoCaso = 'VIO-' . now()->format('Y') . '-' . strtoupper(Str::random(6));

        $caso = Caso::create([
            'codigo_caso'    => $codigoCaso,
            'tipo_violencia' => $validated['tipo_violencia'],
            'descripcion'    => $validated['descripcion'],
            'estado'         => 'pendiente',
            'prioridad'      => $prioridad,
            'sla_limite'     => $slaLimite,
            'es_anonimo'     => $esAnonimo,
            'denunciante_id' => $esAnonimo ? null : auth()->id(),
            'fecha_incidente'=> $validated['fecha_incidente'] ?? null,
            'region'         => $validated['region'],
            'provincia'      => $validated['provincia'],
            'distrito'       => $validated['distrito'],
            'escuela_nombre' => $validated['escuela_nombre'],
            'codigo_modular' => $validated['codigo_modular'] ?? null,
        ]);

        // Increment throttle counter (TTL 1 hour)
        Cache::put($throttleKey, Cache::get($throttleKey, 0) + 1, now()->addHour());

        // Audit log
        AuditLog::create([
            'user_id'     => auth()->id(),
            'accion'      => 'crear',
            'modulo'      => 'casos',
            'descripcion' => "Reporte de incidente registrado: {$codigoCaso}",
            'modelo_tipo' => Caso::class,
            'modelo_id'   => $caso->id,
            'datos_nuevos'=> json_encode([
                'codigo_caso'    => $codigoCaso,
                'tipo_violencia' => $caso->tipo_violencia,
                'prioridad'      => $prioridad,
                'es_anonimo'     => $esAnonimo,
                'estado'         => 'pendiente',
            ]),
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
        ]);

        return redirect()
            ->route('alumno.mis-casos')
            ->with('success', "Tu reporte ha sido registrado con el código {$codigoCaso}. Un psicólogo lo revisará pronto.");
    }

    /** Consulta pública del estado de un expediente/caso */
    public function consultarExpediente(Request $request)
    {
        $resultados = null;
        $busqueda   = null;
        $tipo       = null;
        $buscado    = false;

        // Búsqueda rápida desde welcome (?q=) — detecta tipo automáticamente
        if ($request->filled('q') && !$request->filled('busqueda')) {
            $q        = trim($request->input('q'));
            $tipo     = preg_match('/^[A-Z]{2,}-\d{4}-[A-Z0-9]+$/i', $q) ? 'codigo' : 'nombre';
            $busqueda = $q;
            $buscado  = true;
        } elseif ($request->filled('busqueda')) {
            $request->validate([
                'busqueda' => 'required|string|min:2|max:100',
                'tipo'     => 'required|in:codigo,nombre',
            ]);
            $busqueda = trim($request->input('busqueda'));
            $tipo     = $request->input('tipo');
            $buscado  = true;
        }

        if ($buscado) {
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
                })
                ->where('es_anonimo', false)
                ->orderByDesc('created_at');
            }

            $resultados = $query->paginate(2)->withQueryString();
        }

        return view('consultar-expediente', compact('resultados', 'busqueda', 'tipo', 'buscado'));
    }

    /** Mis casos (alumno autenticado) */
    /** Auto-assign priority based on violence type */
    private function calcularPrioridad(string $tipoViolencia): string
    {
        return match ($tipoViolencia) {
            'sexual'         => 'urgente',
            'fisica'         => 'alta',
            'psicologica'    => 'alta',
            default          => 'media',
        };
    }

    /** Calculate SLA deadline based on priority */
    private function calcularSla(string $prioridad): \Carbon\Carbon
    {
        return match ($prioridad) {
            'urgente' => now()->addHours(4),
            'alta'    => now()->addHours(24),
            'media'   => now()->addDays(3),
            default   => now()->addDays(7),
        };
    }

    public function regiones(): JsonResponse
    {
        return response()->json(
            $this->cachedCatalog('minedu.regiones', function () {
                $rows = $this->fetchMineduRows('regiones');

                return collect($rows)
                    ->map(fn (array $item) => [
                        'codigo' => (string) ($item['CODREGION'] ?? ''),
                        'nombre' => (string) ($item['NOMBRE'] ?? ''),
                    ])
                    ->filter(fn (array $item) => $item['codigo'] !== '' && $item['nombre'] !== '')
                    ->sortBy('nombre', SORT_NATURAL | SORT_FLAG_CASE)
                    ->values()
                    ->all();
            })
        );
    }

    public function provincias(Request $request): JsonResponse
    {
        $request->validate([
            'region' => 'required|string|size:2',
        ]);

        $region = $request->string('region')->toString();

        return response()->json(
            $this->cachedCatalog("minedu.provincias.{$region}", function () use ($region) {
                $rows = $this->fetchMineduRows('provincias', [
                    'codregion' => $region,
                ]);

                return collect($rows)
                    ->map(fn (array $item) => [
                        'codigo' => (string) ($item['CODPROVINCIA'] ?? ''),
                        'nombre' => (string) ($item['NOMBRE'] ?? ''),
                    ])
                    ->filter(fn (array $item) => $item['codigo'] !== '' && $item['nombre'] !== '')
                    ->sortBy('nombre', SORT_NATURAL | SORT_FLAG_CASE)
                    ->values()
                    ->all();
            })
        );
    }

    public function distritos(Request $request): JsonResponse
    {
        $request->validate([
            'provincia' => 'required|string|size:4',
        ]);

        $provincia = $request->string('provincia')->toString();

        return response()->json(
            $this->cachedCatalog("minedu.distritos.{$provincia}", function () use ($provincia) {
                $rows = $this->fetchMineduRows('distritos', [
                    'codprovincia' => $provincia,
                ]);

                return collect($rows)
                    ->map(fn (array $item) => [
                        'codigo' => (string) ($item['CODUBIGEO'] ?? ''),
                        'nombre' => (string) ($item['NOMBRE'] ?? ''),
                    ])
                    ->filter(fn (array $item) => $item['codigo'] !== '' && $item['nombre'] !== '')
                    ->sortBy('nombre', SORT_NATURAL | SORT_FLAG_CASE)
                    ->values()
                    ->all();
            })
        );
    }

    public function escuelas(Request $request): JsonResponse
    {
        $request->validate([
            'ubigeo' => 'nullable|string|size:6',
            'region' => 'nullable|string|size:2',
            'q' => 'nullable|string|max:100',
        ]);

        $ubigeo = $request->string('ubigeo')->toString();
        $region = $request->string('region')->toString();
        $query = trim($request->string('q')->toString());

        if ($ubigeo === '' && $region === '' && $query === '') {
            return response()->json([
                'data' => [],
                'meta' => [
                    'message' => 'Ingresa un nombre o codigo modular, o selecciona una ubicacion para buscar.',
                ],
            ]);
        }

        $queryDigits = preg_replace('/\D+/', '', $query);
        $cacheKey = 'minedu.escuelas.' . md5(json_encode([
            'ubigeo' => $ubigeo,
            'region' => $region,
            'query' => $query,
        ]));

        $results = $this->cachedCatalog($cacheKey, function () use ($ubigeo, $region, $query, $queryDigits) {
            $rows = $this->fetchMineduRows('padron', [
                'codgeo' => $ubigeo !== '' ? $ubigeo : $region,
                'codugel' => '',
                'codmod' => ctype_digit($queryDigits) ? $queryDigits : '',
                'anexo' => '',
                'nombreie' => ! ctype_digit($queryDigits) ? $query : '',
                'codlocal' => '',
                'direccion' => '',
                'cenpob' => '',
                'localidad' => '',
                'nivmod' => '',
                'gesdep' => '',
                'codcp' => '',
                'estado' => '1',
                'ubicados' => '',
            ]);

            return collect($rows)
                ->filter(fn (array $item) => (string) ($item['ESTADO'] ?? '') === '1')
                ->map(fn (array $item) => [
                    'codigo_modular' => trim((string) ($item['CODIGO_MODULAR'] ?? '')),
                    'anexo' => trim((string) ($item['ANEXO'] ?? '')),
                    'nombre' => trim((string) ($item['NOMBRE_ESCUELA'] ?? '')),
                    'nivel_modalidad' => trim((string) ($item['NIVEL_MODALIDAD'] ?? '')),
                    'direccion' => trim((string) ($item['DIRECCION_ESCUELA'] ?? '')),
                    'region' => trim((string) ($item['DEPARTAMENTO'] ?? '')),
                    'provincia' => trim((string) ($item['PROVINCIA'] ?? '')),
                    'distrito' => trim((string) ($item['DISTRITO'] ?? '')),
                    'codigo_ubigeo' => trim((string) ($item['CODIGO_UBIGEO'] ?? '')),
                    'codigo_local' => trim((string) ($item['CODIGO_LOCAL'] ?? '')),
                ])
                ->filter(fn (array $item) => $item['codigo_modular'] !== '' && $item['nombre'] !== '')
                ->sortBy('nombre', SORT_NATURAL | SORT_FLAG_CASE)
                ->values()
                ->all();
        });

        return response()->json([
            'data' => $results,
            'meta' => [
                'count' => count($results),
            ],
        ]);
    }

    public function misCasos()
    {
        $casos = Caso::with('seguimientos')
            ->delAlumno(auth()->id())
            ->orderByDesc('created_at')
            ->get();

        return view('alumno.mis-casos', compact('casos'));
    }

    private function cachedCatalog(string $key, callable $callback): array
    {
        return Cache::remember($key, now()->addHours(12), $callback);
    }

    private function fetchMineduRows(string $endpoint, array $query = []): array
    {
        try {
            $response = Http::timeout(60)
                ->acceptJson()
                ->get(self::MINEDU_BASE_URL . '/' . $endpoint, $query)
                ->throw();
        } catch (Throwable $e) {
            abort(502, 'No se pudo consultar el padron oficial del MINEDU en este momento.');
        }

        $outer = json_decode($response->body(), true);
        $payload = is_string($outer) ? json_decode($outer, true) : $outer;

        return is_array($payload['Rows'] ?? null) ? $payload['Rows'] : [];
    }
}

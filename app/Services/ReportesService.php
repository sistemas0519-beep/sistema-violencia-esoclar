<?php

namespace App\Services;

use App\Models\Asignacion;
use App\Models\Caso;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Servicio optimizado para reportes y estadísticas
 * Implementa caching inteligente para mejorar rendimiento
 */
class ReportesService
{
    private const CACHE_TTL = 3600; // 1 hora
    private const STATS_CACHE_TTL = 300; // 5 minutos

    /**
     * Obtener estadísticas generales del sistema (cached)
     */
    public static function getEstadisticasGenerales(): array
    {
        return Cache::remember('estadisticas:generales', self::STATS_CACHE_TTL, function () {
            $hoy   = today()->toDateString();
            $semana = today()->subDays(7)->toDateString();
            $mes    = today()->subDays(30)->toDateString();

            // Una sola consulta para todos los agregados de casos (evita 11 COUNT separados)
            $casosRow = DB::selectOne("
                SELECT
                    COUNT(*)                                                   AS total,
                    SUM(DATE(created_at) = ?)                                  AS hoy,
                    SUM(DATE(created_at) >= ?)                                 AS semana,
                    SUM(DATE(created_at) >= ?)                                 AS mes,
                    SUM(estado = 'pendiente')                                  AS pendiente,
                    SUM(estado = 'en_proceso')                                 AS en_proceso,
                    SUM(estado = 'resuelto')                                   AS resuelto,
                    SUM(estado = 'cerrado')                                    AS cerrado,
                    SUM(prioridad = 'urgente' AND estado != 'cerrado')         AS urgente,
                    SUM(sla_vencido = 1)                                       AS sla_vencido,
                    SUM(escalado = 1)                                          AS escalado
                FROM casos
            ", [$hoy, $semana, $mes]);

            // Una consulta para asignaciones
            $asigRow = DB::selectOne("
                SELECT
                    COUNT(*)                             AS total,
                    SUM(estado = 'activa')               AS activa,
                    SUM(estado = 'finalizada')           AS finalizada,
                    SUM(estado = 'cancelada')            AS cancelada
                FROM asignaciones
            ");

            // Una consulta para usuarios
            $userRow = DB::selectOne("
                SELECT
                    COUNT(*)                                                                   AS total,
                    SUM(activo = 1)                                                            AS activos,
                    SUM(activo = 0)                                                            AS inactivos,
                    SUM(rol = 'psicologo' AND activo = 1)                                     AS psicologos,
                    SUM(rol = 'psicologo' AND activo = 1 AND disponibilidad = 'disponible')   AS psicologos_disponibles
                FROM users
            ");

            return [
                'casos' => [
                    'total'      => (int) $casosRow->total,
                    'hoy'        => (int) $casosRow->hoy,
                    'semana'     => (int) $casosRow->semana,
                    'mes'        => (int) $casosRow->mes,
                    'pendiente'  => (int) $casosRow->pendiente,
                    'en_proceso' => (int) $casosRow->en_proceso,
                    'resuelto'   => (int) $casosRow->resuelto,
                    'cerrado'    => (int) $casosRow->cerrado,
                    'urgente'    => (int) $casosRow->urgente,
                    'sla_vencido'=> (int) $casosRow->sla_vencido,
                    'escalado'   => (int) $casosRow->escalado,
                ],
                'asignaciones' => [
                    'total'     => (int) $asigRow->total,
                    'activa'    => (int) $asigRow->activa,
                    'finalizada'=> (int) $asigRow->finalizada,
                    'cancelada' => (int) $asigRow->cancelada,
                ],
                'usuarios' => [
                    'total'                 => (int) $userRow->total,
                    'activos'               => (int) $userRow->activos,
                    'inactivos'             => (int) $userRow->inactivos,
                    'psicologos'            => (int) $userRow->psicologos,
                    'psicologos_disponibles'=> (int) $userRow->psicologos_disponibles,
                ],
            ];
        });
    }

    /**
     * Invalidar caché de estadísticas
     */
    public static function invalidarCacheEstadisticas(): void
    {
        Cache::forget('estadisticas:generales');
    }

    /**
     * Obtener resumen de casos por filtros (optimizado)
     */
    public static function getResumenCasos(
        ?string $fechaInicio = null,
        ?string $fechaFin = null,
        ?string $tipoViolencia = null,
        ?string $estado = null
    ): array {
        $cacheKey = 'reportes:resumen:' . md5(json_encode(compact(
            'fechaInicio', 'fechaFin', 'tipoViolencia', 'estado'
        )));

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use (
            $fechaInicio, $fechaFin, $tipoViolencia, $estado
        ) {
            $query = Caso::query();

            if ($fechaInicio) {
                $query->whereDate('created_at', '>=', $fechaInicio);
            }
            if ($fechaFin) {
                $query->whereDate('created_at', '<=', $fechaFin);
            }
            if ($tipoViolencia) {
                $query->where('tipo_violencia', $tipoViolencia);
            }
            if ($estado) {
                $query->where('estado', $estado);
            }

            // Una sola consulta con todos los agregados
            $row = (clone $query)->selectRaw("
                COUNT(*)                                              AS total,
                SUM(estado = 'pendiente')                             AS pendiente,
                SUM(estado = 'en_proceso')                            AS en_proceso,
                SUM(estado = 'resuelto')                              AS resuelto,
                SUM(estado = 'cerrado')                               AS cerrado,
                SUM(es_anonimo = 1)                                   AS anonimos,
                SUM(asignado_a IS NULL)                               AS sin_asignar,
                SUM(prioridad = 'urgente')                            AS urgentes,
                SUM(sla_vencido = 1)                                  AS sla_vencido
            ")->first();

            $total   = (int) $row->total;
            $resuelto = (int) $row->resuelto;
            $cerrado  = (int) $row->cerrado;

            return [
                'total'           => $total,
                'pendiente'       => (int) $row->pendiente,
                'en_proceso'      => (int) $row->en_proceso,
                'resuelto'        => $resuelto,
                'cerrado'         => $cerrado,
                'anonimos'        => (int) $row->anonimos,
                'sin_asignar'     => (int) $row->sin_asignar,
                'urgentes'        => (int) $row->urgentes,
                'sla_vencido'     => (int) $row->sla_vencido,
                'tasa_resolucion' => $total > 0 ? round((($resuelto + $cerrado) / $total) * 100, 1) : 0,
            ];
        });
    }

    /**
     * Casos agrupados por tipo de violencia (optimizado)
     */
    public static function getCasosPorTipo(
        ?string $fechaInicio = null,
        ?string $fechaFin = null
    ): array {
        $cacheKey = 'reportes:por_tipo:' . md5(json_encode(compact('fechaInicio', 'fechaFin')));

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($fechaInicio, $fechaFin) {
            $query = Caso::query();

            if ($fechaInicio) {
                $query->whereDate('created_at', '>=', $fechaInicio);
            }
            if ($fechaFin) {
                $query->whereDate('created_at', '<=', $fechaFin);
            }

            $total = $query->count() ?: 1;

            return $query
                ->select('tipo_violencia', DB::raw('count(*) as total'))
                ->groupBy('tipo_violencia')
                ->orderByDesc('total')
                ->get()
                ->map(fn ($r) => [
                    'tipo' => $r->tipo_violencia,
                    'total' => $r->total,
                    'porcentaje' => round(($r->total / $total) * 100, 1),
                ])
                ->toArray();
        });
    }

    /**
     * Casos por mes para gráfico de tendencias
     */
    public static function getCasosPorMes(
        ?string $fechaInicio = null,
        ?string $fechaFin = null
    ): array {
        $cacheKey = 'reportes:por_mes:' . md5(json_encode(compact('fechaInicio', 'fechaFin')));

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($fechaInicio, $fechaFin) {
            $query = Caso::query();

            if ($fechaInicio) {
                $query->whereDate('created_at', '>=', $fechaInicio);
            }
            if ($fechaFin) {
                $query->whereDate('created_at', '<=', $fechaFin);
            }

            return $query
                ->select(
                    DB::raw('YEAR(created_at) as año'),
                    DB::raw('MONTH(created_at) as mes'),
                    DB::raw('COUNT(*) as total')
                )
                ->groupBy('año', 'mes')
                ->orderBy('año')->orderBy('mes')
                ->get()
                ->map(fn ($r) => [
                    'mes' => Carbon::createFromDate($r->año, $r->mes, 1)->translatedFormat('M Y'),
                    'total' => $r->total,
                ])
                ->toArray();
        });
    }

    /**
     * Casos por región (Top 10)
     */
    public static function getCasosPorRegion(int $limit = 10): array
    {
        return Cache::remember("reportes:por_region:{$limit}", self::CACHE_TTL, function () use ($limit) {
            return Caso::select('region', DB::raw('count(*) as total'))
                ->whereNotNull('region')
                ->where('region', '!=', '')
                ->groupBy('region')
                ->orderByDesc('total')
                ->limit($limit)
                ->get()
                ->map(fn ($r) => [
                    'region' => $r->region,
                    'total' => $r->total,
                ])
                ->toArray();
        });
    }

    /**
     * Carga de trabajo por psicólogo
     */
    public static function getCargaPorPsicologo(): array
    {
        return Cache::remember('reportes:carga_psicologo:v3', self::CACHE_TTL, function () {
            $rows = User::where('rol', 'psicologo')
                ->where('activo', true)
                ->select('id', 'name', 'especialidad', 'disponibilidad')
                ->withCount([
                    'asignacionesComoPsicologo as activas_count' => fn ($q) => $q->where('estado', 'activa'),
                    'asignacionesComoPsicologo as finalizadas_count' => fn ($q) => $q->where('estado', 'finalizada'),
                    'casosAsignados as casos_activos_count' => fn ($q) => $q->where('estado', '!=', 'cerrado'),
                ])
                ->get()
                ->map(fn ($p) => [
                    'id' => $p->id,
                    'nombre' => $p->name,
                    'especialidad' => $p->especialidad,
                    'disponibilidad' => $p->disponibilidad,
                    'activas' => $p->activas_count,
                    'finalizadas' => $p->finalizadas_count,
                    'total' => $p->activas_count + $p->finalizadas_count,
                    'asignaciones_activas' => $p->activas_count,
                    'casos_activos' => $p->casos_activos_count,
                ])
                ->toArray();

            return array_map(function ($row) {
                $activas = (int) ($row['activas'] ?? $row['asignaciones_activas'] ?? 0);
                $finalizadas = (int) ($row['finalizadas'] ?? 0);

                $row['total'] = (int) ($row['total'] ?? ($activas + $finalizadas));
                $row['activas'] = $activas;
                $row['finalizadas'] = $finalizadas;

                return $row;
            }, $rows);
        });
    }

    /**
     * Gráfico de últimos 14 días
     */
    public static function getCasosUltimos14Dias(): array
    {
        return Cache::remember('reportes:ultimos_14_dias', 300, function () {
            $datos = [];
            for ($i = 13; $i >= 0; $i--) {
                $fecha = today()->subDays($i);
                $datos[] = [
                    'fecha' => $fecha->format('d/m'),
                    'casos' => Caso::whereDate('created_at', $fecha)->count(),
                ];
            }
            return $datos;
        });
    }

    /**
     * Obtener distribución de roles activos
     */
    public static function getDistribucionRoles(): array
    {
        return Cache::remember('reportes:distribucion_roles', self::STATS_CACHE_TTL, function () {
            return User::where('activo', true)
                ->select('rol', DB::raw('count(*) as total'))
                ->groupBy('rol')
                ->get()
                ->map(fn ($r) => [
                    'rol' => $r->rol,
                    'cantidad' => $r->total,
                ])
                ->toArray();
        });
    }

    /**
     * Tasas de resolución por mes
     */
    public static function getTasasResolucionPorMes(): array
    {
        return Cache::remember('reportes:tasas_resolucion', self::CACHE_TTL, function () {
            $datos = DB::table('casos')
                ->select(
                    DB::raw('YEAR(created_at) as año'),
                    DB::raw('MONTH(created_at) as mes'),
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(CASE WHEN estado IN ("resuelto", "cerrado") THEN 1 ELSE 0 END) as resueltos')
                )
                ->groupBy('año', 'mes')
                ->orderBy('año')
                ->orderBy('mes')
                ->get();

            return $datos->map(fn ($r) => [
                'mes' => Carbon::createFromDate($r->año, $r->mes, 1)->translatedFormat('M Y'),
                'tasa' => $r->total > 0 ? round(($r->resueltos / $r->total) * 100, 1) : 0,
            ])->toArray();
        });
    }
}

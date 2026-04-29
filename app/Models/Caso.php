<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Caso extends Model
{
    protected $fillable = [
        'codigo_caso',
        'tipo_violencia',
        'descripcion',
        'estado',
        'prioridad',
        'sla_limite',
        'fecha_primera_atencion',
        'sla_vencido',
        'categoria',
        'etiquetas',
        'notas_internas',
        'escalado',
        'fecha_escalamiento',
        'es_anonimo',
        'denunciante_id',
        'asignado_a',
        'fecha_incidente',
        'region',
        'provincia',
        'distrito',
        'escuela_nombre',
        'codigo_modular',
        'es_sensible',
        'nivel_sensibilidad',
        'area_tematica',
        'nivel_urgencia',
        'motivo_sensibilidad',
        // Nuevos campos extendidos
        'agresor_nombre',
        'agresor_rol',
        'agresor_grado_grupo',
        'victima_nombre',
        'victima_rol',
        'victima_grado_grupo',
        'testigos',
        'grado_grupo',
        'ubicacion_exacta',
        'nivel_severidad',
        'acciones_tomadas',
        'docente_responsable_id',
    ];

    protected $casts = [
        'es_anonimo'              => 'boolean',
        'es_sensible'             => 'boolean',
        'sla_vencido'             => 'boolean',
        'escalado'                => 'boolean',
        'fecha_incidente'         => 'datetime',
        'sla_limite'              => 'datetime',
        'fecha_primera_atencion'  => 'datetime',
        'fecha_escalamiento'      => 'datetime',
        'etiquetas'               => 'array',
        'nivel_severidad'         => 'integer',
    ];

    /**
     * Relaciones cargadas automáticamente (evita N+1 en la mayoría de vistas).
     * Solo carga `asignado` por defecto para no sobrecargar en listados grandes.
     */
    protected $with = ['asignado'];

    // ─── Relaciones ───────────────────────────────────────────────────────────

    public function denunciante()
    {
        return $this->belongsTo(User::class, 'denunciante_id')
                    ->select(['id', 'name', 'email', 'rol']); // solo columnas necesarias
    }

    public function seguimientos()
    {
        return $this->hasMany(Seguimiento::class)
                    ->orderByDesc('fecha_seguimiento');
    }

    public function asignado()
    {
        return $this->belongsTo(User::class, 'asignado_a')
                    ->select(['id', 'name', 'rol']);
    }

    // ─── Scopes de consulta frecuente ─────────────────────────────────────────

    /** Casos visibles para el psicólogo (pendientes o propios) */
    public function scopeParaPsicologo(Builder $q, int $userId): Builder
    {
        return $q->where(fn ($sub) =>
            $sub->where('estado', 'pendiente')
                ->orWhere('asignado_a', $userId)
        );
    }

    /** Casos de un alumno específico (no anónimos) */
    public function scopeDelAlumno(Builder $q, int $userId): Builder
    {
        return $q->where('denunciante_id', $userId);
    }

    /** Solo casos activos (no cerrados) */
    public function scopeActivos(Builder $q): Builder
    {
        return $q->whereNotIn('estado', ['cerrado']);
    }

    /** Casos urgentes o con SLA vencido */
    public function scopeUrgentes(Builder $q): Builder
    {
        return $q->where(function ($sub) {
            $sub->where('prioridad', 'urgente')
                ->orWhere('sla_vencido', true)
                ->orWhere('escalado', true);
        });
    }

    /** Casos de una prioridad específica */
    public function scopeDePrioridad(Builder $q, string $prioridad): Builder
    {
        return $q->where('prioridad', $prioridad);
    }

    // ─── Relaciones adicionales ───────────────────────────────────────────────

    /** Docente responsable del caso */
    public function docenteResponsable()
    {
        return $this->belongsTo(User::class, 'docente_responsable_id')
                    ->select(['id', 'name', 'rol']);
    }

    /** Documentos asociados a este caso */
    public function documentos()
    {
        return $this->hasMany(Documento::class);
    }

    /** Notas confidenciales del caso */
    public function notasConfidenciales()
    {
        return $this->hasMany(NotaConfidencial::class)->orderByDesc('created_at');
    }

    /** Registro de accesos a este caso */
    public function accesos()
    {
        return $this->hasMany(AccesoCaso::class)->orderByDesc('created_at');
    }

    /** Solicitudes de asesoría vinculadas */
    public function solicitudesAsesoria()
    {
        return $this->hasMany(SolicitudAsesoria::class);
    }

    /** Sesiones relacionadas */
    public function sesiones()
    {
        return $this->hasMany(Sesion::class);
    }

    /** Intervenciones del caso */
    public function intervenciones()
    {
        return $this->hasMany(Intervencion::class);
    }

    /** Mensajes relacionados al caso */
    public function mensajes()
    {
        return $this->hasMany(Mensaje::class);
    }

    // ─── Scopes adicionales ───────────────────────────────────────────────────

    /** Solo casos sensibles */
    public function scopeSensibles(Builder $q): Builder
    {
        return $q->where('es_sensible', true);
    }

    /** Filtrar por nivel de sensibilidad */
    public function scopeDeNivelSensibilidad(Builder $q, string $nivel): Builder
    {
        return $q->where('nivel_sensibilidad', $nivel);
    }

    /** Filtrar por área temática */
    public function scopeDeAreaTematica(Builder $q, string $area): Builder
    {
        return $q->where('area_tematica', $area);
    }

    /** Filtrar por nivel de urgencia */
    public function scopeDeNivelUrgencia(Builder $q, string $nivel): Builder
    {
        return $q->where('nivel_urgencia', $nivel);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /** Verificar si el SLA está vencido */
    public function verificarSla(): bool
    {
        if ($this->sla_limite && !$this->fecha_primera_atencion && now()->gt($this->sla_limite)) {
            $this->update(['sla_vencido' => true]);
            return true;
        }
        return false;
    }

    /** Obtener color CSS según prioridad */
    public function getColorPrioridadAttribute(): string
    {
        return match ($this->prioridad) {
            'urgente' => 'danger',
            'alta'    => 'warning',
            'media'   => 'info',
            'baja'    => 'gray',
            default   => 'gray',
        };
    }
}

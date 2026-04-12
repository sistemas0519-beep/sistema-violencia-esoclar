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
        'es_anonimo',
        'denunciante_id',
        'asignado_a',
        'fecha_incidente',
    ];

    protected $casts = [
        'es_anonimo'      => 'boolean',
        'fecha_incidente' => 'datetime',
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
}

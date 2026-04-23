<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudAsesoria extends Model
{
    protected $table = 'solicitudes_asesoria';

    protected $fillable = [
        'codigo',
        'solicitante_id',
        'atendido_por',
        'caso_id',
        'tipo',
        'estado',
        'prioridad',
        'motivo',
        'descripcion',
        'observaciones_resolucion',
        'fecha_solicitud',
        'fecha_asignacion',
        'fecha_resolucion',
    ];

    protected $casts = [
        'fecha_solicitud'  => 'datetime',
        'fecha_asignacion' => 'datetime',
        'fecha_resolucion' => 'datetime',
    ];

    protected $with = ['solicitante', 'atendidoPor'];

    // ─── Relaciones ───────────────────────────────────────────────────────────

    public function solicitante()
    {
        return $this->belongsTo(User::class, 'solicitante_id')->select(['id', 'name', 'email', 'rol']);
    }

    public function atendidoPor()
    {
        return $this->belongsTo(User::class, 'atendido_por')->select(['id', 'name', 'email', 'rol']);
    }

    public function caso()
    {
        return $this->belongsTo(Caso::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeEnProceso($query)
    {
        return $query->where('estado', 'en_proceso');
    }

    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }

    public function scopeDelProfesional($query, int $userId)
    {
        return $query->where('atendido_por', $userId);
    }

    public function scopeUrgentes($query)
    {
        return $query->where('prioridad', 'urgente');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public static function generarCodigo(): string
    {
        $ultimo = self::whereYear('created_at', now()->year)->count() + 1;
        return 'ASE-' . now()->format('Y') . '-' . str_pad($ultimo, 4, '0', STR_PAD_LEFT);
    }

    public function asignar(int $profesionalId): void
    {
        $this->update([
            'atendido_por'     => $profesionalId,
            'estado'           => 'en_proceso',
            'fecha_asignacion' => now(),
        ]);
    }

    public function completar(string $observaciones): void
    {
        $this->update([
            'estado'                   => 'completada',
            'observaciones_resolucion' => $observaciones,
            'fecha_resolucion'         => now(),
        ]);
    }

    public function tiempoRespuestaHoras(): ?float
    {
        if (!$this->fecha_asignacion) {
            return null;
        }
        return $this->fecha_solicitud->diffInHours($this->fecha_asignacion, true);
    }

    public function tiempoResolucionHoras(): ?float
    {
        if (!$this->fecha_resolucion) {
            return null;
        }
        return $this->fecha_solicitud->diffInHours($this->fecha_resolucion, true);
    }
}

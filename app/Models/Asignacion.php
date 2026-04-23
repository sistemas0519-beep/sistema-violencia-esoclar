<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Asignacion extends Model
{
    protected $table = 'asignaciones';

    protected $fillable = [
        'psicologo_id',
        'paciente_id',
        'caso_id',
        'notas',
        'fecha_inicio',
        'fecha_fin',
        'frecuencia_atencion',
        'dia_atencion',
        'hora_atencion',
        'estado',
        'motivo_fin',
        'solicitud_cambio',
        'created_by',
        'actualizado_por',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'hora_atencion' => 'datetime:H:i',
        'solicitud_cambio' => 'boolean',
    ];

    protected $with = ['psicologo', 'paciente', 'caso', 'creador'];

    public function psicologo()
    {
        return $this->belongsTo(User::class, 'psicologo_id')
            ->select(['id', 'name', 'email', 'especialidad', 'disponibilidad', 'foto_perfil']);
    }

    public function paciente()
    {
        return $this->belongsTo(User::class, 'paciente_id')
            ->select(['id', 'name', 'email', 'rol']);
    }

    public function caso()
    {
        return $this->belongsTo(Caso::class, 'caso_id')
            ->select(['id', 'codigo_caso', 'tipo_violencia', 'estado']);
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by')
            ->select(['id', 'name']);
    }

    public function actualizador()
    {
        return $this->belongsTo(User::class, 'actualizado_por')
            ->select(['id', 'name']);
    }

    public function scopeActivas(Builder $query): Builder
    {
        return $query->where('estado', 'activa');
    }

    public function scopeFinalizadas(Builder $query): Builder
    {
        return $query->where('estado', 'finalizada');
    }

    public function scopeDelPsicologo(Builder $query, int $psicologoId): Builder
    {
        return $query->where('psicologo_id', $psicologoId);
    }

    public function scopeDelPaciente(Builder $query, int $pacienteId): Builder
    {
        return $query->where('paciente_id', $pacienteId);
    }

    public function tieneAsignacionActiva(): bool
    {
        return self::where('paciente_id', $this->paciente_id)
            ->where('estado', 'activa')
            ->exists();
    }

    public function estaDisponible(): bool
    {
        return $this->psicologo->disponibilidad === 'disponible';
    }

    public function finalizar(?string $motivo, int $userId): bool
    {
        $this->update([
            'estado' => 'finalizada',
            'fecha_fin' => now()->toDateString(),
            'motivo_fin' => $motivo,
            'actualizado_por' => $userId,
        ]);

        return true;
    }

    public function solicitarCambio(): bool
    {
        $this->update(['solicitud_cambio' => true]);

        return true;
    }
}

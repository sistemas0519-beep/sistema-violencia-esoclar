<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sesion extends Model
{
    protected $table = 'sesiones';

    protected $fillable = [
        'profesional_id',
        'paciente_id',
        'caso_id',
        'asignacion_id',
        'tipo_sesion',
        'modalidad',
        'estado',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'lugar',
        'objetivo',
        'notas_preparacion',
        'resumen_sesion',
        'motivo_cancelacion',
        'duracion_real_minutos',
    ];

    protected $casts = [
        'fecha'      => 'date',
        'hora_inicio' => 'datetime:H:i',
        'hora_fin'    => 'datetime:H:i',
    ];

    protected $with = ['profesional', 'paciente'];

    // ─── Relaciones ───────────────────────────────────────────────────────────

    public function profesional()
    {
        return $this->belongsTo(User::class, 'profesional_id')
            ->select(['id', 'name', 'email', 'especialidad']);
    }

    public function paciente()
    {
        return $this->belongsTo(User::class, 'paciente_id')
            ->select(['id', 'name', 'email', 'rol']);
    }

    public function caso()
    {
        return $this->belongsTo(Caso::class);
    }

    public function asignacion()
    {
        return $this->belongsTo(Asignacion::class);
    }

    public function intervenciones()
    {
        return $this->hasMany(Intervencion::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeDelProfesional($query, int $profesionalId)
    {
        return $query->where('profesional_id', $profesionalId);
    }

    public function scopeHoy($query)
    {
        return $query->whereDate('fecha', today());
    }

    public function scopeProximas($query)
    {
        return $query->where('fecha', '>=', today())
            ->whereIn('estado', ['programada', 'confirmada'])
            ->orderBy('fecha')
            ->orderBy('hora_inicio');
    }

    public function scopeSemanaActual($query)
    {
        return $query->whereBetween('fecha', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function completar(string $resumen, int $duracionMinutos): void
    {
        $this->update([
            'estado'               => 'completada',
            'resumen_sesion'       => $resumen,
            'duracion_real_minutos' => $duracionMinutos,
        ]);
    }

    public function cancelar(string $motivo): void
    {
        $this->update([
            'estado'              => 'cancelada',
            'motivo_cancelacion'  => $motivo,
        ]);
    }

    public function marcarNoAsistio(): void
    {
        $this->update(['estado' => 'no_asistio']);
    }

    public function getHorarioFormateadoAttribute(): string
    {
        $inicio = \Carbon\Carbon::parse($this->hora_inicio)->format('H:i');
        $fin = \Carbon\Carbon::parse($this->hora_fin)->format('H:i');
        return "{$inicio} - {$fin}";
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Intervencion extends Model
{
    protected $table = 'intervenciones';

    protected $fillable = [
        'codigo',
        'caso_id',
        'profesional_id',
        'sesion_id',
        'tipo_intervencion',
        'estado',
        'descripcion',
        'acciones_realizadas',
        'resultados_observados',
        'recomendaciones',
        'efectividad',
        'fecha_inicio',
        'fecha_fin',
        'requiere_seguimiento',
        'proximo_seguimiento',
    ];

    protected $casts = [
        'fecha_inicio'          => 'date',
        'fecha_fin'             => 'date',
        'proximo_seguimiento'   => 'date',
        'requiere_seguimiento'  => 'boolean',
    ];

    protected $with = ['profesional'];

    // ─── Relaciones ───────────────────────────────────────────────────────────

    public function caso()
    {
        return $this->belongsTo(Caso::class);
    }

    public function profesional()
    {
        return $this->belongsTo(User::class, 'profesional_id')
            ->select(['id', 'name', 'especialidad']);
    }

    public function sesion()
    {
        return $this->belongsTo(Sesion::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeDelProfesional($query, int $profesionalId)
    {
        return $query->where('profesional_id', $profesionalId);
    }

    public function scopeActivas($query)
    {
        return $query->whereIn('estado', ['planificada', 'en_curso']);
    }

    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }

    public function scopePendientesSeguimiento($query)
    {
        return $query->where('requiere_seguimiento', true)
            ->whereNotNull('proximo_seguimiento')
            ->where('proximo_seguimiento', '<=', today());
    }

    public function scopeEfectivas($query)
    {
        return $query->whereIn('efectividad', ['muy_efectiva', 'efectiva']);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public static function generarCodigo(): string
    {
        $ultimo = self::whereYear('created_at', now()->year)->count() + 1;
        return 'INT-' . now()->format('Y') . '-' . str_pad($ultimo, 4, '0', STR_PAD_LEFT);
    }

    public function completar(string $resultados, string $efectividad): void
    {
        $this->update([
            'estado'                => 'completada',
            'fecha_fin'             => today(),
            'resultados_observados' => $resultados,
            'efectividad'           => $efectividad,
        ]);
    }
}

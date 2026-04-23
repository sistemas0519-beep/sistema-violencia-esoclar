<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'accion',
        'modulo',
        'descripcion',
        'modelo_tipo',
        'modelo_id',
        'datos_anteriores',
        'datos_nuevos',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos'     => 'array',
    ];

    // ─── Relaciones ───────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class)->select(['id', 'name', 'email', 'rol']);
    }

    public function modelo()
    {
        return $this->morphTo('modelo', 'modelo_tipo', 'modelo_id');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeDelModulo($query, string $modulo)
    {
        return $query->where('modulo', $modulo);
    }

    public function scopeDelUsuario($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecientes($query, int $dias = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($dias));
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public static function registrar(
        string $accion,
        string $modulo,
        string $descripcion,
        ?Model $modelo = null,
        ?array $datosAnteriores = null,
        ?array $datosNuevos = null,
    ): self {
        return self::create([
            'user_id'           => auth()->id(),
            'accion'            => $accion,
            'modulo'            => $modulo,
            'descripcion'       => $descripcion,
            'modelo_tipo'       => $modelo ? get_class($modelo) : null,
            'modelo_id'         => $modelo?->getKey(),
            'datos_anteriores'  => $datosAnteriores,
            'datos_nuevos'      => $datosNuevos,
            'ip_address'        => request()->ip(),
            'user_agent'        => request()->userAgent(),
        ]);
    }
}

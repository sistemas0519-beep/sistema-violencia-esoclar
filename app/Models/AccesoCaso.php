<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccesoCaso extends Model
{
    public $timestamps = false;

    protected $table = 'acceso_casos';

    protected $fillable = [
        'caso_id',
        'user_id',
        'tipo_acceso',
        'seccion_accedida',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // ─── Relaciones ───────────────────────────────────────────────────────────

    public function caso()
    {
        return $this->belongsTo(Caso::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->select(['id', 'name', 'email', 'rol']);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeDelCaso($query, int $casoId)
    {
        return $query->where('caso_id', $casoId);
    }

    public function scopeDelUsuario($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecientes($query, int $dias = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($dias));
    }

    // ─── Helper ───────────────────────────────────────────────────────────────

    public static function registrar(int $casoId, string $tipoAcceso, ?string $seccion = null): self
    {
        return self::create([
            'caso_id'           => $casoId,
            'user_id'           => auth()->id(),
            'tipo_acceso'       => $tipoAcceso,
            'seccion_accedida'  => $seccion,
            'ip_address'        => request()->ip(),
            'user_agent'        => request()->userAgent(),
        ]);
    }
}

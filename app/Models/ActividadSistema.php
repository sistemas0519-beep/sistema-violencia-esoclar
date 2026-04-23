<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActividadSistema extends Model
{
    protected $table = 'actividad_sistema';

    protected $fillable = [
        'tipo',
        'nivel',
        'mensaje',
        'datos',
        'user_id',
        'ip_address',
    ];

    protected $casts = [
        'datos' => 'array',
    ];

    // ─── Relaciones ───────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class)->select(['id', 'name', 'rol']);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeDelTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeDelNivel($query, string $nivel)
    {
        return $query->where('nivel', $nivel);
    }

    public function scopeRecientes($query, int $horas = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($horas));
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public static function registrar(
        string $tipo,
        string $mensaje,
        string $nivel = 'info',
        ?array $datos = null,
    ): self {
        return self::create([
            'tipo'       => $tipo,
            'nivel'      => $nivel,
            'mensaje'    => $mensaje,
            'datos'      => $datos,
            'user_id'    => auth()->id(),
            'ip_address' => request()->ip(),
        ]);
    }

    public static function info(string $tipo, string $mensaje, ?array $datos = null): self
    {
        return self::registrar($tipo, $mensaje, 'info', $datos);
    }

    public static function warning(string $tipo, string $mensaje, ?array $datos = null): self
    {
        return self::registrar($tipo, $mensaje, 'warning', $datos);
    }

    public static function error(string $tipo, string $mensaje, ?array $datos = null): self
    {
        return self::registrar($tipo, $mensaje, 'error', $datos);
    }

    public static function critical(string $tipo, string $mensaje, ?array $datos = null): self
    {
        return self::registrar($tipo, $mensaje, 'critical', $datos);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'configuraciones';

    protected $fillable = [
        'clave',
        'valor',
        'grupo',
        'tipo',
        'etiqueta',
        'descripcion',
        'opciones',
    ];

    protected $casts = [
        'opciones' => 'array',
    ];

    // ─── Helpers estáticos ────────────────────────────────────────────────────

    /**
     * Obtener el valor de una configuración por clave.
     */
    public static function obtener(string $clave, mixed $default = null): mixed
    {
        $config = self::where('clave', $clave)->first();

        if (!$config) {
            return $default;
        }

        return match ($config->tipo) {
            'boolean' => (bool) $config->valor,
            'number'  => is_numeric($config->valor) ? (float) $config->valor : $default,
            'json'    => json_decode($config->valor, true) ?? $default,
            default   => $config->valor,
        };
    }

    /**
     * Establecer el valor de una configuración.
     */
    public static function establecer(string $clave, mixed $valor): bool
    {
        $config = self::where('clave', $clave)->first();

        if (!$config) {
            return false;
        }

        if ($config->tipo === 'json' && is_array($valor)) {
            $valor = json_encode($valor);
        } elseif ($config->tipo === 'boolean') {
            $valor = $valor ? '1' : '0';
        }

        return $config->update(['valor' => (string) $valor]);
    }

    /**
     * Obtener todas las configuraciones de un grupo.
     */
    public static function delGrupo(string $grupo): array
    {
        return self::where('grupo', $grupo)
            ->orderBy('id')
            ->get()
            ->mapWithKeys(fn ($c) => [$c->clave => $c])
            ->toArray();
    }
}

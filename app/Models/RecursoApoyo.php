<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecursoApoyo extends Model
{
    use SoftDeletes;

    protected $table = 'recursos_apoyo';

    protected $fillable = [
        'creado_por',
        'titulo',
        'contenido',
        'categoria',
        'etiquetas',
        'es_publico',
        'destacado',
        'archivo_adjunto',
        'visitas',
    ];

    protected $casts = [
        'etiquetas'  => 'array',
        'es_publico' => 'boolean',
        'destacado'  => 'boolean',
    ];

    // ─── Relaciones ───────────────────────────────────────────────────────────

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por')->select(['id', 'name', 'rol']);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeDeCategoria($query, string $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    public function scopeDestacados($query)
    {
        return $query->where('destacado', true);
    }

    public function scopePublicos($query)
    {
        return $query->where('es_publico', true);
    }

    public function scopeBuscar($query, string $termino)
    {
        return $query->whereFullText(['titulo', 'contenido'], $termino);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function incrementarVisitas(): void
    {
        $this->increment('visitas');
    }
}

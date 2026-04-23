<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotaConfidencial extends Model
{
    use SoftDeletes;

    protected $table = 'notas_confidenciales';

    protected $fillable = [
        'caso_id',
        'autor_id',
        'contenido',
        'visibilidad',
        'es_critica',
    ];

    protected $casts = [
        'es_critica' => 'boolean',
    ];

    // ─── Relaciones ───────────────────────────────────────────────────────────

    public function caso()
    {
        return $this->belongsTo(Caso::class);
    }

    public function autor()
    {
        return $this->belongsTo(User::class, 'autor_id')->select(['id', 'name', 'rol']);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeVisiblesPara($query, User $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('visibilidad', 'equipo_apoyo');

            if ($user->esPsicologo()) {
                $q->orWhere('visibilidad', 'psicologos');
            }

            $q->orWhere('autor_id', $user->id);
        });
    }

    public function scopeCriticas($query)
    {
        return $query->where('es_critica', true);
    }
}

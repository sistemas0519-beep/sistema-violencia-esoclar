<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seguimiento extends Model
{
    protected $fillable = [
        'caso_id',
        'responsable_id',
        'notas',
        'accion',
        'fecha_seguimiento',
    ];

    protected $casts = [
        'fecha_seguimiento' => 'datetime',
    ];

    /** Carga automática del responsable en cada seguimiento. */
    protected $with = ['responsable'];

    // ─── Relaciones ───────────────────────────────────────────────────────────

    public function caso()
    {
        return $this->belongsTo(Caso::class)
                    ->select(['id', 'codigo_caso', 'estado', 'tipo_violencia']);
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id')
                    ->select(['id', 'name', 'rol']);
    }
}

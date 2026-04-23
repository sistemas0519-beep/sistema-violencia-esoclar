<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Documento extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'caso_id',
        'asignacion_id',
        'nombre',
        'nombre_original',
        'ruta',
        'tipo_archivo',
        'mime_type',
        'tamaño',
        'version',
        'documento_padre_id',
        'categoria',
        'descripcion',
        'acceso',
        'subido_por',
    ];

    protected $casts = [
        'tamaño' => 'integer',
        'version' => 'integer',
    ];

    // ─── Relaciones ───────────────────────────────────────────────────────────

    public function caso()
    {
        return $this->belongsTo(Caso::class);
    }

    public function asignacion()
    {
        return $this->belongsTo(Asignacion::class);
    }

    public function autor()
    {
        return $this->belongsTo(User::class, 'subido_por')->select(['id', 'name', 'rol']);
    }

    public function documentoPadre()
    {
        return $this->belongsTo(self::class, 'documento_padre_id');
    }

    public function versiones()
    {
        return $this->hasMany(self::class, 'documento_padre_id')->orderByDesc('version');
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getTamañoFormateadoAttribute(): string
    {
        $bytes = $this->tamaño;
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }

    public function getUrlAttribute(): ?string
    {
        return Storage::url($this->ruta);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeDelCaso($query, int $casoId)
    {
        return $query->where('caso_id', $casoId);
    }

    public function scopeDeCategoria($query, string $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    public function scopeAccesibles($query)
    {
        if (auth()->user()?->esAdmin()) {
            return $query;
        }
        return $query->where('acceso', '!=', 'confidencial');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mensaje extends Model
{
    use SoftDeletes;

    protected $table = 'mensajes';

    protected $fillable = [
        'remitente_id',
        'destinatario_id',
        'caso_id',
        'mensaje_padre_id',
        'asunto',
        'contenido',
        'prioridad',
        'es_confidencial',
        'leido_en',
        'archivado_remitente_en',
        'archivado_destinatario_en',
    ];

    protected $casts = [
        'es_confidencial'            => 'boolean',
        'leido_en'                   => 'datetime',
        'archivado_remitente_en'     => 'datetime',
        'archivado_destinatario_en'  => 'datetime',
    ];

    // ─── Relaciones ───────────────────────────────────────────────────────────

    public function remitente()
    {
        return $this->belongsTo(User::class, 'remitente_id')->select(['id', 'name', 'email', 'rol']);
    }

    public function destinatario()
    {
        return $this->belongsTo(User::class, 'destinatario_id')->select(['id', 'name', 'email', 'rol']);
    }

    public function caso()
    {
        return $this->belongsTo(Caso::class);
    }

    public function mensajePadre()
    {
        return $this->belongsTo(self::class, 'mensaje_padre_id');
    }

    public function respuestas()
    {
        return $this->hasMany(self::class, 'mensaje_padre_id')->orderBy('created_at');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeRecibidos($query, int $userId)
    {
        return $query->where('destinatario_id', $userId)
            ->whereNull('archivado_destinatario_en');
    }

    public function scopeEnviados($query, int $userId)
    {
        return $query->where('remitente_id', $userId)
            ->whereNull('archivado_remitente_en');
    }

    public function scopeNoLeidos($query, int $userId)
    {
        return $query->where('destinatario_id', $userId)->whereNull('leido_en');
    }

    public function scopePrincipales($query)
    {
        return $query->whereNull('mensaje_padre_id');
    }

    public function scopeUrgentes($query)
    {
        return $query->where('prioridad', 'urgente');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function marcarLeido(): void
    {
        if (!$this->leido_en) {
            $this->update(['leido_en' => now()]);
        }
    }

    public function archivarPara(int $userId): void
    {
        if ($this->remitente_id === $userId) {
            $this->update(['archivado_remitente_en' => now()]);
        }
        if ($this->destinatario_id === $userId) {
            $this->update(['archivado_destinatario_en' => now()]);
        }
    }

    public function estaLeido(): bool
    {
        return $this->leido_en !== null;
    }
}

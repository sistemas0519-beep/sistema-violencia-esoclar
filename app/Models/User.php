<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
     * Solo los administradores pueden acceder al panel de Filament.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->rol === 'admin';
    }

    // ─── Helpers de rol ───────────────────────────────────────────────────────

    public function esAdmin(): bool      { return $this->rol === 'admin'; }
    public function esAlumno(): bool     { return $this->rol === 'alumno'; }
    public function esDocente(): bool    { return $this->rol === 'docente'; }
    public function esPsicologo(): bool  { return $this->rol === 'psicologo'; }

    // ─── Relaciones ───────────────────────────────────────────────────────────

    /** Casos registrados por este usuario como denunciante */
    public function casosComodenunciante()
    {
        return $this->hasMany(Caso::class, 'denunciante_id');
    }

    /** Casos asignados a este usuario (psicólogo) */
    public function casosAsignados()
    {
        return $this->hasMany(Caso::class, 'asignado_a');
    }

    /** Seguimientos registrados por este usuario */
    public function seguimientos()
    {
        return $this->hasMany(Seguimiento::class, 'responsable_id');
    }
}

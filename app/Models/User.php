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
        'activo',
        'ultimo_acceso',
        'notas_admin',
        'especialidad',
        'disponibilidad',
        'foto_perfil',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
            'ultimo_acceso' => 'datetime',
        ];
    }

    public function estaActivo(): bool
    {
        return $this->activo;
    }

    public function esDisponible(): bool
    {
        return $this->disponibilidad === 'disponible';
    }

    public function tieneDisponibilidad(): bool
    {
        return in_array($this->disponibilidad, ['disponible', 'ocupado']);
    }

    /**
     * Solo los administradores pueden acceder al panel admin de Filament.
     * El panel de apoyo acepta psicólogos y asistentes.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'admin' => $this->rol === 'admin',
            'apoyo' => in_array($this->rol, ['psicologo', 'asistente']),
            default => false,
        };
    }

    // ─── Helpers de rol ───────────────────────────────────────────────────────

    public function esAdmin(): bool
    {
        return $this->rol === 'admin';
    }

    public function esAlumno(): bool
    {
        return $this->rol === 'alumno';
    }

    public function esDocente(): bool
    {
        return $this->rol === 'docente';
    }

    public function esPsicologo(): bool
    {
        return $this->rol === 'psicologo';
    }

    public function esAsistente(): bool
    {
        return $this->rol === 'asistente';
    }

    public function esPersonalApoyo(): bool
    {
        return in_array($this->rol, ['psicologo', 'asistente']);
    }

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

    /** Asignaciones donde es psicólogo */
    public function asignacionesComoPsicologo()
    {
        return $this->hasMany(Asignacion::class, 'psicologo_id');
    }

    /** Asignaciones donde es paciente */
    public function asignacionesComoPaciente()
    {
        return $this->hasMany(Asignacion::class, 'paciente_id');
    }

    /** Asignación activa como paciente */
    public function asignacionActiva()
    {
        return $this->hasOne(Asignacion::class, 'paciente_id')
            ->where('estado', 'activa');
    }

    /** Registros de auditoría de este usuario */
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    /** Documentos subidos por este usuario */
    public function documentos()
    {
        return $this->hasMany(Documento::class, 'subido_por');
    }

    /** Sesiones como profesional */
    public function sesionesComoProfesional()
    {
        return $this->hasMany(Sesion::class, 'profesional_id');
    }

    /** Sesiones como paciente */
    public function sesionesComoPaciente()
    {
        return $this->hasMany(Sesion::class, 'paciente_id');
    }

    /** Intervenciones realizadas */
    public function intervenciones()
    {
        return $this->hasMany(Intervencion::class, 'profesional_id');
    }

    /** Mensajes recibidos */
    public function mensajesRecibidos()
    {
        return $this->hasMany(Mensaje::class, 'destinatario_id');
    }

    /** Mensajes enviados */
    public function mensajesEnviados()
    {
        return $this->hasMany(Mensaje::class, 'remitente_id');
    }

    /** Solicitudes de asesoría atendidas */
    public function solicitudesAtendidas()
    {
        return $this->hasMany(SolicitudAsesoria::class, 'atendido_por');
    }

    /** Carga de trabajo actual (asignaciones activas + sesiones esta semana) */
    public function getCargaTrabajoAttribute(): int
    {
        return $this->asignacionesComoPsicologo()->activas()->count()
            + $this->sesionesComoProfesional()->semanaActual()->count();
    }
}

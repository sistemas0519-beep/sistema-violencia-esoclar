<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Caso;
use App\Models\Documento;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Comando de retención de datos conforme a RGPD/LGPD.
 *
 * Política de retención configurada:
 *   - Casos cerrados: anonimizar datos personales tras 5 años
 *   - Audit logs: eliminar registros > 2 años
 *   - Documentos eliminados (soft-delete): purgar tras 1 año
 *   - Usuarios inactivos sin actividad en 3 años: anonimizar
 *
 * Ejecución recomendada: diaria, 03:00 AM
 *   0 3 * * * php artisan gdpr:retener-datos --force >> /var/log/gdpr.log 2>&1
 */
class RetenerDatosGdpr extends Command
{
    protected $signature = 'gdpr:retener-datos
                            {--dry-run : Mostrar qué se procesaría sin hacer cambios}
                            {--force   : Ejecutar sin confirmación interactiva}';

    protected $description = 'Aplica política de retención de datos (RGPD/LGPD): anonimiza y purga registros vencidos';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if (! $dryRun && ! $this->option('force')) {
            if (! $this->confirm('¿Ejecutar política de retención de datos? Esta acción es irreversible.')) {
                $this->info('Operación cancelada.');
                return self::SUCCESS;
            }
        }

        $this->info($dryRun ? '[MODO DRY-RUN] Simulando retención de datos...' : 'Aplicando política de retención de datos...');

        $resumen = [
            'casos_anonimizados'     => $this->anonimizarCasosCerrados($dryRun),
            'audit_logs_eliminados'  => $this->purgarAuditLogs($dryRun),
            'documentos_purgados'    => $this->purgarDocumentosEliminados($dryRun),
            'usuarios_anonimizados'  => $this->anonimizarUsuariosInactivos($dryRun),
        ];

        $this->table(
            ['Operación', 'Registros afectados'],
            collect($resumen)->map(fn ($count, $op) => [$op, $count])->values()->toArray()
        );

        $logMessage = sprintf(
            'GDPR retención completada%s: casos=%d, audit_logs=%d, docs=%d, usuarios=%d',
            $dryRun ? ' (dry-run)' : '',
            ...array_values($resumen)
        );

        Log::channel('single')->info($logMessage);
        $this->info($logMessage);

        return self::SUCCESS;
    }

    /**
     * Anonimiza datos personales de casos cerrados hace más de 5 años.
     * Conserva los datos estadísticos (tipo_violencia, estado, región, fechas).
     */
    private function anonimizarCasosCerrados(bool $dryRun): int
    {
        $limite = now()->subYears(5);

        $query = Caso::where('estado', 'cerrado')
            ->where('updated_at', '<', $limite)
            ->whereNotNull('denunciante_id'); // ya anonimizados no tienen denunciante

        $count = $query->count();

        if (! $dryRun && $count > 0) {
            $query->update([
                'denunciante_id'      => null,
                'asignado_a'          => null,
                'descripcion'         => '[DATOS ANONIMIZADOS - RGPD]',
                'agresor_nombre'      => null,
                'victima_nombre'      => null,
                'testigos'            => null,
                'notas_internas'      => null,
                'ubicacion_exacta'    => null,
                'docente_responsable_id' => null,
                'es_anonimo'          => true,
            ]);
        }

        return $count;
    }

    /**
     * Elimina registros de auditoría con más de 2 años.
     * Los logs de auditoría solo son necesarios para el periodo de revisión regulatoria.
     */
    private function purgarAuditLogs(bool $dryRun): int
    {
        $limite = now()->subYears(2);
        $count  = AuditLog::where('created_at', '<', $limite)->count();

        if (! $dryRun && $count > 0) {
            AuditLog::where('created_at', '<', $limite)->delete();
        }

        return $count;
    }

    /**
     * Purga físicamente documentos con soft-delete de más de 1 año.
     */
    private function purgarDocumentosEliminados(bool $dryRun): int
    {
        $limite = now()->subYear();

        $docs = Documento::onlyTrashed()
            ->where('deleted_at', '<', $limite)
            ->get(['id', 'ruta']);

        if (! $dryRun && $docs->isNotEmpty()) {
            foreach ($docs as $doc) {
                // Eliminar archivo físico si existe
                if ($doc->ruta && Storage::exists($doc->ruta)) {
                    Storage::delete($doc->ruta);
                }
            }

            Documento::onlyTrashed()
                ->whereIn('id', $docs->pluck('id'))
                ->forceDelete();
        }

        return $docs->count();
    }

    /**
     * Anonimiza usuarios sin actividad en 3 años que no sean administradores.
     * Preserva el registro para mantener integridad referencial, pero elimina PII.
     */
    private function anonimizarUsuariosInactivos(bool $dryRun): int
    {
        $limite = now()->subYears(3);

        $query = User::where('activo', false)
            ->where('rol', '!=', 'admin')
            ->where(function ($q) use ($limite) {
                $q->where('ultimo_acceso', '<', $limite)
                  ->orWhereNull('ultimo_acceso');
            })
            ->where('updated_at', '<', $limite)
            ->where('name', 'not like', '[ANONIMIZADO%'); // evitar re-procesar

        $count = $query->count();

        if (! $dryRun && $count > 0) {
            $query->each(function (User $user) {
                $user->update([
                    'name'          => '[ANONIMIZADO-' . $user->id . ']',
                    'email'         => 'anonimizado_' . $user->id . '@eliminado.invalid',
                    'password'      => bcrypt(bin2hex(random_bytes(32))),
                    'notas_admin'   => null,
                    'foto_perfil'   => null,
                    'especialidad'  => null,
                    'remember_token'=> null,
                ]);
            });
        }

        return $count;
    }
}

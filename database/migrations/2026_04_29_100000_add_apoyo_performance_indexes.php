<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Índices de rendimiento para tablas del panel de apoyo.
 *
 * Razonamiento:
 *   - intervenciones: filtros frecuentes por profesional + estado y por caso
 *   - mensajes: bandeja de entrada filtra por receptor + leido
 *   - recursos_apoyo: búsquedas por categoria y tipo
 *   - notas_confidenciales: ya tiene (caso_id, visibilidad) — añadir autor + deleted_at
 *   - solicitudes_asesoria: ya tiene (estado, prioridad) — añadir solicitante
 *   - audit_logs: consultas de administración por módulo y acción
 *   - actividad_sistema: queries de monitoreo por tipo + nivel + created_at
 */
return new class extends Migration
{
    public function up(): void
    {
        // ─── intervenciones ───────────────────────────────────────────────────
        if (Schema::hasTable('intervenciones')) {
            Schema::table('intervenciones', function (Blueprint $table) {
                if (! $this->indexExists('intervenciones', 'intervenciones_profesional_estado_index')) {
                    $table->index(['profesional_id', 'estado'], 'intervenciones_profesional_estado_index');
                }
                if (! $this->indexExists('intervenciones', 'intervenciones_caso_id_index')) {
                    $table->index('caso_id', 'intervenciones_caso_id_index');
                }
                if (! $this->indexExists('intervenciones', 'intervenciones_created_at_index')) {
                    $table->index('created_at', 'intervenciones_created_at_index');
                }
            });
        }

        // ─── mensajes (ya tiene índices en creación: destinatario+leido_en, remitente+created_at) ──
        // No se agregan índices duplicados.

        // ─── recursos_apoyo (ya tiene índice en categoria) ────────────────────
        // No se agregan índices duplicados — destacado no justifica índice aislado.

        // ─── notas_confidenciales ─────────────────────────────────────────────
        if (Schema::hasTable('notas_confidenciales')) {
            Schema::table('notas_confidenciales', function (Blueprint $table) {
                if (! $this->indexExists('notas_confidenciales', 'notas_autor_deleted_index')) {
                    $table->index(['autor_id', 'deleted_at'], 'notas_autor_deleted_index');
                }
            });
        }

        // ─── solicitudes_asesoria ─────────────────────────────────────────────
        if (Schema::hasTable('solicitudes_asesoria')) {
            Schema::table('solicitudes_asesoria', function (Blueprint $table) {
                if (! $this->indexExists('solicitudes_asesoria', 'solicitudes_solicitante_estado_index')) {
                    $table->index(['solicitante_id', 'estado'], 'solicitudes_solicitante_estado_index');
                }
            });
        }

        // ─── audit_logs ───────────────────────────────────────────────────────
        if (Schema::hasTable('audit_logs')) {
            Schema::table('audit_logs', function (Blueprint $table) {
                if (! $this->indexExists('audit_logs', 'audit_modulo_accion_index')) {
                    $table->index(['modulo', 'accion'], 'audit_modulo_accion_index');
                }
                if (! $this->indexExists('audit_logs', 'audit_created_at_index')) {
                    $table->index('created_at', 'audit_created_at_index');
                }
            });
        }

        // ─── actividad_sistema ────────────────────────────────────────────────
        if (Schema::hasTable('actividad_sistema')) {
            Schema::table('actividad_sistema', function (Blueprint $table) {
                if (! $this->indexExists('actividad_sistema', 'actividad_tipo_nivel_created_index')) {
                    $table->index(['tipo', 'nivel', 'created_at'], 'actividad_tipo_nivel_created_index');
                }
            });
        }
    }

    public function down(): void
    {
        $drops = [
            'intervenciones'       => ['intervenciones_profesional_estado_index', 'intervenciones_caso_id_index', 'intervenciones_created_at_index'],
            'notas_confidenciales' => ['notas_autor_deleted_index'],
            'solicitudes_asesoria' => ['solicitudes_solicitante_estado_index'],
            'audit_logs'           => ['audit_modulo_accion_index', 'audit_created_at_index'],
            'actividad_sistema'    => ['actividad_tipo_nivel_created_index'],
        ];

        foreach ($drops as $table => $indexes) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $t) use ($indexes) {
                    foreach ($indexes as $index) {
                        if ($this->indexExists($t->getTable(), $index)) {
                            $t->dropIndex($index);
                        }
                    }
                });
            }
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        return collect(Schema::getIndexes($table))
            ->contains(fn ($idx) => $idx['name'] === $indexName);
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agregar índices de rendimiento estratégicos
     */
    public function up(): void
    {
        Schema::table('casos', function (Blueprint $table) {
            if (!Schema::hasIndex('casos', 'casos_estado_created_at_index')) {
                $table->index(['estado', 'created_at']);
            }
            if (!Schema::hasIndex('casos', 'casos_tipo_violencia_estado_index')) {
                $table->index(['tipo_violencia', 'estado']);
            }
            if (!Schema::hasIndex('casos', 'casos_prioridad_estado_index')) {
                $table->index(['prioridad', 'estado']);
            }
            if (!Schema::hasIndex('casos', 'casos_asignado_a_estado_index')) {
                $table->index(['asignado_a', 'estado']);
            }
            if (!Schema::hasIndex('casos', 'casos_denunciante_id_created_at_index')) {
                $table->index(['denunciante_id', 'created_at']);
            }
            if (!Schema::hasIndex('casos', 'casos_region_index')) {
                $table->index(['region']);
            }
            // Los otros índices pueden ya existir, los omitimos
        });

        Schema::table('asignaciones', function (Blueprint $table) {
            if (!Schema::hasIndex('asignaciones', 'asignaciones_estado_created_at_index')) {
                $table->index(['estado', 'created_at']);
            }
            if (!Schema::hasIndex('asignaciones', 'asignaciones_psicologo_id_estado_index')) {
                $table->index(['psicologo_id', 'estado']);
            }
            if (!Schema::hasIndex('asignaciones', 'asignaciones_paciente_id_estado_index')) {
                $table->index(['paciente_id', 'estado']);
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasIndex('users', 'users_rol_activo_index')) {
                $table->index(['rol', 'activo']);
            }
        });

        Schema::table('seguimientos', function (Blueprint $table) {
            if (!Schema::hasIndex('seguimientos', 'seguimientos_caso_id_created_at_index')) {
                $table->index(['caso_id', 'created_at']);
            }
            if (!Schema::hasIndex('seguimientos', 'seguimientos_responsable_id_index')) {
                $table->index(['responsable_id']);
            }
        });

        Schema::table('documentos', function (Blueprint $table) {
            if (!Schema::hasIndex('documentos', 'documentos_caso_id_index')) {
                $table->index(['caso_id']);
            }
            if (!Schema::hasIndex('documentos', 'documentos_asignacion_id_index')) {
                $table->index(['asignacion_id']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('casos', function (Blueprint $table) {
            $table->dropIndex(['estado', 'created_at']);
            $table->dropIndex(['tipo_violencia', 'estado']);
            $table->dropIndex(['prioridad', 'estado']);
            $table->dropIndex(['asignado_a', 'estado']);
            $table->dropIndex(['denunciante_id', 'created_at']);
            $table->dropIndex(['region']);
            $table->dropIndex('sla_vencido');
            $table->dropIndex('escalado');
        });

        Schema::table('asignaciones', function (Blueprint $table) {
            $table->dropIndex(['estado', 'created_at']);
            $table->dropIndex(['psicologo_id', 'estado']);
            $table->dropIndex(['paciente_id', 'estado']);
            $table->dropIndex('estado');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['rol', 'activo']);
            $table->dropIndex('email');
            $table->dropIndex('activo');
        });

        Schema::table('seguimientos', function (Blueprint $table) {
            $table->dropIndex(['caso_id', 'created_at']);
            $table->dropIndex(['responsable_id']);
        });

        Schema::table('documentos', function (Blueprint $table) {
            $table->dropIndex(['caso_id']);
            $table->dropIndex(['asignacion_id']);
            $table->dropIndex(['categoria']);
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            if (Schema::hasIndex('audit_logs', 'audit_logs_user_id_created_at_index')) {
                $table->dropIndex(['user_id', 'created_at']);
            }
        });
    }
};

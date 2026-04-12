<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega índices en las columnas más consultadas para acelerar queries.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Índices en casos ──────────────────────────────────────────────────
        Schema::table('casos', function (Blueprint $table) {
            $table->index('estado',          'idx_casos_estado');
            $table->index('denunciante_id',  'idx_casos_denunciante');
            $table->index('asignado_a',      'idx_casos_asignado');
            $table->index('tipo_violencia',  'idx_casos_tipo');
            $table->index('created_at',      'idx_casos_created');
        });

        // ── Índices en seguimientos ───────────────────────────────────────────
        Schema::table('seguimientos', function (Blueprint $table) {
            $table->index('caso_id',          'idx_seg_caso');
            $table->index('responsable_id',   'idx_seg_responsable');
            $table->index('fecha_seguimiento','idx_seg_fecha');
        });

        // ── Índices en users ──────────────────────────────────────────────────
        Schema::table('users', function (Blueprint $table) {
            $table->index('rol', 'idx_users_rol');
        });
    }

    public function down(): void
    {
        Schema::table('casos', function (Blueprint $table) {
            $table->dropIndex('idx_casos_estado');
            $table->dropIndex('idx_casos_denunciante');
            $table->dropIndex('idx_casos_asignado');
            $table->dropIndex('idx_casos_tipo');
            $table->dropIndex('idx_casos_created');
        });

        Schema::table('seguimientos', function (Blueprint $table) {
            $table->dropIndex('idx_seg_caso');
            $table->dropIndex('idx_seg_responsable');
            $table->dropIndex('idx_seg_fecha');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_rol');
        });
    }
};

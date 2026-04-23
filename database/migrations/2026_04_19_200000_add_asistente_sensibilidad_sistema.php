<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Agregar rol 'asistente' a la columna enum de users ───────────────
        // SQLite no soporta ALTER TABLE MODIFY COLUMN, así que solo continuamos
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE users MODIFY COLUMN rol ENUM('admin','alumno','docente','psicologo','asistente') DEFAULT 'alumno'");
        }

        // ─── Campos de sensibilidad en casos ──────────────────────────────────
        Schema::table('casos', function (Blueprint $table) {
            $table->boolean('es_sensible')->default(false)->after('es_anonimo');
            $table->enum('nivel_sensibilidad', ['normal', 'sensible', 'altamente_confidencial'])
                  ->default('normal')->after('es_sensible');
            $table->enum('area_tematica', [
                'acoso_escolar', 'violencia_fisica', 'violencia_psicologica',
                'violencia_sexual', 'autolesion', 'consumo_sustancias',
                'violencia_familiar', 'discriminacion', 'ciberacoso', 'otro'
            ])->nullable()->after('nivel_sensibilidad');
            $table->enum('nivel_urgencia', ['inmediata', 'alta', 'media', 'baja'])
                  ->default('media')->after('area_tematica');
            $table->text('motivo_sensibilidad')->nullable()->after('nivel_urgencia');

            $table->index('es_sensible');
            $table->index('nivel_sensibilidad');
            $table->index('nivel_urgencia');
            $table->index('area_tematica');
        });
    }

    public function down(): void
    {
        Schema::table('casos', function (Blueprint $table) {
            $table->dropIndex(['es_sensible']);
            $table->dropIndex(['nivel_sensibilidad']);
            $table->dropIndex(['nivel_urgencia']);
            $table->dropIndex(['area_tematica']);
            $table->dropColumn([
                'es_sensible', 'nivel_sensibilidad', 'area_tematica',
                'nivel_urgencia', 'motivo_sensibilidad',
            ]);
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE users MODIFY COLUMN rol ENUM('admin','alumno','docente','psicologo') DEFAULT 'alumno'");
        }
    }
};

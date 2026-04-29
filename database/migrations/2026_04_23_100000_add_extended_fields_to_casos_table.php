<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('casos', function (Blueprint $table) {
            // Datos del agresor
            $table->string('agresor_nombre')->nullable()->after('descripcion');
            $table->string('agresor_rol')->nullable()->after('agresor_nombre'); // alumno, docente, personal, externo
            $table->string('agresor_grado_grupo')->nullable()->after('agresor_rol');

            // Datos de la víctima
            $table->string('victima_nombre')->nullable()->after('agresor_grado_grupo');
            $table->string('victima_rol')->nullable()->after('victima_nombre');
            $table->string('victima_grado_grupo')->nullable()->after('victima_rol');

            // Testigos
            $table->text('testigos')->nullable()->after('victima_grado_grupo');

            // Grado y grupo involucrado
            $table->string('grado_grupo')->nullable()->after('testigos');

            // Ubicación exacta dentro de la escuela
            $table->string('ubicacion_exacta')->nullable()->after('grado_grupo'); // aula, patio, baño, etc.

            // Nivel de severidad (1-5)
            $table->unsignedTinyInteger('nivel_severidad')->default(3)->after('ubicacion_exacta');

            // Acciones tomadas resumen
            $table->text('acciones_tomadas')->nullable()->after('nivel_severidad');

            // Docente responsable del caso
            $table->foreignId('docente_responsable_id')->nullable()->after('acciones_tomadas')
                ->constrained('users')->nullOnDelete();
        });

        // Índices adicionales para filtros
        Schema::table('casos', function (Blueprint $table) {
            $table->index('nivel_severidad');
            $table->index('grado_grupo');
            $table->index('ubicacion_exacta');
            $table->index('docente_responsable_id');
        });
    }

    public function down(): void
    {
        Schema::table('casos', function (Blueprint $table) {
            $table->dropForeign(['docente_responsable_id']);
            $table->dropIndex(['nivel_severidad']);
            $table->dropIndex(['grado_grupo']);
            $table->dropIndex(['ubicacion_exacta']);
            $table->dropIndex(['docente_responsable_id']);
            $table->dropColumn([
                'agresor_nombre', 'agresor_rol', 'agresor_grado_grupo',
                'victima_nombre', 'victima_rol', 'victima_grado_grupo',
                'testigos', 'grado_grupo', 'ubicacion_exacta',
                'nivel_severidad', 'acciones_tomadas', 'docente_responsable_id',
            ]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Registro de accesos a casos ──────────────────────────────────────
        Schema::create('acceso_casos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caso_id')->constrained('casos')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('tipo_acceso', ['lectura', 'escritura', 'descarga', 'impresion']);
            $table->string('seccion_accedida')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['caso_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });

        // ─── Notas confidenciales ─────────────────────────────────────────────
        Schema::create('notas_confidenciales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caso_id')->constrained('casos')->cascadeOnDelete();
            $table->foreignId('autor_id')->constrained('users')->cascadeOnDelete();
            $table->text('contenido');
            $table->enum('visibilidad', ['solo_autor', 'psicologos', 'equipo_apoyo'])
                  ->default('equipo_apoyo');
            $table->boolean('es_critica')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['caso_id', 'visibilidad']);
        });

        // ─── Solicitudes de asesoría ──────────────────────────────────────────
        Schema::create('solicitudes_asesoria', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->foreignId('solicitante_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('atendido_por')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('caso_id')->nullable()->constrained('casos')->nullOnDelete();
            $table->enum('tipo', ['orientacion', 'intervencion', 'derivacion', 'seguimiento', 'emergencia']);
            $table->enum('estado', ['pendiente', 'en_proceso', 'completada', 'cancelada', 'derivada'])
                  ->default('pendiente');
            $table->enum('prioridad', ['urgente', 'alta', 'media', 'baja'])->default('media');
            $table->string('motivo');
            $table->text('descripcion')->nullable();
            $table->text('observaciones_resolucion')->nullable();
            $table->timestamp('fecha_solicitud')->useCurrent();
            $table->timestamp('fecha_asignacion')->nullable();
            $table->timestamp('fecha_resolucion')->nullable();
            $table->timestamps();

            $table->index(['estado', 'prioridad']);
            $table->index('atendido_por');
            $table->index('fecha_solicitud');
        });

        // ─── Sesiones programadas ─────────────────────────────────────────────
        Schema::create('sesiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profesional_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('paciente_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('caso_id')->nullable()->constrained('casos')->nullOnDelete();
            $table->foreignId('asignacion_id')->nullable()->constrained('asignaciones')->nullOnDelete();
            $table->enum('tipo_sesion', [
                'evaluacion_inicial', 'seguimiento', 'intervencion',
                'cierre', 'emergencia', 'grupal',
            ]);
            $table->enum('modalidad', ['presencial', 'virtual', 'telefonica'])->default('presencial');
            $table->enum('estado', [
                'programada', 'confirmada', 'en_curso', 'completada',
                'cancelada', 'no_asistio', 'reprogramada',
            ])->default('programada');
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->string('lugar')->nullable();
            $table->text('objetivo')->nullable();
            $table->text('notas_preparacion')->nullable();
            $table->text('resumen_sesion')->nullable();
            $table->text('motivo_cancelacion')->nullable();
            $table->integer('duracion_real_minutos')->nullable();
            $table->timestamps();

            $table->index(['profesional_id', 'fecha']);
            $table->index(['paciente_id', 'fecha']);
            $table->index(['estado', 'fecha']);
        });

        // ─── Intervenciones realizadas ────────────────────────────────────────
        Schema::create('intervenciones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->foreignId('caso_id')->constrained('casos')->cascadeOnDelete();
            $table->foreignId('profesional_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('sesion_id')->nullable()->constrained('sesiones')->nullOnDelete();
            $table->enum('tipo_intervencion', [
                'contencion_emocional', 'orientacion_individual', 'orientacion_familiar',
                'derivacion_externa', 'mediacion', 'plan_seguridad',
                'intervencion_crisis', 'taller_grupal', 'acompanamiento', 'otro',
            ]);
            $table->enum('estado', ['planificada', 'en_curso', 'completada', 'suspendida'])
                  ->default('planificada');
            $table->text('descripcion');
            $table->text('acciones_realizadas')->nullable();
            $table->text('resultados_observados')->nullable();
            $table->text('recomendaciones')->nullable();
            $table->enum('efectividad', ['muy_efectiva', 'efectiva', 'parcial', 'sin_efecto', 'pendiente_evaluacion'])
                  ->default('pendiente_evaluacion');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->boolean('requiere_seguimiento')->default(true);
            $table->date('proximo_seguimiento')->nullable();
            $table->timestamps();

            $table->index(['caso_id', 'tipo_intervencion']);
            $table->index(['profesional_id', 'estado']);
            $table->index('fecha_inicio');
        });

        // ─── Mensajería interna segura ────────────────────────────────────────
        Schema::create('mensajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('remitente_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('destinatario_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('caso_id')->nullable()->constrained('casos')->nullOnDelete();
            $table->foreignId('mensaje_padre_id')->nullable()->constrained('mensajes')->nullOnDelete();
            $table->string('asunto');
            $table->text('contenido');
            $table->enum('prioridad', ['urgente', 'alta', 'normal', 'baja'])->default('normal');
            $table->boolean('es_confidencial')->default(false);
            $table->timestamp('leido_en')->nullable();
            $table->timestamp('archivado_remitente_en')->nullable();
            $table->timestamp('archivado_destinatario_en')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['destinatario_id', 'leido_en']);
            $table->index(['remitente_id', 'created_at']);
            $table->index('caso_id');
        });

        // ─── Recursos de consulta / Base de conocimientos ─────────────────────
        Schema::create('recursos_apoyo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creado_por')->constrained('users')->cascadeOnDelete();
            $table->string('titulo');
            $table->text('contenido');
            $table->enum('categoria', [
                'protocolo', 'guia_intervencion', 'normativa',
                'recurso_externo', 'formato', 'material_psicoeducativo', 'otro',
            ]);
            $table->json('etiquetas')->nullable();
            $table->boolean('es_publico')->default(false);
            $table->boolean('destacado')->default(false);
            $table->string('archivo_adjunto')->nullable();
            $table->integer('visitas')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('categoria');
            // SQLite doesn't support fulltext indexes
            if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
                $table->fullText(['titulo', 'contenido']);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recursos_apoyo');
        Schema::dropIfExists('mensajes');
        Schema::dropIfExists('intervenciones');
        Schema::dropIfExists('sesiones');
        Schema::dropIfExists('solicitudes_asesoria');
        Schema::dropIfExists('notas_confidenciales');
        Schema::dropIfExists('acceso_casos');
    }
};

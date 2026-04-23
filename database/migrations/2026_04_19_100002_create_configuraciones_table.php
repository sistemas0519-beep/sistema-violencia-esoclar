<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuraciones', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique();
            $table->text('valor')->nullable();
            $table->string('grupo', 50);            // general, notificaciones, seguridad, casos
            $table->string('tipo', 20)->default('text'); // text, number, boolean, json, select
            $table->string('etiqueta');
            $table->text('descripcion')->nullable();
            $table->json('opciones')->nullable();    // Para tipo select: opciones disponibles
            $table->timestamps();

            $table->index('grupo');
        });

        // Insertar configuraciones por defecto
        DB::table('configuraciones')->insert([
            // General
            ['clave' => 'sistema_nombre', 'valor' => 'Sistema de Violencia Escolar', 'grupo' => 'general', 'tipo' => 'text', 'etiqueta' => 'Nombre del Sistema', 'descripcion' => 'Nombre que aparece en la interfaz del sistema', 'opciones' => null, 'created_at' => now(), 'updated_at' => now()],
            ['clave' => 'sistema_descripcion', 'valor' => 'Plataforma de gestión de casos de violencia escolar', 'grupo' => 'general', 'tipo' => 'text', 'etiqueta' => 'Descripción', 'descripcion' => 'Descripción corta del sistema', 'opciones' => null, 'created_at' => now(), 'updated_at' => now()],
            ['clave' => 'timezone', 'valor' => 'America/Lima', 'grupo' => 'general', 'tipo' => 'select', 'etiqueta' => 'Zona Horaria', 'descripcion' => 'Zona horaria del sistema', 'opciones' => json_encode(['America/Lima', 'America/Bogota', 'America/Mexico_City', 'America/Buenos_Aires', 'America/Santiago']), 'created_at' => now(), 'updated_at' => now()],
            ['clave' => 'registros_por_pagina', 'valor' => '25', 'grupo' => 'general', 'tipo' => 'number', 'etiqueta' => 'Registros por Página', 'descripcion' => 'Cantidad de registros mostrados por página en tablas', 'opciones' => null, 'created_at' => now(), 'updated_at' => now()],

            // Notificaciones
            ['clave' => 'notif_email_activo', 'valor' => '1', 'grupo' => 'notificaciones', 'tipo' => 'boolean', 'etiqueta' => 'Notificaciones por Email', 'descripcion' => 'Activar/desactivar envío de notificaciones por email', 'opciones' => null, 'created_at' => now(), 'updated_at' => now()],
            ['clave' => 'notif_nueva_denuncia', 'valor' => '1', 'grupo' => 'notificaciones', 'tipo' => 'boolean', 'etiqueta' => 'Notificar Nueva Denuncia', 'descripcion' => 'Notificar al admin cuando se registra una nueva denuncia', 'opciones' => null, 'created_at' => now(), 'updated_at' => now()],
            ['clave' => 'notif_asignacion', 'valor' => '1', 'grupo' => 'notificaciones', 'tipo' => 'boolean', 'etiqueta' => 'Notificar Asignaciones', 'descripcion' => 'Notificar al psicólogo y paciente sobre nuevas asignaciones', 'opciones' => null, 'created_at' => now(), 'updated_at' => now()],
            ['clave' => 'notif_cambio_estado', 'valor' => '1', 'grupo' => 'notificaciones', 'tipo' => 'boolean', 'etiqueta' => 'Notificar Cambio de Estado', 'descripcion' => 'Notificar cuando un caso cambia de estado', 'opciones' => null, 'created_at' => now(), 'updated_at' => now()],
            ['clave' => 'email_admin', 'valor' => 'admin@escuela.edu', 'grupo' => 'notificaciones', 'tipo' => 'text', 'etiqueta' => 'Email del Administrador', 'descripcion' => 'Email donde se reciben las notificaciones administrativas', 'opciones' => null, 'created_at' => now(), 'updated_at' => now()],

            // Seguridad
            ['clave' => 'password_min_length', 'valor' => '8', 'grupo' => 'seguridad', 'tipo' => 'number', 'etiqueta' => 'Longitud Mínima de Contraseña', 'descripcion' => 'Mínimo de caracteres requeridos para contraseñas', 'opciones' => null, 'created_at' => now(), 'updated_at' => now()],
            ['clave' => 'session_lifetime', 'valor' => '120', 'grupo' => 'seguridad', 'tipo' => 'number', 'etiqueta' => 'Duración de Sesión (min)', 'descripcion' => 'Minutos de inactividad antes de cerrar sesión', 'opciones' => null, 'created_at' => now(), 'updated_at' => now()],
            ['clave' => 'max_intentos_login', 'valor' => '5', 'grupo' => 'seguridad', 'tipo' => 'number', 'etiqueta' => 'Máx. Intentos de Login', 'descripcion' => 'Intentos de login permitidos antes de bloquear la cuenta', 'opciones' => null, 'created_at' => now(), 'updated_at' => now()],
            ['clave' => 'bloqueo_duracion', 'valor' => '15', 'grupo' => 'seguridad', 'tipo' => 'number', 'etiqueta' => 'Duración de Bloqueo (min)', 'descripcion' => 'Minutos de bloqueo tras exceder intentos de login', 'opciones' => null, 'created_at' => now(), 'updated_at' => now()],

            // Casos
            ['clave' => 'caso_auto_asignar', 'valor' => '0', 'grupo' => 'casos', 'tipo' => 'boolean', 'etiqueta' => 'Auto-asignar Casos', 'descripcion' => 'Asignar automáticamente casos nuevos al psicólogo con menor carga', 'opciones' => null, 'created_at' => now(), 'updated_at' => now()],
            ['clave' => 'caso_prioridad_default', 'valor' => 'media', 'grupo' => 'casos', 'tipo' => 'select', 'etiqueta' => 'Prioridad por Defecto', 'descripcion' => 'Prioridad asignada automáticamente a nuevos casos', 'opciones' => json_encode(['baja', 'media', 'alta', 'urgente']), 'created_at' => now(), 'updated_at' => now()],
            ['clave' => 'caso_sla_horas', 'valor' => '48', 'grupo' => 'casos', 'tipo' => 'number', 'etiqueta' => 'SLA de Atención (horas)', 'descripcion' => 'Horas máximas para primera atención de un caso', 'opciones' => null, 'created_at' => now(), 'updated_at' => now()],
            ['clave' => 'caso_dias_escalamiento', 'valor' => '3', 'grupo' => 'casos', 'tipo' => 'number', 'etiqueta' => 'Días para Escalamiento', 'descripcion' => 'Días sin atención antes de escalar un caso', 'opciones' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('configuraciones');
    }
};

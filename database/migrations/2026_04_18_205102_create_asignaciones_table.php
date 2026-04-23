<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asignaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('psicologo_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('paciente_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('caso_id')->nullable()->constrained('casos')->onDelete('set null');
            $table->text('notas')->nullable();
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->enum('frecuencia_atencion', ['semanal', 'quincenal', 'mensual'])->default('semanal');
            $table->enum('dia_atencion', ['lunes', 'martes', 'miercoles', 'jueves', 'viernes'])->nullable();
            $table->time('hora_atencion')->nullable();
            $table->enum('estado', ['activa', 'finalizada', 'cancelada'])->default('activa');
            $table->text('motivo_fin')->nullable();
            $table->boolean('solicitud_cambio')->default(false);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('actualizado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->unique(['paciente_id', 'estado'], 'asignacion_activa_unique');
            $table->index('psicologo_id');
            $table->index('paciente_id');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignaciones');
    }
};

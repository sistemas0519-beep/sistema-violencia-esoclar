<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('casos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_caso')->unique();
            $table->enum('tipo_violencia', [
                'fisica',
                'psicologica',
                'verbal',
                'sexual',
                'ciberacoso',
                'discriminacion',
                'otro',
            ]);
            $table->text('descripcion');
            $table->enum('estado', ['pendiente', 'en_proceso', 'resuelto', 'cerrado'])->default('pendiente');
            $table->boolean('es_anonimo')->default(false);
            $table->foreignId('denunciante_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('asignado_a')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('fecha_incidente')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('casos');
    }
};

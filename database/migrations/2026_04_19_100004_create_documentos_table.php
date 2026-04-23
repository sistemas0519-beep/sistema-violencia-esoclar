<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caso_id')->nullable()->constrained('casos')->nullOnDelete();
            $table->foreignId('asignacion_id')->nullable()->constrained('asignaciones')->nullOnDelete();
            $table->string('nombre');
            $table->string('nombre_original');
            $table->string('ruta');
            $table->string('tipo_archivo', 50);     // pdf, docx, jpg, png, etc.
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('tamaño');    // bytes
            $table->unsignedInteger('version')->default(1);
            $table->foreignId('documento_padre_id')->nullable()->constrained('documentos')->nullOnDelete();
            $table->enum('categoria', [
                'evidencia', 'informe', 'evaluacion_psicologica',
                'acta', 'consentimiento', 'derivacion', 'otro'
            ])->default('otro');
            $table->text('descripcion')->nullable();
            $table->enum('acceso', ['publico', 'privado', 'confidencial'])->default('privado');
            $table->foreignId('subido_por')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['caso_id', 'categoria']);
            $table->index('subido_por');
            $table->index('acceso');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};

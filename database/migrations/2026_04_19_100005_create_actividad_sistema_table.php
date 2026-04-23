<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actividad_sistema', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 50);             // login, logout, error, alerta, sistema
            $table->enum('nivel', ['info', 'warning', 'error', 'critical'])->default('info');
            $table->string('mensaje');
            $table->json('datos')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['tipo', 'created_at']);
            $table->index(['nivel', 'created_at']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actividad_sistema');
    }
};

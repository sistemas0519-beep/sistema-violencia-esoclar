<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('especialidad')->nullable()->after('rol');
            $table->enum('disponibilidad', ['disponible', 'ocupado', 'no_disponible'])->default('disponible')->after('especialidad');
            $table->string('foto_perfil')->nullable()->after('disponibilidad');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['especialidad', 'disponibilidad', 'foto_perfil']);
        });
    }
};

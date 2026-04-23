<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('casos', function (Blueprint $table) {
            $table->string('region')->nullable()->after('fecha_incidente');
            $table->string('provincia')->nullable()->after('region');
            $table->string('distrito')->nullable()->after('provincia');
            $table->string('escuela_nombre')->nullable()->after('distrito');
            $table->string('codigo_modular', 20)->nullable()->after('escuela_nombre');
        });
    }

    public function down(): void
    {
        Schema::table('casos', function (Blueprint $table) {
            $table->dropColumn([
                'region',
                'provincia',
                'distrito',
                'escuela_nombre',
                'codigo_modular',
            ]);
        });
    }
};

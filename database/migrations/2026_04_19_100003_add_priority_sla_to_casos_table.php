<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('casos', function (Blueprint $table) {
            $table->enum('prioridad', ['baja', 'media', 'alta', 'urgente'])->default('media')->after('estado');
            $table->timestamp('sla_limite')->nullable()->after('prioridad');
            $table->timestamp('fecha_primera_atencion')->nullable()->after('sla_limite');
            $table->boolean('sla_vencido')->default(false)->after('fecha_primera_atencion');
            $table->string('categoria', 100)->nullable()->after('sla_vencido');
            $table->json('etiquetas')->nullable()->after('categoria');
            $table->text('notas_internas')->nullable()->after('etiquetas');
            $table->boolean('escalado')->default(false)->after('notas_internas');
            $table->timestamp('fecha_escalamiento')->nullable()->after('escalado');

            $table->index('prioridad');
            $table->index('sla_vencido');
            $table->index('escalado');
        });
    }

    public function down(): void
    {
        Schema::table('casos', function (Blueprint $table) {
            $table->dropIndex(['prioridad']);
            $table->dropIndex(['sla_vencido']);
            $table->dropIndex(['escalado']);
            $table->dropColumn([
                'prioridad', 'sla_limite', 'fecha_primera_atencion',
                'sla_vencido', 'categoria', 'etiquetas', 'notas_internas',
                'escalado', 'fecha_escalamiento',
            ]);
        });
    }
};

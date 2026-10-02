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
        Schema::table('suscriptions', function (Blueprint $table) {
            $table->foreignId('cliente_id')
                ->constrained('clients')
                ->cascadeOnDelete();

            $table->foreignId('plan_id')
                ->constrained('plans')
                ->cascadeOnDelete();

            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->string('estado')->default('activa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suscriptions', function (Blueprint $table) {
            $table->dropForeign(['cliente_id']);
            $table->dropForeign(['plan_id']);

            $table->dropColumn([
                'cliente_id',
                'plan_id',
                'fecha_inicio',
                'fecha_fin',
                'estado',
            ]);
        });
    }
};


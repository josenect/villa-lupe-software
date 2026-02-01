<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('facturas', function (Blueprint $table) {
            // Estado: activa, anulada, reabierta
            $table->string('estado')->default('activa')->after('medio_pago');
            // Motivo de anulación (opcional)
            $table->text('motivo_anulacion')->nullable()->after('estado');
            // Fecha de anulación
            $table->dateTime('fecha_anulacion')->nullable()->after('motivo_anulacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropColumn(['estado', 'motivo_anulacion', 'fecha_anulacion']);
        });
    }
};

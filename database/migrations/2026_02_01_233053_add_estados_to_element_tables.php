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
        Schema::table('element_tables', function (Blueprint $table) {
            $table->string('estado')->default('pendiente')->after('status'); // pendiente, en_cocina, listo, cancelacion_solicitada, cancelado
            $table->text('observacion')->nullable()->after('estado');
            $table->text('motivo_cancelacion')->nullable()->after('observacion');
            $table->unsignedBigInteger('solicitado_por')->nullable()->after('motivo_cancelacion');
            $table->timestamp('fecha_solicitud_cancelacion')->nullable()->after('solicitado_por');
            $table->unsignedBigInteger('aprobado_por')->nullable()->after('fecha_solicitud_cancelacion');
            $table->timestamp('fecha_cancelacion')->nullable()->after('aprobado_por');
            $table->unsignedBigInteger('user_id')->nullable()->after('fecha_cancelacion'); // quien agregó el producto
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('element_tables', function (Blueprint $table) {
            $table->dropColumn([
                'estado', 'observacion', 'motivo_cancelacion', 
                'solicitado_por', 'fecha_solicitud_cancelacion', 
                'aprobado_por', 'fecha_cancelacion', 'user_id'
            ]);
        });
    }
};

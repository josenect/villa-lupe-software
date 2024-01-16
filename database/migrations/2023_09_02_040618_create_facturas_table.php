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
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('table_id');
            $table->string('numero_factura')->unique();
            $table->decimal('valor_total', 10, 2);
            $table->decimal('valor_propina', 10, 2);
            $table->decimal('valor_pagado', 10, 2);
            $table->dateTime('fecha_hora_factura');
            $table->string('medio_pago');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('facturas');
    }
};

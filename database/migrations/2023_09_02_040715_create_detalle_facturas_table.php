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
        Schema::create('detalle_facturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('table_id');
            $table->foreignId('factura_id')->constrained('facturas');
            $table->foreignId('producto_id');
            $table->integer('price');
            $table->integer('amount');
            $table->integer('discount');
            $table->dateTime('record');
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
        Schema::dropIfExists('detalle_facturas');
    }
};

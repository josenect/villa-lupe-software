<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('domicilios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('table_id')->nullable();
            $table->string('cliente_nombre', 100);
            $table->string('cliente_telefono', 20);
            $table->string('cliente_direccion', 255);
            $table->enum('estado', ['activo', 'facturado', 'cancelado'])->default('activo');
            $table->timestamps();

            $table->foreign('table_id')->references('id')->on('tables')->nullOnDelete();
        });

        // Quitar campos de cliente de tables (ya no se necesitan ahi)
        Schema::table('tables', function (Blueprint $table) {
            $table->dropColumn(['cliente_nombre', 'cliente_telefono', 'cliente_direccion']);
        });
    }

    public function down()
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->string('cliente_nombre', 100)->nullable()->after('is_domicilio');
            $table->string('cliente_telefono', 20)->nullable()->after('cliente_nombre');
            $table->string('cliente_direccion', 255)->nullable()->after('cliente_telefono');
        });

        Schema::dropIfExists('domicilios');
    }
};

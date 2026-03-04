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
        Schema::table('tables', function (Blueprint $table) {
            $table->boolean('is_domicilio')->default(false)->after('status');
            $table->string('cliente_nombre', 100)->nullable()->after('is_domicilio');
            $table->string('cliente_telefono', 20)->nullable()->after('cliente_nombre');
            $table->string('cliente_direccion', 255)->nullable()->after('cliente_telefono');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropColumn(['is_domicilio', 'cliente_nombre', 'cliente_telefono', 'cliente_direccion']);
        });
    }
};

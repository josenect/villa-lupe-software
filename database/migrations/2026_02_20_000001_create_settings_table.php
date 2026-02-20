<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Valores por defecto
        $defaults = [
            'menu_cocina_visible'      => '1',
            'menu_mis_pedidos_visible' => '1',
            'restaurante_nombre'       => 'Villa Lupe',
            'restaurante_propiedad'    => 'Casa de Campo',
            'restaurante_direccion'    => '',
            'restaurante_logo'         => '',
        ];

        foreach ($defaults as $key => $value) {
            DB::table('settings')->insert([
                'key'        => $key,
                'value'      => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
};

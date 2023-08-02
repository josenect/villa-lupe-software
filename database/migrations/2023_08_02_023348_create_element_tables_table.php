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
        Schema::create('element_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('table_id');
            $table->foreignId('product_id');
            $table->integer('price');
            $table->integer('amount');
            $table->integer('dicount');
            $table->dateTime('record');
            $table->boolean('status');
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
        Schema::dropIfExists('element_tables');
    }
};

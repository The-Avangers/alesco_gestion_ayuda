<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAidsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aid', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('measure');
            $table->enum('type', ['Medicina', 'Alimento', 'Limpieza']);
            $table->bigInteger('unit');
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
        Schema::dropIfExists('aid');
    }
}

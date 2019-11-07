<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableProjectProgress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_progress', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('projectId')->unsigned();
            $table->foreign('projectId')->references('id')->on('project')->onDelete('cascade');
            $table->string('milestone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_progress');
    }
}

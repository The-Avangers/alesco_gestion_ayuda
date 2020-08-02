<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTasksPerson extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_person', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('taskId')->unsigned();
            $table->foreign('taskId')->references('id')->on('task')->onDelete('cascade');
            $table->integer('personId')->unsigned();
            $table->foreign('personId')->references('id')->on('person')->onDelete('cascade');
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
        Schema::dropIfExists('table_tasks_person');
    }
}

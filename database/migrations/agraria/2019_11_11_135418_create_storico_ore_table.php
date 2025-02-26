<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoricoOreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storico_ore', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('data');
            $table->integer('ore');
            $table->bigInteger('mezzo_agricolo')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('storico_ore');
    }
}

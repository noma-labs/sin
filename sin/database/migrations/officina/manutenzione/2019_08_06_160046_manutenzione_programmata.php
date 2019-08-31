<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ManutenzioneProgrammata extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('db_officina')->create('manutenzione_programmata', function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->increments('id');
            $table->integer('manutenzione_id')->unsigned();
            $table->integer('veicolo_id');
            $table->integer('meccanico_id');
            $table->integer('kilometri');
            $table->date('data');

            //foreign keys
            $table->foreign('manutenzione_id')->references('id')->on('tipo_manutenzione');
            $table->foreign('veicolo_id')->references('id')->on('veicolo');
            $table->foreign('meccanico_id')->references('id')->on('db_nomadelfia.persone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('db_officina')->dropIfExists('manutenzione_programmata');
    }
}

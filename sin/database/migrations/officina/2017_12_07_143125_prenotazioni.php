<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Prenotazioni extends Migration
{

   // protected string	$connection = "db_officina";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::connection('db_officina')->create('prenotazioni', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('cliente_id');
          $table->integer('veicolo_id');
          $table->date('data_partenza');
          $table->string('ora_partenza', 20);
          $table->date('data_arrivo');
          $table->string('ora_arrivo', 20);
          $table->integer('meccanico_id');
          $table->integer('uso_id');
          $table->string('note', 100)->default('')->nullable();
          $table->timestamps();
          $table->softDeletes();
          
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('prenotazioni');
    }
}

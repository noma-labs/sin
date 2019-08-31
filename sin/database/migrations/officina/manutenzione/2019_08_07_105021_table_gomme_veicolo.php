<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableGommeVeicolo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('db_officina')->create('gomme_veicolo', function (Blueprint $table) {
            $table->integer('gomme_id')->unsigned();
            $table->integer('veicolo_id');

            $table->unique(['gomme_id', 'veicolo_id']);
            // foreign key
            $table->foreign('gomme_id')->references('id')->on('tipo_gomme');
            $table->foreign('veicolo_id')->references('id')->on('veicolo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('db_officina')->dropIfExists('gomme_veicolo');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableVeicoloForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('db_officina')->table('veicolo', function (Blueprint $table) {
            $table->foreign('filtro_olio')->references('id')->on('tipo_filtro');
            $table->foreign('filtro_gasolio')->references('id')->on('tipo_filtro');
            $table->foreign('filtro_aria')->references('id')->on('tipo_filtro');
            $table->foreign('filtro_aria_condizionata')->references('id')->on('tipo_filtro');
            $table->foreign('olio_id')->references('id')->on('tipo_olio');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('db_officina')->table('veicolo', function (Blueprint $table) {
            $table->dropForeign(['filtro_olio']);
            $table->dropForeign(['filtro_gasolio']);
            $table->dropForeign(['filtro_aria']);
            $table->dropForeign(['filtro_aria_condizionata']);
            $table->dropForeign(['olio_id']);
        });
    }
}

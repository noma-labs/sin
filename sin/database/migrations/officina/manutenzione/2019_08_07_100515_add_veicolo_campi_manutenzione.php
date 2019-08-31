<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVeicoloCampiManutenzione extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('db_officina')->table('veicolo', function (Blueprint $table) {
            $table->integer('filtro_olio')->unsigned()->nullable();
            $table->integer('filtro_gasolio')->unsigned()->nullable();
            $table->integer('filtro_aria')->unsigned()->nullable();
            $table->integer('filtro_aria_condizionata')->unsigned()->nullable();
            $table->integer('olio_id')->unsigned()->nullable();
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
            $table->dropColumn(['filtro_olio', 'filtro_gasolio', 'filtro_aria', 'filtro_aria_condizionata', 'olio_id']);
        });
    }
}

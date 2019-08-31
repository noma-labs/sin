<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLitriOlioVeicolo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('db_officina')->table('veicolo', function (Blueprint $table) {
            $table->integer('litri_olio')->nullable();
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
            $table->dropColumn('litri_olio');
        });
    }
}

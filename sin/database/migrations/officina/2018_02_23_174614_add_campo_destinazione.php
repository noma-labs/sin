<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCampoDestinazione extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('db_officina')->table('prenotazioni', function (Blueprint $table) {
            $table->string('destinazione')->default('Grosseto');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prenotazioni', function (Blueprint $table) {
            $table->dropColumn('destinazione');
        });
    }
}

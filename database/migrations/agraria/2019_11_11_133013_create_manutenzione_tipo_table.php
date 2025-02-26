<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManutenzioneTipoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manutenzione_tipo', function (Blueprint $table) {
            $table->bigInteger('manutenzione')->unsigned();
            $table->bigInteger('tipo')->unsigned();

            $table->primary(['manutenzione', 'tipo']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manutenzione_tipo');
    }
}

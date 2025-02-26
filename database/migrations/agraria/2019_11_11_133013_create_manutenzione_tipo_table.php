<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateManutenzioneTipoTable extends Migration
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

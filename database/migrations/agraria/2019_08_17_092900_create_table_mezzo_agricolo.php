<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateTableMezzoAgricolo extends Migration
{
    public $connection = 'db_agraria';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mezzo_agricolo', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nome');
            $table->integer('tot_ore');
            $table->string('numero_telaio')->unique();
            $table->string('filtro_olio')->nullable();
            $table->string('filtro_gasolio')->nullable();
            $table->string('filtro_servizi')->nullable();
            $table->string('filtro_aria_int')->nullable();
            $table->string('filtro_aria_ext')->nullable();
            $table->bigInteger('gomme_ant')->unsigned()->nullable();
            $table->bigInteger('gomme_post')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mezzo_agricolo');
    }
}

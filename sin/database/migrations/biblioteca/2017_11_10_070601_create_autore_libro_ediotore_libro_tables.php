<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAutoreLibroEdiotoreLibroTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('autore', function (Blueprint $table) {
          $table->integer('ID_AUTORE')->unsigned()->default(null)->change();
        });
        Schema::table('editore', function (Blueprint $table) {
          $table->integer('id_editore')->unsigned()->default(null)->change();
        });
        Schema::create('autore_libro', function (Blueprint $table) {
            $table->integer('autore_id')->unsigned();
            $table->integer('libro_id')->unsigned();
            $table->primary(['autore_id', 'libro_id']);

            // $table->foreign('autore_id')->references('ID_AUTORE')->on('autore');
            // $table->foreign('libro_id')->references('ID_LIBRO')->on('libro');

        });
        //
        Schema::create('editore_libro', function (Blueprint $table) {
            $table->integer('editore_id')->unsigned();
            $table->integer('libro_id')->unsigned();

            $table->primary(['editore_id', 'libro_id']);
            // $table->foreign('editore_id')->references('id_editore')->on('editore');
            // $table->foreign('libro_id')->references('ID_LIBRO')->on('libro');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('autore_libro');
        Schema::dropIfExists('editore_libro');
    }
}

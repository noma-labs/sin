<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ForeignKeyDbBiblioteca extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//
//       When creating a foreign key constraint in MySQL, use the template
// {referring_table}_{attribute}_foreign_idx, rather than just
// {attribute}_foreign_idx, to avoid duplicate constraint names.
        // Schema::table('libro', function (Blueprint $table) {
        //    $table->engine = 'InnoDB';
        //   $table->foreign('ID_AUTORE')->references('ID_AUTORE')->on('autore');
        // });

        // Schema::table('libro', function (Blueprint $table) {
        //    $table->engine = 'InnoDB';
        //   $table->foreign('ID_EDITORE')->references('id_editore')->on('editore');
        // });

        // Schema::table('libro', function (Blueprint $table) {
        //    $table->engine = 'InnoDB';
        //   $table->foreign('CLASSIFICAZIONE')->references('ID_CLASSE')->on('classificazione');
        // });
        //
        // Schema::table('prestito', function (Blueprint $table) {
        //    $table->engine = 'InnoDB';
        //     $table->foreign('LIBRO')->references('ID_LIBRO')->on('libro');
        // });
        //
        // Schema::table('prestito', function (Blueprint $table) {
        //    $table->engine = 'InnoDB';
        //     $table->foreign('CLIENTE')->references('ID_CLIENTE')->on('cliente');
        // });
        //
        // Schema::table('prestito', function (Blueprint $table) {
        //    $table->engine = 'InnoDB';
        //     $db = DB::connection('db_auth')->getDatabaseName();
        //     // $table->integer('BIBLIOTECARIO')->unsigned();
        //     $table->foreign('BIBLIOTECARIO')->references('id')->on(new Expression($db . '.users'));
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      $table->dropForeign('libro_id_autore_foreign');
      // $table->dropForeign('editore_ID_AUTORE_foreign');
      // $table->dropForeign('classificazione_ID_AUTORE_foreign');

        // Schema::table('libro', function (Blueprint $table) {
        //     //
        // });
    }
}

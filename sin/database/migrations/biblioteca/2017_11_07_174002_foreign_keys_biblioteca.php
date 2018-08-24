<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ForeignKeysBiblioteca extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

      ## Add foreignkeys from libro->autore, libro->editore, libro->classificazione      
      Schema::table('editore', function (Blueprint $table) {
        $table->integer('id_editore')->unsigned()->default(null)->change();
      });

      Schema::table('autore', function (Blueprint $table) {
        $table->integer('ID_AUTORE')->unsigned()->default(null)->change();
      });

      Schema::table('classificazione', function (Blueprint $table) {
        $table->integer('ID_CLASSE')->unsigned()->default(null)->change();
      });

      Schema::table('libro', function (Blueprint $table) {
        $table->integer('ID_AUTORE')->unsigned()->change();
        $table->foreign('ID_AUTORE')->references('ID_AUTORE')->on('autore')
                ->onDelete('restrict')
                ->onUpdate('cascade');
      });

      Schema::disableForeignKeyConstraints();
      Schema::table('libro', function (Blueprint $table) {
            $table->integer('ID_EDITORE')->unsigned()->change();
            $table->foreign('ID_EDITORE')->references('id_editore')->on('editore')
                ->onDelete('restrict')
                ->onUpdate('cascade');

      });
      Schema::enableForeignKeyConstraints();

      Schema::table('libro', function (Blueprint $table) {
            $table->integer('CLASSIFICAZIONE')->unsigned()->change();
            $table->foreign('CLASSIFICAZIONE')->references('ID_CLASSE')->on('classificazione')
                ->onDelete('set null')
                ->onUpdate('cascade');
      });

      ## Add foreignkeys from presito-> cliente, prestito-> libro
      Schema::table('cliente', function (Blueprint $table) {
        $table->integer('ID_CLIENTE')->unsigned()->default(null)->change();
      });
      Schema::table('libro', function (Blueprint $table) {
        $table->integer('ID_LIBRO')->unsigned()->default(null)->change();
      });

      // Con errore : data 0000-00-00 00-00-00
      //  UPDATE  `cliente` SET `created_at` = NULL WHERE `created_at` = '0000-00-00 00:00:00'
      //  UPDATE  `cliente` SET `updated_at` = NULL WHERE `updated_at` = '0000-00-00 00:00:00'
      Schema::table('prestito', function (Blueprint $table) {
            $table->integer('CLIENTE')->unsigned()->change();
            $table->foreign('CLIENTE')->references('ID_CLIENTE')->on('cliente')
                ->onDelete('restrict')
                ->onUpdate('cascade');
      });

      Schema::table('prestito', function (Blueprint $table) {
            $table->integer('LIBRO')->unsigned()->change();
            $table->foreign('LIBRO')->references('ID_LIBRO')->on('libro')
                ->onDelete('restrict')
                ->onUpdate('cascade');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('libro', function (Blueprint $table) {
          $table->dropForeign('libro_id_autore_foreign');
          $table->dropForeign('libro_id_editore_foreign');
          $table->dropForeign('libro_id_collocazione_foreign');
      });

      Schema::table('prestito', function (Blueprint $table) {
          $table->dropForeign('prestito_id_autore_foreign');
          $table->dropForeign('prestito_id_editore_foreign');
      });

    }
}

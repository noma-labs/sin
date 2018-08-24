<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameTableLibro extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      //enum problem
      Schema::getConnection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

      Schema::table('libro', function (Blueprint $table) {
          $table->renameColumn('ID_LIBRO', 'id');
          $table->renameColumn('TITOLO', 'titolo');
          $table->renameColumn('AUTORE', 'autore');
          $table->renameColumn('EDITORE', 'editore');
          $table->renameColumn('COLLOCAZIONE', 'collocazione');
          $table->renameColumn('CLASSIFICAZIONE', 'classificazione_id');
          $table->renameColumn('NOTE', 'note');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

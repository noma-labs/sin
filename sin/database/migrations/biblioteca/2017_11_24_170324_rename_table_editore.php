<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameTableEditore extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::getConnection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
      
      Schema::table('editore', function (Blueprint $table) {
          $table->renameColumn('id_editore', 'id');
          $table->renameColumn('Editore', 'editore');
      });
      // X è l'is massimo +1 della tabella autore.
      //  ALTER TABLE editore AUTO_INCREMENT=X, CHANGE id id INT(10) NOT NULL AUTO_INCREMENT

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

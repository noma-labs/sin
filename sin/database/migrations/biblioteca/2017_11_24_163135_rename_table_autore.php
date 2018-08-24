<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameTableAutore extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('autore', function (Blueprint $table) {
            $table->renameColumn('ID_AUTORE', 'id');
            $table->renameColumn('Autore', 'autore');
        });
        // X è l'is massimo +1 della tabella autore.
       //  ALTER TABLE autore AUTO_INCREMENT=X, CHANGE id id INT(10) NOT NULL AUTO_INCREMENT
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

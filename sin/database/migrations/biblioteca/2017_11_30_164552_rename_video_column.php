<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameVideoColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('video', function (Blueprint $table) {
          $table->renameColumn('ID_VIDEO', 'id');
          $table->renameColumn('CASS', 'cassetta');
          $table->renameColumn('INI', 'inizio');
          $table->renameColumn('FIN', 'fine');
          $table->renameColumn('DATAREG', 'data_registrazione');
          $table->renameColumn('DESCRIZ', 'descrizione');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('video', function (Blueprint $table) {
            //
        });
    }
}

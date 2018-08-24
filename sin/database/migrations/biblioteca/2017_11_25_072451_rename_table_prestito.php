<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameTablePrestito extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('prestito', function (Blueprint $table) {
          $table->renameColumn('ID_PRESTITO', 'id');
          $table->renameColumn('DATA_PRENOTAZIONE', 'data_inizio_prestito');
          $table->renameColumn('DATA_RESTITUZIONE', 'data_fine_prestito');
          $table->renameColumn('LIBRO', 'libro_id');
          $table->renameColumn('IN_PRESTITO', 'in_prestito');
          $table->renameColumn('BIBLIOTECARIO', 'bibliotecario_id');




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

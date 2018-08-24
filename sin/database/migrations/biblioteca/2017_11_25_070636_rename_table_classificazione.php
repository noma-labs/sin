<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameTableClassificazione extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('classificazione', function (Blueprint $table) {
            $table->renameColumn('ID_CLASSE', 'id');
            $table->renameColumn('DESCRIZIONE', 'descrizione');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('classificazione', function (Blueprint $table) {
            //
        });
    }
}

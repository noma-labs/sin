<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Default0ToAutoreEditore extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('editore', function (Blueprint $table) {
          $table->integer('id_editore')->default(0)->change();
      });

      Schema::table('autore', function (Blueprint $table) {
          $table->integer('ID_AUTORE')->default(0)->change();
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

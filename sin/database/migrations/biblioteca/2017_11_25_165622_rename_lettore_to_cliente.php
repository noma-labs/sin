<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameLettoreToCliente extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('prestito', function (Blueprint $table) {
          $table->renameColumn('lettore_id', 'cliente_id');
          $table->renameColumn('lettore_type', 'cliente_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prestito', function (Blueprint $table) {
            //
        });
    }
}
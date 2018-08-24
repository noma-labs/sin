<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AuthAddTableSistemi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::connection('db_auth')->create('sistemi', function (Blueprint $table) {
        $table->increments('id');
        $table->char('nome', 20);
        $table->char('descrizione', 100);
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

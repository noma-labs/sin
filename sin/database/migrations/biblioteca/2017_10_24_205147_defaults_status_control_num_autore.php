<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DefaultsStatusControlNumAutore extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('autore', function (Blueprint $table) {
              $table->string('status')->nullable()->change();
              $table->integer('nuonum')->nullable()->change();
              $table->integer('control')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('autore', function (Blueprint $table) {
            //
        });
    }
}

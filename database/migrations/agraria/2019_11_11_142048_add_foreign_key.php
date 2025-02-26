<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mezzo_agricolo', function (Blueprint $table) {
            $table->foreign('gomme_ant')->references('id')->on('gomma');
            $table->foreign('gomme_post')->references('id')->on('gomma');
        });

        Schema::table('manutenzione', function (Blueprint $table) {
            $table->foreign('mezzo_agricolo')->references('id')->on('mezzo_agricolo');
        });

        Schema::table('manutenzione_tipo', function (Blueprint $table) {
            $table->foreign('manutenzione')->references('id')->on('manutenzione');
            $table->foreign('tipo')->references('id')->on('manutenzione_programmata');
        });

        Schema::table('storico_ore', function (Blueprint $table) {
            $table->foreign('mezzo_agricolo')->references('id')->on('mezzo_agricolo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mezzo_agricolo', function (Blueprint $table) {
            $table->dropForeign(['gomme_ant']);
            $table->dropForeign(['gomme_post']);
        });

        Schema::table('manutenzione', function (Blueprint $table) {
            $table->dropForeign(['mezzo_agricolo']);
        });

        Schema::table('manutenzione_tipo', function (Blueprint $table) {
            $table->dropForeign(['manutenzione']);
            $table->dropForeign(['tipo']);
        });

        Schema::table('storico_ore', function (Blueprint $table) {
            $table->dropForeign(['mezzo_agricolo']);
        });
    }
}

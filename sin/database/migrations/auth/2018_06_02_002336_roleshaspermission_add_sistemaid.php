<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RoleshaspermissionAddSistemaid extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

      Schema::disableForeignKeyConstraints();
      Schema::connection('db_auth')->table('role_has_permissions', function (Blueprint $table) {
        $table->unsignedInteger('sistema_id');
        $table->foreign('sistema_id')->references('id')->on('sistemi');
      });
      Schema::enableForeignKeyConstraints();

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

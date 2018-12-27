<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AuthRinominaTabelle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('db_auth')->rename('role_has_permissions','ruoli_risorse_permessi');
        Schema::connection('db_auth')->rename('roles','ruoli');
        Schema::connection('db_auth')->rename('roles','ruoli');
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

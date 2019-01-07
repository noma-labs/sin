<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DbnomadelfiaTimestamppa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nominativi_storici', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('persone', function (Blueprint $table) {
            $table->timestamps();
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

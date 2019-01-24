<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTablePersoneCategorie extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('persone_categorie', function (Blueprint $table) {
            $table->integer('persona_id');
            $table->integer('categoria_id');
            $table->date('data_inizio');
            $table->date('data_fine')->nullable();
            $table->enum('stato', ['1', '0']);
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
        Schema::dropIfExists('persone_categorie');
    }
}

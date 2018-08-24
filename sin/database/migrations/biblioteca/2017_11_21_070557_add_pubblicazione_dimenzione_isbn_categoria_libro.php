<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPubblicazioneDimenzioneIsbnCategoriaLibro extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('libro', function (Blueprint $table) {
          $table->integer('isbn')->nullable()->comment('Sequenza numerica di 13 cifre usata internazionalmente per la classificazione dei libri ');
          $table->integer('critica')->nullable()->comment("Votazione del libro da 1 a 10");
          $table->enum('categoria', ['piccoli', 'elementari','medie','superiori','adulti'])->nullable();
          $table->text('dimensione')->nullable()->comment("Altezza per larghezza (e.g. 30cmx20cm)");
          $table->text('data_pubblicazione')->nullable()->comment('Mese e anno di pubblicazione del libro (e.g. Aprile 2017).');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('libro', function (Blueprint $table) {
            $table->dropColumn(['isbn', 'critica', 'categoria','dimensione','data_pubblicazione']);
        });
    }
}

<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateManutenzioneTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manutenzione', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('data');
            $table->integer('ore');
            $table->double('spesa', 10, 2);
            $table->string('persona', 100);
            $table->text('lavori_extra')->nullable();
            $table->bigInteger('mezzo_agricolo')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manutenzione');
    }
}

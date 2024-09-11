<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('db_foto')->create('foto_enrico', function (Blueprint $table) {
            $table->integer('id');
            $table->date('data');
            $table->string('datnum', 10);
            $table->string('localita', 50);
            $table->string('argomento', 100);
            $table->string('descrizione', 200);
            $table->integer('anum');
            $table->string('cddvd', 10);
            $table->string('hdint', 10);
            $table->string('hdext', 10);
            $table->string('sc', 2);
            $table->string('fi', 2);
            $table->string('tp', 2);
            $table->string('nfo', 6);
        });

        Schema::connection('db_foto')->create('foto_persone', function (Blueprint $table) {
            $table->string('photo_id');
            $table->bigInteger('persona_id')->nullable();
            $table->string('persona_nome')->nullable();
            $table->dateTime('created_at')->nullable()->useCurrent();
            $table->dateTime('updated_at')->nullable()->useCurrent();

            $table->unique(['photo_id', 'persona_id'], 'photo_id');
        });

        Schema::connection('db_foto')->create('photos', function (Blueprint $table) {
            $table->string('uid');
            $table->string('sha');
            $table->string('source_file');
            $table->string('directory')->nullable();
            $table->string('folder_title')->nullable()->comment('Parent folder name of the photo');
            $table->bigInteger('file_size')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_type', 16)->nullable();
            $table->string('file_type_extension', 16)->nullable();
            $table->integer('image_height')->nullable();
            $table->integer('image_width')->nullable();
            $table->text('keywords')->nullable();
            $table->json('region_info')->nullable();
            $table->text('subject')->nullable();
            $table->dateTime('taken_at')->nullable()->comment('the create date of the photo set by the camera');
            $table->dateTime('created_at', 6)->default('current_timestamp(6)');
            $table->dateTime('updated_at', 6)->default('current_timestamp(6)');
            $table->dateTime('deleted_at', 6)->nullable();
        });

        Schema::connection('db_foto')->create('tipo', function (Blueprint $table) {
            $table->integer('NR');
            $table->string('FI', 5)->comment('File di partenza. E.g., FO=FotoDigitale');
            $table->string('TP', 5)->comment('Tipo di pellicola');
            $table->text('DESCRIZ');
        });

        DB::connection('db_foto')->statement("CREATE VIEW `v_folders` AS select `db_foto`.`photos`.`folder_title` AS `folders`,count(0) AS `c` from `db_foto`.`photos` group by `db_foto`.`photos`.`folder_title`");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection('db_foto')->statement("DROP VIEW IF EXISTS `v_folders`");

        Schema::connection('db_foto')->dropIfExists('tipo');

        Schema::connection('db_foto')->dropIfExists('photos');

        Schema::connection('db_foto')->dropIfExists('foto_persone');

        Schema::connection('db_foto')->dropIfExists('foto_enrico');
    }
};

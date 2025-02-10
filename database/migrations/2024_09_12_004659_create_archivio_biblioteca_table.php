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
        Schema::connection('db_biblioteca')->create('autore', function (Blueprint $table) {
            $table->increments('id')->comment('Id Unico Autore, esiste un reference con la tabella Autore');
            $table->string('autore', 120);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent();
            $table->enum('tipaut', ['S', 'V', 'D']);
        });

        Schema::connection('db_biblioteca')->create('autore_libro', function (Blueprint $table) {
            $table->unsignedInteger('autore_id');
            $table->integer('libro_id');

            $table->primary(['autore_id', 'libro_id']);
        });

        Schema::connection('db_biblioteca')->create('classificazione', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('descrizione');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent();
        });

        Schema::connection('db_biblioteca')->create('editore', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('editore', 120);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent();
            $table->enum('tipedi', ['S', 'V', 'D']);
        });

        Schema::connection('db_biblioteca')->create('editore_libro', function (Blueprint $table) {
            $table->integer('editore_id');
            $table->integer('libro_id');

            $table->primary(['libro_id', 'editore_id']);
        });

        Schema::connection('db_biblioteca')->create('libro', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('titolo')->nullable();
            $table->integer('ID_AUTORE')->nullable();
            $table->string('autore')->nullable();
            $table->integer('ID_EDITORE')->nullable();
            $table->string('editore')->nullable();
            $table->string('collocazione')->nullable();
            $table->integer('classificazione_id')->nullable();
            $table->string('note')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent();
            $table->boolean('tobe_printed')->nullable();
            $table->softDeletes();
            $table->text('deleted_note')->nullable();
            $table->char('isbn', 13)->nullable();
            $table->integer('critica')->nullable()->comment('Votazione del libro da 1 a 10');
            $table->enum('categoria', ['piccoli', 'elementari', 'medie', 'superiori', 'adulti'])->nullable();
            $table->text('dimensione')->nullable()->comment('Altezza per larghezza (e.g. 30cmx20cm)');
            $table->text('data_pubblicazione')->nullable()->comment('Mese e anno di pubblicazione del libro (e.g. Aprile 2017).');
        });

        Schema::connection('db_biblioteca')->create('media', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->char('uuid', 36)->nullable()->unique();
            $table->string('collection_name');
            $table->string('name');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->string('disk');
            $table->string('conversions_disk')->nullable();
            $table->unsignedBigInteger('size');
            $table->longText('manipulations');
            $table->longText('custom_properties');
            $table->longText('generated_conversions');
            $table->longText('responsive_images');
            $table->unsignedInteger('order_column')->nullable()->index();
            $table->timestamps();

            $table->index(['model_type', 'model_id']);
        });

        Schema::connection('db_biblioteca')->create('prestito', function (Blueprint $table) {
            $table->integer('id', true);
            $table->date('data_inizio_prestito')->nullable();
            $table->date('data_fine_prestito')->nullable();
            $table->integer('libro_id')->nullable();
            $table->boolean('in_prestito')->nullable();
            $table->dateTime('updated_at')->nullable()->useCurrent();
            $table->dateTime('created_at')->nullable()->useCurrent();
            $table->integer('bibliotecario_id');
            $table->integer('cliente_id')->nullable();
            $table->string('cliente_type', 191)->nullable();
            $table->string('note', 100)->nullable();
        });

        Schema::connection('db_biblioteca')->create('video', function (Blueprint $table) {
            $table->integer('id');
            $table->string('cassetta', 6)->nullable();
            $table->string('R', 1)->nullable();
            $table->double('inizio')->nullable();
            $table->double('fine')->nullable();
            $table->dateTime('data_registrazione')->nullable();
            $table->string('V', 1)->nullable();
            $table->string('Q', 1)->nullable();
            $table->string('K', 1)->nullable();
            $table->dateTime('UTRASM')->nullable();
            $table->string('S', 1)->nullable();
            $table->string('X', 1)->nullable();
            $table->double('MIC')->nullable();
            $table->string('CAT', 4)->nullable();
            $table->double('MIN')->nullable();
            $table->string('N', 1)->nullable();
            $table->string('descrizione', 80)->nullable();
        });

        DB::connection('db_biblioteca')->statement("CREATE VIEW `v_lavoratori_biblioteca` AS select `db_nomadelfia`.`aziende_persone`.`azienda_id` AS `azienda_id`,`db_nomadelfia`.`aziende_persone`.`persona_id` AS `persona_id`,`db_nomadelfia`.`persone`.`nominativo` AS `nominativo`,`db_nomadelfia`.`aziende_persone`.`stato` AS `stato`,`db_nomadelfia`.`aziende_persone`.`mansione` AS `mansione`,`db_nomadelfia`.`aziende_persone`.`data_inizio_azienda` AS `data_inizio_azienda`,`db_nomadelfia`.`aziende_persone`.`data_fine_azienda` AS `data_fine_azienda` from (`db_nomadelfia`.`aziende_persone` join `db_nomadelfia`.`persone`) where `db_nomadelfia`.`aziende_persone`.`azienda_id` = 30 and `db_nomadelfia`.`persone`.`id` = `db_nomadelfia`.`aziende_persone`.`persona_id` order by `db_nomadelfia`.`aziende_persone`.`mansione`");

        DB::connection('db_biblioteca')->statement("CREATE VIEW `v_colloc_split` AS select `archivio_biblioteca`.`libro`.`collocazione` AS `COLLOCAZIONE`,substr(`archivio_biblioteca`.`libro`.`collocazione`,1,3) AS `lettere`,cast(substr(`archivio_biblioteca`.`libro`.`collocazione`,4,3) as unsigned) AS `numeri` from `archivio_biblioteca`.`libro`");

        DB::connection('db_biblioteca')->statement("CREATE VIEW `v_clienti_biblioteca` AS select `db_nomadelfia`.`persone`.`id` AS `id`,`db_nomadelfia`.`persone`.`nominativo` AS `nominativo`,`db_nomadelfia`.`persone`.`data_nascita` AS `data_nascita`,substr(sysdate(),1,4) - substr(`db_nomadelfia`.`persone`.`data_nascita`,1,4) AS `eta` from `db_nomadelfia`.`persone` where `db_nomadelfia`.`persone`.`data_nascita` <= sysdate() - interval 70 year_month order by `db_nomadelfia`.`persone`.`nominativo` desc");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection('db_biblioteca')->statement("DROP VIEW IF EXISTS `v_clienti_biblioteca`");

        DB::connection('db_biblioteca')->statement("DROP VIEW IF EXISTS `v_colloc_split`");

        DB::connection('db_biblioteca')->statement("DROP VIEW IF EXISTS `v_lavoratori_biblioteca`");

        Schema::connection('db_biblioteca')->dropIfExists('video');

        Schema::connection('db_biblioteca')->dropIfExists('prestito');

        Schema::connection('db_biblioteca')->dropIfExists('media');

        Schema::connection('db_biblioteca')->dropIfExists('libro');

        Schema::connection('db_biblioteca')->dropIfExists('editore_libro');

        Schema::connection('db_biblioteca')->dropIfExists('editore');

        Schema::connection('db_biblioteca')->dropIfExists('classificazione');

        Schema::connection('db_biblioteca')->dropIfExists('autore_libro');

        Schema::connection('db_biblioteca')->dropIfExists('autore');
    }
};

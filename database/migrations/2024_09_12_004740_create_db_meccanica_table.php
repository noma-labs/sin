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
        Schema::connection('db_officina')->create('alimentazione', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nome', 30);
        });

        Schema::connection('db_officina')->create('colori', function (Blueprint $table) {
            $table->integer('ofco_iden');
            $table->string('ofco_nome', 50);
        });

        Schema::connection('db_officina')->create('gomme_veicolo', function (Blueprint $table) {
            $table->unsignedInteger('gomme_id');
            $table->integer('veicolo_id')->index('gomme_veicolo_veicolo_id_foreign');

            $table->unique(['gomme_id', 'veicolo_id']);
        });

        Schema::connection('db_officina')->create('impiego', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nome', 100);
            $table->integer('ord');
        });

        Schema::connection('db_officina')->create('incidenti', function (Blueprint $table) {
            $table->integer('ofin_veic');
            $table->integer('ofin_ident');
            $table->string('ofin_desc', 500);
            $table->integer('ofin_data');
            $table->integer('ofin_idpe');
        });

        Schema::connection('db_officina')->create('libretto_circolazione', function (Blueprint $table) {
            $table->integer('ofli_iden');
            $table->string('ofli_desc', 300);
        });

        Schema::connection('db_officina')->create('manutenzione_programmata', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('manutenzione_id')->index('manutenzione_programmata_manutenzione_id_foreign');
            $table->integer('veicolo_id')->index('manutenzione_programmata_veicolo_id_foreign');
            $table->integer('meccanico_id')->index('manutenzione_programmata_meccanico_id_foreign');
            $table->integer('kilometri');
            $table->date('data');
        });

        Schema::connection('db_officina')->create('marca', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nome', 30);
        });

        Schema::connection('db_officina')->create('modello', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('marca_id');
            $table->string('nome', 40);
        });

        Schema::connection('db_officina')->create('prenotazioni', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cliente_id')->index('cliente_id');
            $table->integer('veicolo_id')->nullable()->index('veicolo_id');
            $table->date('data_partenza');
            $table->string('ora_partenza', 20);
            $table->date('data_arrivo');
            $table->string('ora_arrivo', 20);
            $table->integer('meccanico_id')->index('meccanico_id');
            $table->integer('uso_id')->index('uso_id');
            $table->string('note', 100)->nullable()->default('');
            $table->timestamps();
            $table->softDeletes();
            $table->string('destinazione', 191)->default('Grosseto')->index('destinazione');
        });

        Schema::connection('db_officina')->create('tipo_filtro', function (Blueprint $table) {
            $table->increments('id');
            $table->string('codice', 191);
            $table->enum('tipo', ['aria', 'gasolio', 'olio', 'ac']);
            $table->string('note', 191);

            $table->unique(['codice', 'tipo']);
        });

        Schema::connection('db_officina')->create('tipo_gomme', function (Blueprint $table) {
            $table->increments('id');
            $table->string('codice', 191)->unique();
            $table->string('note', 191);
        });

        Schema::connection('db_officina')->create('tipo_manutenzione', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nome', 191);
            $table->integer('frequenza')->nullable();
            $table->enum('unita', ['km', 'anni']);
        });

        Schema::connection('db_officina')->create('tipo_olio', function (Blueprint $table) {
            $table->increments('id');
            $table->string('codice', 191)->unique();
            $table->string('note', 191);
        });

        Schema::connection('db_officina')->create('tipologia', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('nome', 30);
        });

        Schema::connection('db_officina')->create('usi', function (Blueprint $table) {
            $table->integer('ofus_iden', true);
            $table->string('ofus_nome', 30);
            $table->string('ofus_abbr', 30);
            $table->integer('ordinamento');
        });

        Schema::connection('db_officina')->create('veicolo', function (Blueprint $table) {
            $table->integer('id', true)->comment('identificativo veicolo');
            $table->string('nome', 100)->comment('nome interno del vaicolo');
            $table->string('targa', 30)->nullable()->comment('targa del veicolo');
            $table->integer('modello_id')->index('modello_mezzo')->comment('modello del veicolo');
            $table->integer('colore')->nullable()->comment('Colore del veicolo');
            $table->integer('impiego_id')->index('impiego_mezzo')->comment('impiego del veicolo nella comunitÃ ');
            $table->integer('tipologia_id')->index('tipologia_veicolo')->comment('tipologia di veicolo');
            $table->integer('alimentazione_id')->index('alimentazione_id')->comment('tipo di carburante');
            $table->integer('num_posti')->comment('numero di posti');
            $table->boolean('prenotabile')->default(false);
            $table->unsignedInteger('filtro_olio')->nullable()->index('veicolo_filtro_olio_foreign');
            $table->unsignedInteger('filtro_gasolio')->nullable()->index('veicolo_filtro_gasolio_foreign');
            $table->unsignedInteger('filtro_aria')->nullable()->index('veicolo_filtro_aria_foreign');
            $table->unsignedInteger('filtro_aria_condizionata')->nullable()->index('veicolo_filtro_aria_condizionata_foreign');
            $table->unsignedInteger('olio_id')->nullable()->index('veicolo_olio_id_foreign');
            $table->softDeletes();
            $table->integer('litri_olio')->nullable();
        });

        DB::connection('db_officina')->statement("CREATE VIEW `v_clienti_meccanica` AS select `db_nomadelfia`.`persone`.`id` AS `id`,`db_nomadelfia`.`persone`.`nominativo` AS `nominativo`,`db_nomadelfia`.`persone`.`data_nascita` AS `data_nascita`,(select distinct case `db_patente`.`persone_patenti`.`numero_patente` when '' then '' else 'CP ' end from `db_patente`.`persone_patenti` where `db_patente`.`persone_patenti`.`numero_patente` is not null and `db_patente`.`persone_patenti`.`persona_id` = `db_nomadelfia`.`persone`.`id`) AS `cliente_con_patente` from (`db_nomadelfia`.`persone` join `db_nomadelfia`.`popolazione` on(`db_nomadelfia`.`popolazione`.`persona_id` = `db_nomadelfia`.`persone`.`id`)) where `db_nomadelfia`.`persone`.`data_decesso` is null and `db_nomadelfia`.`persone`.`data_nascita` <= sysdate() - interval 200 year_month and `db_nomadelfia`.`popolazione`.`data_uscita` is null union select `db_nomadelfia`.`persone`.`id` AS `id`,`db_nomadelfia`.`persone`.`nominativo` AS `nominativo`,`db_nomadelfia`.`persone`.`data_nascita` AS `data_nascita`,'' AS `cliente_con_patente` from `db_nomadelfia`.`persone` where `db_nomadelfia`.`persone`.`id` = 0");

        DB::connection('db_officina')->statement("CREATE VIEW `v_lavoratori_meccanica` AS select `db_nomadelfia`.`aziende_persone`.`azienda_id` AS `azienda_id`,`db_nomadelfia`.`aziende_persone`.`persona_id` AS `persona_id`,`db_nomadelfia`.`persone`.`nominativo` AS `nominativo`,`db_nomadelfia`.`aziende_persone`.`stato` AS `stato`,`db_nomadelfia`.`aziende_persone`.`mansione` AS `mansione`,`db_nomadelfia`.`aziende_persone`.`data_inizio_azienda` AS `data_inizio_azienda`,`db_nomadelfia`.`aziende_persone`.`data_fine_azienda` AS `data_fine_azienda` from (`db_nomadelfia`.`aziende_persone` join `db_nomadelfia`.`persone`) where `db_nomadelfia`.`aziende_persone`.`azienda_id` = 1 and `db_nomadelfia`.`persone`.`id` = `db_nomadelfia`.`aziende_persone`.`persona_id` and `db_nomadelfia`.`persone`.`id` <> 127 order by `db_nomadelfia`.`aziende_persone`.`mansione`");

        Schema::connection('db_officina')->table('gomme_veicolo', function (Blueprint $table) {
            $table->foreign(['veicolo_id'])->references(['id'])->on('veicolo');
            $table->foreign(['gomme_id'])->references(['id'])->on('tipo_gomme');
        });

        Schema::connection('db_officina')->table('manutenzione_programmata', function (Blueprint $table) {
            $table->foreign(['meccanico_id'])->references(['id'])->on('persone');
            $table->foreign(['manutenzione_id'])->references(['id'])->on('tipo_manutenzione');
            $table->foreign(['veicolo_id'])->references(['id'])->on('veicolo');
        });

        Schema::connection('db_officina')->table('prenotazioni', function (Blueprint $table) {
            $table->foreign(['meccanico_id'], 'prenotazioni_ibfk_2')->references(['id'])->on('persone');
            $table->foreign(['veicolo_id'], 'prenotazioni_ibfk_4')->references(['id'])->on('veicolo');
            $table->foreign(['cliente_id'], 'prenotazioni_ibfk_1')->references(['id'])->on('persone');
            $table->foreign(['uso_id'], 'prenotazioni_ibfk_3')->references(['ofus_iden'])->on('usi');
        });

        Schema::connection('db_officina')->table('veicolo', function (Blueprint $table) {
            $table->foreign(['filtro_olio'])->references(['id'])->on('tipo_filtro')->onDelete('SET NULL');
            $table->foreign(['olio_id'])->references(['id'])->on('tipo_olio')->onDelete('SET NULL');
            $table->foreign(['modello_id'], 'modello_mezzo')->references(['id'])->on('modello');
            $table->foreign(['filtro_aria_condizionata'])->references(['id'])->on('tipo_filtro')->onDelete('SET NULL');
            $table->foreign(['filtro_gasolio'])->references(['id'])->on('tipo_filtro')->onDelete('SET NULL');
            $table->foreign(['alimentazione_id'], 'veicolo_ibfk_1')->references(['id'])->on('alimentazione');
            $table->foreign(['impiego_id'], 'impiego_mezzo')->references(['id'])->on('impiego');
            $table->foreign(['tipologia_id'], 'tipologia_veicolo')->references(['id'])->on('tipologia');
            $table->foreign(['filtro_aria'])->references(['id'])->on('tipo_filtro')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('db_officina')->table('veicolo', function (Blueprint $table) {
            $table->dropForeign('veicolo_filtro_olio_foreign');
            $table->dropForeign('veicolo_olio_id_foreign');
            $table->dropForeign('modello_mezzo');
            $table->dropForeign('veicolo_filtro_aria_condizionata_foreign');
            $table->dropForeign('veicolo_filtro_gasolio_foreign');
            $table->dropForeign('veicolo_ibfk_1');
            $table->dropForeign('impiego_mezzo');
            $table->dropForeign('tipologia_veicolo');
            $table->dropForeign('veicolo_filtro_aria_foreign');
        });

        Schema::connection('db_officina')->table('prenotazioni', function (Blueprint $table) {
            $table->dropForeign('prenotazioni_ibfk_2');
            $table->dropForeign('prenotazioni_ibfk_4');
            $table->dropForeign('prenotazioni_ibfk_1');
            $table->dropForeign('prenotazioni_ibfk_3');
        });

        Schema::connection('db_officina')->table('manutenzione_programmata', function (Blueprint $table) {
            $table->dropForeign('manutenzione_programmata_meccanico_id_foreign');
            $table->dropForeign('manutenzione_programmata_manutenzione_id_foreign');
            $table->dropForeign('manutenzione_programmata_veicolo_id_foreign');
        });

        Schema::connection('db_officina')->table('gomme_veicolo', function (Blueprint $table) {
            $table->dropForeign('gomme_veicolo_veicolo_id_foreign');
            $table->dropForeign('gomme_veicolo_gomme_id_foreign');
        });

        DB::connection('db_officina')->statement("DROP VIEW IF EXISTS `v_lavoratori_meccanica`");

        DB::connection('db_officina')->statement("DROP VIEW IF EXISTS `v_clienti_meccanica`");

        Schema::connection('db_officina')->dropIfExists('veicolo');

        Schema::connection('db_officina')->dropIfExists('usi');

        Schema::connection('db_officina')->dropIfExists('tipologia');

        Schema::connection('db_officina')->dropIfExists('tipo_olio');

        Schema::connection('db_officina')->dropIfExists('tipo_manutenzione');

        Schema::connection('db_officina')->dropIfExists('tipo_gomme');

        Schema::connection('db_officina')->dropIfExists('tipo_filtro');

        Schema::connection('db_officina')->dropIfExists('prenotazioni');

        Schema::connection('db_officina')->dropIfExists('modello');

        Schema::connection('db_officina')->dropIfExists('marca');

        Schema::connection('db_officina')->dropIfExists('manutenzione_programmata');

        Schema::connection('db_officina')->dropIfExists('libretto_circolazione');

        Schema::connection('db_officina')->dropIfExists('incidenti');

        Schema::connection('db_officina')->dropIfExists('impiego');

        Schema::connection('db_officina')->dropIfExists('gomme_veicolo');

        Schema::connection('db_officina')->dropIfExists('colori');

        Schema::connection('db_officina')->dropIfExists('alimentazione');
    }
};

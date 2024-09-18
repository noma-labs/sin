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
        Schema::connection('db_nomadelfia')->create('aziende', function (Blueprint $table) {
            $table->integer('id', true)->comment('Id Azienda');
            $table->string('nome_azienda', 50)->comment('nome');
            $table->string('descrizione_azienda', 200)->nullable()->comment('Descrizione Azienda/Incarico');
            $table->enum('tipo', ['azienda', 'incarico']);
            $table->timestamp('created_at')->useCurrent();
            $table->softDeletes();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::connection('db_nomadelfia')->create('aziende_persone', function (Blueprint $table) {
            $table->integer('azienda_id');
            $table->integer('persona_id')->index('persona_id');
            $table->enum('stato', ['Attivo', 'Non Attivo', 'Sospeso'])->nullable()->default('Attivo');
            $table->date('data_inizio_azienda');
            $table->date('data_fine_azienda')->nullable();
            $table->enum('mansione', ['RESPONSABILE AZIENDA', 'LAVORATORE']);

            $table->primary(['azienda_id', 'persona_id', 'data_inizio_azienda']);
        });

        Schema::connection('db_nomadelfia')->create('cariche', function (Blueprint $table) {
            $table->integer('id', true)->comment('Id carica');
            $table->string('nome', 50);
            $table->integer('num')->default(1)->comment('Numero di persone con questa carica');
            $table->enum('org', ['associazione', 'solidarieta', 'fondazione', 'agricola', 'culturale']);
            $table->string('descrizione', 200)->nullable()->comment('Descrizione');
            $table->integer('ord')->comment('ordine progressivo per ordinare la carica per ogni org');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::connection('db_nomadelfia')->create('esercizi_spirituali', function (Blueprint $table) {
            $table->integer('id', true);
            $table->enum('turno', ['no-esercizi', '1-turno', '2-turno', '3-turno', 'abetone-bimbi', 'abetone-grandi']);
            $table->integer('responsabile_id')->nullable()->index('esercizi_responsabile_1');
            $table->date('data_inizio')->nullable();
            $table->date('data_fine')->nullable();
            $table->string('luogo', 50)->nullable();
            $table->string('descrizione', 100)->nullable();
            $table->enum('stato', ['0', '1']);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });

        Schema::connection('db_nomadelfia')->create('famiglie', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nome_famiglia', 100)->unique('famiglia');
            $table->date('data_creazione');
            $table->timestamps();

            $table->primary(['id', 'nome_famiglia']);
        });

        Schema::connection('db_nomadelfia')->create('famiglie_persone', function (Blueprint $table) {
            $table->integer('famiglia_id');
            $table->integer('persona_id')->index('persona_id');
            $table->date('deleteme_data_entrata')->nullable();
            $table->date('deleteme_data_uscita')->nullable();
            $table->string('note', 500)->nullable();
            $table->enum('posizione_famiglia', ['CAPO FAMIGLIA', 'MOGLIE', 'FIGLIO NATO', 'FIGLIO ACCOLTO', 'SINGLE']);
            $table->enum('stato', ['0', '1']);

            $table->unique(['famiglia_id', 'persona_id', 'posizione_famiglia', 'stato'], 'famiglia_id');
            $table->primary(['famiglia_id', 'persona_id']);
        });

        Schema::connection('db_nomadelfia')->create('gruppi_familiari', function (Blueprint $table) {
            $table->integer('id', true)->comment('Id Gruppo Familiare');
            $table->string('nome', 30)->unique('nome')->comment('Nome del Gruppo familiare');
            $table->string('descrizione', 200)->nullable();
            $table->string('borgata', 200)->nullable()->comment('Borgata');
            $table->string('ubicazione', 500)->nullable()->comment('Ubicazione Gruppo');
            $table->date('data_creazione')->nullable()->comment('Data Nascita Gruppo');
        });

        Schema::connection('db_nomadelfia')->create('gruppi_familiari_capogruppi', function (Blueprint $table) {
            $table->comment('Mantiene lo storico dei capogruppi');
            $table->integer('gruppo_familiare_id');
            $table->integer('persona_id')->index('gruppi_familiari_capogruppi_ibfk_2');
            $table->date('data_inizio_incarico');
            $table->date('data_fine_incarico')->nullable();
            $table->string('note', 100)->nullable();
            $table->boolean('stato');

            $table->unique(['gruppo_familiare_id', 'persona_id', 'data_inizio_incarico', 'data_fine_incarico', 'stato'], 'gruppo_familiare_id');
            $table->primary(['gruppo_familiare_id', 'persona_id', 'data_inizio_incarico']);
        });

        Schema::connection('db_nomadelfia')->create('gruppi_persone', function (Blueprint $table) {
            $table->integer('gruppo_famigliare_id');
            $table->integer('persona_id')->index('persona_id');
            $table->enum('stato', ['0', '1']);
            $table->date('data_entrata_gruppo');
            $table->date('data_uscita_gruppo')->nullable();

            $table->primary(['gruppo_famigliare_id', 'persona_id', 'stato', 'data_entrata_gruppo']);
        });

        Schema::connection('db_nomadelfia')->create('incarichi', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nome', 50);
            $table->string('descrizione', 200)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->softDeletes();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::connection('db_nomadelfia')->create('incarichi_persone', function (Blueprint $table) {
            $table->integer('incarico_id');
            $table->integer('persona_id')->index('incarichi_persone_ibfk_2');
            $table->date('data_inizio');
            $table->date('data_fine')->nullable();
            $table->string('note', 200)->nullable();

            $table->primary(['incarico_id', 'persona_id', 'data_inizio']);
        });

        Schema::connection('db_nomadelfia')->create('nominativi_storici', function (Blueprint $table) {
            $table->integer('persona_id');
            $table->string('nominativo', 100)->unique('nominativo_2');
            $table->enum('stato', ['0', '1']);
            $table->timestamps();

            $table->primary(['persona_id', 'nominativo']);
        });

        Schema::connection('db_nomadelfia')->create('persone', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nominativo', 100)->unique('nominativo');
            $table->string('nome', 30);
            $table->string('cognome', 30);
            $table->integer('id_arch_pietro');
            $table->integer('id_alfa_enrico')->nullable();
            $table->string('provincia_nascita', 64)->nullable();
            $table->date('data_nascita');
            $table->enum('sesso', ['F', 'M']);
            $table->timestamps();
            $table->enum('dispense', ['0', '1'])->nullable();
            $table->enum('guardia', ['0', '1'])->nullable();
            $table->string('sigla_biancheria', 30)->nullable();
            $table->softDeletes();
            $table->date('data_decesso')->nullable()->index('idx_persone_datadecesso');
            $table->string('numero_elenco', 32)->nullable()->unique('numero_elenco');
            $table->text('biografia')->nullable();
            $table->string('cf', 32)->nullable();

            $table->primary(['id', 'nominativo']);
        });

        Schema::connection('db_nomadelfia')->create('persone_cariche', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('persona_id')->index('persone_cariche_ibfk_1')->comment('Id Persone');
            $table->integer('cariche_id')->index('persone_cariche_ibfk_2')->comment('Id Posizione');
            $table->date('data_inizio')->comment('inizio posizione');
            $table->date('data_fine')->nullable()->comment('fine posizione');
        });

        Schema::connection('db_nomadelfia')->create('persone_esercizi', function (Blueprint $table) {
            $table->integer('persona_id');
            $table->integer('esercizi_id')->index('persone_esercizi_2');
            $table->timestamp('created_at')->useCurrent();

            $table->primary(['persona_id', 'esercizi_id']);
        });

        Schema::connection('db_nomadelfia')->create('persone_posizioni', function (Blueprint $table) {
            $table->integer('persona_id')->index('persone_posizioni_ibfk_1')->comment('Id Persone');
            $table->integer('posizione_id')->index('persone_posizioni_ibfk_2')->comment('Id Posizione');
            $table->date('data_inizio')->comment('inizio posizione');
            $table->date('data_fine')->nullable()->comment('fine posizione');
            $table->enum('stato', ['0', '1']);
            $table->mediumInteger('uuid', true);
        });

        Schema::connection('db_nomadelfia')->create('persone_stati', function (Blueprint $table) {
            $table->integer('persona_id');
            $table->integer('stato_id')->index('persone_stati_ibfk_2');
            $table->date('data_inizio');
            $table->date('data_fine')->nullable();
            $table->enum('stato', ['0', '1']);

            $table->unique(['persona_id', 'stato_id', 'stato'], 'persona_id');
            $table->primary(['persona_id', 'stato_id', 'data_inizio']);
        });

        Schema::connection('db_nomadelfia')->create('popolazione', function (Blueprint $table) {
            $table->integer('persona_id');
            $table->date('data_entrata');
            $table->date('data_uscita')->nullable()->index('idx_popolazione_datauscita');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->primary(['persona_id', 'data_entrata']);
        });

        Schema::connection('db_nomadelfia')->create('posizioni', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('abbreviato', 4);
            $table->string('nome', 100);
            $table->string('descrizione', 200)->nullable();
            $table->integer('ordinamento');
        });

        Schema::connection('db_nomadelfia')->create('stati', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('stato', 3)->unique('stato');
            $table->string('nome', 100);
            $table->string('descrizione', 200)->nullable();
        });

        Schema::connection('db_nomadelfia')->create('tipo_evento', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nome', 100);
            $table->string('descrizione', 100)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        DB::connection('db_nomadelfia')->statement("CREATE VIEW `v_popolazione_attuale` AS with pop as (select `db_nomadelfia`.`persone`.`id` AS `id`,`db_nomadelfia`.`persone`.`nominativo` AS `nominativo`,`db_nomadelfia`.`persone`.`nome` AS `nome`,`db_nomadelfia`.`persone`.`cognome` AS `cognome`,`db_nomadelfia`.`persone`.`data_nascita` AS `data_nascita`,`db_nomadelfia`.`persone`.`sesso` AS `sesso`,`db_nomadelfia`.`persone`.`provincia_nascita` AS `provincia_nascita`,`db_nomadelfia`.`persone`.`numero_elenco` AS `numero_elenco`,`db_nomadelfia`.`popolazione`.`data_entrata` AS `data_entrata` from (`db_nomadelfia`.`popolazione` join `db_nomadelfia`.`persone` on(`db_nomadelfia`.`persone`.`id` = `db_nomadelfia`.`popolazione`.`persona_id`)) where `db_nomadelfia`.`popolazione`.`data_uscita` is null and `db_nomadelfia`.`persone`.`data_decesso` is null order by `db_nomadelfia`.`persone`.`nominativo`), posizione as (select `db_nomadelfia`.`persone_posizioni`.`persona_id` AS `persona_id`,max(`db_nomadelfia`.`persone_posizioni`.`data_inizio`) AS `posizione_inizio`,`db_nomadelfia`.`posizioni`.`nome` AS `posizione` from (`db_nomadelfia`.`persone_posizioni` join `db_nomadelfia`.`posizioni` on(`db_nomadelfia`.`posizioni`.`id` = `db_nomadelfia`.`persone_posizioni`.`posizione_id`)) where `db_nomadelfia`.`persone_posizioni`.`persona_id` in (select `pop`.`id` from `pop`) and `db_nomadelfia`.`persone_posizioni`.`stato` = '1' or `db_nomadelfia`.`persone_posizioni`.`data_fine` is null group by `db_nomadelfia`.`persone_posizioni`.`persona_id`), stato as (select `db_nomadelfia`.`persone_stati`.`persona_id` AS `persona_id`,max(`db_nomadelfia`.`persone_stati`.`data_inizio`) AS `stato_inizio`,`db_nomadelfia`.`stati`.`nome` AS `stato` from (`db_nomadelfia`.`persone_stati` join `db_nomadelfia`.`stati` on(`db_nomadelfia`.`stati`.`id` = `db_nomadelfia`.`persone_stati`.`stato_id`)) where `db_nomadelfia`.`persone_stati`.`persona_id` in (select `pop`.`id` from `pop`) group by `db_nomadelfia`.`persone_stati`.`persona_id` order by `db_nomadelfia`.`persone_stati`.`data_inizio` desc), gruppo as (select `db_nomadelfia`.`gruppi_persone`.`persona_id` AS `persona_id`,max(`db_nomadelfia`.`gruppi_persone`.`data_entrata_gruppo`) AS `gruppo_inizio`,`db_nomadelfia`.`gruppi_familiari`.`nome` AS `gruppo` from (`db_nomadelfia`.`gruppi_persone` join `db_nomadelfia`.`gruppi_familiari` on(`db_nomadelfia`.`gruppi_familiari`.`id` = `db_nomadelfia`.`gruppi_persone`.`gruppo_famigliare_id`)) where `db_nomadelfia`.`gruppi_persone`.`persona_id` in (select `pop`.`id` from `pop`) and `db_nomadelfia`.`gruppi_persone`.`stato` = '1' group by `db_nomadelfia`.`gruppi_persone`.`persona_id` order by `db_nomadelfia`.`gruppi_persone`.`data_entrata_gruppo` desc), famiglia as (select `db_nomadelfia`.`famiglie_persone`.`persona_id` AS `persona_id`,`db_nomadelfia`.`famiglie`.`data_creazione` AS `famiglia_inizio`,`db_nomadelfia`.`famiglie`.`nome_famiglia` AS `famiglia`,`db_nomadelfia`.`famiglie_persone`.`posizione_famiglia` AS `posizione_famiglia` from (`db_nomadelfia`.`famiglie_persone` join `db_nomadelfia`.`famiglie` on(`db_nomadelfia`.`famiglie`.`id` = `db_nomadelfia`.`famiglie_persone`.`famiglia_id`)) where `db_nomadelfia`.`famiglie_persone`.`persona_id` in (select `pop`.`id` from `pop`) group by `db_nomadelfia`.`famiglie_persone`.`persona_id` order by `db_nomadelfia`.`famiglie`.`data_creazione` desc), azienda as (select `db_nomadelfia`.`aziende_persone`.`persona_id` AS `persona_id`,max(`db_nomadelfia`.`aziende_persone`.`data_inizio_azienda`) AS `azienda_inizio`,`db_nomadelfia`.`aziende`.`nome_azienda` AS `azienda` from (`db_nomadelfia`.`aziende_persone` join `db_nomadelfia`.`aziende` on(`db_nomadelfia`.`aziende`.`id` = `db_nomadelfia`.`aziende_persone`.`azienda_id`)) where `db_nomadelfia`.`aziende_persone`.`persona_id` in (select `pop`.`id` from `pop`) group by `db_nomadelfia`.`aziende_persone`.`persona_id` order by `db_nomadelfia`.`aziende_persone`.`data_inizio_azienda` desc), scuola as (select `db_scuola`.`alunni_classi`.`persona_id` AS `persona_id`,max(`db_scuola`.`alunni_classi`.`data_inizio`) AS `scuola_inizio`,`db_scuola`.`tipo`.`nome` AS `scuola` from ((`db_scuola`.`alunni_classi` join `db_scuola`.`classi` on(`db_scuola`.`classi`.`id` = `db_scuola`.`alunni_classi`.`classe_id`)) join `db_scuola`.`tipo` on(`db_scuola`.`tipo`.`id` = `db_scuola`.`classi`.`tipo_id`)) where `db_scuola`.`alunni_classi`.`persona_id` in (select `pop`.`id` from `pop`) and `db_scuola`.`alunni_classi`.`data_fine` is null group by `db_scuola`.`alunni_classi`.`persona_id` order by `db_scuola`.`alunni_classi`.`data_inizio` desc)select `pop`.`id` AS `id`,`pop`.`nominativo` AS `nominativo`,`pop`.`nome` AS `nome`,`pop`.`cognome` AS `cognome`,`pop`.`data_nascita` AS `data_nascita`,`pop`.`sesso` AS `sesso`,`pop`.`provincia_nascita` AS `provincia_nascita`,`pop`.`numero_elenco` AS `numero_elenco`,`pop`.`data_entrata` AS `data_entrata`,`posizione`.`posizione` AS `posizione`,`posizione`.`posizione_inizio` AS `posizione_inizio`,`stato`.`stato` AS `stato`,`gruppo`.`gruppo` AS `gruppo`,`famiglia`.`famiglia` AS `famiglia`,`famiglia`.`posizione_famiglia` AS `posizione_famiglia`,`azienda`.`azienda` AS `azienda`,`scuola`.`scuola` AS `scuola` from ((((((`pop` left join `posizione` on(`posizione`.`persona_id` = `pop`.`id`)) left join `stato` on(`stato`.`persona_id` = `pop`.`id`)) left join `gruppo` on(`gruppo`.`persona_id` = `pop`.`id`)) left join `famiglia` on(`famiglia`.`persona_id` = `pop`.`id`)) left join `azienda` on(`azienda`.`persona_id` = `pop`.`id`)) left join `scuola` on(`scuola`.`persona_id` = `pop`.`id`)) order by `pop`.`nominativo`");

        Schema::connection('db_nomadelfia')->table('aziende_persone', function (Blueprint $table) {
            $table->foreign(['persona_id'], 'aziende_persone_ibfk_2')->references(['id'])->on('persone');
            $table->foreign(['azienda_id'], 'aziende_persone_ibfk_1')->references(['id'])->on('aziende');
        });

        Schema::connection('db_nomadelfia')->table('esercizi_spirituali', function (Blueprint $table) {
            $table->foreign(['responsabile_id'], 'esercizi_responsabile_1')->references(['id'])->on('persone');
        });

        Schema::connection('db_nomadelfia')->table('famiglie_persone', function (Blueprint $table) {
            $table->foreign(['persona_id'], 'famiglie_persone_ibfk_2')->references(['id'])->on('persone');
            $table->foreign(['famiglia_id'], 'famiglie_persone_ibfk_1')->references(['id'])->on('famiglie');
        });

        Schema::connection('db_nomadelfia')->table('gruppi_familiari_capogruppi', function (Blueprint $table) {
            $table->foreign(['persona_id'], 'gruppi_familiari_capogruppi_ibfk_2')->references(['id'])->on('persone');
            $table->foreign(['gruppo_familiare_id'], 'gruppi_familiari_capogruppi_ibfk_1')->references(['id'])->on('gruppi_familiari');
        });

        Schema::connection('db_nomadelfia')->table('gruppi_persone', function (Blueprint $table) {
            $table->foreign(['persona_id'], 'gruppi_persone_ibfk_2')->references(['id'])->on('persone');
            $table->foreign(['gruppo_famigliare_id'], 'gruppi_persone_ibfk_1')->references(['id'])->on('gruppi_familiari');
        });

        Schema::connection('db_nomadelfia')->table('incarichi_persone', function (Blueprint $table) {
            $table->foreign(['persona_id'], 'incarichi_persone_ibfk_2')->references(['id'])->on('persone');
            $table->foreign(['incarico_id'], 'incarichi_persone_ibfk_1')->references(['id'])->on('incarichi')->onDelete('CASCADE');
        });

        Schema::connection('db_nomadelfia')->table('nominativi_storici', function (Blueprint $table) {
            $table->foreign(['persona_id'], 'nominativi_storici_ibfk_1')->references(['id'])->on('persone');
        });

        Schema::connection('db_nomadelfia')->table('persone_cariche', function (Blueprint $table) {
            $table->foreign(['cariche_id'], 'persone_cariche_ibfk_2')->references(['id'])->on('cariche');
            $table->foreign(['persona_id'], 'persone_cariche_ibfk_1')->references(['id'])->on('persone');
        });

        Schema::connection('db_nomadelfia')->table('persone_esercizi', function (Blueprint $table) {
            $table->foreign(['esercizi_id'], 'persone_esercizi_2')->references(['id'])->on('esercizi_spirituali');
            $table->foreign(['persona_id'], 'persone_esercizi_1')->references(['id'])->on('persone');
        });

        Schema::connection('db_nomadelfia')->table('persone_posizioni', function (Blueprint $table) {
            $table->foreign(['posizione_id'], 'persone_posizioni_ibfk_2')->references(['id'])->on('posizioni');
            $table->foreign(['persona_id'], 'persone_posizioni_ibfk_1')->references(['id'])->on('persone');
        });

        Schema::connection('db_nomadelfia')->table('persone_stati', function (Blueprint $table) {
            $table->foreign(['stato_id'], 'persone_stati_ibfk_2')->references(['id'])->on('stati');
            $table->foreign(['persona_id'], 'persone_stati_ibfk_1')->references(['id'])->on('persone');
        });

        Schema::connection('db_nomadelfia')->table('popolazione', function (Blueprint $table) {
            $table->foreign(['persona_id'], 'popolazione_persona_fk')->references(['id'])->on('persone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('db_nomadelfia')->table('popolazione', function (Blueprint $table) {
            $table->dropForeign('popolazione_persona_fk');
        });

        Schema::connection('db_nomadelfia')->table('persone_stati', function (Blueprint $table) {
            $table->dropForeign('persone_stati_ibfk_2');
            $table->dropForeign('persone_stati_ibfk_1');
        });

        Schema::connection('db_nomadelfia')->table('persone_posizioni', function (Blueprint $table) {
            $table->dropForeign('persone_posizioni_ibfk_2');
            $table->dropForeign('persone_posizioni_ibfk_1');
        });

        Schema::connection('db_nomadelfia')->table('persone_esercizi', function (Blueprint $table) {
            $table->dropForeign('persone_esercizi_2');
            $table->dropForeign('persone_esercizi_1');
        });

        Schema::connection('db_nomadelfia')->table('persone_cariche', function (Blueprint $table) {
            $table->dropForeign('persone_cariche_ibfk_2');
            $table->dropForeign('persone_cariche_ibfk_1');
        });

        Schema::connection('db_nomadelfia')->table('nominativi_storici', function (Blueprint $table) {
            $table->dropForeign('nominativi_storici_ibfk_1');
        });

        Schema::connection('db_nomadelfia')->table('incarichi_persone', function (Blueprint $table) {
            $table->dropForeign('incarichi_persone_ibfk_2');
            $table->dropForeign('incarichi_persone_ibfk_1');
        });

        Schema::connection('db_nomadelfia')->table('gruppi_persone', function (Blueprint $table) {
            $table->dropForeign('gruppi_persone_ibfk_2');
            $table->dropForeign('gruppi_persone_ibfk_1');
        });

        Schema::connection('db_nomadelfia')->table('gruppi_familiari_capogruppi', function (Blueprint $table) {
            $table->dropForeign('gruppi_familiari_capogruppi_ibfk_2');
            $table->dropForeign('gruppi_familiari_capogruppi_ibfk_1');
        });

        Schema::connection('db_nomadelfia')->table('famiglie_persone', function (Blueprint $table) {
            $table->dropForeign('famiglie_persone_ibfk_2');
            $table->dropForeign('famiglie_persone_ibfk_1');
        });

        Schema::connection('db_nomadelfia')->table('esercizi_spirituali', function (Blueprint $table) {
            $table->dropForeign('esercizi_responsabile_1');
        });

        Schema::connection('db_nomadelfia')->table('aziende_persone', function (Blueprint $table) {
            $table->dropForeign('aziende_persone_ibfk_2');
            $table->dropForeign('aziende_persone_ibfk_1');
        });

        DB::connection('db_nomadelfia')->statement("DROP VIEW IF EXISTS `v_popolazione_attuale`");

        Schema::connection('db_nomadelfia')->dropIfExists('tipo_evento');

        Schema::connection('db_nomadelfia')->dropIfExists('stati');

        Schema::connection('db_nomadelfia')->dropIfExists('posizioni');

        Schema::connection('db_nomadelfia')->dropIfExists('popolazione');

        Schema::connection('db_nomadelfia')->dropIfExists('persone_stati');

        Schema::connection('db_nomadelfia')->dropIfExists('persone_posizioni');

        Schema::connection('db_nomadelfia')->dropIfExists('persone_esercizi');

        Schema::connection('db_nomadelfia')->dropIfExists('persone_cariche');

        Schema::connection('db_nomadelfia')->dropIfExists('persone');

        Schema::connection('db_nomadelfia')->dropIfExists('nominativi_storici');

        Schema::connection('db_nomadelfia')->dropIfExists('incarichi_persone');

        Schema::connection('db_nomadelfia')->dropIfExists('incarichi');

        Schema::connection('db_nomadelfia')->dropIfExists('gruppi_persone');

        Schema::connection('db_nomadelfia')->dropIfExists('gruppi_familiari_capogruppi');

        Schema::connection('db_nomadelfia')->dropIfExists('gruppi_familiari');

        Schema::connection('db_nomadelfia')->dropIfExists('famiglie_persone');

        Schema::connection('db_nomadelfia')->dropIfExists('famiglie');

        Schema::connection('db_nomadelfia')->dropIfExists('esercizi_spirituali');

        Schema::connection('db_nomadelfia')->dropIfExists('cariche');

        Schema::connection('db_nomadelfia')->dropIfExists('aziende_persone');

        Schema::connection('db_nomadelfia')->dropIfExists('aziende');
    }
};

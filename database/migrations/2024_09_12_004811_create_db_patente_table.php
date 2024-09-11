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
        Schema::connection('db_patente')->create('categorie', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('categoria', 200);
            $table->string('descrizione', 150);
            $table->string('note', 500);
        });

        Schema::connection('db_patente')->create('patenti_categorie', function (Blueprint $table) {
            $table->string('numero_patente', 30);
            $table->integer('categoria_patente_id');
            $table->date('data_rilascio')->nullable()->comment('campo 10 patente europea');
            $table->date('data_scadenza')->nullable()->comment('capo 11 patente europea');
            $table->string('note', 200)->nullable();

            $table->index(['numero_patente', 'categoria_patente_id'], 'numero_patente_persona');
            $table->primary(['categoria_patente_id', 'numero_patente']);
        });

        Schema::connection('db_patente')->create('persone_patenti', function (Blueprint $table) {
            $table->integer('persona_id');
            $table->string('numero_patente', 15)->unique('numero_patente_2');
            $table->string('rilasciata_dal', 100);
            $table->date('data_rilascio_patente');
            $table->date('data_scadenza_patente');
            $table->enum('stato', ['commissione'])->nullable();
            $table->string('note', 200)->nullable();

            $table->primary(['persona_id', 'numero_patente', 'data_rilascio_patente', 'data_scadenza_patente']);
        });

        DB::connection('db_patente')->statement("CREATE VIEW `v_clienti_patente` AS select `db_nomadelfia`.`persone`.`id` AS `persona_id`,concat(`db_nomadelfia`.`persone`.`nome`,' ',`db_nomadelfia`.`persone`.`cognome`) AS `nome_cognome`,`db_nomadelfia`.`persone`.`nominativo` AS `nominativo`,`db_nomadelfia`.`persone`.`nome` AS `nome`,`db_nomadelfia`.`persone`.`cognome` AS `cognome`,`db_nomadelfia`.`persone`.`data_nascita` AS `data_nascita`,`db_nomadelfia`.`persone`.`provincia_nascita` AS `provincia_nascita`,(select distinct case `db_patente`.`persone_patenti`.`numero_patente` when '' then '' else 'CP ' end from `db_patente`.`persone_patenti` where `db_patente`.`persone_patenti`.`numero_patente` is not null and `db_patente`.`persone_patenti`.`persona_id` = `db_nomadelfia`.`persone`.`id`) AS `cliente_con_patente` from (`db_nomadelfia`.`persone` join `db_nomadelfia`.`popolazione` on(`db_nomadelfia`.`popolazione`.`persona_id` = `db_nomadelfia`.`persone`.`id`)) where `db_nomadelfia`.`persone`.`data_nascita` <= sysdate() - interval 180 year_month and `db_nomadelfia`.`popolazione`.`data_uscita` is null");

        Schema::connection('db_patente')->table('patenti_categorie', function (Blueprint $table) {
            $table->foreign(['categoria_patente_id'], 'patenti_categorie_ibfk_1')->references(['id'])->on('categorie');
        });

        Schema::connection('db_patente')->table('persone_patenti', function (Blueprint $table) {
            $table->foreign(['persona_id'], 'persone_patenti_ibfk_1')->references(['id'])->on('persone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('db_patente')->table('persone_patenti', function (Blueprint $table) {
            $table->dropForeign('persone_patenti_ibfk_1');
        });

        Schema::connection('db_patente')->table('patenti_categorie', function (Blueprint $table) {
            $table->dropForeign('patenti_categorie_ibfk_1');
        });

        DB::connection('db_patente')->statement("DROP VIEW IF EXISTS `v_clienti_patente`");

        Schema::connection('db_patente')->dropIfExists('persone_patenti');

        Schema::connection('db_patente')->dropIfExists('patenti_categorie');

        Schema::connection('db_patente')->dropIfExists('categorie');
    }
};

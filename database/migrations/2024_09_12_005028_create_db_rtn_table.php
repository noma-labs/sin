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
        Schema::connection('db_rtn')->create('arch_prof', function (Blueprint $table) {
            $table->integer('Id_regpro', true)->comment('identificatore DB');
            $table->integer('Id_ap')->comment('Identificatore Film');
            $table->enum('Fonte', ['Hard-Disk', '', 'DVD'])->comment('Dispositivo Magnetico');
            $table->enum('Categoria_ap', ['', 'BD', 'BV', 'DB', 'DV', 'DVR', 'HD', 'NB', 'ND', 'NR', 'NS', 'S8', '16M', '8M'])->comment('Serie');
            $table->integer('Numero_ap')->comment('Numero');
            $table->integer('Numreg')->comment('Numero de registrazione');
            $table->integer('Min_Ini')->comment('Minuti Inizio');
            $table->integer('Min_fin')->comment('Minuti Fine');
            $table->date('Datareg')->index('Datareg_2')->comment('Date Registrazione');
            $table->string('Localita', 400)->index('Localita')->comment('Localita');
            $table->string('Caratt', 200)->index('Caratt')->comment('Caratteristiche');
            $table->string('ca_k', 1)->comment('lettera k');
            $table->string('Mic', 30)->comment('Espazio Bianco');
            $table->date('ULTrasm')->nullable()->comment('Data Trasmisione');
            $table->string('Formato_reg', 100)->comment('Formato de Registrazione');
            $table->integer('Dur_Nastro')->comment('Minuti totale');
            $table->string('Sot_Categ', 4)->index('Categ')->comment('Catalogo');
            $table->integer('Min_tot')->comment('Minuti totali');
            $table->text('Descrizione_ap')->index('Descrizione_ap')->comment('Descrizione');
            $table->string('Operatore', 200)->index('Operatore')->comment('Operatore telecamera');
            $table->enum('estato', ['0', '1']);

            $table->unique(['Id_ap', 'Fonte', 'Categoria_ap', 'Numero_ap', 'Numreg', 'Min_Ini', 'Min_fin', 'Datareg', 'Descrizione_ap'], 'Id_ap');
            $table->index(['Datareg'], 'Datareg');
        });

        Schema::connection('db_rtn')->create('categ_arch_prof', function (Blueprint $table) {
            $table->integer('Id_categoria_arch_prof', true)->comment('Id categoria');
            $table->string('Cat_arch_prof', 2)->comment('Serie');
            $table->string('Sotto_cat_arch_prof', 2)->comment('Categoria');
            $table->string('Descrizione', 100)->unique('categoria')->comment('Descrizione categoria');

            $table->unique(['Id_categoria_arch_prof', 'Cat_arch_prof', 'Sotto_cat_arch_prof'], 'Id_categoria_arch_prof');
        });

        Schema::connection('db_rtn')->create('categ_trasm_tv', function (Blueprint $table) {
            $table->integer('id_serie', true);
            $table->string('sigla_serie', 5)->index('sigla_serie');
            $table->string('desc_serie', 512)->index('desc_serie');
        });

        Schema::connection('db_rtn')->create('categoria', function (Blueprint $table) {
            $table->integer('id_categoria', true);
            $table->string('desc_categoria', 25)->unique('des_categoria');

            $table->index(['desc_categoria'], 'des_categoria_2');
        });

        Schema::connection('db_rtn')->create('critica', function (Blueprint $table) {
            $table->integer('id_critica', true);
            $table->string('desc_critica', 25)->index('des_critica_2');

            $table->unique(['desc_critica'], 'des_critica');
        });

        Schema::connection('db_rtn')->create('load_film', function (Blueprint $table) {
            $table->string('descrizione', 200)->primary();
            $table->string('Serie_l', 2)->nullable();
            $table->integer('Numero_l')->nullable();

            $table->index(['descrizione'], 'Index Descrizione');
        });

        Schema::connection('db_rtn')->create('persone_alias', function (Blueprint $table) {
            $table->integer('persona_id');
            $table->string('alias', 100);

            $table->primary(['persona_id', 'alias']);
        });

        Schema::connection('db_rtn')->create('prestiti_film', function (Blueprint $table) {
            $table->integer('Id_pres', true)->comment('id prestiti');
            $table->integer('Id_film')->index('Id_film')->comment('Identificazione Film');
            $table->string('Serie', 2)->index('Serie')->comment('Serie');
            $table->string('Numero', 5)->index('Numero')->comment('Numero');
            $table->date('Data_Pres')->index('Data_Pres')->comment('Data Prestiti');
            $table->date('Data_Rest')->index('Data_Rest')->comment('Data Restituido');
            $table->string('Nome_Prest', 30)->index('Nome_Prest')->comment('Persona consegnate');
        });

        Schema::connection('db_rtn')->create('progr_settima', function (Blueprint $table) {
            $table->integer('Id_proset', true)->comment('Id prog sett');
            $table->date('Data_trasm')->index('Data_trasm')->comment('Data Trasmissione');
            $table->string('Numset', 2)->comment('Numero Settima');
            $table->string('Nomgio', 20)->comment('Nome giorino');
            $table->string('Tv', 10)->comment('Canale Trasmissione');
            $table->time('Ora_trasm')->comment('Ora Trasmissione');
            $table->string('Visione', 20)->comment('Tipo visione');
            $table->string('Serie', 2)->comment('Serie Film');
            $table->integer('Numero')->comment('Numero film');
            $table->string('Descrizione', 200)->index('Descrizione')->comment('Titolo/Descrizione film');
        });

        Schema::connection('db_rtn')->create('storico_trasmissioni', function (Blueprint $table) {
            $table->comment('Mantiene lo storico dei film trasmessi.');
            $table->integer('trasmissioni_tv_ID')->comment('Referenza a id nella tabella trasmissioni_tv');
            $table->date('DataTrasm');

            $table->primary(['trasmissioni_tv_ID', 'DataTrasm']);
        });

        Schema::connection('db_rtn')->create('supporto', function (Blueprint $table) {
            $table->integer('id_supporto', true);
            $table->string('des_supporto', 25);
        });

        Schema::connection('db_rtn')->create('trasmissioni_tv', function (Blueprint $table) {
            $table->comment('Registra le trasmissioni registrate');
            $table->integer('trasmissioni_tv_ID', true);
            $table->string('Series', 2)->index('Index by Serie')->comment('Serie Film');
            $table->integer('Numeros')->index('Index by Numero')->comment('Numero Film');
            $table->integer('Record')->nullable()->default(1)->comment('Record progressivo nella cassetta');
            $table->integer('Ini')->nullable()->default(0)->comment('Inizio minutaggio del video nella cassetta');
            $table->integer('Fin')->nullable()->comment('Fine dei minuti del video nella cassetta');
            $table->date('DataReg')->nullable()->comment('La data di registrazione');
            $table->enum('Visione', ['Tutti', '14Anni', 'Elementari', '12Anni', 'Superiori', 'NonVisibile', 'Medie', 'NonVisionato'])->nullable()->comment('Categorie di visione');
            $table->enum('Critica', ['0', '6', '7', '8', '9', '10'])->nullable()->comment('Giudizio critico di valutazione da 6(min)-9(max).');
            $table->integer('Visionatore')->comment('Referenza esterna,Nome del visionatore del film');
            $table->date('UTrasm')->nullable()->comment('Ultima trasmissione del video');
            $table->enum('Supporto', ['VHS', 'DVD', 'SD', 'HD', 'FILE', 'DVD FILE', 'MANCA'])->nullable()->comment('Il supporto del video');
            $table->enum('Cat', ['FiReligiosi', 'FiComico', 'FiCommedia', 'FiStorico', 'FiAzione', 'FiAvventura', 'FiWestern', 'FiDrammatico', 'FiFantascienza', 'FiMusical', 'FiThriller', 'FiSportivo', 'FiRomantico', 'FiPoliziesco', 'FiAnimazione', 'FiDocumentario', 'CiAC', 'CiRisorgimento', 'CiContemporanea', 'CiMedioevoRinascimento', 'CiGenerale', 'CiReligione', 'CiNonClass', 'AmNatura', 'AmEcologia', 'AnAnimali', 'AmAstronomia', 'MuLeggera', 'MuClassica', 'MuNonClass', 'SpNonClass', 'SpComico'])->nullable()->comment('Categoria storiche del video');
            $table->integer('Min')->nullable()->comment('Minuti durata video');
            $table->text('Descriz')->nullable()->comment('Descrizione e titolo del video');
            $table->string('Note', 300)->comment('Note di riferimento al film');
            $table->string('link', 1000)->index('Index by Link')->comment('Link Trial');

            $table->unique(['Series', 'Numeros', 'Record'], 'Unique Key');
        });

        Schema::connection('db_rtn')->create('vecchio_tv', function (Blueprint $table) {
            $table->comment('Registra le trasmissioni registrate');
            $table->integer('trasmissioni_tv_ID', true);
            $table->string('Serie', 12)->nullable()->comment('(FA=film, SP=spettacoli, CI= vivilta..)');
            $table->integer('Record')->nullable()->default(1)->comment('Record progressivo nella cassetta');
            $table->integer('Ini')->nullable()->default(0)->comment('Inizio minutaggio del video nella cassetta');
            $table->integer('Fin')->nullable()->comment('Fine dei minuti del video nella cassetta');
            $table->date('DataReg')->nullable()->comment('La data di registrazione');
            $table->enum('Visione', ['Tutti', '14Anni', 'Elementari', '12Anni', 'Superiori', 'NonVisibile', 'Medie', 'NonVisionato'])->nullable()->comment('Categorie di visione');
            $table->enum('Critica', ['6', '7', '8', '9', '10'])->nullable()->comment('Giudizio critico di valutazione da 6(min)-9(max).');
            $table->integer('Visionatore')->index('Visionatore')->comment('Referenza esterna,Nome del visionatore del film');
            $table->date('UTrasm')->nullable()->comment('Ultima trasmissione del video');
            $table->enum('Supporto', ['VHS', 'DVD'])->nullable()->comment('Il supporto del video');
            $table->enum('Cat', ['FiReligiosi', 'FiComico', 'FiCommedia', 'FiStorico', 'FiAzione', 'FiNonClass', 'FiAvventura', 'FiWestern', 'FiDrammatico', 'FiFantascienza', 'CiAC', 'CiRisorgimento', 'CiContemporanea', 'CiMedioevoRinascimento', 'AmNatura', 'AmEcologia', 'AnAnimali', 'AmAstronomia', 'CiGenerale', 'CiReligione', 'CiNonClass', 'FaNonClass', 'MuLeggera', 'MuClassica', 'MuNonClass', 'SpNonClass', 'SpComici'])->nullable()->comment('Categoria storiche del video');
            $table->integer('Min')->nullable()->comment('Minuti durata video');
            $table->text('Desciz')->nullable()->comment('Descrizione e titolo del video');
        });

        Schema::connection('db_rtn')->create('visionatori', function (Blueprint $table) {
            $table->comment('Contiene l\'id e il nome del visionatore del film');
            $table->integer('idVisionatore', true)->comment('ID del Visionatore, ha una referenza in archvio_tv');
            $table->string('Nome', 50)->comment('Nome del visionatore');
        });

        Schema::connection('db_rtn')->create('visione', function (Blueprint $table) {
            $table->integer('id_visione', true);
            $table->string('desc_visione', 23)->index('d_visione_2');

            $table->unique(['desc_visione'], 'd_visione');
        });

        Schema::connection('db_rtn')->table('persone_alias', function (Blueprint $table) {
            $table->foreign(['persona_id'], 'fk_persone_alias_persona_id')->references(['id'])->on('persone');
        });

        Schema::connection('db_rtn')->table('prestiti_film', function (Blueprint $table) {
            $table->foreign(['Id_film'], 'prestiti_film_ibfk_1')->references(['trasmissioni_tv_ID'])->on('trasmissioni_tv');
        });

        Schema::connection('db_rtn')->table('storico_trasmissioni', function (Blueprint $table) {
            $table->foreign(['trasmissioni_tv_ID'], 'storico_trasmissioni_ibfk_1')->references(['trasmissioni_tv_ID'])->on('trasmissioni_tv');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('db_rtn')->table('storico_trasmissioni', function (Blueprint $table) {
            $table->dropForeign('storico_trasmissioni_ibfk_1');
        });

        Schema::connection('db_rtn')->table('prestiti_film', function (Blueprint $table) {
            $table->dropForeign('prestiti_film_ibfk_1');
        });

        Schema::connection('db_rtn')->table('persone_alias', function (Blueprint $table) {
            $table->dropForeign('fk_persone_alias_persona_id');
        });

        Schema::connection('db_rtn')->dropIfExists('visione');

        Schema::connection('db_rtn')->dropIfExists('visionatori');

        Schema::connection('db_rtn')->dropIfExists('vecchio_tv');

        Schema::connection('db_rtn')->dropIfExists('trasmissioni_tv');

        Schema::connection('db_rtn')->dropIfExists('supporto');

        Schema::connection('db_rtn')->dropIfExists('storico_trasmissioni');

        Schema::connection('db_rtn')->dropIfExists('progr_settima');

        Schema::connection('db_rtn')->dropIfExists('prestiti_film');

        Schema::connection('db_rtn')->dropIfExists('persone_alias');

        Schema::connection('db_rtn')->dropIfExists('load_film');

        Schema::connection('db_rtn')->dropIfExists('critica');

        Schema::connection('db_rtn')->dropIfExists('categoria');

        Schema::connection('db_rtn')->dropIfExists('categ_trasm_tv');

        Schema::connection('db_rtn')->dropIfExists('categ_arch_prof');

        Schema::connection('db_rtn')->dropIfExists('arch_prof');
    }
};

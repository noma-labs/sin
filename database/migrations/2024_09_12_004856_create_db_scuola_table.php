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
        Schema::connection('db_scuola')->create('alunni_classi', function (Blueprint $table) {
            $table->integer('classe_id')->index('alunni_classi_idx');
            $table->integer('persona_id');
            $table->date('data_inizio')->comment('Data inizio dell alunno nella classe');
            $table->date('data_fine')->nullable()->comment('Data fine dell alunno nella classe');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->unique(['classe_id', 'persona_id', 'data_inizio'], 'alunni_classi_unique');
        });

        Schema::connection('db_scuola')->create('anno', function (Blueprint $table) {
            $table->integer('id', true)->comment('Anno (intero) è la chiave primaria');
            $table->integer('responsabile_id')->nullable();
            $table->string('scolastico', 10)->unique('unique_anno_scolastico')->comment('Anno scolastico. E.g. 2018/2019');
            $table->string('descrizione', 100)->nullable();
            $table->date('data_inizio')->comment('Data inizio dell anno scolastico');
            $table->date('data_fine')->nullable()->comment('Data fine dell anno scolastico');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::connection('db_scuola')->create('classi', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('tipo_id');
            $table->integer('anno_id')->index('classi_anni_idx')->comment('Anno scolastico di riferimento');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->unique(['tipo_id', 'anno_id'], 'unique_classe_as');
        });

        Schema::connection('db_scuola')->create('coordinatori_classi', function (Blueprint $table) {
            $table->integer('classe_id');
            $table->integer('coordinatore_id');
            $table->enum('tipo', ['responsabile', 'coordinatore', 'collaboratore'])->default('coordinatore');
            $table->date('data_inizio')->comment('Data inizio del coordiantore nella classe');
            $table->date('data_fine')->nullable()->comment('Data fine del coordinatore nella classe');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->unique(['classe_id', 'coordinatore_id'], 'unique_coord');
        });

        Schema::connection('db_scuola')->create('elaborati', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('collocazione', 6)->nullable()->comment('Collocazione nel formato ABC123');
            $table->string('anno_scolastico', 9)->nullable()->comment('Anno scolastico di riferimento nel formato YYYY/YYYY');
            $table->string('titolo', 128)->nullable();
            $table->string('classi', 128)->nullable()->comment('Classi separate da virgole oppure lavoro personale.');
            $table->string('file_path', 256)->nullable();
            $table->string('file_mime_type', 64)->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->string('file_hash', 64)->nullable()->unique('file_hash');
            $table->string('dimensione', 8)->nullable()->comment('Formato del libro (larghazza X Altezza) in cm. 24x43');
            $table->string('rilegatura', 32)->nullable()->comment('Rilegatura del libro');
            $table->text('note')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->integer('libro_id')->nullable();
        });

        Schema::connection('db_scuola')->create('elaborati_coordinatori', function (Blueprint $table) {
            $table->integer('elaborato_id');
            $table->integer('coordinatore_id')->index('coordinatore_id');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent();

            $table->primary(['elaborato_id', 'coordinatore_id']);
        });

        Schema::connection('db_scuola')->create('elaborati_studenti', function (Blueprint $table) {
            $table->integer('elaborato_id');
            $table->integer('studente_id')->index('studente_id');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent();

            $table->primary(['elaborato_id', 'studente_id']);
        });

        Schema::connection('db_scuola')->create('tipo', function (Blueprint $table) {
            $table->integer('id', true)->comment('Id univoco della classe in un anno scolastico');
            $table->string('nome', 50);
            $table->enum('ciclo', ['prescuola', 'elementari', 'medie', 'superiori', 'universita'])->default('superiori');
            $table->string('descrizione', 100)->nullable();
            $table->integer('ord')->comment('ordine progressivo per ordinare le classi');
            $table->integer('next')->nullable()->comment('tipo di clsse successivo');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::connection('db_scuola')->table('alunni_classi', function (Blueprint $table) {
            $table->foreign(['classe_id'], 'alunni_classi_ibfk_1')->references(['id'])->on('classi')->onDelete('CASCADE');
        });

        Schema::connection('db_scuola')->table('classi', function (Blueprint $table) {
            $table->foreign(['tipo_id'], 'classi_ibfk_1')->references(['id'])->on('tipo');
            $table->foreign(['anno_id'], 'classi_ibfk_2')->references(['id'])->on('anno');
        });

        Schema::connection('db_scuola')->table('coordinatori_classi', function (Blueprint $table) {
            $table->foreign(['classe_id'], 'coordinatori_classi_ibfk_1')->references(['id'])->on('classi')->onDelete('CASCADE');
        });

        Schema::connection('db_scuola')->table('elaborati_coordinatori', function (Blueprint $table) {
            $table->foreign(['coordinatore_id'], 'elaborati_coordinatori_ibfk_1')->references(['id'])->on('persone');
            $table->foreign(['elaborato_id'], 'elaborati_coordinatori_ibfk_2')->references(['id'])->on('elaborati');
        });

        Schema::connection('db_scuola')->table('elaborati_studenti', function (Blueprint $table) {
            $table->foreign(['studente_id'], 'elaborati_studenti_ibfk_1')->references(['id'])->on('persone');
            $table->foreign(['elaborato_id'], 'elaborati_studenti_ibfk_2')->references(['id'])->on('elaborati');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('db_scuola')->table('elaborati_studenti', function (Blueprint $table) {
            $table->dropForeign('elaborati_studenti_ibfk_1');
            $table->dropForeign('elaborati_studenti_ibfk_2');
        });

        Schema::connection('db_scuola')->table('elaborati_coordinatori', function (Blueprint $table) {
            $table->dropForeign('elaborati_coordinatori_ibfk_1');
            $table->dropForeign('elaborati_coordinatori_ibfk_2');
        });

        Schema::connection('db_scuola')->table('coordinatori_classi', function (Blueprint $table) {
            $table->dropForeign('coordinatori_classi_ibfk_1');
        });

        Schema::connection('db_scuola')->table('classi', function (Blueprint $table) {
            $table->dropForeign('classi_ibfk_1');
            $table->dropForeign('classi_ibfk_2');
        });

        Schema::connection('db_scuola')->table('alunni_classi', function (Blueprint $table) {
            $table->dropForeign('alunni_classi_ibfk_1');
        });

        Schema::connection('db_scuola')->dropIfExists('tipo');

        Schema::connection('db_scuola')->dropIfExists('elaborati_studenti');

        Schema::connection('db_scuola')->dropIfExists('elaborati_coordinatori');

        Schema::connection('db_scuola')->dropIfExists('elaborati');

        Schema::connection('db_scuola')->dropIfExists('coordinatori_classi');

        Schema::connection('db_scuola')->dropIfExists('classi');

        Schema::connection('db_scuola')->dropIfExists('anno');

        Schema::connection('db_scuola')->dropIfExists('alunni_classi');
    }
};

<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Domain\Nomadelfia\Azienda\Models\Azienda;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataDallaNascitaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneConFamigliaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMinorenneAccoltoAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataInNomadelfiaAction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NomadelfiaTableSeeder extends Seeder
{
    public function run()
    {
        $this->createGruppiFamiliari()
            ->createAziende()
            ->createPosizioni()
            ->createStati()
            ->createCaricheCostituzionali()
            ->createIncarichi();

        $this->insertFamigliaInPopolazione();

    }

    protected function createGruppiFamiliari(): self
    {
        GruppoFamiliare::factory()
            ->count(12)
            ->sequence(
                ['nome' => 'Cenacolo'],
                ['nome' => 'Diaccialone'],
                ['nome' => 'Nazareth'],
                ['nome' => 'Assunta'],
                ['nome' => 'Betlam Basso'],
                ['nome' => 'Betlem Alto'],
                ['nome' => 'Bruciata'],
                ['nome' => 'Sughera'],
                ['nome' => 'Rosellana'],
                ['nome' => 'Subiaco'],
                ['nome' => 'Poggetto'],
                ['nome' => 'Giovanni Paolo II']
            )->create();

        return $this;
    }

    protected function createAziende(): self
    {

        Azienda::factory()
            ->count(7)
            ->sequence(
                ['nome_azienda' => 'Senza Azienda'],
                ['nome_azienda' => 'Officina Meccanica'],
                ['nome_azienda' => 'Falegnameria'],
                ['nome_azienda' => 'Agraria'],
                ['nome_azienda' => 'Elettrotecnica'],
                ['nome_azienda' => 'Idraulica'],
                ['nome_azienda' => 'scuola']
            )->create();

        return $this;
    }

    protected function createPosizioni(): self
    {
        $data = [
            [
                'abbreviato' => 'DADE',
                'nome' => 'Da Definire',
                'ordinamento' => 5,
            ],
            [
                'abbreviato' => 'EFFE',
                'nome' => 'Effettivo',
                'ordinamento' => 1,
            ],
            [
                'abbreviato' => 'FIGL',
                'nome' => 'Figlio',
                'ordinamento' => 4,
            ], [
                'abbreviato' => 'OSPP',
                'nome' => 'Ospite',
                'ordinamento' => 3,
            ],
            [
                'abbreviato' => 'POST',
                'nome' => 'Postulante',
                'ordinamento' => 2,
            ]];
        DB::connection('db_nomadelfia')->table('posizioni')->insert($data);

        return $this;

    }

    protected function createStati(): self
    {
        $data = [
            [
                'stato' => 'CDE',
                'nome' => 'Celibe di elezione',
            ],
            [
                'stato' => 'CEL',
                'nome' => 'Celibe',
            ],
            [
                'stato' => 'MAM',
                'nome' => 'NOMADELFA MAMMA',
            ],
            [
                'stato' => 'MAV',
                'nome' => 'MAMMA DI VOCAZIONE',
            ],
            [
                'stato' => 'NUB',
                'nome' => 'Nubile',
            ],
            [
                'stato' => 'MAN',
                'nome' => 'MAMMA NUBILE',
            ],
            [
                'stato' => 'SAC',
                'nome' => 'SACERDOTE',
            ],
            [
                'stato' => 'SEP',
                'nome' => 'CONIUGE SEPARATO(A)',
            ],
            [
                'stato' => 'SPO',
                'nome' => 'SPOSATO/A',
            ],
            [
                'stato' => 'VED',
                'nome' => 'VEDOVO/A',
            ],
        ];
        DB::connection('db_nomadelfia')->table('stati')->insert($data);

        return $this;
    }

    protected function createCaricheCostituzionali()
    {
        $data = [
            [
                'nome' => 'Presidente',
                'org' => 'associazione',
                'num' => 1,
                'ord' => 1,
            ],
            [
                'nome' => 'Vicepresidente',  // 2 vicepresidenti
                'org' => 'associazione',
                'num' => 2,
                'ord' => 2,
            ],
            [
                'nome' => 'Economo',
                'org' => 'associazione',
                'num' => 1,
                'ord' => 4,
            ],
            [
                'nome' => 'Vice economo',
                'org' => 'associazione',
                'num' => 2,
                'ord' => 5,
            ],
            [
                'nome' => 'Capogruppo',
                'org' => 'associazione',
                'num' => 12,
                'ord' => 6,
            ],
            [
                'nome' => 'Capogiudice',
                'org' => 'associazione',
                'num' => 1,
                'ord' => 7,
            ],
            [
                'nome' => 'Giudici',
                'org' => 'associazione',
                'num' => 2,
                'ord' => 8,
            ],
            [
                'nome' => 'Consiglio degli anziani',  // 12 persone e 1 è coordinatore
                'org' => 'associazione',
                'num' => 12,
                'ord' => 10,
            ],
            [
                'nome' => 'Consiglio degli anziani - coordinatore',  // 12 persone e 1 è coordinatore
                'org' => 'associazione',
                'num' => 1,
                'ord' => 11,
            ],
            [
                'nome' => 'Congresso dei figli - presidente',
                'org' => 'associazione',
                'num' => 1,
                'ord' => 12,
            ],
            [
                'nome' => 'Congresso dei figli - vicepresidente',
                'org' => 'associazione',
                'num' => 1,
                'ord' => 13,
            ],
            [
                'nome' => 'Congresso dei figli - consigliere',  // 4 consiglieri
                'org' => 'associazione',
                'num' => 4,
                'ord' => 14,
            ],
            // Solidarietà nomadelfia ODV
            [
                'nome' => 'Presidente',
                'org' => 'solidarieta',
                'num' => 1,
                'ord' => 1,
            ],
            [
                'nome' => 'Vicepresidente',
                'org' => 'solidarieta',
                'num' => 1,
                'ord' => 2,
            ],
            [
                'nome' => 'Tesoriere',
                'org' => 'solidarieta',
                'num' => 1,
                'ord' => 3,
            ],
            [
                'nome' => 'Consiglio direttivo', // 2 persone
                'org' => 'solidarieta',
                'num' => 2,
                'ord' => 4,
            ],
            // Fondazione Nomadelfia
            [
                'nome' => 'Presidente',
                'org' => 'fondazione',
                'num' => 1,
                'ord' => 1,

            ],
            [
                'nome' => 'Segretario',
                'org' => 'fondazione',
                'num' => 1,
                'ord' => 2,
            ],
            [
                'nome' => 'Revisori Conti',
                'org' => 'fondazione',
                'num' => 1,
                'ord' => 3,
            ],
            // Cooperativa Agricola
            [
                'nome' => 'Presidente',
                'org' => 'agricola',
                'num' => 1,
                'ord' => 1,
            ],
            [
                'nome' => 'Consigliere',  //  2 consiglieri
                'org' => 'agricola',
                'num' => 2,
                'ord' => 2,
            ],
            [
                'nome' => 'Responsabile Tecnico', // 6 persone
                'org' => 'agricola',
                'num' => 6,
                'ord' => 3,
            ],
            // Cooperativa Culturale
            [
                'nome' => 'Presidente',
                'org' => 'culturale',
                'num' => 1,
                'ord' => 1,
            ],
            [
                'nome' => 'Consiglieri',  // 2 consiglieri
                'org' => 'culturale',
                'num' => 2,
                'ord' => 2,
            ],

        ];
        DB::connection('db_nomadelfia')->table('cariche')->insert($data);

        return $this;
    }

    protected function createIncarichi()
    {
        $data = [
            ['nome' => 'Programmazione TV'],
            ['nome' => 'Commissione liturgia'],
            ['nome' => 'Commissione nuovi media'],
            ['nome' => 'Commissione Sport'],
            ['nome' => 'Commissione feste'],
            ['nome' => 'Approfondimento politico'],
            ['nome' => 'Pastorale giovanile diocesana'],
            ['nome' => 'Pastorale familiare'],
            ['nome' => 'Incontri giovani famiglie'],
            ['nome' => 'Pulizia Chiesa'],
            ['nome' => 'Pulizia Sala don Zeno'],
            ['nome' => 'Pulizia cimitero'],
            ['nome' => 'Resp. Bar'],
            ['nome' => 'Resp. Sala don Zeno'],
            ['nome' => 'Resp. Sala Cultura'],
            ['nome' => 'Resp. Spogliatoi campo sportivo'],
            ['nome' => 'Resp. Cimitero'],
            ['nome' => 'Resp. Biblioteca'],
            ['nome' => 'Resp. Campo sportivo'],
            ['nome' => 'Resp. Ballo'],
            ['nome' => 'Resp. Pallavolo'],
            ['nome' => 'Resp. Ginnastica Posturale'],
            ['nome' => 'Resp. Fitness'],
            ['nome' => 'Affido minori'],
            ['nome' => 'Centro studi di Nomadelfia'],
            ['nome' => 'Coordinatore cultura'],
            ['nome' => 'Ministro Comunione'],
            ['nome' => 'Attenzione ai poveri'],
            ['nome' => 'Pacchi viveri'],
            ['nome' => 'Nomadelfia è una proposta'],
            ['nome' => 'Nomadelfia social'],
            ['nome' => 'Nomadelfia internet'],
            ['nome' => 'Nomadelfia news'],
            ['nome' => ' Pubbliche relazioni'],
            ['nome' => 'Mailing list'],
        ];
        DB::connection('db_nomadelfia')->table('incarichi')->insert($data);

        return $this;
    }

    protected function insertFamigliaInPopolazione(): self
    {
        $gruppo = GruppoFamiliare::all()->random();
        $famiglia = Famiglia::factory()->create();
        $now = Carbon::now()->toDatestring();

        $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
        $moglie = Persona::factory()->maggiorenne()->femmina()->create();
        $fnato = Persona::factory()->minorenne()->femmina()->create();
        $faccolto = Persona::factory()->minorenne()->maschio()->create();

        $act = app(EntrataMaggiorenneConFamigliaAction::class);
        $act->execute($capoFam, $now, $gruppo);
        $act = app(EntrataMaggiorenneConFamigliaAction::class);
//        $act = app(EntrataMaggiorenneConFamigliaAction::class);
        $act->execute($moglie, $now, $gruppo);
        $famiglia->assegnaCapoFamiglia($capoFam, $now);
        $famiglia->assegnaMoglie($moglie, $now);
//        $act = app( EntrataDallaNascitaAction::class);
        $act = app(EntrataDallaNascitaAction::class);
        $act->execute($fnato, Famiglia::findOrFail($famiglia->id));
//        $act = app( EntrataMinorenneAccoltoAction::class);
        $act = app(EntrataMinorenneAccoltoAction::class);
        $act->execute($faccolto, Carbon::now()->addYears(2)->toDatestring(), $famiglia);

        return $this;
    }
}

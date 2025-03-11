<?php

declare(strict_types=1);

use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public $connection = 'db_nomadelfia';

    public function up(): void
    {
        $this->insertAltroCliente();
        $this->createGruppiFamiliari();
        $this->createAziende();
        $this->createPosizioni();
        $this->createStati();
        $this->createCaricheCostituzionali();
        $this->createIncarichi();
    }

    public function down(): void
    {
        //
    }

    protected function insertAltroCliente(): void
    {
        $nominativo = 'Altro Cliente';
        DB::connection('db_nomadelfia')->table('persone')->insert(
            [
                'nominativo' => $nominativo,
                'sesso' => 'M',
                'nome' => 'Altro',
                'cognome' => 'Cliente',
                'provincia_nascita' => 'Grosseto',
                'data_nascita' => '1900-01-01',
                'id_arch_pietro' => 0,
            ]
        );

        // By convention "Altro Cliente" has id=0 and it is used to when the person is not present into the system.
        $persona = Persona::where('nominativo', $nominativo)->first();
        $persona->id = 0;
        $persona->save();
    }

    protected function createGruppiFamiliari(): void
    {
        $gruppi = [
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
            ['nome' => 'Giovanni Paolo II'],
        ];
        DB::connection('db_nomadelfia')->table('gruppi_familiari')->insert($gruppi);
    }

    protected function createAziende(): void
    {

        $aziende = [
            ['nome_azienda' => 'Senza Azienda'],
            ['nome_azienda' => 'Officina Meccanica'],
            ['nome_azienda' => 'Falegnameria'],
            ['nome_azienda' => 'Agraria'],
            ['nome_azienda' => 'Elettrotecnica'],
            ['nome_azienda' => 'Idraulica'],
            ['nome_azienda' => 'scuola'],
        ];

        DB::connection('db_nomadelfia')->table('aziende')->insert($aziende);
    }

    protected function createPosizioni(): void
    {
        $data = [
            [
                'abbreviato' => 'DADE', 'nome' => 'Da Definire', 'ordinamento' => 5,
            ],
            [
                'abbreviato' => 'EFFE', 'nome' => 'Effettivo', 'ordinamento' => 1,
            ],
            [
                'abbreviato' => 'FIGL', 'nome' => 'Figlio', 'ordinamento' => 4,
            ],
            [
                'abbreviato' => 'OSPP', 'nome' => 'Ospite', 'ordinamento' => 3,
            ],
            [
                'abbreviato' => 'POST', 'nome' => 'Postulante', 'ordinamento' => 2,
            ]];
        DB::connection('db_nomadelfia')->table('posizioni')->insert($data);

    }

    protected function createStati(): void
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
    }

    protected function createCaricheCostituzionali(): void
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
    }
};

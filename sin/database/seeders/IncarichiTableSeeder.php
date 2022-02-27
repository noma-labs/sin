<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class IncarichiTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'nome' => 'Programmazione TV',
            ],
            [
                'nome' => 'Commissione liturgia',

            ],
            [
                'nome' => 'Commissione nuovi media',
            ],
            [
                'nome' => 'Commissione Sport',
            ],
            [
                'nome' => 'Commissione feste',
            ],
            [
                'nome' => 'Approfondimento politico',
            ],
            [
                'nome' => 'Pastorale giovanile diocesana',
            ],
            [
                'nome' => 'Pastorale familiare',
            ],
            [
                'nome' => 'Incontri giovani famiglie',
            ],
            [
                'nome' => 'Pulizia Chiesa',
            ],
            [
                'nome' => 'Pulizia Sala don Zeno',
            ]
            ,
            [
                'nome' => 'Pulizia cimitero',
            ]
            ,
            [
                'nome' => 'Resp. Bar',
            ]
            ,
            [
                'nome' => 'Resp. Sala don Zeno',
            ]
            ,
            [
                'nome' => 'Resp. Sala Cultura',
            ],
            [
                'nome' => 'Resp. Spogliatoi campo sportivo',
            ],
            [
                'nome' => 'Resp. Cimitero',
            ],
            [
                'nome' => 'Resp. Biblioteca',
            ],
            [
                'nome' => 'Resp. Campo sportivo',
            ],
            [
                'nome' => 'Resp. Ballo',
            ],
            [
                'nome' => 'Resp. Pallavolo',
            ],
            [
                'nome' => 'Resp. Ginnastica Posturale',
            ],
            [
                'nome' => 'Resp. Fitness',
            ],
            [
                'nome' => 'Affido minori',
            ],
            [
                'nome' => 'Centro studi di Nomadelfia',
            ],
            [
                'nome' => 'Coordinatore cultura',
            ],
            [
                'nome' => 'Ministro Comunione',
            ],
            [
                'nome' => 'Attenzione ai poveri',
            ],
            [
                'nome' => 'Pacchi viveri',
            ],
            [
                'nome' => 'Nomadelfia è una proposta',
            ],
            [
                'nome' => 'Nomadelfia social',
            ],
            [
                'nome' => 'Nomadelfia internet',
            ],
            [
                'nome' => 'Nomadelfia news',
            ],
            [
                'nome' => ' Pubbliche relazioni',
            ],
            [
                'nome' => 'Mailing list',
            ],
            [
                'nome' => 'Ringraziamenti',
            ],
            [
                'nome' => 'Resp. Pulizia polli',
            ],
            [
                'nome' => 'Raccolta differenziata',
            ],
            [
                'nome' => 'Smaltimento rifiuti',
            ],
            [
                'nome' => 'Centralino',
            ],
            [
                'nome' => 'Controllo estintori',
            ],
            [
                'nome' => 'Squadra Antincendio',
            ],
            [
                'nome' => 'Patenti di guida',
            ],
            [
                'nome' => 'Tessere sanitarie',
            ],
            [
                'nome' => 'Ufficio amministrativo',
            ],
            [
                'nome' => 'Acquisti carta credito',
            ],
            [
                'nome' => 'Rapporti con la FIPS (pesca)',
            ],
            [
                'nome' => 'Giri autobus',
            ]
        ];
        DB::connection('db_nomadelfia')->table('incarichi')->insert($data);
    }
}

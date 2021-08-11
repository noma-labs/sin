<?php

use Illuminate\Database\Seeder;


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
                'nome_azienda' => 'Programmazione TV',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Commissione liturgia',
                "tipo" => 'incarico'

            ],
            [
                'nome_azienda' => 'Commissione nuovi media',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Commissione Sport',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Commissione feste',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Approfondimento politico',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Pastorale giovanile diocesana',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Pastorale familiare',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Incontri giovani famiglie',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Pulizia Chiesa',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Pulizia Sala don Zeno',
                "tipo" => 'incarico'
            ]
            ,
            [
                'nome_azienda' => 'Pulizia cimitero',
                "tipo" => 'incarico'
            ]
            ,
            [
                'nome_azienda' => 'Resp. Bar',
                "tipo" => 'incarico'
            ]
            ,
            [
                'nome_azienda' => 'Resp. Sala don Zeno',
                "tipo" => 'incarico'
            ]
            ,
            [
                'nome_azienda' => 'Resp. Sala Cultura',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Resp. Spogliatoi campo sportivo',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Resp. Cimitero',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Resp. Biblioteca',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Resp. Campo sportivo',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Resp. Ballo',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Resp. Pallavolo',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Resp. Ginnastica Posturale',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Resp. Fitness',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Affido minori',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Centro studi di Nomadelfia',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Coordinatore cultura',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Ministro Comunione',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Attenzione ai poveri',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Pacchi viveri',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Nomadelfia è una proposta',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Nomadelfia social',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Nomadelfia internet',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Nomadelfia news',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => ' Pubbliche relazioni',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Mailing list',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Ringraziamenti',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Resp. Pulizia polli',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Raccolta differenziata',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Smaltimento rifiuti',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Centralino',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Controllo estintori',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Squadra Antincendio',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Patenti di guida',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Tessere sanitarie',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Ufficio amministrativo',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Acquisti carta credito',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Rapporti con la FIPS (pesca)',
                "tipo" => 'incarico'
            ],
            [
                'nome_azienda' => 'Giri autobus',
                "tipo" => 'incarico'
            ]
        ];
        DB::connection('db_nomadelfia')->table('aziende')->insert($data);
    }
}

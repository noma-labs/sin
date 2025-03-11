<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class MezzoAgricoloTableSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
            'nome' => 'KUBOTA M7060',
            'tot_ore' => 940,
            'numero_telaio' => 'M7060D61391',
            'filtro_olio' => 'KUBOTA W21ESO1C00',
            'filtro_gasolio' => '1J800-43170',
            'filtro_servizi' => 'KUBOTA W21TSHTA10',
            'filtro_aria_int' => 'DONALDSON P829332',
            'filtro_aria_ext' => 'DONALDSON P827653',
            'gomme_ant' => 2,
            'gomme_post' => 1,
        ],[
            'nome' => 'LANDINI  POWERFARM 60',
            'tot_ore' => 8237,
            'numero_telaio' => 'LMBLX51068',
            'filtro_olio' => 'Perkins 2654403',
            'filtro_gasolio' => 'PPA GF45',
            'filtro_servizi' => 'cartuccia',
            'filtro_aria_int' => 'P77-5300',
            'filtro_aria_ext' => 'ARGO 3540048M1',
            'gomme_ant' => 4,
            'gomme_post' => 3,
        ],[
            'nome' => 'LANDINI EVOLUTION',
            'tot_ore' => 9532,
            'numero_telaio' => 'THWLT38202',
            'filtro_olio' => 'Perkins 2654408',
            'filtro_gasolio' => 'COOPERS FIAAM FT4788',
            'filtro_servizi' => 'cartuccia',
            'filtro_aria_int' => 'P77-5300',
            'filtro_aria_ext' => 'ARGO 3540046M1',
            'gomme_ant' => 2,
            'gomme_post' => 5,
        ],[
            'nome' => 'LANDINI ATLANTIS 90',
            'tot_ore' => 6910,
            'numero_telaio' => 'LECLM07188',
            'filtro_olio' => 'SCAR TECH 030026026',
            'filtro_gasolio' => '_030026006',
            'filtro_servizi' => 'cartuccia',
            'filtro_aria_int' => 'P77-5302',
            'filtro_aria_ext' => '_030026045',
            'gomme_ant' => 7,
            'gomme_post' => 6,
        ],[
            'nome' => 'LANDINI LANDPOWER 125',
            'tot_ore' => 6180,
            'numero_telaio' => 'SJMLW24029',
            'filtro_olio' => 'ecoline E3541580M1',
            'filtro_gasolio' => 'NEW HOLLAND 87840590',
            'filtro_servizi' => 'O PARTS 3684239M91',
            'filtro_aria_int' => 'Landini 3671171M1',
            'filtro_aria_ext' => 'Landini 3671170M1',
            'gomme_ant' => 9,
            'gomme_post' => 8,
        ],[
            'nome' => 'NEW HOLLAND T3030',
            'tot_ore' => 2386,
            'numero_telaio' => 'HSSN24546',
            'filtro_olio' => 'Donaldson P550162',
            'filtro_gasolio' => 'P550048',
            'filtro_servizi' => 'P171611',
            'filtro_aria_int' => 'DONALDSON P822858',
            'filtro_aria_ext' => 'Scar tech 020026043',
            'gomme_ant' => 11,
            'gomme_post' => 10,
        ]];
        DB::connection('db_agraria')->table('mezzo_agricolo')->insert($data);
    }
}

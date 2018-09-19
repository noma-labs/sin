<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class NomadelfiaTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
     public function run()
     {
       if (App::environment() === 'production') exit();
       // Reset cached roles and permissions
       app()['cache']->forget('spatie.permission.cache');


       // Truncate all tables, except migrations
        // $tables = DB::select('SHOW TABLES');
        // foreach ($tables as $table) {
        //     if ($table->Tables_in_archivio_auth !== 'migrations')
        //         DB::table($table->Tables_in_archivio_auth)->truncate();
        // }

        //famiglia Daniele e Silvia
       $famiglia = new Famiglia(['nome_famiglia' => 'prova']);
       $famiglia->save();

       $daniele = Persona::find(74);
       $silvia = Persona::find(273);

       $gioia = Persona::find(134);
       $gionata = Persona::find(135);
       $davide = Persona::find(313);
       $stella = Persona::find(286);
       $samuele = Persona::find(263);
       $luca = Persona::find(176);
       $enza = Persona::find(104);
       $diletta = Persona::find(181);

       //assegna i  componenti alla famiglia di Daniele e Silvia
      $famiglia->componenti()->attach([ $daniele->id,
                                        $silvia->id,
                                        $gioia->id,
                                        $gionata->id,
                                        $davide->id,
                                        $stella->id,
                                        $samuele->id,
                                        $luca->id,
                                        $enza->id,
                                        $diletta->id
                                       ]);

      $gruppo = GruppoFamiliare::find(1);
      $gruppo->membri()->attach([ $daniele->id,
                                        $silvia->id,
                                        $gioia->id,
                                        $gionata->id,
                                        $davide->id,
                                        $stella->id,
                                        $samuele->id,
                                        $luca->id,
                                        $enza->id,
                                        $diletta->id
                                       ]);




     }
}

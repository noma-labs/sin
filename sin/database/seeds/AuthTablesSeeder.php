<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Admin\Models\Sistema;
use App\Admin\Models\Permission;

class AuthTablesSeeder extends Seeder
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
        $tables = DB::select('SHOW TABLES');
        foreach ($tables as $table) {
            if ($table->Tables_in_db_nomadelfia_test !== 'migrations')
                DB::table($table->Tables_in_db_nomadelfia_test)->truncate();
        }

        //Creazione dei sistemi
        Sistema::create(['nome'=>'Popolazione Nomadelfia', 'descrizione'=>"Gestione della popolazione di Nomadelfia"]);
        Sistema::create(['nome'=>'Anagrafe', 'descrizione'=>"Gestione dati anagrafici della popolazione attuale"]);
        Sistema::create(['nome'=>'Autenticazione', 'descrizione'=>"Gestione dei ruoli e permessi degli utentid"]);
        Sistema::create(['nome'=>'Biblioteca', 'descrizione'=>"Gestione libri biblioteca"]);
        Sistema::create(['nome'=>'Meccaniza', 'descrizione'=>"Gestione autoveicoli e prenotazioni azienda meccanica."]);
        Sistema::create(['nome'=>'Patente', 'descrizione'=>"Gestione delle patenti delle persone."]);
        Sistema::create(['nome'=>'Scuola', 'descrizione'=>"Gestione degli alunni della scuola familiare di Nomadelfia."]);

       // create permissions
       Permission::create(['name'=>'Administer roles & permissions']);
       Permission::create(['name'=>'admin-roles']);
       Permission::create(['name'=>'admin-permissions']);
       Permission::create(['name'=>'admin-users']);

       //autori
       Permission::create(['name'=>'autore-crea','_belong_to_archivio'=>"biblioteca"]);
       Permission::create(['name'=>'autore-visualizza','_belong_to_archivio'=>"biblioteca"]);
       Permission::create(['name'=>'autore-modifica','_belong_to_archivio'=>"biblioteca"]);
       Permission::create(['name'=>'autore-elimina','_belong_to_archivio'=>"biblioteca"]);

       //editori
       Permission::create(['name'=>'editore-crea','_belong_to_archivio'=>"biblioteca"]);
       Permission::create(['name'=>'editore-visualizza','_belong_to_archivio'=>"biblioteca"]);
       Permission::create(['name'=>'editore-modifica','_belong_to_archivio'=>"biblioteca"]);
       Permission::create(['name'=>'editore-elimina','_belong_to_archivio'=>"biblioteca"]);

       //libro
       Permission::create(['name'=>'libro-crea','_belong_to_archivio'=>"biblioteca"]);
       Permission::create(['name'=>'libro-visualizza','_belong_to_archivio'=>"biblioteca"]);
       Permission::create(['name'=>'libro-modifica','_belong_to_archivio'=>"biblioteca"]);
       Permission::create(['name'=>'libro-elimina','_belong_to_archivio'=>"biblioteca"]);

       //libro media
       Permission::create(['name'=>'libro-media-crea','_belong_to_archivio'=>"biblioteca"]);
       Permission::create(['name'=>'libro-media-visualizza','_belong_to_archivio'=>"biblioteca"]);
       Permission::create(['name'=>'libro-media-modifica','_belong_to_archivio'=>"biblioteca"]);
       Permission::create(['name'=>'libro-media-elimina','_belong_to_archivio'=>"biblioteca"]);

       // prestito libro
       Permission::create(['name'=>'libro-prestito-crea','_belong_to_archivio'=>"biblioteca"]);
       Permission::create(['name'=>'libro-prestito-visualizza','_belong_to_archivio'=>"biblioteca"]);
       Permission::create(['name'=>'libro-prestito-modifica','_belong_to_archivio'=>"biblioteca"]);
       Permission::create(['name'=>'libro-prestito-elimina','_belong_to_archivio'=>"biblioteca"]);

       //cliente
       Permission::create(['name'=>'cliente-crea','_belong_to_archivio'=>"biblioteca"]);
       Permission::create(['name'=>'cliente-visualizza','_belong_to_archivio'=>"biblioteca"]);
       Permission::create(['name'=>'cliente-modifica','_belong_to_archivio'=>"biblioteca"]);
       Permission::create(['name'=>'cliente-elimina','_belong_to_archivio'=>"biblioteca"]);

       // etichetta
       Permission::create(['name'=>'etichetta-crea','_belong_to_archivio'=>"biblioteca"]);
       Permission::create(['name'=>'etichetta-visualizza','_belong_to_archivio'=>"biblioteca"]);
       Permission::create(['name'=>'etichetta-modifica','_belong_to_archivio'=>"biblioteca"]);
       Permission::create(['name'=>'etichetta-elimina','_belong_to_archivio'=>"biblioteca"]);

       Permission::create(['name'=>'etichetta-anteprima','_belong_to_archivio'=>"biblioteca"]);
       Permission::create(['name'=>'etichetta-stampa','_belong_to_archivio'=>"biblioteca"]);

       //prenotazioni officina
       Permission::create(['name'=>'veicoli-prenotazione','_belong_to_archivio'=>"officina"]);


       //create Role
       $roleAdmin = Role::create(['name' => 'Admin']);
       $roleAdmin->givePermissionTo('Administer roles & permissions');
       $roleAdmin->givePermissionTo('admin-roles');
       $roleAdmin->givePermissionTo('admin-permissions');
       $roleAdmin->givePermissionTo('admin-users');

       $roleBiblio = Role::create(['name' => 'biblioteca']);

       $roleBiblio->givePermissionTo('autore-crea');
       $roleBiblio->givePermissionTo('autore-visualizza');
       $roleBiblio->givePermissionTo('autore-modifica');
       $roleBiblio->givePermissionTo('autore-elimina');
       $roleBiblio->givePermissionTo('editore-crea');
       $roleBiblio->givePermissionTo('editore-visualizza');
       $roleBiblio->givePermissionTo('editore-modifica');
       $roleBiblio->givePermissionTo('editore-elimina');
       $roleBiblio->givePermissionTo('libro-crea');
       $roleBiblio->givePermissionTo('libro-visualizza');
       $roleBiblio->givePermissionTo('libro-modifica');
       $roleBiblio->givePermissionTo('libro-elimina');
       $roleBiblio->givePermissionTo('libro-prestito-crea');
       $roleBiblio->givePermissionTo('libro-prestito-visualizza');
       $roleBiblio->givePermissionTo('libro-prestito-modifica');
       $roleBiblio->givePermissionTo('libro-prestito-elimina');
       $roleBiblio->givePermissionTo('libro-media-crea');
       $roleBiblio->givePermissionTo('libro-media-visualizza');
       $roleBiblio->givePermissionTo('libro-media-modifica');
       $roleBiblio->givePermissionTo('libro-media-elimina');
       $roleBiblio->givePermissionTo('cliente-crea');
       $roleBiblio->givePermissionTo('cliente-visualizza');
       $roleBiblio->givePermissionTo('cliente-modifica');
       $roleBiblio->givePermissionTo('cliente-elimina');
       $roleBiblio->givePermissionTo('etichetta-crea');
       $roleBiblio->givePermissionTo('etichetta-visualizza');
       $roleBiblio->givePermissionTo('etichetta-modifica');
       $roleBiblio->givePermissionTo('etichetta-elimina');
       $roleBiblio->givePermissionTo('etichetta-anteprima');
       $roleBiblio->givePermissionTo('etichetta-stampa');

       $roleOfficina = Role::create(['name' => 'officina']);

       $roleOfficina->givePermissionTo('veicoli-prenotazione');

       $roleRtn = Role::create(['name' => 'RTN']);
       Permission::create(['name'=>'video-creazione','_belong_to_archivio'=>"rtn"]);
       $roleRtn->givePermissionTo('video-creazione');

       $userAdmin = App\Admin\Models\User::create([
             'username' => 'Admin',
             'email' => 'archivio@nomadelfia.it',
             'password' => 'nomadelfia',
             'persona_id' => 0
       ]);
       $userLice = App\Admin\Models\User::create([
             'username' => 'Lice',
             'email' => 'archivio@nomadelfia.it',
             'password' => 'biblioteca',
             'persona_id' => 167
       ]);

       // Adding permissions via a role
       $userAdmin->assignRole('Admin');
       $userLice->assignRole('biblioteca');


     }
}

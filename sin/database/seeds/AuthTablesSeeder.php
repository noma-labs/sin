<?php

use Illuminate\Database\Seeder;
use App\Admin\Models\Ruolo;
use App\Admin\Models\Sistema;
use App\Admin\Models\Risorsa;

class AuthTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment() === 'production') {
            exit();
        }
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Creazione dei sistemi
        $nomadelfia = Sistema::create(['nome'=>'Popolazione Nomadelfia', 'descrizione'=>"Gestione della popolazione di Nomadelfia"]);
        $anagrafe = Sistema::create(['nome'=>'Anagrafe', 'descrizione'=>"Gestione dati anagrafici della popolazione attuale"]);
        $auth    = Sistema::create(['nome'=>'Autenticazione', 'descrizione'=>"Gestione dei ruoli e permessi degli utentid"]);
        $biblioteca = Sistema::create(['nome'=>'Biblioteca', 'descrizione'=>"Gestione libri biblioteca"]);
        $meccanica = Sistema::create(['nome'=>'Meccaniza', 'descrizione'=>"Gestione autoveicoli e prenotazioni azienda meccanica."]);
        $patente = Sistema::create(['nome'=>'Patente', 'descrizione'=>"Gestione delle patenti delle persone."]);
        $scuola = Sistema::create(['nome'=>'Scuola', 'descrizione'=>"Gestione degli alunni della scuola familiare di Nomadelfia."]);
        
        // Creazione dei ruoli
        $roleAdmin = Ruolo::create(['nome' => 'Admin', 'descrizione'=>'Amministratore del sistema (utenti, ruoli, permessi, backup,logs).']);
        $presAdmin = Ruolo::create(['nome' => 'presidenza-amm', 'descrizione'=>'Amminstratore della presidenza']);
        $meccAdmin = Ruolo::create(['nome' => 'meccanica-amm', 'descrizione'=>'Amminstratore della meccanica con tutti i permessi.']);
        $biblioAdmin = Ruolo::create(['nome' => 'biblioteca-amm', 'descrizione'=>'Amministratore della biblioteca']);
        $scuolaAdmin = Ruolo::create(['nome' => 'scuola-amm', 'descrizione'=>'Amministratore della scuola di Nomadelfia']);
        $master = Ruolo::create(['nome' => 'master', 'descrizione'=>'A tutti i permessi su tutte le risorse dei sistemi.']);
        
        // Creazione delle risorse
        $persona = Risorsa::create(['nome' => 'persona', 'sistema_id'=>$nomadelfia->id]);
        $veicolo = Risorsa::create(['nome' => 'veicolo', 'sistema_id'=>$meccanica->id]);
        $libro = Risorsa::create(['nome' => 'libro', 'sistema_id'=>$scuola->id]);
        $autore = Risorsa::create(['nome' => 'autore', 'sistema_id'=>$scuola->id]);
        Risorsa::create(['nome' => 'editore', 'sistema_id'=>$scuola->id]);
        Risorsa::create(['nome' => 'video', 'sistema_id'=>$scuola->id]);
        Risorsa::create(['nome' => 'etichetta', 'sistema_id'=>$scuola->id]);
        Risorsa::create(['nome' => 'film', 'sistema_id'=>$scuola->id]);
        Risorsa::create(['nome' => 'professionale', 'sistema_id'=>$scuola->id]);
        $patente = Risorsa::create(['nome' => 'patente', 'sistema_id'=>$patente->id]);
        $classi = Risorsa::create(['nome' => 'classe', 'sistema_id'=>$scuola->id]);

        // Gestione dei permessi di ogni risorse dei ruoli
        // `ruolo_id`  `risorsa_id` => `visualizza`, `inserisci`, `elimina`, `modifica`, `prenota`, `esporta`, `svuota`
        $master->risorse()->save($persona, [
                                                "visualizza" => "1",
                                                "inserisci" => "1",
                                                "elimina" => "1",
                                                "modifica" => "1",
                                                "prenota" => "1",
                                                "esporta" => "1",
                                                "svuota" => "1",
        ]);
        $master->risorse()->save($veicolo, [
            "visualizza" => "1",
            "inserisci" => "1",
            "elimina" => "1",
            "modifica" => "1",
            "prenota" => "1",
            "esporta" => "1",
            "svuota" => "1",
        ]);
        $master->risorse()->save($patente, [
            "visualizza" => "1",
            "inserisci" => "1",
            "elimina" => "1",
            "modifica" => "1",
            "prenota" => "1",
            "esporta" => "1",
            "svuota" => "1",
        ]);
        $master->risorse()->save($classi, [
            "visualizza" => "1",
            "inserisci" => "1",
            "elimina" => "1",
            "modifica" => "1",
            "prenota" => "1",
            "esporta" => "1",
            "svuota" => "1",
        ]);

        // creazione degli utenti
        $userAdmin = App\Admin\Models\User::create([
            'username' => 'Admin',
            'email' => 'archivio@nomadelfia.it',
            'password' => 'nomadelfia',
            'persona_id' => 0
       ]);

        // Assegnamento degli utenti ai ruoli
        $userAdmin->assignRole($master);


    /*
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
       */
    }
}

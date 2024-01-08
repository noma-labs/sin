<?php

namespace Database\Seeders;

use App\Admin\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class LocalEnvironmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createDefaultAdminUser();
        $this->call(NomadelfiaTableSeeder::class);
        $this->call(OfficinaMeccanicaTableSeeder::class);
        $this->call(BibliotecaTableSeeder::class);
        $this->call(ScuolaTableSeeder::class);
        $this->call(PhotoTableSeeder::class);
    }

    protected function createDefaultAdminUser(): self
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        $superAdmin = Role::create(['name' => 'super-admin']);
        $presidenteAmmRole = Role::create(['name' => 'presidenza-amm']);
        $presidenteOpeRole = Role::create(['name' => 'presidenza-ope']);
        $meccanicaAmmRole = Role::create(['name' => 'meccanica-amm']);
        $meccanicaOpeRole = Role::create(['name' => 'meccanica-ope']);
        $bibliotecaAmmRole = Role::create(['name' => 'biblioteca-amm']);
        $bibliotecaOpeRole = Role::create(['name' => 'biblioteca-ope']);
        $patenteAmmRole = Role::create(['name' => 'patente-amm']);
        $scuolaAmmRole = Role::create(['name' => 'scuola-amm']);
        $rtnAmmRole = Role::create(['name' => 'rtn-amm']);
        $rtnOpeRole = Role::create(['name' => 'rtn-ope']);
        $agrariaAmmRole = Role::create(['name' => 'agraria-amm']);

        Permission::create(['name' => 'popolazione.*']);
        Permission::create(['name' => 'popolazione.visualizza']);
        Permission::create(['name' => 'popolazione.persona.*']);
        Permission::create(['name' => 'popolazione.persona.visualizza']);
        Permission::create(['name' => 'popolazione.persona.inserisci']);
        Permission::create(['name' => 'popolazione.persona.elimina']);
        Permission::create(['name' => 'popolazione.persona.modifica']);
        Permission::create(['name' => 'popolazione.persona.esporta']);

        Permission::create(['name' => 'scuolaguida.*']);
        Permission::create(['name' => 'scuolaguida.visualizza']);
        Permission::create(['name' => 'scuolaguida.patente.*']);
        Permission::create(['name' => 'scuolaguida.patente.visualizza']);
        Permission::create(['name' => 'scuolaguida.patente.inserisci']);
        Permission::create(['name' => 'scuolaguida.patente.elimina']);
        Permission::create(['name' => 'scuolaguida.patente.modifica']);
        Permission::create(['name' => 'scuolaguida.patente.esporta']);

        Permission::create(['name' => 'meccanica.*']);
        Permission::create(['name' => 'meccanica.visualizza']);
        Permission::create(['name' => 'meccanica.veicolo.*']);
        Permission::create(['name' => 'meccanica.veicolo.visualizza']);
        Permission::create(['name' => 'meccanica.veicolo.inserisci']);
        Permission::create(['name' => 'meccanica.veicolo.prenota']);
        Permission::create(['name' => 'meccanica.veicolo.elimina']);
        Permission::create(['name' => 'meccanica.veicolo.modifica']);
        Permission::create(['name' => 'meccanica.prenotazione.*']);
        Permission::create(['name' => 'meccanica.prenotazione.visualizza']);
        Permission::create(['name' => 'meccanica.prenotazione.inserisci']);
        Permission::create(['name' => 'meccanica.prenotazione.elimina']);
        Permission::create(['name' => 'meccanica.prenotazione.modifica']);

        Permission::create(['name' => 'biblioteca.*']);
        Permission::create(['name' => 'biblioteca.visualizza']);
        Permission::create(['name' => 'biblioteca.libro.*']);
        Permission::create(['name' => 'biblioteca.libro.visualizza']);
        Permission::create(['name' => 'biblioteca.libro.inserisci']);
        Permission::create(['name' => 'biblioteca.libro.elimina']);
        Permission::create(['name' => 'biblioteca.libro.modifica']);
        Permission::create(['name' => 'biblioteca.libro.prenota']);
        Permission::create(['name' => 'biblioteca.etichetta.*']);
        Permission::create(['name' => 'biblioteca.etichetta.visualizza']);
        Permission::create(['name' => 'biblioteca.etichetta.inserisci']);
        Permission::create(['name' => 'biblioteca.etichetta.elimina']);
        Permission::create(['name' => 'biblioteca.etichetta.modifica']);
        Permission::create(['name' => 'biblioteca.etichetta.esporta']);
        Permission::create(['name' => 'biblioteca.autore.*']);
        Permission::create(['name' => 'biblioteca.autore.visualizza']);
        Permission::create(['name' => 'biblioteca.autore.inserisci']);
        Permission::create(['name' => 'biblioteca.editore.*']);
        Permission::create(['name' => 'biblioteca.editore.visualizza']);
        Permission::create(['name' => 'biblioteca.editore.inserisci']);

        Permission::create(['name' => 'scuola.*']);
        Permission::create(['name' => 'scuola.visualizza']);

        Permission::create(['name' => 'agraria.*']);
        Permission::create(['name' => 'agraria.visualizza']);

        Permission::create(['name' => 'rtn.*']);
        Permission::create(['name' => 'rtn.visualizza']);

        Permission::create(['name' => 'archivio.*']);
        Permission::create(['name' => 'archivio.visualizza']);

        $presidenteAmmRole->givePermissionTo('popolazione.*');
        $presidenteAmmRole->givePermissionTo('archivio.*');
        $presidenteAmmRole->givePermissionTo('meccanica.visualizza');
        $presidenteAmmRole->givePermissionTo('meccanica.veicolo.visualizza');
        $presidenteAmmRole->givePermissionTo('biblioteca.visualizza');
        $presidenteAmmRole->givePermissionTo('biblioteca.libro.visualizza');
        $presidenteAmmRole->givePermissionTo('biblioteca.autore.visualizza');
        $presidenteAmmRole->givePermissionTo('biblioteca.editore.visualizza');
        $presidenteAmmRole->givePermissionTo('scuolaguida.visualizza');
        $presidenteAmmRole->givePermissionTo('scuolaguida.patente.visualizza');
        $presidenteAmmRole->givePermissionTo('rtn.visualizza');
        $presidenteAmmRole->givePermissionTo('agraria.visualizza');
        $presidenteAmmRole->givePermissionTo('scuola.visualizza');

        $presidenteOpeRole->givePermissionTo('popolazione.visualizza');
        $presidenteOpeRole->givePermissionTo('popolazione.persona.visualizza');
        $presidenteOpeRole->givePermissionTo('scuolaguida.visualizza');
        $presidenteOpeRole->givePermissionTo('scuolaguida.patente.visualizza');
        $presidenteOpeRole->givePermissionTo('scuolaguida.patente.esporta');

        $patenteAmmRole->givePermissionTo('scuolaguida.*');

        $meccanicaAmmRole->givePermissionTo('meccanica.*');
        $meccanicaAmmRole->givePermissionTo('scuolaguida.*');

        $meccanicaOpeRole->givePermissionTo('meccanica.visualizza');
        $meccanicaOpeRole->givePermissionTo('meccanica.veicolo.visualizza');
        $meccanicaOpeRole->givePermissionTo('meccanica.veicolo.visualizza');
        $meccanicaOpeRole->givePermissionTo('meccanica.veicolo.inserisci');
        $meccanicaOpeRole->givePermissionTo('meccanica.veicolo.prenota');
        $meccanicaOpeRole->givePermissionTo('meccanica.prenotazione.visualizza');
        $meccanicaOpeRole->givePermissionTo('meccanica.prenotazione.inserisci');
        $meccanicaOpeRole->givePermissionTo('meccanica.prenotazione.modifica');
        $meccanicaOpeRole->givePermissionTo('scuolaguida.patente.visualizza');
        $meccanicaOpeRole->givePermissionTo('scuolaguida.patente.esporta');

        $bibliotecaAmmRole->givePermissionTo('biblioteca.*');

        $bibliotecaOpeRole->givePermissionTo('biblioteca.visualizza');
        $bibliotecaOpeRole->givePermissionTo('biblioteca.libro.visualizza');
        $bibliotecaOpeRole->givePermissionTo('biblioteca.libro.inserisci');
        $bibliotecaOpeRole->givePermissionTo('biblioteca.libro.prenota');
        $bibliotecaOpeRole->givePermissionTo('biblioteca.etichetta.visualizza');
        $bibliotecaOpeRole->givePermissionTo('biblioteca.etichetta.inserisci');
        $bibliotecaOpeRole->givePermissionTo('biblioteca.etichetta.esporta');
        $bibliotecaOpeRole->givePermissionTo('biblioteca.editore.visualizza');
        $bibliotecaOpeRole->givePermissionTo('biblioteca.editore.inserisci');
        $bibliotecaOpeRole->givePermissionTo('biblioteca.autore.visualizza');
        $bibliotecaOpeRole->givePermissionTo('biblioteca.autore.inserisci');

        $scuolaAmmRole->givePermissionTo('scuola.*');

        $rtnAmmRole->givePermissionTo('rtn.*');
        $rtnOpeRole->givePermissionTo('rtn.visualizza');

        $agrariaAmmRole->givePermissionTo('agraria.*');

        $userAdmin = User::create(['username' => 'admin', 'email' => 'admin@email.it', 'password' => 'admin', 'persona_id' => 0]);
        $userAdmin->assignRole($superAdmin);

        return $this;
    }
}

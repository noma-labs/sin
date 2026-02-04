<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Admin\Models\User;
use App\Photo\Controllers\PhotoController;
use App\Photo\Models\DbfAll;
use App\Photo\Models\Photo;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\get;

it('index photos', function (): void {
    $photo = Photo::factory()->create();

    login();

    get(action([PhotoController::class, 'index']))
        ->assertSuccessful()
        ->assertSee($photo->taken_at->format('d/m/Y'));
});

it('a photo-ope roel can see only the photo card in the home page', function (): void {
    $photoOpe = User::create(['username' => 'i-am-an-user-for-photo', 'email' => 'archivio@nomadelfia.it', 'password' => 'nomadelfia', 'persona_id' => 0]);
    $photoOpe->assignRole(Role::findByName('photo-ope'));

    login($photoOpe);

    get('/home')
        ->assertSuccessful()
        ->assertSee('Foto')
        ->assertDontSee('Biblioteca')
        ->assertDontSee('Gestione Nomadelfia')
        ->assertDontSee('Officina')
        ->assertDontSee('Amministratore')
        ->assertDontSee('Agraria')
        ->assertDontSee('Gestione Scuola')
        ->assertDontSee('Patenti');
});

it('show photo system to logged user', function (): void {
    $notSuperAdmin = User::create(['username' => 'i-am-an-user', 'email' => 'archivio@nomadelfia.it', 'password' => 'nomadelfia', 'persona_id' => 0]);
    $notSuperAdmin->assignRole(Role::findByName('meccanica-amm'));

    login($notSuperAdmin);

    get(action([PhotoController::class, 'index']))
        ->assertForbidden();
});

it('filters photos without strip', function (): void {
    // Create a stripe (dbf_all) entry and link a photo to it
    $dbf = DbfAll::create([
        'fingerprint' => null,
        'source' => 'foto',
        'datnum' => '00001',
        'anum' => '00001',
        'cddvd' => 'CD000001',
        'hdint' => 'HDINT001',
        'hdext' => 'HDEXT001',
        'sc' => 'SC',
        'fi' => 'FI',
        'tp' => 'TP',
        'nfo' => 1,
        'data' => now()->format('Y-m-d'),
        'localita' => 'Test City',
        'argomento' => 'Argomento di test',
        'descrizione' => 'Descrizione di test',
    ]);

    // One photo linked to a strip (dbf_id set)
    $withStrip = Photo::factory()->create(['dbf_id' => $dbf->id]);
    // One photo without a strip (dbf_id NULL)
    $withoutStrip = Photo::factory()->create(['dbf_id' => null]);

    login();

    get(action([PhotoController::class, 'index'], ['no_strip' => 1]))
        ->assertSuccessful()
        ->assertSee($withoutStrip->file_name)
        ->assertDontSee($withStrip->file_name);
});

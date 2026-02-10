<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Admin\Models\User;
use App\Photo\Controllers\PhotoController;
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

it('shows folders page with directory nodes', function (): void {
    $inDir = Photo::factory()->inFolder('MyFolder')->create();
    $noDir = Photo::factory()->create(['directory' => null]);

    login();

    get(route('photos.folders.index'))
        ->assertSuccessful()
        ->assertSee('MyFolder');
});

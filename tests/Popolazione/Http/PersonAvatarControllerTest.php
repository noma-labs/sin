<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Admin\Models\User;
use App\Anagrafe\Persona\Models\Persona;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    Storage::fake('media_previews');
});

it('serves the avatar of the persona, kept by the browser for a week', function (): void {
    login();
    $persona = Persona::factory()->create(['avatar_path' => 'persone/avatar.jpg']);
    Storage::disk('media_previews')->put('persone/avatar.jpg', 'jpeg dell\'avatar');

    $risposta = $this->get(route('nomadelfia.person.avatar', $persona->id));

    $risposta->assertOk()
        ->assertHeader('Cache-Control', 'max-age=604800, private');
    expect($risposta->baseResponse->getFile()->getContent())->toBe('jpeg dell\'avatar');
});

it('returns 404 when the persona has no avatar or its file is missing', function (?string $avatarPath): void {
    login();
    $persona = Persona::factory()->create(['avatar_path' => $avatarPath]);

    $this->get(route('nomadelfia.person.avatar', $persona->id))
        ->assertNotFound();
})->with([
    'no avatar' => [null],
    'a missing file' => ['persone/mancante.jpg'],
]);

it('forbids the avatar to a user without the permission to see the popolazione', function (): void {
    $user = User::query()->firstOrCreate(['username' => 'senza-permessi-avatar'], ['email' => 'senza-permessi-avatar@email.it', 'password' => 'secret', 'persona_id' => 0]);
    login($user);
    $persona = Persona::factory()->create(['avatar_path' => 'persone/avatar.jpg']);
    Storage::disk('media_previews')->put('persone/avatar.jpg', 'jpeg dell\'avatar');

    $this->get(route('nomadelfia.person.avatar', $persona->id))
        ->assertForbidden();
});

it('redirects a guest to the login', function (): void {
    $persona = Persona::factory()->create(['avatar_path' => 'persone/avatar.jpg']);

    $this->get(route('nomadelfia.person.avatar', $persona->id))
        ->assertRedirect(route('login'));
});

it('saves the uploaded photo as the avatar of a persona without one, cut in a square', function (): void {
    login();
    $persona = Persona::factory()->create();

    $this->from(route('nomadelfia.person.profile', $persona->id))
        ->post(route('nomadelfia.person.avatar.store', $persona->id), ['avatar' => UploadedFile::fake()->image('foto.jpg', 600, 900)])
        ->assertRedirect(route('nomadelfia.person.profile', $persona->id))
        ->assertSessionHas('success', "Foto di {$persona->nome} {$persona->cognome} salvata.");

    $this->assertDatabaseHas('persone', ['id' => $persona->id, 'avatar_path' => "persone/{$persona->id}.jpg"], 'db_nomadelfia');
    expect(getimagesizefromstring(Storage::disk('media_previews')->get("persone/{$persona->id}.jpg")))->toMatchArray([0 => 256, 1 => 256, 'mime' => 'image/jpeg']);
});

it('replaces the avatar of a persona with the uploaded photo, changing its address', function (): void {
    login();
    $this->travelTo(now()->subDay());
    $persona = Persona::factory()->create(['avatar_path' => 'persone/vecchio.jpg']);
    $indirizzoVecchio = $persona->avatarUrl();
    $this->travelBack();

    $this->post(route('nomadelfia.person.avatar.store', $persona->id), ['avatar' => UploadedFile::fake()->image('nuova.png', 400, 400)])
        ->assertRedirect();

    $persona->refresh();
    expect($persona->avatar_path)->toBe("persone/{$persona->id}.jpg")
        ->and($persona->avatarUrl())->not->toBe($indirizzoVecchio);
    Storage::disk('media_previews')->assertExists("persone/{$persona->id}.jpg");
});

it('refuses an upload that is not a photo, saving nothing', function (array $dati, string $messaggio): void {
    login();
    $persona = Persona::factory()->create();

    $this->post(route('nomadelfia.person.avatar.store', $persona->id), $dati)
        ->assertSessionHasErrors(['avatar' => $messaggio]);

    $this->assertDatabaseHas('persone', ['id' => $persona->id, 'avatar_path' => null], 'db_nomadelfia');
    Storage::disk('media_previews')->assertDirectoryEmpty('/');
})->with([
    'no file' => [[], 'Scegli una foto.'],
    'a pdf' => [['avatar' => UploadedFile::fake()->create('documento.pdf', 100, 'application/pdf')], 'Il file deve essere una foto.'],
    'a file too big' => [['avatar' => UploadedFile::fake()->image('enorme.jpg')->size(10241)], 'La foto non può superare i 10 MB.'],
]);

it('forbids the upload of the avatar to a user without the permission to modify a persona', function (): void {
    $user = User::query()->firstOrCreate(['username' => 'solo-visualizza-avatar'], ['email' => 'solo-visualizza-avatar@email.it', 'password' => 'secret', 'persona_id' => 0]);
    $user->givePermissionTo('popolazione.persona.visualizza');
    login($user);
    $persona = Persona::factory()->create();

    $this->post(route('nomadelfia.person.avatar.store', $persona->id), ['avatar' => UploadedFile::fake()->image('foto.jpg')])
        ->assertForbidden();

    $this->assertDatabaseHas('persone', ['id' => $persona->id, 'avatar_path' => null], 'db_nomadelfia');
});

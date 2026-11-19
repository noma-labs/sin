<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Admin\Models\User;
use App\Anagrafe\Persona\Models\Persona;
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

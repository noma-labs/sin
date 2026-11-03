<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Admin\Models\User;
use App\Anagrafe\Persona\Models\Persona;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('uploads the photo of a person from the profile and shows it in the detail pages', function (): void {
    Storage::fake('avatars');
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    $this->from(route('nomadelfia.person.profile', $persona->id))
        ->post(route('anagrafe.person.avatar.store', $persona->id), [
            'avatar' => UploadedFile::fake()->image('foto.jpg'),
        ])
        ->assertRedirect(route('nomadelfia.person.profile', $persona->id))
        ->assertSessionHas('success');

    $persona->refresh();
    expect($persona->avatar_path)->toBe("{$persona->id}.jpg");
    Storage::disk('avatars')->assertExists($persona->avatar_path);

    $avatarUrl = route('anagrafe.person.avatar.show', $persona->id);
    $this->get(route('nomadelfia.person.show', $persona->id))->assertSee($avatarUrl);
    $this->get(route('nomadelfia.person.profile', $persona->id))->assertSee($avatarUrl)->assertSee('Cambia foto');
    $this->get($avatarUrl)
        ->assertSuccessful()
        ->assertHeader('Content-Type', 'image/jpeg');
});

it('replaces the previous photo of a person', function (): void {
    Storage::fake('avatars');
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    Storage::disk('avatars')->put("{$persona->id}.jpg", 'vecchia foto');
    $persona->update(['avatar_path' => "{$persona->id}.jpg"]);

    $this->post(route('anagrafe.person.avatar.store', $persona->id), [
        'avatar' => UploadedFile::fake()->image('foto.png'),
    ])->assertRedirect();

    expect($persona->refresh()->avatar_path)->toBe("{$persona->id}.png");
    Storage::disk('avatars')->assertMissing("{$persona->id}.jpg");
    Storage::disk('avatars')->assertExists("{$persona->id}.png");
});

it('rejects a file that is not an image', function (): void {
    Storage::fake('avatars');
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    $this->post(route('anagrafe.person.avatar.store', $persona->id), [
        'avatar' => UploadedFile::fake()->create('documento.pdf', 100, 'application/pdf'),
    ])->assertSessionHasErrors(['avatar' => 'Il file deve essere una immagine.']);

    expect($persona->refresh()->avatar_path)->toBeNull();
});

it('rejects a photo bigger than 5 MB', function (): void {
    Storage::fake('avatars');
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    $this->post(route('anagrafe.person.avatar.store', $persona->id), [
        'avatar' => UploadedFile::fake()->image('foto.jpg')->size(6000),
    ])->assertSessionHasErrors(['avatar' => 'La foto non può superare i 5 MB.']);

    expect($persona->refresh()->avatar_path)->toBeNull();
});

it('deletes the photo of a person and shows the initials in the profile', function (): void {
    Storage::fake('avatars');
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    Storage::disk('avatars')->put("{$persona->id}.jpg", 'foto');
    $persona->update(['avatar_path' => "{$persona->id}.jpg"]);

    $this->from(route('nomadelfia.person.show', $persona->id))
        ->delete(route('anagrafe.person.avatar.destroy', $persona->id))
        ->assertRedirect(route('nomadelfia.person.show', $persona->id));

    expect($persona->refresh()->avatar_path)->toBeNull();
    Storage::disk('avatars')->assertMissing("{$persona->id}.jpg");
    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertDontSee('Cambia foto');
    $this->get(route('anagrafe.person.avatar.show', $persona->id))->assertNotFound();
});

it('offers the upload of the photo in the detail pages to a user who can edit the persone', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $uploadUrl = route('anagrafe.person.avatar.store', $persona->id);

    $this->get(route('nomadelfia.person.show', $persona->id))
        ->assertSee('Carica foto')
        ->assertSee($uploadUrl);
    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSee('Carica foto')
        ->assertSee($uploadUrl);
});

it('shows the photo without the upload to a user who can only see the popolazione', function (): void {
    Storage::fake('avatars');
    $user = User::query()->create(['username' => 'solo-visualizza-foto', 'email' => 'solo-visualizza-foto@email.it', 'password' => 'secret', 'persona_id' => 0]);
    $user->givePermissionTo('popolazione.persona.visualizza');
    login($user);
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    Storage::disk('avatars')->put("{$persona->id}.jpg", 'foto');
    $persona->update(['avatar_path' => "{$persona->id}.jpg"]);

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSee(route('anagrafe.person.avatar.show', $persona->id))
        ->assertDontSee('Cambia foto')
        ->assertDontSee('modal-foto');
    $this->get(route('anagrafe.person.avatar.show', $persona->id))->assertSuccessful();
});

it('forbids uploading a photo to a user without the permission to edit the persone', function (): void {
    Storage::fake('avatars');
    $user = User::query()->create(['username' => 'senza-permessi-carica-foto', 'email' => 'senza-permessi-carica-foto@email.it', 'password' => 'secret', 'persona_id' => 0]);
    login($user);
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    $this->post(route('anagrafe.person.avatar.store', $persona->id), [
        'avatar' => UploadedFile::fake()->image('foto.jpg'),
    ])->assertForbidden();

    expect($persona->refresh()->avatar_path)->toBeNull();
});

it('forbids seeing the photo to a user without the permission to see the popolazione', function (): void {
    Storage::fake('avatars');
    $user = User::query()->create(['username' => 'senza-permessi-foto', 'email' => 'senza-permessi-foto@email.it', 'password' => 'secret', 'persona_id' => 0]);
    login($user);
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    Storage::disk('avatars')->put("{$persona->id}.jpg", 'foto');
    $persona->update(['avatar_path' => "{$persona->id}.jpg"]);

    $this->get(route('anagrafe.person.avatar.show', $persona->id))->assertForbidden();
});

it('redirects a guest to the login when seeing the photo', function (): void {
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    $this->get(route('anagrafe.person.avatar.show', $persona->id))->assertRedirect(route('login'));
});

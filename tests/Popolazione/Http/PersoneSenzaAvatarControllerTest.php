<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Admin\Models\User;
use App\Anagrafe\Persona\Models\Persona;
use App\Photo\Models\Photo;

it('lists the persone without an avatar who are in the photos, with the way to choose their photo', function (): void {
    login();
    $persona = Persona::factory()->create();
    // the test database is shared by the suite: with many photos the persona is on the first page of the queue
    Photo::factory()->count(30)->create()->each(fn (Photo $foto) => $foto->persone()->attach($persona->id, ['persona_nome' => 'NOME FOTO']));

    $this->get(route('nomadelfia.person.senza-avatar'))
        ->assertOk()
        ->assertSee('Persone senza foto')
        ->assertSee(route('nomadelfia.person.avatar.volti', ['id' => $persona->id, 'coda' => 1]));
});

it('shows the avatars already saved with their date in the tab Fatte, to change them', function (): void {
    login();
    $fatta = Persona::factory()->create(['nome' => 'Anna', 'cognome' => 'Rota', 'avatar_path' => 'persone/anna.jpg', 'avatar_aggiornato_il' => now()->addYears(5)]);
    $daFare = Persona::factory()->create();
    foreach ([$fatta, $daFare] as $persona) {
        Photo::factory()->create()->persone()->attach($persona->id, ['persona_nome' => 'NOME FOTO']);
    }

    // saved in the future, so the persona is the first of the tab in the database shared by the suite
    $this->get(route('nomadelfia.person.senza-avatar', ['stato' => 'fatte']))
        ->assertOk()
        ->assertSeeInOrder(['Fatte (', route('nomadelfia.person.avatar.volti', $fatta->id), 'Anna Rota', $fatta->avatar_aggiornato_il->format('d/m/Y H:i')], false)
        ->assertDontSee(route('nomadelfia.person.avatar.volti', $daFare->id));
});

it('forbids the queue to a user who cannot see the photos', function (): void {
    $user = User::query()->firstOrCreate(['username' => 'coda-senza-foto'], ['email' => 'coda-senza-foto@email.it', 'password' => 'secret', 'persona_id' => 0]);
    $user->givePermissionTo('popolazione.persona.modifica');
    login($user);

    $this->get(route('nomadelfia.person.senza-avatar'))->assertForbidden();
});

<?php

declare(strict_types=1);

use App\Anagrafe\Persona\Models\Persona;
use App\View\Components\PersonaAvatar;

it('shows the photo of a persona with an avatar, loaded lazily', function (): void {
    $persona = Persona::factory()->create(['avatar_path' => 'persone/avatar.jpg']);

    $this->blade('<x-persona-avatar :persona="$persona" size="lg" />', ['persona' => $persona])
        ->assertSee('class="persona-avatar persona-avatar-lg"', false)
        ->assertSee('<img src="'.e($persona->avatarUrl()).'" alt="" loading="lazy" />', false);
});

it('shows the photo of a query row with an avatar', function (): void {
    $riga = (object) ['id' => 42, 'nome' => 'Anna', 'cognome' => 'Neri', 'sesso' => 'F', 'avatar_path' => 'persone/42.jpg'];

    $this->blade('<x-persona-avatar :persona="$riga" />', ['riga' => $riga])
        ->assertSee('<img src="'.e(route('nomadelfia.person.avatar', ['id' => 42])).'"', false);
});

it('shows the initials on the color of the sesso without an avatar', function (array $persona, string $classe, string $iniziali): void {
    $riga = (object) ($persona + ['id' => 7, 'avatar_path' => null]);

    $this->blade('<x-persona-avatar :persona="$riga" size="sm" />', ['riga' => $riga])
        ->assertSee("class=\"persona-avatar persona-avatar-sm {$classe}\"", false)
        ->assertSeeText($iniziali)
        ->assertDontSee('<img', false);
})->with([
    'a man' => [['nome' => 'Giovanni', 'cognome' => 'Bianchi', 'sesso' => 'M'], 'persona-avatar-m', 'GB'],
    'a woman' => [['nome' => 'lidia', 'cognome' => 'ferri', 'sesso' => 'F'], 'persona-avatar-f', 'LF'],
    'a missing sesso' => [['nome' => 'Ottavio', 'cognome' => 'Ruggeri', 'sesso' => null], 'persona-avatar-n', 'OR'],
    'only the nominativo' => [['nominativo' => 'Pierino'], 'persona-avatar-n', 'P'],
]);

it('refuses a size that does not exist', function (): void {
    expect(fn (): PersonaAvatar => new PersonaAvatar((object) ['nome' => 'Anna'], 'xl'))
        ->toThrow(InvalidArgumentException::class, 'The size of the avatar must be one of xs, sm, md, lg: `xl` given');
});

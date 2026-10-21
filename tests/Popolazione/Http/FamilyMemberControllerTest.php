<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Persona\Models\Persona;

it('adds the deceased husband as capofamiglia outside the nucleo of the famiglia headed by the vedova', function (): void {
    login();
    $vedova = Persona::factory()->maggiorenne()->femmina()->create();
    $marito = Persona::factory()->maggiorenne()->maschio()->create(['data_decesso' => '2015-11-03']);
    $famiglia = Famiglia::factory()->create();
    $famiglia->assegnaCapoFamiglia($vedova);

    $this->post(route('anagrafe.family.member.store', $famiglia->id), [
        'persona_id' => $marito->id,
        'posizione' => 'CAPO FAMIGLIA',
        'stato' => '0',
    ])->assertRedirect(route('anagrafe.families.show', ['id' => $famiglia->id]))
        ->assertSessionHasNoErrors();

    expect($famiglia->componenti()->where('persone.id', $marito->id)->first()->pivot->toArray())
        ->toMatchArray(['posizione_famiglia' => 'CAPO FAMIGLIA', 'stato' => '0'])
        ->and($famiglia->capofamiglia()->id)->toBe($vedova->id);
});

it('refuses a second capofamiglia in the nucleo', function (): void {
    login();
    $capo = Persona::factory()->maggiorenne()->maschio()->create(['nominativo' => 'Capo Presente']);
    $altro = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia = Famiglia::factory()->create(['nome_famiglia' => 'Famiglia Con Capo']);
    $famiglia->assegnaCapoFamiglia($capo);

    $this->post(route('anagrafe.family.member.store', $famiglia->id), [
        'persona_id' => $altro->id,
        'posizione' => 'CAPO FAMIGLIA',
        'stato' => '1',
    ])->assertRedirect(route('anagrafe.families.show', ['id' => $famiglia->id]))
        ->assertSessionHasErrors(['persona_id' => 'Il capo famiglia `Capo Presente` non può essere aggiunto alla famiglia `Famiglia Con Capo` perchè esiste già un capo famiglia.']);

    expect($famiglia->componenti()->where('persone.id', $altro->id)->exists())->toBeFalse();
});

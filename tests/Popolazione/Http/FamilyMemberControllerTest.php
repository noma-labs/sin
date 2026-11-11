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

it('goes back to the profile that added the parente to the famiglia', function (): void {
    login();
    $capofamiglia = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia = Famiglia::factory()->coniugi()->create();
    $famiglia->assegnaCapoFamiglia($capofamiglia);
    $figlia = Persona::factory()->minorenne()->femmina()->create();

    $this->from(route('nomadelfia.person.profile', $capofamiglia->id))
        ->post(route('anagrafe.family.member.store', $famiglia->id), [
            'persona_id' => $figlia->id,
            'posizione' => 'FIGLIO ACCOLTO',
            'stato' => '1',
            'data_entrata' => '2020-09-01',
            'torna_indietro' => '1',
        ])->assertRedirect(route('nomadelfia.person.profile', $capofamiglia->id))
        ->assertSessionHasNoErrors();

    expect($famiglia->componenti()->where('persone.id', $figlia->id)->first()->pivot->toArray())
        ->toMatchArray(['posizione_famiglia' => 'FIGLIO ACCOLTO', 'stato' => '1', 'data_entrata' => '2020-09-01']);
});

it('records the data di entrata of a figlio accolto in the famiglia', function (): void {
    login();
    $figlio = Persona::factory()->minorenne()->femmina()->create();
    $famiglia = Famiglia::factory()->create();
    $famiglia->assegnaFiglioAccolto($figlio);

    $this->put(route('anagrafe.family.member.update', $famiglia->id), [
        'persona_id' => $figlio->id,
        'posizione' => 'FIGLIO ACCOLTO',
        'stato' => '1',
        'data_entrata' => '2014-09-01',
    ])->assertRedirect(route('anagrafe.families.show', ['id' => $famiglia->id]))
        ->assertSessionHasNoErrors();

    expect($famiglia->componenti()->where('persone.id', $figlio->id)->first()->pivot->data_entrata)->toBe('2014-09-01');
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

it('removes a figlio from the famiglia, keeping the other componenti', function (string $stato): void {
    login();
    $capo = Persona::factory()->maggiorenne()->maschio()->create();
    $figlio = Persona::factory()->minorenne()->maschio()->create();
    $fratello = Persona::factory()->minorenne()->femmina()->create();
    $famiglia = Famiglia::factory()->create();
    $famiglia->assegnaCapoFamiglia($capo);
    $famiglia->assegnaFiglioNato($figlio, stato: $stato);
    $famiglia->assegnaFiglioAccolto($fratello);

    $this->delete(route('anagrafe.family.member.destroy', ['id' => $famiglia->id, 'personaId' => $figlio->id]))
        ->assertRedirect(route('anagrafe.families.show', ['id' => $famiglia->id]))
        ->assertSessionHasNoErrors();

    expect($famiglia->componenti()->pluck('persone.id')->sort()->values()->all())
        ->toEqual(collect([$capo->id, $fratello->id])->sort()->values()->all())
        ->and(Persona::query()->whereKey($figlio->id)->exists())->toBeTrue();
})->with([
    'in the nucleo' => '1',
    'outside the nucleo' => '0',
]);

it('refuses to remove the capofamiglia or the moglie with the removal of a figlio', function (string $posizione): void {
    login();
    $persona = Persona::factory()->maggiorenne()->create(['sesso' => $posizione === 'MOGLIE' ? 'F' : 'M']);
    $famiglia = Famiglia::factory()->create();
    $posizione === 'MOGLIE' ? $famiglia->assegnaMoglie($persona) : $famiglia->assegnaCapoFamiglia($persona);

    $this->delete(route('anagrafe.family.member.destroy', ['id' => $famiglia->id, 'personaId' => $persona->id]))
        ->assertRedirect(route('anagrafe.families.show', ['id' => $famiglia->id]))
        ->assertSessionHasErrors(['persona_id' => 'Si può rimuovere dalla famiglia solo un figlio.']);

    expect($famiglia->componenti()->where('persone.id', $persona->id)->exists())->toBeTrue();
})->with(['CAPO FAMIGLIA', 'MOGLIE']);

it('offers the removal only for the figli on the page of the famiglia', function (): void {
    login();
    $capo = Persona::factory()->maggiorenne()->maschio()->create();
    $figlio = Persona::factory()->minorenne()->maschio()->create();
    $famiglia = Famiglia::factory()->create();
    $famiglia->assegnaCapoFamiglia($capo);
    $famiglia->assegnaFiglioNato($figlio);

    $this->get(route('anagrafe.families.show', $famiglia->id))
        ->assertSuccessful()
        ->assertSee(route('anagrafe.family.member.destroy', ['id' => $famiglia->id, 'personaId' => $figlio->id]))
        ->assertDontSee(route('anagrafe.family.member.destroy', ['id' => $famiglia->id, 'personaId' => $capo->id]));
});

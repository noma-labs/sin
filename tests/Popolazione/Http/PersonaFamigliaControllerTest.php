<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Admin\Models\User;
use App\Anagrafe\Famiglia\Controllers\PersonaFamigliaController;
use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Persona\Models\Persona;

it('can render the famiglia index page of a person', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $this->get(action([PersonaFamigliaController::class, 'index'], $persona->id))
        ->assertSuccessful();
});

it('adds a person to a family as figlio', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $famiglia = Famiglia::factory()->create();

    $this->post(action([PersonaFamigliaController::class, 'store'], $persona->id), [
        'family_id' => $famiglia->id,
        'type' => 'FIGLIO ACCOLTO',
    ])->assertRedirectToRoute('nomadelfia.person.profile', $persona->id);

    expect($persona->famigliaAttuale()->id)->toBe($famiglia->id)
        ->and($persona->isFiglioAccolto())->toBeTrue();
});

it('adds a person to a family as aggregato', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $famiglia = Famiglia::factory()->create();

    $this->post(action([PersonaFamigliaController::class, 'store'], $persona->id), [
        'family_id' => $famiglia->id,
        'type' => 'AGGREGATO',
    ])->assertRedirectToRoute('nomadelfia.person.profile', $persona->id);

    expect($persona->famigliaAttuale()->id)->toBe($famiglia->id)
        ->and($persona->isAggregato())->toBeTrue()
        ->and($persona->isFiglio())->toBeTrue()
        ->and($famiglia->figliAttuali()->pluck('persone.id')->all())->toBe([$persona->id]);
});

it('refuses to add a person to a family as capofamiglia or moglie', function (string $posizione): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia = Famiglia::factory()->create();

    $this->post(action([PersonaFamigliaController::class, 'store'], $persona->id), [
        'family_id' => $famiglia->id,
        'type' => $posizione,
    ])->assertSessionHasErrors(['type' => 'La posizione deve essere figlio nato, figlio accolto o aggregato. Capofamiglia e moglie si assegnano con un nuovo matrimonio.']);

    expect($persona->famigliaAttuale())->toBeNull();
})->with(['CAPO FAMIGLIA', 'MOGLIE']);

it('sends to the famiglie of a person in the nucleo of more than one famiglia to resolve it', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->femmina()->create();
    Famiglia::factory()->create()->assegnaFiglioNato($persona);
    Famiglia::factory()->create()->assegnaFiglioAccolto($persona);

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertRedirectToRoute('anagrafe.person.families', $persona->id)
        ->assertSessionHasErrors(['famiglia_id' => "La persona `{$persona->nominativo}` ha più di uno stato familiare attuale. Scegli la famiglia attuale: le altre passano nello storico."]);
});

it('shows every famiglia attuale of a person in the nucleo of more than one famiglia', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->femmina()->create();
    $nata = Famiglia::factory()->create(['nome_famiglia' => 'Famiglia Nata']);
    $nata->assegnaFiglioNato($persona);
    $accolta = Famiglia::factory()->create(['nome_famiglia' => 'Famiglia Accolta']);
    $accolta->assegnaFiglioAccolto($persona);

    $this->get(route('anagrafe.person.families', $persona->id))
        ->assertOk()
        ->assertSee('è nel nucleo di')
        ->assertSee('Famiglia Nata')
        ->assertSee('Famiglia Accolta')
        ->assertSee('Mantieni questa');
});

it('keeps the chosen famiglia attuale and moves the others in the storico', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->femmina()->create();
    $nata = Famiglia::factory()->create();
    $nata->assegnaFiglioNato($persona);
    $accolta = Famiglia::factory()->create();
    $accolta->assegnaFiglioAccolto($persona);

    $this->put(route('anagrafe.person.families.attuale', $persona->id), ['famiglia_id' => $accolta->id])
        ->assertRedirectToRoute('anagrafe.person.families', $persona->id)
        ->assertSessionHasNoErrors();

    expect($persona->famigliaAttuale()->id)->toBe($accolta->id)
        ->and($persona->famiglieStorico()->pluck('famiglie.id')->all())->toBe([$nata->id]);
});

it('refuses to keep as attuale a famiglia that is not attuale for the person', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->femmina()->create();
    $nata = Famiglia::factory()->create();
    $nata->assegnaFiglioNato($persona);
    $accolta = Famiglia::factory()->create();
    $accolta->assegnaFiglioAccolto($persona);
    $altra = Famiglia::factory()->create();

    $this->put(route('anagrafe.person.families.attuale', $persona->id), ['famiglia_id' => $altra->id])
        ->assertSessionHasErrors(['famiglia_id' => 'La famiglia scelta non è una famiglia attuale della persona.']);

    expect($persona->famiglieAttuali()->count())->toBe(2);
});

it('forbids to choose the famiglia attuale to a user without the permission to modify a person', function (): void {
    $user = User::query()->create(['username' => 'senza-permessi-famiglia', 'email' => 'senza-permessi-famiglia@email.it', 'password' => 'secret', 'persona_id' => 0]);
    login($user);
    $persona = Persona::factory()->minorenne()->femmina()->create();
    $nata = Famiglia::factory()->create();
    $nata->assegnaFiglioNato($persona);
    $accolta = Famiglia::factory()->create();
    $accolta->assegnaFiglioAccolto($persona);

    $this->put(route('anagrafe.person.families.attuale', $persona->id), ['famiglia_id' => $accolta->id])
        ->assertForbidden();

    expect($persona->famiglieAttuali()->count())->toBe(2);
});

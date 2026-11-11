<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Admin\Models\User;
use App\Anagrafe\Famiglia\Controllers\PersonaFamigliaController;
use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Famiglia\Models\TipoFamiglia;
use App\Anagrafe\Persona\Models\Persona;
use Closure;

it('can render the famiglia index page of a person', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $this->get(action([PersonaFamigliaController::class, 'index'], $persona->id))
        ->assertSuccessful();
});

it('shows the albero familiare from the genitori to the figli of the famiglia formed by the persona', function (): void {
    login();
    $origine = Famiglia::factory()->coniugi()->create();
    $padre = Persona::factory()->diEta(80)->maschio()->create(['nome' => 'Giuseppe']);
    $origine->assegnaCapoFamiglia($padre);
    $persona = Persona::factory()->cinquantenne()->femmina()->create(['nome' => 'Maria']);
    $origine->assegnaFiglioNato($persona, stato: '0');
    $propria = Famiglia::factory()->create(['nome_famiglia' => 'Famiglia Propria', 'tipo' => TipoFamiglia::MammaDiVocazione]);
    $propria->assegnaCapoFamiglia($persona);
    $sposato = Persona::factory()->maggiorenne()->maschio()->create(['nome' => 'Luca']);
    $propria->assegnaFiglioAccolto($sposato, stato: '0');
    Famiglia::factory()->coniugi()->create(['nome_famiglia' => 'Famiglia Del Figlio'])->assegnaCapoFamiglia($sposato);

    $response = $this->get(route('anagrafe.person.families', $persona->id));

    $response->assertOk()
        ->assertSeeInOrder(['Genitori non registrati', 'nonni di Maria', 'Giuseppe', 'genitori di Maria', 'Maria', '1 figlio di Maria', 'Famiglia Propria', 'Luca', 'Famiglia Del Figlio']);
});

it('shows the percorso of the famiglie d\'origine and draws the albero from the chosen one', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $nascita = Famiglia::factory()->coniugi()->create(['nome_famiglia' => 'Famiglia Nascita']);
    $nascita->assegnaCapoFamiglia(Persona::factory()->cinquantenne()->maschio()->create(['nome' => 'Padrenaturale']));
    $nascita->assegnaFiglioNato($persona, stato: '0');
    $accoglienza = Famiglia::factory()->create(['nome_famiglia' => 'Famiglia Accoglienza', 'tipo' => TipoFamiglia::MammaDiVocazione]);
    $accoglienza->assegnaCapoFamiglia(Persona::factory()->cinquantenne()->femmina()->create(['nome' => 'Mammadivocazione']));
    $accoglienza->assegnaFiglioAccolto($persona, dataEntrata: '2020-09-01');

    $response = $this->get(route('anagrafe.person.families', ['id' => $persona->id, 'origine' => $nascita->id]));

    $response->assertOk()
        ->assertSeeInOrder(['Famiglia Nascita · nato', 'Famiglia Accoglienza · accolto 2020', '2 famiglie d&#039;origine'], false)
        ->assertSee('Padrenaturale')
        ->assertDontSee('Mammadivocazione');
});

it('numbers the unioni of a persona with more famiglie and keeps the persona between them', function (): void {
    login();
    $persona = Persona::factory()->diEta(80)->maschio()->create(['nome' => 'Giovanni', 'cognome' => 'Bianchi']);
    $prima = Famiglia::factory()->coniugi()->create(['nome_famiglia' => 'Prima Famiglia', 'data_creazione' => '1970-06-01']);
    $prima->assegnaCapoFamiglia($persona, stato: '0');
    $prima->assegnaMoglie(Persona::factory()->diEta(78)->femmina()->create(['nome' => 'Anna', 'cognome' => 'Rossi']), stato: '0');
    $seconda = Famiglia::factory()->coniugi()->create(['nome_famiglia' => 'Seconda Famiglia', 'data_creazione' => '1996-06-01']);
    $seconda->assegnaCapoFamiglia($persona);
    $seconda->assegnaMoglie(Persona::factory()->diEta(70)->femmina()->create(['nome' => 'Carla', 'cognome' => 'Verdi']));

    $response = $this->get(route('anagrafe.person.families', $persona->id));

    $response->assertOk()
        ->assertSeeInOrder(['Carla', '1996', 'Giovanni', '1970', 'Anna', '0 figli di Giovanni e Anna', '0 figli di Giovanni e Carla']);
});

it('shows where a figlio accolto in more famiglie was before and goes after', function (): void {
    login();
    $mamma = Persona::factory()->cinquantenne()->femmina()->create();
    $propria = Famiglia::factory()->create(['tipo' => TipoFamiglia::MammaDiVocazione]);
    $propria->assegnaCapoFamiglia($mamma);
    $figlio = Persona::factory()->minorenne()->maschio()->create();
    Famiglia::factory()->coniugi()->create(['nome_famiglia' => 'Famiglia Precedente'])->assegnaFiglioNato($figlio, stato: '0');
    $propria->assegnaFiglioAccolto($figlio, stato: '0', dataEntrata: '2016-01-01');
    Famiglia::factory()->coniugi()->create(['nome_famiglia' => 'Famiglia Successiva'])->assegnaFiglioAccolto($figlio, dataEntrata: '2022-01-01');

    $response = $this->get(route('anagrafe.person.families', $mamma->id));

    $response->assertOk()
        ->assertSeeInOrder(['prima:', 'Famiglia Precedente', 'poi:', 'Famiglia Successiva', '(accolto 2022)']);
});

it('leaves the changes of the famiglie to the profile and to the famiglia, not to the albero', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->femmina()->create();
    $origine = Famiglia::factory()->coniugi()->create();
    $origine->assegnaFiglioNato($persona, stato: '0');
    $propria = Famiglia::factory()->create(['tipo' => TipoFamiglia::MammaDiVocazione]);
    $propria->assegnaCapoFamiglia($persona);
    $propria->assegnaFiglioAccolto(Persona::factory()->maggiorenne()->maschio()->create(), stato: '0');

    $response = $this->get(route('anagrafe.person.families', $persona->id));

    $response->assertOk()
        ->assertDontSee('Nuova famiglia')
        ->assertDontSee('Aggiungi figlio')
        ->assertDontSee('Aggiungi fratello')
        ->assertDontSee('Aggiungi genitori')
        ->assertDontSee('Tutte le famiglie');
});

it('does not offer a nuova famiglia in the albero to a persona who cannot form one', function (Closure $persona): void {
    login();
    $origine = Famiglia::factory()->coniugi()->create();
    $figlia = $persona();
    $origine->assegnaFiglioNato($figlia);

    $response = $this->get(route('anagrafe.person.families', $figlia->id));

    $response->assertOk()
        ->assertDontSee('Nuova famiglia');
})->with([
    'minorenne' => [fn (): Persona => Persona::factory()->minorenne()->femmina()->create()],
    'deceduta' => [fn (): Persona => Persona::factory()->maggiorenne()->femmina()->create(['data_decesso' => '2020-01-01'])],
]);

it('does not offer a nuova famiglia in the albero to a persona who already formed one', function (): void {
    login();
    $origine = Famiglia::factory()->coniugi()->create();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $origine->assegnaFiglioNato($persona, stato: '0');
    Famiglia::factory()->coniugi()->create()->assegnaCapoFamiglia($persona);

    $response = $this->get(route('anagrafe.person.families', $persona->id));

    $response->assertOk()
        ->assertDontSee('Nuova famiglia');
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
        ->and($persona->isFiglioAccolto())->toBeTrue()
        ->and($persona->famigliaAttuale()->pivot->data_entrata)->toBeNull();
});

it('adds a person already in the comunità to a family as figlio accolto with the data di entrata', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $famiglia = Famiglia::factory()->create();

    $this->post(action([PersonaFamigliaController::class, 'store'], $persona->id), [
        'family_id' => $famiglia->id,
        'type' => 'FIGLIO ACCOLTO',
        'data_entrata' => '2018-03-01',
    ])->assertRedirectToRoute('nomadelfia.person.profile', $persona->id);

    expect($persona->famigliaAttuale()->pivot->toArray())
        ->toMatchArray(['posizione_famiglia' => 'FIGLIO ACCOLTO', 'data_entrata' => '2018-03-01']);
});

it('refuses an invalid data di entrata in the family', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $famiglia = Famiglia::factory()->create();

    $this->post(action([PersonaFamigliaController::class, 'store'], $persona->id), [
        'family_id' => $famiglia->id,
        'type' => 'FIGLIO ACCOLTO',
        'data_entrata' => 'non una data',
    ])->assertSessionHasErrors(['data_entrata' => 'La data di ingresso nella famiglia non è valida.']);

    expect($persona->famiglie()->exists())->toBeFalse();
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

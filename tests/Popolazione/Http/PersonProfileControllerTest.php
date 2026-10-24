<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Admin\Models\User;
use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Famiglia\Models\TipoFamiglia;
use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneConFamigliaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMinorenneAccoltoAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\UscitaPersonaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Stato;
use Carbon\Carbon;

it('shows the profile of a person present in Nomadelfia with a link back to the classic page', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::parse('1995-09-15'), GruppoFamiliare::all()->random());

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSee('Presente')
        ->assertSeeInOrder(['Dati anagrafici', 'Situazione attuale', 'Percorso in Nomadelfia', 'Famiglia'])
        ->assertSeeInOrder(['Entra in Nomadelfia come Ospite', 'Nascita'])
        ->assertSee(route('nomadelfia.person.show', $persona->id));
});

it('links the classic page of a person to the new profile', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    $this->get(route('nomadelfia.person.show', $persona->id))
        ->assertSuccessful()
        ->assertSee(route('nomadelfia.person.profile', $persona->id));
});

it('shows a person who never entered Nomadelfia', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSee('Mai entrato')
        ->assertDontSee('Entra in Nomadelfia');
});

it('shows the exit and the re-entry in the timeline from the most recent', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    $gruppo = GruppoFamiliare::all()->random();
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::parse('1995-09-15'), $gruppo);
    app(UscitaPersonaAction::class)->execute($persona, Carbon::parse('2001-03-10'));
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::parse('2003-03-10'), $gruppo);

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Rientra in Nomadelfia', 'Dopo 2 anni fuori dalla comunità', 'Esce da Nomadelfia', 'Entra in Nomadelfia']);
});

it('shows the changes of posizione with the previous one', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::parse('1995-09-15'), GruppoFamiliare::all()->random());
    $persona->assegnaPosizione(Posizione::perNome('postulante'), Carbon::parse('1998-01-06'));

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Diventa Postulante', 'Prima: Ospite', 'Entra in Nomadelfia come Ospite']);
});

it('shows the marriage and the children born in the timeline and in the family', function (): void {
    login();
    $marito = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    $moglie = Persona::factory()->nato(Carbon::parse('1972-02-01'))->femmina()->create(['nominativo' => 'Moglie Sposata']);
    $figlio = Persona::factory()->nato(Carbon::parse('2000-08-20'))->maschio()->create(['nominativo' => 'Figlio Nato']);
    $famiglia = Famiglia::factory()->create(['data_creazione' => '1998-06-12']);
    $famiglia->assegnaCapoFamiglia($marito);
    $famiglia->assegnaMoglie($moglie);
    $famiglia->assegnaFiglioNato($figlio);

    $this->get(route('nomadelfia.person.profile', $marito->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Nasce il figlio Figlio Nato', 'Sposa Moglie Sposata', 'Nascita'])
        ->assertSeeInOrder(['Coniuge e figli', 'Moglie Sposata', 'Figlio Nato'])
        ->assertSee(route('nomadelfia.person.profile', $figlio->id));
});

it('shows the children no longer in the nucleo apart from the ones still in it', function (): void {
    login();
    $marito = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    $nelNucleo = Persona::factory()->nato(Carbon::parse('2005-08-20'))->maschio()->create(['nominativo' => 'Figlio Nel Nucleo']);
    $uscito = Persona::factory()->nato(Carbon::parse('2000-08-20'))->maschio()->create(['nominativo' => 'Figlio Uscito']);
    $famiglia = Famiglia::factory()->create(['data_creazione' => '1998-06-12']);
    $famiglia->assegnaCapoFamiglia($marito);
    $famiglia->assegnaFiglioNato($uscito);
    $famiglia->assegnaFiglioNato($nelNucleo);
    $famiglia->uscitaDalNucleoFamiliare($uscito);

    $this->get(route('nomadelfia.person.profile', $marito->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Coniuge e figli', 'Figlio Nel Nucleo', 'Figli non più nel nucleo', 'Figlio Uscito']);
});

it('shows the figlio accolto in the timeline at the date of the entrata', function (): void {
    login();
    $marito = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    $accolto = Persona::factory()->nato(Carbon::parse('2004-03-02'))->femmina()->create(['nominativo' => 'Figlia Accolta']);
    $famiglia = Famiglia::factory()->create(['data_creazione' => '1998-06-12']);
    app(EntrataMaggiorenneConFamigliaAction::class)->execute($marito, Carbon::parse('1995-09-15'), GruppoFamiliare::all()->random());
    $famiglia->assegnaCapoFamiglia($marito);
    app(EntrataMinorenneAccoltoAction::class)->execute($accolto, Carbon::parse('2010-07-01'), Famiglia::findOrFail($famiglia->id));

    $this->get(route('nomadelfia.person.profile', $marito->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Accoglie la figlia Figlia Accolta', 'Nata il 02/03/2004', 'Nasce la famiglia', 'Entra in Nomadelfia']);
});

it('shows the parents of a figlio as the family of origin', function (): void {
    login();
    $padre = Persona::factory()->maggiorenne()->maschio()->create(['nominativo' => 'Padre Da Mostrare']);
    $figlio = Persona::factory()->minorenne()->maschio()->create();
    $famiglia = Famiglia::factory()->create();
    $famiglia->assegnaCapoFamiglia($padre);
    $famiglia->assegnaFiglioNato($figlio);

    $this->get(route('nomadelfia.person.profile', $figlio->id))
        ->assertSuccessful()
        ->assertSeeInOrder(["Famiglia d'origine", 'Padre Da Mostrare', 'Padre'], false);
});

it('shows a married person as sposato from the creation of the famiglia', function (): void {
    login();
    $marito = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    $famiglia = Famiglia::factory()->create(['data_creazione' => '1998-06-12']);
    $famiglia->assegnaCapoFamiglia($marito);
    $famiglia->assegnaMoglie(Persona::factory()->nato(Carbon::parse('1972-02-01'))->femmina()->create());

    $this->get(route('nomadelfia.person.profile', $marito->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Stato familiare', 'Sposato', 'dal 12 giu 1998']);
});

it('shows a married person as sposato without a date when the famiglia has no data_creazione', function (): void {
    login();
    $marito = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    $famiglia = Famiglia::factory()->create(['data_creazione' => null]);
    $famiglia->assegnaCapoFamiglia($marito);
    $famiglia->assegnaMoglie(Persona::factory()->nato(Carbon::parse('1972-02-01'))->femmina()->create());

    expect($marito->statoCivile())->toBe(['nome' => 'Sposato', 'dal' => null]);
    $this->get(route('nomadelfia.person.profile', $marito->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Stato familiare', 'Sposato'])
        ->assertDontSee('dal '.now()->translatedFormat('j M Y'));
});

it('shows a person whose coniuge died as vedova from the death of the coniuge', function (): void {
    login();
    $moglie = Persona::factory()->nato(Carbon::parse('1972-02-01'))->femmina()->create();
    $marito = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create(['nominativo' => 'Marito Defunto', 'data_decesso' => '2015-11-03']);
    $famiglia = Famiglia::factory()->create(['data_creazione' => '1998-06-12']);
    $famiglia->assegnaCapoFamiglia($marito);
    $famiglia->assegnaMoglie($moglie);
    $famiglia->uscitaDalNucleoFamiliare($marito);

    $this->get(route('nomadelfia.person.profile', $moglie->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Rimane vedova', 'Muore Marito Defunto', 'Sposa Marito Defunto'])
        ->assertSeeInOrder(['Stato familiare', 'Vedova', 'dal 3 nov 2015']);
});

it('shows a vedova heading the famiglia as vedova of the husband capofamiglia outside the nucleo', function (): void {
    login();
    $vedova = Persona::factory()->nato(Carbon::parse('1945-02-01'))->femmina()->create();
    $marito = Persona::factory()->nato(Carbon::parse('1940-05-01'))->maschio()->create(['nominativo' => 'Marito Capofamiglia', 'data_decesso' => '2015-11-03']);
    $famiglia = Famiglia::factory()->create(['data_creazione' => '1968-06-12']);
    $famiglia->assegnaCapoFamiglia($vedova);
    $famiglia->assegnaCapoFamiglia($marito, stato: '0');

    $this->get(route('nomadelfia.person.profile', $vedova->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Rimane vedova', 'Sposa Marito Capofamiglia'])
        ->assertSeeInOrder(['Stato familiare', 'Vedova', 'dal 3 nov 2015']);
});

it('keeps a deceased married person sposato', function (): void {
    login();
    $marito = Persona::factory()->nato(Carbon::parse('1940-05-01'))->maschio()->create(['data_decesso' => '2015-11-03']);
    $famiglia = Famiglia::factory()->create(['data_creazione' => '1968-06-12']);
    $famiglia->assegnaCapoFamiglia($marito);
    $famiglia->assegnaMoglie(Persona::factory()->nato(Carbon::parse('1942-02-01'))->femmina()->create());
    $famiglia->uscitaDalNucleoFamiliare($marito);

    $this->get(route('nomadelfia.person.profile', $marito->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Stato familiare', 'Sposato', 'dal 12 giu 1968']);
});

it('shows a married person with the stato separato as separato', function (): void {
    login();
    $marito = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    $famiglia = Famiglia::factory()->create(['data_creazione' => '1998-06-12']);
    $famiglia->assegnaCapoFamiglia($marito);
    $famiglia->assegnaMoglie(Persona::factory()->nato(Carbon::parse('1972-02-01'))->femmina()->create());
    $marito->assegnaStato(Stato::perNome('separato'), '2010-04-01');

    $this->get(route('nomadelfia.person.profile', $marito->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Si separa dalla moglie', 'Sposa'])
        ->assertSeeInOrder(['Stato familiare', 'Separato', 'dal 1 apr 2010']);
});

it('shows a mamma di vocazione heading her own famiglia as nubile and not married', function (): void {
    login();
    $mamma = Persona::factory()->nato(Carbon::parse('1950-05-01'))->femmina()->create();
    $famiglia = Famiglia::factory()->create(['nome_famiglia' => 'Famiglia Della Mamma', 'data_creazione' => '1980-03-12', 'tipo' => TipoFamiglia::MammaDiVocazione]);
    $famiglia->assegnaCapoFamiglia($mamma);

    $this->get(route('nomadelfia.person.profile', $mamma->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Diventa mamma di vocazione', 'Nasce la famiglia Famiglia Della Mamma', 'Nascita'])
        ->assertSeeInOrder(['Stato familiare', 'Nubile', 'Mamma di vocazione', 'dal 12 mar 1980'])
        ->assertDontSee('Sposa');
});

it('shows a sacerdote as celibe with the ordination in the timeline', function (): void {
    login();
    $prete = Persona::factory()->nato(Carbon::parse('1950-05-01'))->maschio()->create();
    $prete->assegnaStato(Stato::perNome('sacerdote'), '1978-06-29');

    $this->get(route('nomadelfia.person.profile', $prete->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Ordinato sacerdote', 'Nascita'])
        ->assertSeeInOrder(['Stato familiare', 'Celibe', 'Sacerdote', 'dal 29 giu 1978']);
});

it('forbids the profile to a user without the permission to see the popolazione', function (): void {
    $user = User::query()->create(['username' => 'senza-permessi', 'email' => 'senza-permessi@email.it', 'password' => 'secret', 'persona_id' => 0]);
    login($user);
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertForbidden();
});

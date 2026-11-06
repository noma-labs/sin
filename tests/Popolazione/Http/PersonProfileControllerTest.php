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

it('shows the profile of a person present in Nomadelfia', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::parse('1995-09-15'), GruppoFamiliare::all()->random());

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSee('Presente')
        ->assertSeeInOrder(['Situazione attuale', 'Percorso in Nomadelfia', 'Famiglia'])
        ->assertSeeInOrder(['Entra in Nomadelfia come Ospite', 'Nascita']);
});

it('shows the nome and cognome of a person in the header with the nominativo they are known by', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create(['nome' => 'Giovanni', 'cognome' => 'Bianchi', 'nominativo' => 'Gianni B.']);

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Giovanni Bianchi', 'Conosciuto come', 'Gianni B.']);
});

it('shows the situation of the person in the row below the name', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create(['nome' => 'Marco', 'cognome' => 'Gialli']);
    $gruppo = GruppoFamiliare::all()->random();
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::parse('2020-03-01'), $gruppo);

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Marco Gialli', 'Presente', 'dal 1 mar 2020', 'Ospite', 'Celibe', "Gruppo {$gruppo->nome}", 'Sesso']);
});

it('does not repeat the nominativo when it is the nome and cognome of the person', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create(['nome' => 'Luca', 'cognome' => 'Verdi', 'nominativo' => 'Verdi Luca']);

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSee('Luca Verdi')
        ->assertDontSee('Conosciuto come');
});

it('shows the biografia in the header with the form to edit only the biografia', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create(['biografia' => 'Arrivato a Nomadelfia da bambino']);

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Arrivato a Nomadelfia da bambino', 'Modifica biografia', route('anagrafe.person.biografia.update', $persona->id), 'Situazione attuale']);
});

it('offers to add a biografia to a person without one', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create(['biografia' => null]);

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSee('Aggiungi una biografia');
});

it('shows the sesso and the missing codice fiscale of a person in the header', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->femmina()->create(['cf' => null]);

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Nessun codice fiscale', 'Sesso', 'Femmina', 'Nascita']);
});

it('links the people of a gruppo familiare to their profile', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $gruppo = GruppoFamiliare::all()->random();
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::now(), $gruppo);

    $this->get(route('nomadelfia.gruppifamiliari.show', $gruppo->id))
        ->assertSuccessful()
        ->assertSee(route('nomadelfia.person.profile', $persona->id));
});

it('renders the popolazione history of a person', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();

    $this->get(route('nomadelfia.join-leave-history.index', $persona->id))
        ->assertSuccessful();
});

it('offers a nuova famiglia but not to be added as figlio to a person in the nucleo of a famiglia', function (string $posizione): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia = Famiglia::factory()->create();
    $posizione === 'CAPO FAMIGLIA' ? $famiglia->assegnaCapoFamiglia($persona) : $famiglia->assegnaFiglioNato($persona);

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSee('Nuova famiglia')
        ->assertDontSee('Aggiungi come figlio');
})->with(['CAPO FAMIGLIA', 'FIGLIO NATO']);

it('offers both family actions to an adult without a family', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSee('Nessuna famiglia')
        ->assertSee('Nuova famiglia')
        ->assertSee('Aggiungi come figlio');
});

it('offers a nuova famiglia for the storico to a person who was already capofamiglia', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia = Famiglia::factory()->create();
    $famiglia->assegnaCapoFamiglia($persona);
    $famiglia->uscitaDalNucleoFamiliare($persona);

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSee('Nuova famiglia');
});

it('does not show a former wife as the spouse of a capofamiglia', function (): void {
    login();
    $marito = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia = Famiglia::factory()->create();
    $famiglia->assegnaCapoFamiglia($marito);
    $famiglia->assegnaMoglie(Persona::factory()->maggiorenne()->femmina()->create());
    $famiglia->uscitaDalNucleoFamiliare($famiglia->componenti()->wherePivot('posizione_famiglia', 'MOGLIE')->firstOrFail());

    $this->get(route('nomadelfia.person.profile', $marito->id))
        ->assertSuccessful()
        ->assertDontSee('Coniuge e figli');
});

it('does not show a family of origin for a single', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    Famiglia::factory()->create()->componenti()->attach($persona->id, ['stato' => '1', 'posizione_famiglia' => 'SINGLE']);

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSee('Single')
        ->assertDontSee("Famiglia d'origine", false);
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

it('shows the percorso familiare from the famiglia attuale back to the one of birth with the role in each one', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1980-04-10'))->femmina()->create();
    Famiglia::factory()->create(['nome_famiglia' => 'Propria', 'data_creazione' => '2005-09-01'])
        ->assegnaMoglie($persona);
    Famiglia::factory()->create(['nome_famiglia' => 'Che Accoglie', 'data_creazione' => '1960-01-01'])
        ->assegnaFiglioAccolto($persona, stato: '0');
    Famiglia::factory()->create(['nome_famiglia' => 'Di Nascita', 'data_creazione' => '1975-01-01'])
        ->assegnaFiglioNato($persona, stato: '0');

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSeeInOrder([
            'Percorso familiare',
            'Moglie', 'Propria', 'Dal 01/09/2005', 'nel nucleo',
            'Figlia accolta', 'Che Accoglie', 'Data di ingresso non registrata', 'non più nel nucleo',
            'Figlia nata', 'Di Nascita', 'Dalla nascita 10/04/1980', 'non più nel nucleo',
        ]);
});

it('shows the percorso familiare of a person married twice from the most recent famiglia', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    Famiglia::factory()->create(['nome_famiglia' => 'Prime Nozze', 'data_creazione' => '1980-01-01'])
        ->assegnaCapoFamiglia($persona, stato: '0');
    Famiglia::factory()->create(['nome_famiglia' => 'Seconde Nozze', 'data_creazione' => '1995-01-01'])
        ->assegnaCapoFamiglia($persona, stato: '0');

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Percorso familiare', 'Seconde Nozze', 'Dal 01/01/1995', 'Prime Nozze', 'Dal 01/01/1980']);
});

it('shows the famiglie of a figlio accolto moved inside the comunità in the order of the data di entrata', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('2004-03-02'))->femmina()->create();
    $terza = Famiglia::factory()->create(['nome_famiglia' => 'Terza Accoglienza', 'data_creazione' => '1970-01-01']);
    $seconda = Famiglia::factory()->create(['nome_famiglia' => 'Seconda Accoglienza', 'data_creazione' => '1980-01-01']);
    $prima = Famiglia::factory()->create(['nome_famiglia' => 'Prima Accoglienza', 'data_creazione' => '1990-01-01']);
    $capofamiglia = Persona::factory()->maggiorenne()->maschio()->create();
    app(EntrataMaggiorenneConFamigliaAction::class)->execute($capofamiglia, Carbon::parse('1990-01-01'), GruppoFamiliare::all()->random());
    $prima->assegnaCapoFamiglia($capofamiglia);
    app(EntrataMinorenneAccoltoAction::class)->execute($persona, Carbon::parse('2010-07-01'), $prima);
    $prima->uscitaDalNucleoFamiliare($persona);
    $terza->assegnaFiglioAccolto($persona, dataEntrata: '2018-03-01');
    $seconda->assegnaFiglioAccolto($persona, stato: '0', dataEntrata: '2014-09-01');

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSeeInOrder([
            'Percorso familiare',
            'Terza Accoglienza', 'Dal 01/03/2018', 'nel nucleo',
            'Seconda Accoglienza', 'Dal 01/09/2014', 'non più nel nucleo',
            'Prima Accoglienza', 'Dal 01/07/2010', 'non più nel nucleo',
        ]);
});

it('shows the figlio accolto from another famiglia in the timeline at the data di entrata in the famiglia', function (): void {
    login();
    $marito = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    $accolto = Persona::factory()->nato(Carbon::parse('2004-03-02'))->femmina()->create(['nominativo' => 'Figlia Spostata']);
    $famiglia = Famiglia::factory()->create(['data_creazione' => '2012-06-12']);
    $famiglia->assegnaCapoFamiglia($marito);
    $famiglia->assegnaMoglie(Persona::factory()->maggiorenne()->femmina()->create());
    $primaAccoglienza = Famiglia::factory()->create();
    $altroCapofamiglia = Persona::factory()->maggiorenne()->maschio()->create();
    app(EntrataMaggiorenneConFamigliaAction::class)->execute($altroCapofamiglia, Carbon::parse('1990-01-01'), GruppoFamiliare::all()->random());
    $primaAccoglienza->assegnaCapoFamiglia($altroCapofamiglia);
    app(EntrataMinorenneAccoltoAction::class)->execute($accolto, Carbon::parse('2010-07-01'), $primaAccoglienza);
    $primaAccoglienza->uscitaDalNucleoFamiliare($accolto);
    $famiglia->assegnaFiglioAccolto($accolto, dataEntrata: '2014-09-01');

    $this->get(route('nomadelfia.person.profile', $marito->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Accoglie la figlia Figlia Spostata', 'Nasce la famiglia']);
});

it('does not show the percorso familiare of a person with a single famiglia', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    Famiglia::factory()->create()->assegnaCapoFamiglia($persona);

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertDontSee('Percorso familiare');
});

it('shows the last famiglia of a person outside every nucleo with the coniuge', function (): void {
    login();
    $marito = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    $vedova = Persona::factory()->nato(Carbon::parse('1972-02-01'))->femmina()->create(['nominativo' => 'Vedova Da Mostrare']);
    $famiglia = Famiglia::factory()->create(['nome_famiglia' => 'Del Defunto']);
    $famiglia->assegnaCapoFamiglia($marito, stato: '0');
    $famiglia->assegnaMoglie($vedova);

    $this->get(route('nomadelfia.person.profile', $marito->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Famiglia Del Defunto', 'Capo famiglia', 'non più nel nucleo', 'Coniuge e figli', 'Vedova Da Mostrare'])
        ->assertDontSee('Nessuna famiglia');
});

it('shows a married person as sposato from the creation of the famiglia', function (): void {
    login();
    $marito = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    $famiglia = Famiglia::factory()->coniugi()->create(['data_creazione' => '1998-06-12']);
    $famiglia->assegnaCapoFamiglia($marito);
    $famiglia->assegnaMoglie(Persona::factory()->nato(Carbon::parse('1972-02-01'))->femmina()->create());

    $this->get(route('nomadelfia.person.profile', $marito->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Stato familiare', 'Sposato', 'dal 12 giu 1998']);
});

it('shows a married person as sposato without a date when the famiglia has no data_creazione', function (): void {
    login();
    $marito = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    $famiglia = Famiglia::factory()->coniugi()->create(['data_creazione' => null]);
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
    $famiglia = Famiglia::factory()->coniugi()->create(['data_creazione' => '1968-06-12']);
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

it('links each page of a person only once from the profile', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create(['numero_elenco' => null]);
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::now(), GruppoFamiliare::all()->random());

    $pagina = $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSee('Scheda scuola')
        ->getContent();

    $paginePersona = [
        route('nomadelfia.join-leave-history.index', $persona->id),
        route('anagrafe.person.families', $persona->id),
        route('nomadelfia.persone.aziende', ['idPersona' => $persona->id]),
        route('nomadelfia.person.position.index', $persona->id),
        route('nomadelfia.persone.stato', $persona->id),
        route('nomadelfia.person.gruppo', $persona->id),
        route('nomadelfia.folder-number.create', $persona->id),
        route('scuola.student.show', $persona->id),
    ];
    foreach ($paginePersona as $url) {
        expect(mb_substr_count((string) $pagina, '"'.$url.'"'))->toBe(1, "$url is linked more than once");
    }
});

it('forbids the profile to a user without the permission to see the popolazione', function (): void {
    $user = User::query()->create(['username' => 'senza-permessi', 'email' => 'senza-permessi@email.it', 'password' => 'secret', 'persona_id' => 0]);
    login($user);
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertForbidden();
});

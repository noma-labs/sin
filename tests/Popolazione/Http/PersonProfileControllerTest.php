<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Admin\Models\User;
use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Famiglia\Models\TipoFamiglia;
use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\Azienda\Models\Azienda;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Incarico\Models\Incarico;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\AssegnaAziendaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\AssegnaIncaricoAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\ChangeGruppoFamiliareAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneConFamigliaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMinorenneAccoltoAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\UscitaPersonaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Stato;
use Carbon\Carbon;
use Illuminate\Support\Str;

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

it('shows the coniuge in the famiglia by nome and cognome with the nominativo only when it differs', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia = Famiglia::factory()->create(['tipo' => TipoFamiglia::Coniugi]);
    $famiglia->assegnaCapoFamiglia($persona);
    $famiglia->assegnaMoglie(Persona::factory()->maggiorenne()->femmina()->create(['nome' => 'Maria', 'cognome' => 'Neri', 'nominativo' => 'Mariuccia']));

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Coniuge e figli', 'Maria Neri', '(Mariuccia)', 'Moglie']);
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
    $famiglia->assegnaMoglie(Persona::factory()->maggiorenne()->femmina()->create(['nome' => 'Moglie', 'cognome' => 'Uscita']));
    $famiglia->uscitaDalNucleoFamiliare($famiglia->componenti()->wherePivot('posizione_famiglia', 'MOGLIE')->firstOrFail());

    $response = $this->get(route('nomadelfia.person.profile', $marito->id));

    $response->assertSuccessful();
    expect(Str::after((string) $response->getContent(), 'Coniuge e figli'))->not->toContain('Moglie Uscita');
});

it('shows the genitori first, then the coniuge and the figli', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    $origine = Famiglia::factory()->coniugi()->create();
    $origine->assegnaCapoFamiglia(Persona::factory()->nato(Carbon::parse('1940-01-01'))->maschio()->create(['nome' => 'Padre', 'cognome' => 'Elencato']));
    $origine->assegnaFiglioNato($persona, stato: '0');
    $propria = Famiglia::factory()->coniugi()->create(['data_creazione' => '1995-06-01']);
    $propria->assegnaCapoFamiglia($persona);
    $propria->assegnaMoglie(Persona::factory()->nato(Carbon::parse('1972-01-01'))->femmina()->create(['nome' => 'Moglie', 'cognome' => 'Elencata']));
    $propria->assegnaFiglioNato(Persona::factory()->nato(Carbon::parse('2000-01-01'))->maschio()->create(['nome' => 'Figlio', 'cognome' => 'Elencato']));

    $response = $this->get(route('nomadelfia.person.profile', $persona->id));

    $response->assertSuccessful();
    expect(Str::after((string) $response->getContent(), 'Parentele'))
        ->toMatch('/Genitori.*Padre Elencato.*Coniuge e figli.*Moglie Elencata.*Figlio Elencato/s');
});

it('marks the page as the profile of a persona', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->femmina()->create(['nome' => 'Maria', 'cognome' => 'Bianchi']);

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Persona', 'Maria Bianchi']);
});

it('shows the genitori of the last famiglia d\'origine of a figlio accolto, with all of them to choose', function (): void {
    login();
    $figlio = Persona::factory()->minorenne()->maschio()->create();
    $nascita = Famiglia::factory()->coniugi()->create(['nome_famiglia' => 'Origine Naturale Del Figlio']);
    $nascita->assegnaCapoFamiglia(Persona::factory()->maggiorenne()->maschio()->create(['nome' => 'Padre', 'cognome' => 'Naturale']));
    $nascita->assegnaFiglioNato($figlio, stato: '0');
    $vocazione = Famiglia::factory()->create(['nome_famiglia' => 'Origine Vocazione Del Figlio', 'tipo' => TipoFamiglia::MammaDiVocazione]);
    $vocazione->assegnaCapoFamiglia(Persona::factory()->maggiorenne()->femmina()->create(['nome' => 'Mamma', 'cognome' => 'Vocazione']));
    $vocazione->assegnaFiglioAccolto($figlio, dataEntrata: '2020-09-01');

    $response = $this->get(route('nomadelfia.person.profile', $figlio->id));

    $response->assertSuccessful();
    expect(Str::after((string) $response->getContent(), 'Parentele'))
        ->toContain('Origine Naturale Del Figlio · nato')
        ->toContain('Origine Vocazione Del Figlio · accolto 2020')
        ->toContain('Mamma Vocazione')
        ->not->toContain('Padre Naturale');
});

it('shows the genitori of the famiglia d\'origine chosen in the parentele, where the fratelli are added', function (): void {
    login();
    $figlio = Persona::factory()->minorenne()->maschio()->create();
    $nascita = Famiglia::factory()->coniugi()->create(['nome_famiglia' => 'Origine Naturale Scelta']);
    $nascita->assegnaCapoFamiglia(Persona::factory()->maggiorenne()->maschio()->create(['nome' => 'Padre', 'cognome' => 'Scelto']));
    $nascita->assegnaFiglioNato($figlio, stato: '0');
    $vocazione = Famiglia::factory()->create(['nome_famiglia' => 'Origine Vocazione Non Scelta', 'tipo' => TipoFamiglia::MammaDiVocazione]);
    $vocazione->assegnaCapoFamiglia(Persona::factory()->maggiorenne()->femmina()->create(['nome' => 'Mamma', 'cognome' => 'Nonscelta']));
    $vocazione->assegnaFiglioAccolto($figlio, dataEntrata: '2020-09-01');

    $response = $this->get(route('nomadelfia.person.profile', ['id' => $figlio->id, 'origine' => $nascita->id]));

    $response->assertSuccessful()
        ->assertSeeInOrder(['Genitori', route('anagrafe.families.show', $nascita->id), 'Famiglia Origine Naturale Scelta'])
        ->assertSee(route('anagrafe.family.member.store', ['id' => $nascita->id]))
        ->assertSee(route('anagrafe.person.families', ['id' => $figlio->id, 'origine' => $nascita->id]));
    expect(Str::after((string) $response->getContent(), 'Parentele'))
        ->toContain('Padre Scelto')
        ->not->toContain('Mamma Nonscelta');
});

it('shows the famiglia attuale of a nomadelfa mamma who married, with the earlier famiglia to choose', function (): void {
    login();
    $mamma = Persona::factory()->cinquantenne()->femmina()->create();
    $nomadelfaMamma = Famiglia::factory()->create(['nome_famiglia' => 'Unione Nomadelfa Mamma', 'tipo' => TipoFamiglia::NomadelfaMamma, 'data_creazione' => '1985-03-01']);
    $nomadelfaMamma->assegnaCapoFamiglia($mamma, stato: '0');
    $nomadelfaMamma->assegnaFiglioAccolto(Persona::factory()->maggiorenne()->maschio()->create(['nome' => 'Figlio', 'cognome' => 'Accoltoprima']), stato: '0');
    $matrimonio = Famiglia::factory()->coniugi()->create(['nome_famiglia' => 'Unione Matrimonio', 'data_creazione' => '1995-06-10']);
    $matrimonio->assegnaCapoFamiglia(Persona::factory()->cinquantenne()->maschio()->create(['nome' => 'Marito', 'cognome' => 'Sposato']));
    $matrimonio->assegnaMoglie($mamma);

    $response = $this->get(route('nomadelfia.person.profile', $mamma->id));

    $response->assertSuccessful();
    expect(Str::after((string) $response->getContent(), 'Coniuge e figli'))
        ->toContain('Nomadelfa mamma · dal 1985')
        ->toContain('Coniugi · ⚭ 1995')
        ->toContain('Marito Sposato')
        ->not->toContain('Figlio Accoltoprima');
});

it('shows the coniuge and the figli of the unione chosen in the parentele, where the figli are added', function (): void {
    login();
    $mamma = Persona::factory()->cinquantenne()->femmina()->create();
    $nomadelfaMamma = Famiglia::factory()->create(['nome_famiglia' => 'Unione Scelta Mamma', 'tipo' => TipoFamiglia::NomadelfaMamma, 'data_creazione' => '1985-03-01']);
    $nomadelfaMamma->assegnaCapoFamiglia($mamma, stato: '0');
    $nomadelfaMamma->assegnaFiglioAccolto(Persona::factory()->maggiorenne()->maschio()->create(['nome' => 'Figlio', 'cognome' => 'Dellamamma']), stato: '0');
    $matrimonio = Famiglia::factory()->coniugi()->create(['nome_famiglia' => 'Unione Non Scelta', 'data_creazione' => '1995-06-10']);
    $matrimonio->assegnaCapoFamiglia(Persona::factory()->cinquantenne()->maschio()->create(['nome' => 'Marito', 'cognome' => 'Nonscelto']));
    $matrimonio->assegnaMoglie($mamma);

    $response = $this->get(route('nomadelfia.person.profile', ['id' => $mamma->id, 'unione' => $nomadelfaMamma->id]));

    $response->assertSuccessful()
        ->assertSeeInOrder(['Coniuge e figli', route('anagrafe.families.show', $nomadelfaMamma->id)])
        ->assertSee('data-bs-target="#modal-aggiungi-figlio"', false)
        ->assertSee(route('anagrafe.family.member.store', ['id' => $nomadelfaMamma->id]));
    expect(Str::after((string) $response->getContent(), 'Coniuge e figli'))
        ->toContain('Figlio Dellamamma')
        ->not->toContain('Marito Nonscelto');
});

it('offers to add the genitori to a person outside every nucleo without famiglia d\'origine', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Parentele', 'Genitori', 'Aggiungi padre', 'Aggiungi madre']);
});

it('shows the genitori not registered of a person in a nucleo without famiglia d\'origine', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    Famiglia::factory()->coniugi()->create()->assegnaCapoFamiglia($persona);

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Padre non registrato', 'Madre non registrata'])
        ->assertDontSee('Aggiungi padre');
});

it('offers to add the missing genitore to the famiglia d\'origine of coniugi', function (): void {
    login();
    $figlio = Persona::factory()->minorenne()->maschio()->create();
    $origine = Famiglia::factory()->coniugi()->create();
    $origine->assegnaCapoFamiglia(Persona::factory()->maggiorenne()->maschio()->create(['nome' => 'Padre', 'cognome' => 'Presente']));
    $origine->assegnaFiglioNato($figlio);

    $this->get(route('nomadelfia.person.profile', $figlio->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Genitori', 'Padre Presente', 'Aggiungi madre'])
        ->assertDontSee('Aggiungi padre');
});

it('does not ask for a padre for the figlio of a mamma di vocazione', function (): void {
    login();
    $figlio = Persona::factory()->minorenne()->maschio()->create();
    $origine = Famiglia::factory()->create(['tipo' => TipoFamiglia::MammaDiVocazione]);
    $origine->assegnaCapoFamiglia(Persona::factory()->maggiorenne()->femmina()->create());
    $origine->assegnaFiglioAccolto($figlio);

    $this->get(route('nomadelfia.person.profile', $figlio->id))
        ->assertSuccessful()
        ->assertDontSee('Aggiungi padre')
        ->assertDontSee('Padre non registrato');
});

it('offers to add the coniuge, the figli and the fratelli to the famiglie of the persona', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $origine = Famiglia::factory()->coniugi()->create();
    $origine->assegnaFiglioNato($persona, stato: '0');
    Famiglia::factory()->coniugi()->create()->assegnaCapoFamiglia($persona);

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSee('data-bs-target="#modal-aggiungi-coniuge"', false)
        ->assertSee('data-bs-target="#modal-aggiungi-figlio"', false)
        ->assertSee('data-bs-target="#modal-aggiungi-figlia"', false)
        ->assertSee('data-bs-target="#modal-aggiungi-fratello"', false)
        ->assertSee('data-bs-target="#modal-aggiungi-sorella"', false)
        ->assertSeeInOrder(['Aggiungi la moglie', 'Aggiungi un figlio', 'Figlio nato', 'Aggiungi una figlia', 'Figlia accolta', 'Aggiungi un fratello', 'Aggiungi una sorella']);
});

it('offers a famiglia of coniugi and tells why figli and fratelli cannot be added to a persona without famiglie', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->femmina()->create();

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSee(route('anagrafe.families.create', ['persona' => $persona->id, 'tipo' => TipoFamiglia::Coniugi->value]))
        ->assertSeeInOrder(['Figlio', 'serve una famiglia', 'Fratello', 'servono i genitori'])
        ->assertDontSee('data-bs-target="#modal-aggiungi-figlio"', false);
});

it('does not offer a second coniuge to a persona with the coniuge in the famiglia', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia = Famiglia::factory()->coniugi()->create();
    $famiglia->assegnaCapoFamiglia($persona);
    $famiglia->assegnaMoglie(Persona::factory()->maggiorenne()->femmina()->create());

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Coniuge', 'già presente'])
        ->assertDontSee('data-bs-target="#modal-aggiungi-coniuge"', false);
});

it('offers to add a parente to the famiglia of a capofamiglia', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    Famiglia::factory()->coniugi()->create()->assegnaCapoFamiglia($persona);

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Coniuge e figli', 'Aggiungi parente']);
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

it('shows the entrata in a gruppo familiare with the previous one, and in the entrata in the comunità when it is the same day', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    [$primo, $secondo] = GruppoFamiliare::query()->take(2)->get()->all();
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::parse('1995-09-15'), $primo);
    app(ChangeGruppoFamiliareAction::class)->execute($persona, $primo, Carbon::parse('1995-09-15'), Carbon::parse('2001-03-10'), $secondo, Carbon::parse('2001-03-10'));

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSeeInOrder([
            'Gruppo familiare', "Entra nel gruppo familiare {$secondo->nome}", '10 mar 2001', "Prima: {$primo->nome}",
            'Entra in Nomadelfia come Ospite', '15 set 1995', "Nel gruppo familiare {$primo->nome}",
            'Nascita',
        ])
        ->assertDontSee("Entra nel gruppo familiare {$primo->nome}");
});

it('shows the marriage and the children born in the timeline by nome and cognome with the nominativo when it differs', function (): void {
    login();
    $marito = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    $moglie = Persona::factory()->nato(Carbon::parse('1972-02-01'))->femmina()->create(['nome' => 'Maria', 'cognome' => 'Sposata', 'nominativo' => 'Moglie Sposata']);
    $figlio = Persona::factory()->nato(Carbon::parse('2000-08-20'))->maschio()->create(['nome' => 'Figlio', 'cognome' => 'Nato', 'nominativo' => 'Figlio Nato']);
    $famiglia = Famiglia::factory()->create(['data_creazione' => '1998-06-12']);
    $famiglia->assegnaCapoFamiglia($marito);
    $famiglia->assegnaMoglie($moglie);
    $famiglia->assegnaFiglioNato($figlio);

    $this->get(route('nomadelfia.person.profile', $marito->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Nasce il figlio Figlio Nato', 'Sposa Maria Sposata (Moglie Sposata)', 'Nascita'])
        ->assertSeeInOrder(['Coniuge e figli', 'Maria Sposata', 'Moglie Sposata', 'Figlio Nato'])
        ->assertSee(route('nomadelfia.person.profile', $figlio->id));
});

it('shows the children in the nucleo and the ones gone out in one list in order of birth', function (): void {
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
        ->assertSeeInOrder(['Coniuge e figli', 'Figlio Uscito', 'Figlio Nel Nucleo'])
        ->assertDontSee('Figli non più nel nucleo');
});

it('shows the figlio accolto in the timeline at the date of the entrata', function (): void {
    login();
    $marito = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    $accolto = Persona::factory()->nato(Carbon::parse('2004-03-02'))->femmina()->create(['nome' => 'Figlia', 'cognome' => 'Accolta', 'nominativo' => 'Figlia Accolta']);
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
        ->assertSeeInOrder(['Genitori', 'Padre Da Mostrare', 'Padre'])
        ->assertDontSee("Famiglia d'origine", false);
});

it('shows the percorso familiare in the timeline from the famiglia attuale back to the one of birth', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1980-04-10'))->femmina()->create();
    $propria = Famiglia::factory()->create(['nome_famiglia' => 'Propria', 'data_creazione' => '2005-09-01']);
    $propria->assegnaMoglie($persona);
    $propria->assegnaCapoFamiglia(Persona::factory()->maggiorenne()->maschio()->create(['nome' => 'Marito', 'cognome' => 'Sposato', 'nominativo' => 'Marito Sposato']));
    $accoglie = Famiglia::factory()->create(['nome_famiglia' => 'Che Accoglie', 'data_creazione' => '1960-01-01']);
    $accoglie->assegnaFiglioAccolto($persona, stato: '0', dataEntrata: '1990-06-01');
    Famiglia::factory()->create(['nome_famiglia' => 'Di Nascita', 'data_creazione' => '1975-01-01'])
        ->assegnaFiglioNato($persona, stato: '0');

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertDontSee('Percorso familiare')
        ->assertSeeInOrder([
            'Percorso in Nomadelfia',
            'Sposa Marito Sposato', 'Nasce la famiglia Propria',
            'Accolta nella famiglia Che Accoglie', '1 giu 1990',
            'Nascita', 'Nella famiglia Di Nascita',
        ])
        ->assertSee(route('anagrafe.families.show', $accoglie->id));
});

it('shows the famiglie of a person married twice in the timeline from the most recent', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    Famiglia::factory()->create(['nome_famiglia' => 'Prime Nozze', 'data_creazione' => '1980-01-01'])
        ->assegnaCapoFamiglia($persona, stato: '0');
    Famiglia::factory()->create(['nome_famiglia' => 'Seconde Nozze', 'data_creazione' => '1995-01-01'])
        ->assegnaCapoFamiglia($persona, stato: '0');

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Nasce la famiglia Seconde Nozze', '1 gen 1995', 'Nasce la famiglia Prime Nozze', '1 gen 1980']);
});

it('keeps a famiglia without a known date in the timeline after the previous famiglia of the percorso familiare', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1980-04-10'))->femmina()->create();
    Famiglia::factory()->create(['nome_famiglia' => 'Propria Senza Data', 'data_creazione' => null])
        ->assegnaMoglie($persona);
    Famiglia::factory()->create(['nome_famiglia' => 'Accoglie Senza Data', 'data_creazione' => '1960-01-01'])
        ->assegnaFiglioAccolto($persona, stato: '0');
    Famiglia::factory()->create(['nome_famiglia' => 'Nascita Senza Data', 'data_creazione' => '1975-01-01'])
        ->assegnaFiglioNato($persona, stato: '0');

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSeeInOrder([
            'Moglie nella famiglia Propria Senza Data', 'Data non registrata',
            'Accolta nella famiglia Accoglie Senza Data', 'Data non registrata',
            'Nascita', 'Nella famiglia Nascita Senza Data',
        ]);
});

it('shows the famiglie of a figlio accolto moved inside the comunità in the timeline at the data di entrata', function (): void {
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
            'Accolta nella famiglia Terza Accoglienza', '1 mar 2018',
            'Accolta nella famiglia Seconda Accoglienza', '1 set 2014',
            'Accolta nella famiglia Prima Accoglienza', '1 lug 2010',
            'Nascita',
        ]);
});

it('shows the figlio accolto from another famiglia in the timeline at the data di entrata in the famiglia', function (): void {
    login();
    $marito = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    $accolto = Persona::factory()->nato(Carbon::parse('2004-03-02'))->femmina()->create(['nome' => 'Figlia', 'cognome' => 'Spostata', 'nominativo' => 'Figlia Spostata']);
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

it('shows the last famiglia of a person outside every nucleo with the coniuge', function (): void {
    login();
    $marito = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    $vedova = Persona::factory()->nato(Carbon::parse('1972-02-01'))->femmina()->create(['nominativo' => 'Vedova Da Mostrare']);
    $famiglia = Famiglia::factory()->create(['nome_famiglia' => 'Del Defunto']);
    $famiglia->assegnaCapoFamiglia($marito, stato: '0');
    $famiglia->assegnaMoglie($vedova);

    $this->get(route('nomadelfia.person.profile', $marito->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Coniuge e figli', 'Famiglia Del Defunto', 'Vedova Da Mostrare'])
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
    $marito = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create(['nome' => 'Marito', 'cognome' => 'Defunto', 'nominativo' => 'Marito Defunto', 'data_decesso' => '2015-11-03']);
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
    $marito = Persona::factory()->nato(Carbon::parse('1940-05-01'))->maschio()->create(['nome' => 'Marito', 'cognome' => 'Capofamiglia', 'nominativo' => 'Marito Capofamiglia', 'data_decesso' => '2015-11-03']);
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
        route('anagrafe.person.families', $persona->id),
        route('nomadelfia.persone.aziende', ['idPersona' => $persona->id]),
        route('nomadelfia.person.position.index', $persona->id),
        route('nomadelfia.persone.stato', $persona->id),
        route('nomadelfia.person.gruppo', $persona->id),
        route('nomadelfia.folder-number.create', $persona->id),
        route('scuola.student.show', $persona->id),
    ];
    foreach ($paginePersona as $url) {
        expect(mb_substr_count((string) $pagina, 'href="'.$url.'"'))->toBe(1, "$url is linked more than once");
    }
});

it('shows next to the name that the numero elenco is missing', function (): void {
    login();
    $senzaNumero = Persona::factory()->maggiorenne()->maschio()->create(['numero_elenco' => null]);
    $numeroElenco = 'Z'.fake()->unique()->numberBetween(10000, 99999);
    $conNumero = Persona::factory()->maggiorenne()->maschio()->numeroElenco($numeroElenco)->create();

    $this->get(route('nomadelfia.person.profile', $senzaNumero->id))
        ->assertSuccessful()
        ->assertSee('N° elenco mancante');

    $this->get(route('nomadelfia.person.profile', $conNumero->id))
        ->assertSuccessful()
        ->assertSee('N° elenco '.$numeroElenco)
        ->assertDontSee('N° elenco mancante');
});

it('offers next to the percorso the Aggiungi menu with the posizioni other than the attuale, the move to another gruppo, an azienda, an incarico and a periodo storico', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $gruppo = GruppoFamiliare::first();
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::parse('2020-01-10'), $gruppo);

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Percorso in Nomadelfia', 'Aggiungi', 'Posizione', 'Effettivo', 'Postulante', 'Ospite', 'attuale', 'Da Definire', 'Gruppo familiare', 'Sposta in un altro gruppo', 'Lavoro', 'Azienda', 'Incarico', 'Entrate e uscite', 'Periodo storico', 'Comunità', 'Famiglia'])
        ->assertSee(['#modal-posizione-effe', '#modal-posizione-post', '#modal-posizione-dade'])
        ->assertDontSee('modal-posizione-ospp')
        ->assertDontSee('modal-posizione-figl')
        ->assertSee(route('nomadelfia.persone.gruppo.sposta', ['id' => $persona->id, 'idGruppo' => $gruppo->id]))
        ->assertSee(route('nomadelfia.persone.aziende.assegna', ['idPersona' => $persona->id]))
        ->assertSee(route('nomadelfia.persone.incarichi.assegna', $persona->id))
        ->assertSee(route('nomadelfia.join-leave-history.store', $persona->id));
});

it('offers to put in a gruppo a persona without gruppo familiare', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSee('Aggiungi a un gruppo')
        ->assertDontSee('Sposta in un altro gruppo')
        ->assertSee(route('nomadelfia.person.gruppo.store', $persona->id));
});

it('does not show the Aggiungi menu nor the corrections of the entrate and uscite to a user who cannot insert or modify a person', function (): void {
    $user = User::query()->create(['username' => 'solo-visualizza', 'email' => 'solo-visualizza@email.it', 'password' => 'secret', 'persona_id' => 0]);
    $user->givePermissionTo('popolazione.persona.visualizza');
    login($user);
    $persona = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    PopolazioneNomadelfia::query()->insert(['persona_id' => $persona->id, 'data_entrata' => '1990-01-15', 'data_uscita' => '1995-07-20']);

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertDontSee('modal-gruppo-familiare')
        ->assertDontSee('modal-azienda')
        ->assertDontSee('modal-incarico')
        ->assertDontSee('modal-periodo-storico')
        ->assertDontSee(route('nomadelfia.join.update', ['id' => $persona->id, 'entrata' => '1990-01-15']))
        ->assertDontSee(route('nomadelfia.leave.update', ['id' => $persona->id, 'uscita' => '1995-07-20']));
});

it('shows the aziende and the incarichi in the header linking to their pages', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $azienda = Azienda::factory()->create(['nome_azienda' => 'Falegnameria']);
    app(AssegnaAziendaAction::class)->execute($persona, $azienda, Carbon::parse('2021-04-01'), Azienda::MANSIONE_LAVORATORE);
    $incarico = Incarico::factory()->create(['nome' => 'Cucina']);
    app(AssegnaIncaricoAction::class)->execute($persona, $incarico, Carbon::parse('2022-05-01'));

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSeeInOrder([route('nomadelfia.aziende.edit', $azienda->id), "Nell'azienda dal 1 apr 2021", 'Falegnameria', 'lavoratore', 'Sesso'], false)
        ->assertSeeInOrder([route('nomadelfia.incarichi.edit', $incarico->id), 'Incarico dal 1 mag 2022', 'Cucina', 'Sesso'], false);
});

it('shows the posizione and the gruppo as mancanti for a persona presente without them', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::parse('2020-01-10'), GruppoFamiliare::first());
    $persona->posizioni()->detach();
    $persona->gruppifamiliari()->detach();

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Posizione mancante', 'Gruppo mancante', 'Sesso']);
});

it('lets correct from the percorso the entrate and the uscite, deleting only the closed periods', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    PopolazioneNomadelfia::query()->insert(['persona_id' => $persona->id, 'data_entrata' => '1990-01-15', 'data_uscita' => '1995-07-20']);
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::parse('2010-03-01'), GruppoFamiliare::all()->random());

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSeeInOrder([
            'Rientra in Nomadelfia', 'Dopo 14 anni 7 mesi fuori dalla comunità',
            route('nomadelfia.join.update', ['id' => $persona->id, 'entrata' => '2010-03-01']),
            'Esce da Nomadelfia',
            route('nomadelfia.leave.update', ['id' => $persona->id, 'uscita' => '1995-07-20']),
            route('nomadelfia.join-leave-history.destroy', ['id' => $persona->id, 'entrata' => '1990-01-15']),
            'Entra in Nomadelfia',
            route('nomadelfia.join.update', ['id' => $persona->id, 'entrata' => '1990-01-15']),
            'Nascita',
        ])
        ->assertDontSee(route('nomadelfia.join-leave-history.destroy', ['id' => $persona->id, 'entrata' => '2010-03-01']));
});

it('shows before saving a correction the posizioni, gruppi and aziende that move with the date', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    $gruppo = GruppoFamiliare::all()->random();
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::parse('2000-01-01'), $gruppo);
    $azienda = Azienda::factory()->create();
    app(AssegnaAziendaAction::class)->execute($persona, $azienda, Carbon::parse('2001-02-01'), 'LAVORATORE');
    app(UscitaPersonaAction::class)->execute($persona, Carbon::parse('2005-01-01'));

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSeeInOrder([
            'Modifica data uscita', 'Verranno spostate alla nuova data anche',
            'Posizione', 'Ospite', 'dal 1 gen 2000', 'al 1 gen 2005',
            'Gruppo familiare', $gruppo->nome, 'dal 1 gen 2000', 'al 1 gen 2005',
            'Azienda', $azienda->nome_azienda, 'dal 1 feb 2001', 'al 1 gen 2005',
            'Modifica data entrata', 'Verranno spostate alla nuova data anche',
            'Posizione', 'Ospite', 'dal 1 gen 2000',
            'Gruppo familiare', $gruppo->nome,
        ]);
});

it('says that only the date changes when nothing started or ended with it', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1930-04-10'))->maschio()->create();
    PopolazioneNomadelfia::query()->insert(['persona_id' => $persona->id, 'data_entrata' => '1955-06-01', 'data_uscita' => '1972-09-30']);

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSee('cambia solo la data di uscita.')
        ->assertSee('cambia solo la data di entrata.')
        ->assertDontSee('Verranno spostate alla nuova data anche');
});

it('offers to remove only the last data di uscita, showing the posizione and the gruppo that are reopened', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    PopolazioneNomadelfia::query()->insert(['persona_id' => $persona->id, 'data_entrata' => '1990-01-01', 'data_uscita' => '1995-12-31']);
    $gruppo = GruppoFamiliare::all()->random();
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::parse('2000-01-01'), $gruppo);
    app(UscitaPersonaAction::class)->execute($persona, Carbon::parse('2005-01-01'));

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSee("formEliminaDataUscita{$persona->id}-2005-01-01")
        ->assertDontSee("formEliminaDataUscita{$persona->id}-1995-12-31")
        ->assertSeeInOrder(['risulterà presente in Nomadelfia', 'Verranno riaperte anche', 'Posizione', 'Ospite', 'Gruppo familiare', $gruppo->nome]);
});

it('lets correct on the decesso the data di uscita of a deceased person, without removing it', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1930-04-10'))->maschio()->create(['data_decesso' => '2001-02-03']);
    PopolazioneNomadelfia::query()->insert(['persona_id' => $persona->id, 'data_entrata' => '1960-01-01', 'data_uscita' => '2001-02-03']);

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Decesso', route('nomadelfia.leave.update', ['id' => $persona->id, 'uscita' => '2001-02-03']), 'Entra in Nomadelfia'])
        ->assertDontSee('Esce da Nomadelfia')
        ->assertDontSee('Elimina data uscita');
});

it('forbids the profile to a user without the permission to see the popolazione', function (): void {
    $user = User::query()->create(['username' => 'senza-permessi', 'email' => 'senza-permessi@email.it', 'password' => 'secret', 'persona_id' => 0]);
    login($user);
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertForbidden();
});

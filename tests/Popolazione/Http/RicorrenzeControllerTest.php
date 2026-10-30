<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Admin\Models\User;
use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Famiglia\Models\TipoFamiglia;
use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\DecessoPersonaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use App\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\Ricorrenza;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Stato;
use Carbon\Carbon;
use Illuminate\Support\Collection;

function entraInNomadelfiaPerRicorrenze(Persona $persona, string $dataEntrata = '1990-01-15'): Persona
{
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::parse($dataEntrata), GruppoFamiliare::all()->random());

    return $persona;
}

/**
 * @param  Collection<string, Collection<int, Ricorrenza>>  $giorni
 */
function ricorrenzeDi(Collection $giorni, Persona $persona): Collection
{
    return $giorni->flatten(1)->filter(fn (Ricorrenza $ricorrenza): bool => $ricorrenza->persone->contains('id', $persona->id))->values();
}

it('lists the anniversaries of every tipo in the next 30 days ordered by day', function (): void {
    login();
    $this->travelTo(Carbon::parse('2026-10-30'));
    $marito = entraInNomadelfiaPerRicorrenze(Persona::factory()->nato(Carbon::parse('1950-01-01'))->maschio()->create(['nominativo' => 'Marito Delle Nozze']));
    $moglie = entraInNomadelfiaPerRicorrenze(Persona::factory()->nato(Carbon::parse('1952-01-01'))->femmina()->create(['nominativo' => 'Moglie Delle Nozze']));
    $famiglia = Famiglia::factory()->create(['data_creazione' => '1976-11-02', 'tipo' => TipoFamiglia::Coniugi]);
    $famiglia->assegnaCapoFamiglia($marito);
    $famiglia->assegnaMoglie($moglie);
    entraInNomadelfiaPerRicorrenze(Persona::factory()->nato(Carbon::parse('1980-01-01'))->maschio()->create(['nominativo' => 'Entrato Da Dodici Anni']), '2014-11-05');
    $mamma = entraInNomadelfiaPerRicorrenze(Persona::factory()->nato(Carbon::parse('1955-01-01'))->femmina()->create(['nominativo' => 'Mamma Da Quarant Anni']));
    Famiglia::factory()->create(['data_creazione' => '1986-11-10', 'tipo' => TipoFamiglia::MammaDiVocazione])->assegnaCapoFamiglia($mamma);
    $prete = entraInNomadelfiaPerRicorrenze(Persona::factory()->nato(Carbon::parse('1970-01-01'))->maschio()->create(['nominativo' => 'Prete Da Venticinque Anni']));
    $prete->assegnaStato(Stato::perNome('sacerdote'), '2001-11-15');
    $defunto = entraInNomadelfiaPerRicorrenze(Persona::factory()->nato(Carbon::parse('1930-01-01'))->maschio()->create(['nominativo' => 'Defunto Da Ricordare']), '1960-01-15');
    app(DecessoPersonaAction::class)->execute($defunto, Carbon::parse('2018-11-20'));

    $this->get(route('nomadelfia.ricorrenze'))
        ->assertSuccessful()
        ->assertSeeInOrder([
            'Nozze d’oro', '50 anni di matrimonio', 'Marito Delle Nozze', 'Moglie Delle Nozze', 'Sposati il 2 novembre 1976',
            '12 anni in Nomadelfia', 'Entrato Da Dodici Anni', 'Entrato il 5 novembre 2014',
            '40 anni come mamma di vocazione', 'Mamma Da Quarant Anni',
            'Giubileo d’argento', '25 anni di sacerdozio', 'Prete Da Venticinque Anni',
            '8° anniversario della morte', 'Defunto Da Ricordare', 'Morto il 20 novembre 2018, a 88 anni',
        ]);
});

it('remembers together the people of a famiglia who entered on the same day', function (): void {
    login();
    $this->travelTo(Carbon::parse('2026-10-30'));
    $marito = entraInNomadelfiaPerRicorrenze(Persona::factory()->nato(Carbon::parse('1980-01-01'))->maschio()->create(), '2014-11-05');
    $moglie = entraInNomadelfiaPerRicorrenze(Persona::factory()->nato(Carbon::parse('1982-01-01'))->femmina()->create(), '2014-11-05');
    $famiglia = Famiglia::factory()->create(['data_creazione' => '2005-06-01', 'tipo' => TipoFamiglia::Coniugi]);
    $famiglia->assegnaCapoFamiglia($marito);
    $famiglia->assegnaMoglie($moglie);

    $this->get(route('nomadelfia.ricorrenze'))
        ->assertSuccessful()
        ->assertViewHas('giorni', fn (Collection $giorni): bool => ricorrenzeDi($giorni, $marito)->sole()->persone->pluck('id')->all() === [$marito->id, $moglie->id]);
});

it('does not remember the entrata of a person who entered Nomadelfia at birth', function (): void {
    login();
    $this->travelTo(Carbon::parse('2026-10-30'));
    $nato = Persona::factory()->nato(Carbon::parse('2000-11-05'))->maschio()->create();
    entraInNomadelfiaPerRicorrenze($nato, '2000-11-05');

    $this->get(route('nomadelfia.ricorrenze'))
        ->assertSuccessful()
        ->assertViewHas('giorni', fn (Collection $giorni): bool => ricorrenzeDi($giorni, $nato)->isEmpty());
});

it('does not remember the matrimonio of a vedova', function (): void {
    login();
    $this->travelTo(Carbon::parse('2026-10-30'));
    $marito = Persona::factory()->nato(Carbon::parse('1940-01-01'))->maschio()->create(['data_decesso' => '2015-01-10']);
    $moglie = entraInNomadelfiaPerRicorrenze(Persona::factory()->nato(Carbon::parse('1942-01-01'))->femmina()->create());
    $famiglia = Famiglia::factory()->create(['data_creazione' => '1966-11-02', 'tipo' => TipoFamiglia::Coniugi]);
    $famiglia->assegnaCapoFamiglia($marito, stato: '0');
    $famiglia->assegnaMoglie($moglie);

    $this->get(route('nomadelfia.ricorrenze'))
        ->assertSuccessful()
        ->assertViewHas('giorni', fn (Collection $giorni): bool => ricorrenzeDi($giorni, $moglie)->isEmpty());
});

it('does not remember the matrimonio of a separated couple', function (): void {
    login();
    $this->travelTo(Carbon::parse('2026-10-30'));
    $marito = entraInNomadelfiaPerRicorrenze(Persona::factory()->nato(Carbon::parse('1970-01-01'))->maschio()->create());
    $moglie = entraInNomadelfiaPerRicorrenze(Persona::factory()->nato(Carbon::parse('1972-01-01'))->femmina()->create());
    $famiglia = Famiglia::factory()->create(['data_creazione' => '1998-11-02', 'tipo' => TipoFamiglia::Coniugi]);
    $famiglia->assegnaCapoFamiglia($marito);
    $famiglia->assegnaMoglie($moglie);
    $marito->assegnaStato(Stato::perNome('separato'), '2010-04-01');

    $this->get(route('nomadelfia.ricorrenze'))
        ->assertSuccessful()
        ->assertViewHas('giorni', fn (Collection $giorni): bool => ricorrenzeDi($giorni, $marito)->isEmpty());
});

it('does not remember the death of a person who never lived in Nomadelfia', function (): void {
    login();
    $this->travelTo(Carbon::parse('2026-10-30'));
    $defunto = Persona::factory()->nato(Carbon::parse('1930-01-01'))->maschio()->create(['data_decesso' => '2018-11-20']);

    $this->get(route('nomadelfia.ricorrenze'))
        ->assertSuccessful()
        ->assertViewHas('giorni', fn (Collection $giorni): bool => ricorrenzeDi($giorni, $defunto)->isEmpty());
});

it('does not remember an event of less than one year ago', function (): void {
    login();
    $this->travelTo(Carbon::parse('2026-10-30'));
    $entrato = entraInNomadelfiaPerRicorrenze(Persona::factory()->nato(Carbon::parse('1980-01-01'))->maschio()->create(), '2026-11-05');

    $this->get(route('nomadelfia.ricorrenze'))
        ->assertSuccessful()
        ->assertViewHas('giorni', fn (Collection $giorni): bool => ricorrenzeDi($giorni, $entrato)->isEmpty());
});

it('lists only the anniversaries of the next 7 days when the week is chosen', function (): void {
    login();
    $this->travelTo(Carbon::parse('2026-10-30'));
    $traTreGiorni = entraInNomadelfiaPerRicorrenze(Persona::factory()->nato(Carbon::parse('1980-01-01'))->maschio()->create(), '2014-11-02');
    $traVentiGiorni = entraInNomadelfiaPerRicorrenze(Persona::factory()->nato(Carbon::parse('1980-01-01'))->maschio()->create(), '2014-11-19');

    $this->get(route('nomadelfia.ricorrenze', ['periodo' => 'settimana']))
        ->assertSuccessful()
        ->assertViewHas('giorni', fn (Collection $giorni): bool => ricorrenzeDi($giorni, $traTreGiorni)->isNotEmpty() && ricorrenzeDi($giorni, $traVentiGiorni)->isEmpty());

    $this->get(route('nomadelfia.ricorrenze'))
        ->assertSuccessful()
        ->assertViewHas('giorni', fn (Collection $giorni): bool => ricorrenzeDi($giorni, $traVentiGiorni)->isNotEmpty());
});

it('lists the giubilei until the end of the year beyond the next 30 days', function (): void {
    login();
    $this->travelTo(Carbon::parse('2026-10-30'));
    $prete = entraInNomadelfiaPerRicorrenze(Persona::factory()->nato(Carbon::parse('1950-01-01'))->maschio()->create());
    $prete->assegnaStato(Stato::perNome('sacerdote'), '1976-12-20');
    $preteDelProssimoAnno = entraInNomadelfiaPerRicorrenze(Persona::factory()->nato(Carbon::parse('1950-01-01'))->maschio()->create());
    $preteDelProssimoAnno->assegnaStato(Stato::perNome('sacerdote'), '1977-01-10');

    $this->get(route('nomadelfia.ricorrenze'))
        ->assertSuccessful()
        ->assertViewHas('giorni', fn (Collection $giorni): bool => ricorrenzeDi($giorni, $prete)->isEmpty())
        ->assertViewHas('giubilei', fn (Collection $giubilei): bool => ricorrenzeDi(collect([$giubilei]), $prete)->sole()->giubileo() === 'Giubileo d’oro'
            && ricorrenzeDi(collect([$giubilei]), $preteDelProssimoAnno)->isEmpty());
});

it('remembers an event of 29 February on 28 February of the other years', function (): void {
    login();
    $this->travelTo(Carbon::parse('2027-02-20'));
    $prete = entraInNomadelfiaPerRicorrenze(Persona::factory()->nato(Carbon::parse('1960-01-01'))->maschio()->create());
    $prete->assegnaStato(Stato::perNome('sacerdote'), '1996-02-29');

    $this->get(route('nomadelfia.ricorrenze'))
        ->assertSuccessful()
        ->assertViewHas('giorni', fn (Collection $giorni): bool => ricorrenzeDi($giorni, $prete)->sole()->data->toDateString() === '2027-02-28'
            && ricorrenzeDi($giorni, $prete)->sole()->anni === 31);
});

it('forbids the ricorrenze to a user without the permission to see the popolazione', function (): void {
    $user = User::query()->create(['username' => 'senza-permessi-ricorrenze', 'email' => 'senza-permessi-ricorrenze@email.it', 'password' => 'secret', 'persona_id' => 0]);
    login($user);

    $this->get(route('nomadelfia.ricorrenze'))
        ->assertForbidden();
});

<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Anagrafe\Famiglia\Controllers\FamilyController;
use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Famiglia\Models\TipoFamiglia;
use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use Carbon\Carbon;
use Illuminate\Support\Collection;

it('lists the famiglie of people who are not part of the popolazione only when all are requested', function (): void {
    login();
    $interno = Persona::factory()->maggiorenne()->maschio()->create();
    app(EntrataMaggiorenneSingleAction::class)->execute($interno, Carbon::now(), GruppoFamiliare::all()->random());
    Famiglia::factory()->create(['nome_famiglia' => 'Famiglia In Nomadelfia'])->assegnaCapoFamiglia($interno);
    Famiglia::factory()->create(['nome_famiglia' => 'Famiglia Fuori Nomadelfia'])
        ->assegnaCapoFamiglia(Persona::factory()->maggiorenne()->maschio()->create());

    $this->get(action([FamilyController::class, 'index']))
        ->assertSuccessful()
        ->assertSee('Famiglia In Nomadelfia')
        ->assertDontSee('Famiglia Fuori Nomadelfia');

    $this->get(action([FamilyController::class, 'index'], ['famiglie' => 'tutte']))
        ->assertSuccessful()
        ->assertSee('Famiglia In Nomadelfia')
        ->assertSee('Famiglia Fuori Nomadelfia');
});

it('lists each famiglia in the column of its tipo', function (): void {
    login();
    Famiglia::factory()->create(['nome_famiglia' => 'Famiglia Di Una Mamma', 'tipo' => TipoFamiglia::MammaDiVocazione])
        ->assegnaCapoFamiglia(Persona::factory()->maggiorenne()->femmina()->create());
    Famiglia::factory()->create(['nome_famiglia' => 'Famiglia Di Un Celibe', 'tipo' => TipoFamiglia::CelibeDiElezione])
        ->assegnaCapoFamiglia(Persona::factory()->maggiorenne()->maschio()->create());

    $this->get(action([FamilyController::class, 'index'], ['famiglie' => 'tutte']))
        ->assertSuccessful()
        ->assertSeeInOrder(['Mamma di vocazione', 'Famiglia Di Una Mamma', 'Nomadelfa mamma'])
        ->assertSeeInOrder(['Celibe di elezione', 'Famiglia Di Un Celibe']);
});

it('lists the famiglia of a vedova whose capofamiglia is deceased', function (): void {
    login();
    $famiglia = Famiglia::factory()->create(['nome_famiglia' => 'Famiglia Della Vedova', 'tipo' => TipoFamiglia::Coniugi]);
    $famiglia->assegnaCapoFamiglia(Persona::factory()->maggiorenne()->maschio()->create(['data_decesso' => '2020-01-01']), stato: '0');
    $famiglia->assegnaMoglie(Persona::factory()->maggiorenne()->femmina()->create());

    $this->get(action([FamilyController::class, 'index'], ['famiglie' => 'tutte']))
        ->assertSuccessful()
        ->assertSee('Famiglia Della Vedova');
});

it('lists a famiglia whose componenti are all outside the nucleo when all are requested', function (): void {
    login();
    $famiglia = Famiglia::factory()->create(['nome_famiglia' => 'Famiglia Senza Nucleo']);
    $capofamiglia = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia->assegnaCapoFamiglia($capofamiglia);
    $famiglia->uscitaDalNucleoFamiliare($capofamiglia);

    $this->get(action([FamilyController::class, 'index'], ['famiglie' => 'tutte']))
        ->assertSuccessful()
        ->assertViewHas('colonne', fn (Collection $colonne): bool => $colonne->flatten(1)->pluck('nome_famiglia')->contains('Famiglia Senza Nucleo'))
        ->assertSeeInOrder(['Da sistemare', 'Famiglia Senza Nucleo']);

    $this->get(action([FamilyController::class, 'index']))
        ->assertSuccessful()
        ->assertViewHas('colonne', fn (Collection $colonne): bool => $colonne->flatten(1)->pluck('nome_famiglia')->doesntContain('Famiglia Senza Nucleo'));
});

it('counts the componenti in the nucleo, or all of them when requested', function (): void {
    login();
    $famiglia = Famiglia::factory()->create(['nome_famiglia' => 'Famiglia Da Contare', 'tipo' => TipoFamiglia::Coniugi]);
    $famiglia->assegnaCapoFamiglia(Persona::factory()->maggiorenne()->maschio()->create());
    $famiglia->assegnaFiglioNato(Persona::factory()->minorenne()->maschio()->create());
    $uscito = Persona::factory()->maggiorenne()->femmina()->create();
    $famiglia->assegnaFiglioNato($uscito);
    $famiglia->uscitaDalNucleoFamiliare($uscito);
    $numeroComponenti = fn (Collection $colonne): int => $colonne->flatten(1)->firstWhere('nome_famiglia', 'Famiglia Da Contare')->numero_componenti;

    $this->get(action([FamilyController::class, 'index'], ['famiglie' => 'tutte']))
        ->assertSuccessful()
        ->assertViewHas('colonne', fn (Collection $colonne): bool => $numeroComponenti($colonne) === 2);

    $this->get(action([FamilyController::class, 'index'], ['famiglie' => 'tutte', 'componenti' => 'tutti']))
        ->assertSuccessful()
        ->assertViewHas('colonne', fn (Collection $colonne): bool => $numeroComponenti($colonne) === 3)
        ->assertSee(route('anagrafe.families', ['famiglie' => 'tutte']));
});

it('lists only the single people in Nomadelfia unless all are requested', function (): void {
    login();
    $interno = Persona::factory()->maggiorenne()->maschio()->create(['nominativo' => 'Single Interno']);
    app(EntrataMaggiorenneSingleAction::class)->execute($interno, Carbon::now(), GruppoFamiliare::all()->random());
    Persona::factory()->maggiorenne()->maschio()->create(['nominativo' => 'Single Esterno']);

    $this->get(action([FamilyController::class, 'index']))
        ->assertSee('Single Interno')
        ->assertDontSee('Single Esterno');

    $this->get(action([FamilyController::class, 'index'], ['single' => 'tutti']))
        ->assertSee('Single Interno')
        ->assertSee('Single Esterno');
});

it('redirects the old nomadelfia urls to the anagrafe', function (string $oldUrl, string $newUrl): void {
    $this->get($oldUrl)->assertRedirect($newUrl)->assertStatus(301);
})->with([
    'families' => ['/nomadelfia/families', '/anagrafe/families'],
    'family' => ['/nomadelfia/families/7', '/anagrafe/families/7'],
    'search' => ['/nomadelfia/search', '/anagrafe/search'],
]);

it('shows a family whose members are not part of the popolazione', function (): void {
    login();
    $famiglia = Famiglia::factory()->create(['nome_famiglia' => 'Famiglia Da Mostrare']);
    $famiglia->assegnaCapoFamiglia(Persona::factory()->maggiorenne()->maschio()->create());

    $this->get(action([FamilyController::class, 'show'], $famiglia->id))
        ->assertSuccessful()
        ->assertSee('Famiglia Da Mostrare');
});

it('marks the page as the page of a famiglia, with the coniugi joined in the nucleo', function (): void {
    login();
    $famiglia = Famiglia::factory()->create(['nome_famiglia' => 'Riconoscibile Giuseppe', 'tipo' => TipoFamiglia::Coniugi, 'data_creazione' => '1963-06-01']);
    $famiglia->assegnaCapoFamiglia(Persona::factory()->maggiorenne()->maschio()->create(['nome' => 'Giuseppe', 'cognome' => 'Bianchi']));
    $famiglia->assegnaMoglie(Persona::factory()->maggiorenne()->femmina()->create(['nome' => 'Anna', 'cognome' => 'Ferri']));

    $this->get(action([FamilyController::class, 'show'], $famiglia->id))
        ->assertSuccessful()
        ->assertSee('<title>Famiglia Riconoscibile Giuseppe</title>', false)
        ->assertSeeInOrder(['Famiglie', 'Famiglia', 'Famiglia Riconoscibile Giuseppe'])
        ->assertSeeInOrder(['Giuseppe Bianchi', 'sposati', '1963', 'Anna Ferri']);
});

it('shows the coniugi and the figli in and out of the nucleo of a famiglia', function (): void {
    login();
    $famiglia = Famiglia::factory()->create(['tipo' => TipoFamiglia::Coniugi, 'data_creazione' => '2010-05-01']);
    $famiglia->assegnaCapoFamiglia(Persona::factory()->maggiorenne()->maschio()->create(['nominativo' => 'Marito Della Famiglia']));
    $famiglia->assegnaMoglie(Persona::factory()->maggiorenne()->femmina()->create(['nominativo' => 'Moglie Della Famiglia']));
    $famiglia->assegnaFiglioNato(Persona::factory()->minorenne()->maschio()->create(['nominativo' => 'Figlio Nel Nucleo']));
    $figlioUscito = Persona::factory()->maggiorenne()->femmina()->create(['nominativo' => 'Figlia Uscita']);
    $famiglia->assegnaFiglioAccolto($figlioUscito);
    $famiglia->uscitaDalNucleoFamiliare($figlioUscito);

    $this->get(action([FamilyController::class, 'show'], $famiglia->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Coniugi', 'Data matrimonio'])
        ->assertSeeInOrder([
            'Coniugi', 'Marito Della Famiglia', 'Marito', 'Moglie Della Famiglia', 'Moglie',
            'Figli nel nucleo', 'Figlio Nel Nucleo', 'Figlio nato',
            'Figli non più nel nucleo', 'Figlia Uscita', 'Figlio accolto',
        ]);
});

it('shows the componenti of a famiglia by nome and cognome with the nominativo only when it differs', function (): void {
    login();
    $famiglia = Famiglia::factory()->create(['tipo' => TipoFamiglia::Coniugi]);
    $famiglia->assegnaCapoFamiglia(Persona::factory()->maggiorenne()->maschio()->create(['nome' => 'Pietro', 'cognome' => 'Gallo', 'nominativo' => 'Pierino']));
    $famiglia->assegnaMoglie(Persona::factory()->maggiorenne()->femmina()->create(['nome' => 'Anna', 'cognome' => 'Rossi', 'nominativo' => 'Rossi Anna']));

    $this->get(action([FamilyController::class, 'show'], $famiglia->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Pietro Gallo', '(Pierino)', 'Marito', 'Anna Rossi', 'Moglie'])
        ->assertDontSee('Rossi Anna');
});

it('shows the mamma of a famiglia of a mamma without offering a moglie', function (): void {
    login();
    $famiglia = Famiglia::factory()->create(['tipo' => TipoFamiglia::MammaDiVocazione, 'data_creazione' => null]);
    $famiglia->assegnaCapoFamiglia(Persona::factory()->maggiorenne()->femmina()->create(['nominativo' => 'Mamma Della Famiglia']));

    $this->get(action([FamilyController::class, 'show'], $famiglia->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Data inizio vocazione', 'non conosciuta'])
        ->assertSeeInOrder(['Capofamiglia', 'Mamma Della Famiglia', 'Mamma di vocazione'])
        ->assertDontSee('value="MOGLIE"', escape: false);
});

it('does not list deceased single people', function (): void {
    login();
    Persona::factory()->maggiorenne()->maschio()->create(['nominativo' => 'Single Deceduto', 'data_decesso' => '2020-01-01']);

    $this->get(action([FamilyController::class, 'index'], ['single' => 'tutti']))
        ->assertSuccessful()
        ->assertDontSee('Single Deceduto');
});

it('saves a famiglia without data_creazione', function (): void {
    login();
    $famiglia = Famiglia::factory()->create(['nome_famiglia' => 'Famiglia Senza Data', 'data_creazione' => '1998-06-12']);

    $this->put(action([FamilyController::class, 'update'], $famiglia->id), [
        'nome_famiglia' => 'Famiglia Senza Data',
        'data_creazione' => '',
    ])->assertRedirect(route('anagrafe.families.show', $famiglia->id));

    expect(Famiglia::findOrFail($famiglia->id)->data_creazione)->toBeNull();
});

it('changes the tipo of a famiglia', function (): void {
    login();
    $famiglia = Famiglia::factory()->create(['nome_famiglia' => 'Famiglia Di Vocazione', 'data_creazione' => '1980-03-12']);

    $this->put(action([FamilyController::class, 'update'], $famiglia->id), [
        'nome_famiglia' => 'Famiglia Di Vocazione',
        'tipo' => TipoFamiglia::MammaDiVocazione->value,
        'data_creazione' => '1980-03-12',
    ])->assertRedirect(route('anagrafe.families.show', $famiglia->id));

    expect(Famiglia::findOrFail($famiglia->id)->tipo)->toBe(TipoFamiglia::MammaDiVocazione);
    $this->get(action([FamilyController::class, 'show'], $famiglia->id))->assertSee('Mamma di vocazione');
});

it('refuses a tipo of famiglia that does not exist', function (): void {
    login();
    $famiglia = Famiglia::factory()->create();

    $this->put(action([FamilyController::class, 'update'], $famiglia->id), [
        'nome_famiglia' => $famiglia->nome_famiglia,
        'tipo' => 'SINGLE',
        'data_creazione' => $famiglia->data_creazione,
    ])->assertSessionHasErrors(['tipo' => 'Il tipo della famiglia non esiste.']);

    expect(Famiglia::findOrFail($famiglia->id)->tipo)->toBeNull();
});

it('renders the form of a nuova famiglia with the search of the persone', function (): void {
    login();

    $this->get(action([FamilyController::class, 'create']))
        ->assertSuccessful()
        ->assertSee('Data matrimonio')
        ->assertSeeLivewire('search-persona');
});

it('creates a famiglia of coniugi with figli nati and accolti', function (): void {
    login();
    $marito = Persona::factory()->maggiorenne()->maschio()->create();
    $moglie = Persona::factory()->maggiorenne()->femmina()->create();
    $figlioNato = Persona::factory()->minorenne()->maschio()->create();
    $figlioAccolto = Persona::factory()->minorenne()->femmina()->create();

    $this->post(action([FamilyController::class, 'store']), [
        'tipo' => 'CON',
        'data_creazione' => '2015-06-20',
        'capofamiglia' => $marito->id,
        'moglie' => $moglie->id,
        'figli_nati' => [$figlioNato->id],
        'figli_accolti' => [$figlioAccolto->id],
    ])->assertSessionHasNoErrors()->assertRedirect();

    $famiglia = $marito->famigliaAttuale();
    expect($famiglia)->toBeInstanceOf(Famiglia::class)
        ->and($famiglia->tipo)->toBe(TipoFamiglia::Coniugi)
        ->and($famiglia->data_creazione)->toStartWith('2015-06-20')
        ->and($famiglia->nome_famiglia)->toBe($marito->nominativo)
        ->and($famiglia->moglie()->id)->toBe($moglie->id)
        ->and($famiglia->figliAttuali()->get()->mapWithKeys(fn (Persona $figlio): array => [$figlio->id => $figlio->pivot->posizione_famiglia])->all())
        ->toEqual([
            $figlioNato->id => 'FIGLIO NATO',
            $figlioAccolto->id => 'FIGLIO ACCOLTO',
        ]);
});

it('creates a famiglia with an aggregato', function (): void {
    login();
    $mamma = Persona::factory()->maggiorenne()->femmina()->create();
    $aggregato = Persona::factory()->minorenne()->maschio()->create();

    $this->post(action([FamilyController::class, 'store']), [
        'tipo' => 'MAV',
        'capofamiglia' => $mamma->id,
        'aggregati' => [$aggregato->id],
    ])->assertSessionHasNoErrors()->assertRedirect();

    $famiglia = $mamma->famigliaAttuale();
    expect($famiglia->figliAttuali()->get()->mapWithKeys(fn (Persona $figlio): array => [$figlio->id => $figlio->pivot->posizione_famiglia])->all())
        ->toEqual([$aggregato->id => 'AGGREGATO']);

    $this->get(route('anagrafe.families.show', $famiglia->id))
        ->assertSuccessful()
        ->assertSee($aggregato->nominativo)
        ->assertSee('Aggregato');
});

it('refuses an aggregato who is also accolto', function (): void {
    login();
    $mamma = Persona::factory()->maggiorenne()->femmina()->create();
    $figlio = Persona::factory()->minorenne()->maschio()->create();

    $this->post(action([FamilyController::class, 'store']), [
        'tipo' => 'MAV',
        'capofamiglia' => $mamma->id,
        'figli_accolti' => [$figlio->id],
        'aggregati' => [$figlio->id],
    ])->assertSessionHasErrors(['aggregati.0' => 'Un aggregato non può essere anche figlio nato o accolto.']);

    expect($mamma->famigliaAttuale())->toBeNull();
});

it('creates a famiglia of a mamma di vocazione without data_creazione', function (): void {
    login();
    $mamma = Persona::factory()->maggiorenne()->femmina()->create();
    $nome = 'Famiglia '.ucfirst(fake()->unique()->lexify('??????????'));

    $this->post(action([FamilyController::class, 'store']), [
        'tipo' => 'MAV',
        'capofamiglia' => $mamma->id,
        'nome_famiglia' => $nome,
    ])->assertSessionHasNoErrors();

    $famiglia = $mamma->famigliaAttuale();
    expect($famiglia->tipo)->toBe(TipoFamiglia::MammaDiVocazione)
        ->and($famiglia->data_creazione)->toBeNull()
        ->and($famiglia->nome_famiglia)->toBe($nome)
        ->and($famiglia->capofamiglia()->id)->toBe($mamma->id);
});

it('moves the capofamiglia out of the famiglia in which is a figlio', function (): void {
    login();
    $mamma = Persona::factory()->maggiorenne()->femmina()->create();
    $famigliaOrigine = Famiglia::factory()->create();
    $famigliaOrigine->assegnaFiglioNato($mamma);

    $this->post(action([FamilyController::class, 'store']), [
        'tipo' => 'MAN',
        'capofamiglia' => $mamma->id,
    ])->assertSessionHasNoErrors();

    expect($mamma->famigliaAttuale()->tipo)->toBe(TipoFamiglia::MammaNubile)
        ->and($famigliaOrigine->figliAttuali()->count())->toBe(0);
});

it('creates a famiglia of coniugi without the data del matrimonio', function (): void {
    login();
    $marito = Persona::factory()->maggiorenne()->maschio()->create();
    $moglie = Persona::factory()->maggiorenne()->femmina()->create();

    $this->post(action([FamilyController::class, 'store']), [
        'tipo' => 'CON',
        'data_creazione' => '',
        'capofamiglia' => $marito->id,
        'moglie' => $moglie->id,
    ])->assertSessionHasNoErrors()->assertRedirect();

    $famiglia = $marito->famigliaAttuale();
    expect($famiglia->tipo)->toBe(TipoFamiglia::Coniugi)
        ->and($famiglia->data_creazione)->toBeNull()
        ->and($famiglia->moglie()->id)->toBe($moglie->id);
});

it('requires the moglie for the coniugi', function (): void {
    login();
    $marito = Persona::factory()->maggiorenne()->maschio()->create();

    $this->post(action([FamilyController::class, 'store']), [
        'tipo' => 'CON',
        'capofamiglia' => $marito->id,
    ])->assertSessionHasErrors([
        'moglie' => 'La moglie è obbligatoria.',
    ])->assertSessionDoesntHaveErrors('data_creazione');

    expect($marito->famigliaAttuale())->toBeNull();
});

it('refuses a capofamiglia of the wrong sesso for the tipo', function (string $tipo, string $sesso, string $message): void {
    login();
    $capofamiglia = Persona::factory()->maggiorenne()->create(['sesso' => $sesso]);

    $this->post(action([FamilyController::class, 'store']), [
        'tipo' => $tipo,
        'capofamiglia' => $capofamiglia->id,
    ])->assertSessionHasErrors(['capofamiglia' => $message]);

    expect($capofamiglia->famigliaAttuale())->toBeNull();
})->with([
    'a man as mamma' => ['MAV', 'M', 'La mamma deve essere una donna.'],
    'a woman as celibe' => ['CDE', 'F', 'Il capofamiglia deve essere un uomo.'],
]);

it('records in the storico a figlio who is already in another famiglia attuale', function (): void {
    login();
    $mamma = Persona::factory()->maggiorenne()->femmina()->create();
    $figlio = Persona::factory()->minorenne()->maschio()->create();
    $altraFamiglia = Famiglia::factory()->create(['nome_famiglia' => 'Altra Famiglia']);
    $altraFamiglia->assegnaFiglioNato($figlio);

    $this->post(action([FamilyController::class, 'store']), [
        'tipo' => 'MAV',
        'capofamiglia' => $mamma->id,
        'figli_accolti' => [$figlio->id],
    ])->assertSessionHasNoErrors();

    $famiglia = $mamma->famigliaAttuale();
    expect($famiglia)->not->toBeNull()
        ->and($famiglia->figli()->wherePivot('stato', '0')->pluck('persone.id')->all())->toBe([$figlio->id])
        ->and($figlio->famigliaAttuale()->id)->toBe($altraFamiglia->id);
});

it('refuses the nome of a famiglia that already exists', function (): void {
    login();
    $esistente = Famiglia::factory()->create();
    $mamma = Persona::factory()->maggiorenne()->femmina()->create();

    $this->post(action([FamilyController::class, 'store']), [
        'tipo' => 'MAV',
        'capofamiglia' => $mamma->id,
        'nome_famiglia' => $esistente->nome_famiglia,
    ])->assertSessionHasErrors(['nome_famiglia' => 'Esiste già una famiglia con questo nome.']);

    expect($mamma->famigliaAttuale())->toBeNull();
});

it('links the nuova famiglia from the list of the famiglie', function (): void {
    login();

    $this->get(action([FamilyController::class, 'index']))
        ->assertSuccessful()
        ->assertSee(route('anagrafe.families.create'));
});

it('preselects the persona the famiglia starts from in the role of the tipo', function (string $tipo, string $sesso, string $ruolo): void {
    login();
    $persona = Persona::factory()->maggiorenne()->create(['sesso' => $sesso]);

    $this->get(action([FamilyController::class, 'create'], ['persona' => $persona->id, 'tipo' => $tipo]))
        ->assertSuccessful()
        ->assertViewHas('tipo', TipoFamiglia::from($tipo))
        ->assertViewHas($ruolo, [$persona->id])
        ->assertViewHas($ruolo === 'moglieId' ? 'capofamigliaId' : 'moglieId', []);
})->with([
    'a man as marito' => ['CON', 'M', 'capofamigliaId'],
    'a woman as moglie' => ['CON', 'F', 'moglieId'],
    'a woman as mamma' => ['MAV', 'F', 'capofamigliaId'],
    'a man as celibe di elezione' => ['CDE', 'M', 'capofamigliaId'],
]);

it('keeps the persone selected before a validation error', function (): void {
    login();
    $marito = Persona::factory()->maggiorenne()->maschio()->create();
    $moglie = Persona::factory()->maggiorenne()->femmina()->create();

    $this->withSession(['_old_input' => ['tipo' => 'CON', 'capofamiglia' => (string) $marito->id, 'moglie' => (string) $moglie->id]])
        ->get(action([FamilyController::class, 'create'], ['persona' => $moglie->id]))
        ->assertViewHas('capofamigliaId', [$marito->id])
        ->assertViewHas('moglieId', [$moglie->id]);
});

it('creates a famiglia of a capofamiglia who is minor or deceased to record the storico', function (string $tipo, string $sesso, string $stato): void {
    login();
    $capofamiglia = Persona::factory()->{$stato === 'minor' ? 'minorenne' : 'maggiorenne'}()->create([
        'sesso' => $sesso,
        'data_decesso' => $stato === 'deceased' ? '2020-01-01' : null,
    ]);

    $this->post(action([FamilyController::class, 'store']), [
        'tipo' => $tipo,
        'data_creazione' => '1990-05-01',
        'capofamiglia' => $capofamiglia->id,
    ])->assertSessionHasNoErrors()->assertRedirect();

    expect(Famiglia::query()->latest('id')->first()->capofamiglia()->id)->toBe($capofamiglia->id);
})->with([
    'mamma di vocazione' => ['MAV', 'F'],
    'Nomadelfa mamma' => ['MAM', 'F'],
    'mamma nubile' => ['MAN', 'F'],
    'celibe di elezione' => ['CDE', 'M'],
])->with(['minor', 'deceased']);

it('records in the storico a capofamiglia who is already capofamiglia of another famiglia', function (string $tipo, string $sesso): void {
    login();
    $capofamiglia = Persona::factory()->maggiorenne()->create(['sesso' => $sesso]);
    $famigliaAttuale = Famiglia::factory()->create();
    $famigliaAttuale->assegnaCapoFamiglia($capofamiglia);

    $this->post(action([FamilyController::class, 'store']), [
        'tipo' => $tipo,
        'data_creazione' => '1990-05-01',
        'capofamiglia' => $capofamiglia->id,
    ])->assertSessionHasNoErrors()->assertRedirect();

    $famigliaStorica = Famiglia::query()->latest('id')->first();
    expect($famigliaStorica->componenti()->wherePivot('stato', '0')->wherePivot('posizione_famiglia', 'CAPO FAMIGLIA')->pluck('persone.id')->all())->toBe([$capofamiglia->id])
        ->and($capofamiglia->famigliaAttuale()->id)->toBe($famigliaAttuale->id)
        ->and($famigliaAttuale->capofamiglia()->id)->toBe($capofamiglia->id);
})->with([
    'mamma di vocazione' => ['MAV', 'F'],
    'Nomadelfa mamma' => ['MAM', 'F'],
    'mamma nubile' => ['MAN', 'F'],
    'celibe di elezione' => ['CDE', 'M'],
]);

it('refuses a man as mamma who cannot marry', function (string $tipo): void {
    login();
    $uomo = Persona::factory()->minorenne()->maschio()->create();

    $this->post(action([FamilyController::class, 'store']), [
        'tipo' => $tipo,
        'capofamiglia' => $uomo->id,
    ])->assertSessionHasErrors(['capofamiglia' => 'La mamma deve essere una donna.']);

    expect($uomo->famigliaAttuale())->toBeNull();
})->with(['Nomadelfa mamma' => 'MAM', 'mamma nubile' => 'MAN']);

it('creates the coniugi with a genitore who is minor or deceased to record the storico', function (string $genitore, string $stato): void {
    login();
    $crea = fn (string $sesso): Persona => Persona::factory()->{$stato === 'minor' ? 'minorenne' : 'maggiorenne'}()->create([
        'sesso' => $sesso,
        'data_decesso' => $stato === 'deceased' ? '2020-01-01' : null,
    ]);
    $marito = $genitore === 'marito' ? $crea('M') : Persona::factory()->maggiorenne()->maschio()->create();
    $moglie = $genitore === 'moglie' ? $crea('F') : Persona::factory()->maggiorenne()->femmina()->create();

    $this->post(action([FamilyController::class, 'store']), [
        'tipo' => 'CON',
        'data_creazione' => '1960-06-20',
        'capofamiglia' => $marito->id,
        'moglie' => $moglie->id,
    ])->assertSessionHasNoErrors();

    $famiglia = $marito->famigliaAttuale();
    expect($famiglia->capofamiglia()->id)->toBe($marito->id)
        ->and($famiglia->moglie()->id)->toBe($moglie->id);
})->with(['marito', 'moglie'])->with(['minor', 'deceased']);

it('records in the storico the coniugi who are already married in another famiglia', function (): void {
    login();
    $marito = Persona::factory()->maggiorenne()->maschio()->create();
    $moglie = Persona::factory()->maggiorenne()->femmina()->create();
    $famigliaDelMarito = Famiglia::factory()->create();
    $famigliaDelMarito->assegnaCapoFamiglia($marito);
    $famigliaDellaMoglie = Famiglia::factory()->create();
    $famigliaDellaMoglie->assegnaMoglie($moglie);

    $this->post(action([FamilyController::class, 'store']), [
        'tipo' => 'CON',
        'data_creazione' => '1960-06-20',
        'capofamiglia' => $marito->id,
        'moglie' => $moglie->id,
    ])->assertSessionHasNoErrors();

    $famigliaStorica = Famiglia::query()->latest('id')->first();
    expect($famigliaStorica->componenti()->wherePivot('stato', '0')->pluck('persone.id')->sort()->values()->all())->toBe(collect([$marito->id, $moglie->id])->sort()->values()->all())
        ->and($marito->famigliaAttuale()->id)->toBe($famigliaDelMarito->id)
        ->and($moglie->famigliaAttuale()->id)->toBe($famigliaDellaMoglie->id);
});

it('moves into the nucleo a moglie who is a figlia and records in the storico a marito already married', function (): void {
    login();
    $marito = Persona::factory()->maggiorenne()->maschio()->create();
    $moglie = Persona::factory()->maggiorenne()->femmina()->create();
    Famiglia::factory()->create()->assegnaCapoFamiglia($marito);
    $famigliaOrigine = Famiglia::factory()->create();
    $famigliaOrigine->assegnaFiglioNato($moglie);

    $this->post(action([FamilyController::class, 'store']), [
        'tipo' => 'CON',
        'capofamiglia' => $marito->id,
        'moglie' => $moglie->id,
    ])->assertSessionHasNoErrors();

    $famiglia = $moglie->famigliaAttuale();
    expect($famiglia->moglie()->id)->toBe($moglie->id)
        ->and($famiglia->componenti()->wherePivot('stato', '0')->pluck('persone.id')->all())->toBe([$marito->id])
        ->and($famigliaOrigine->figliAttuali()->count())->toBe(0);
});

it('refuses a man as moglie', function (): void {
    login();
    $marito = Persona::factory()->maggiorenne()->maschio()->create();
    $moglie = Persona::factory()->maggiorenne()->maschio()->create();

    $this->post(action([FamilyController::class, 'store']), [
        'tipo' => 'CON',
        'capofamiglia' => $marito->id,
        'moglie' => $moglie->id,
    ])->assertSessionHasErrors(['moglie' => 'La moglie deve essere una donna.']);

    expect($marito->famigliaAttuale())->toBeNull();
});

it('refuses a capofamiglia that is not a persona id', function (): void {
    login();

    $this->post(action([FamilyController::class, 'store']), [
        'tipo' => 'MAV',
        'capofamiglia' => ['1'],
    ])->assertSessionHasErrors('capofamiglia');
});

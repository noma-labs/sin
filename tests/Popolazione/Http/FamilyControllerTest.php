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

it('lists the families of people who are not part of the popolazione', function (): void {
    login();
    $capofamiglia = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia = Famiglia::factory()->create(['nome_famiglia' => 'Famiglia Fuori Nomadelfia']);
    $famiglia->assegnaCapoFamiglia($capofamiglia);

    $this->get(action([FamilyController::class, 'index']))
        ->assertSuccessful()
        ->assertSee('Famiglia Fuori Nomadelfia');
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

it('requires the data del matrimonio and the moglie for the coniugi', function (): void {
    login();
    $marito = Persona::factory()->maggiorenne()->maschio()->create();

    $this->post(action([FamilyController::class, 'store']), [
        'tipo' => 'CON',
        'capofamiglia' => $marito->id,
    ])->assertSessionHasErrors([
        'data_creazione' => 'La data del matrimonio è obbligatoria.',
        'moglie' => 'La moglie è obbligatoria.',
    ]);

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
    'a man as mamma' => ['MAV', 'M', 'La mamma deve essere una donna maggiorenne, viva e non già capofamiglia o sposata.'],
    'a woman as celibe' => ['CDE', 'F', 'Il capofamiglia deve essere un uomo maggiorenne, vivo e non già capofamiglia o sposato.'],
]);

it('refuses a figlio who is already in another famiglia attuale', function (): void {
    login();
    $mamma = Persona::factory()->maggiorenne()->femmina()->create();
    $figlio = Persona::factory()->minorenne()->maschio()->create();
    $altraFamiglia = Famiglia::factory()->create(['nome_famiglia' => 'Altra Famiglia']);
    $altraFamiglia->assegnaFiglioNato($figlio);

    $this->post(action([FamilyController::class, 'store']), [
        'tipo' => 'MAV',
        'capofamiglia' => $mamma->id,
        'figli_accolti' => [$figlio->id],
    ])->assertSessionHasErrors(['figli_accolti.0' => "$figlio->nominativo è già nella famiglia Altra Famiglia: toglilo prima da quella famiglia."]);

    expect($mamma->famigliaAttuale())->toBeNull();
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

it('refuses a marito who cannot marry', function (Persona $marito): void {
    login();
    $moglie = Persona::factory()->maggiorenne()->femmina()->create();

    $this->post(action([FamilyController::class, 'store']), [
        'tipo' => 'CON',
        'data_creazione' => '2015-06-20',
        'capofamiglia' => $marito->id,
        'moglie' => $moglie->id,
    ])->assertSessionHasErrors(['capofamiglia' => 'Il capofamiglia deve essere un uomo maggiorenne, vivo e non già capofamiglia o sposato.']);

    expect($moglie->famigliaAttuale())->toBeNull();
})->with([
    'deceased' => fn (): Persona => Persona::factory()->maggiorenne()->maschio()->create(['data_decesso' => '2020-01-01']),
    'minor' => fn (): Persona => Persona::factory()->minorenne()->maschio()->create(),
    'already married' => function (): Persona {
        $persona = Persona::factory()->maggiorenne()->maschio()->create();
        Famiglia::factory()->create()->assegnaCapoFamiglia($persona);

        return $persona;
    },
]);

it('refuses a moglie who cannot marry', function (): void {
    login();
    $marito = Persona::factory()->maggiorenne()->maschio()->create();
    $moglie = Persona::factory()->maggiorenne()->femmina()->create(['data_decesso' => '2020-01-01']);

    $this->post(action([FamilyController::class, 'store']), [
        'tipo' => 'CON',
        'data_creazione' => '2015-06-20',
        'capofamiglia' => $marito->id,
        'moglie' => $moglie->id,
    ])->assertSessionHasErrors(['moglie' => 'La moglie deve essere una donna maggiorenne, viva e non già sposata.']);

    expect($marito->famigliaAttuale())->toBeNull();
});

it('refuses a capofamiglia that is not a persona id', function (): void {
    login();

    $this->post(action([FamilyController::class, 'store']), [
        'tipo' => 'MAV',
        'capofamiglia' => ['1'],
    ])->assertSessionHasErrors('capofamiglia');
});

it('redirects the old matrimonio url to the nuova famiglia', function (): void {
    login();

    $this->get('/anagrafe/marriage')->assertRedirect('/anagrafe/families-new');
});

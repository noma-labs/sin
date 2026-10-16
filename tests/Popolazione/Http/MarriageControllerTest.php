<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\Famiglia\Controllers\MarriageController;
use App\Nomadelfia\Famiglia\Models\Famiglia;
use App\Nomadelfia\Persona\Models\Persona;

it('renders the marriage form prefilled with the given person', function (): void {
    $persona = Persona::factory()->maggiorenne()->femmina()->create();
    login();

    $response = $this->get(action([MarriageController::class, 'create'], ['persona_id' => $persona->id]));

    $response->assertOk()->assertSeeText($persona->nominativo);
});

it('creates a family for two persons only in the anagrafe', function (): void {
    $husband = Persona::factory()->maggiorenne()->maschio()->create();
    $wife = Persona::factory()->maggiorenne()->femmina()->create();
    login();

    $response = $this->post(action([MarriageController::class, 'store']), [
        'husband' => $husband->id,
        'wife' => $wife->id,
        'data_matrimonio' => '2026-05-10',
    ]);

    $famiglia = Famiglia::query()->latest('id')->firstOrFail();
    $response->assertRedirect(route('nomadelfia.families.show', ['id' => $famiglia->id]));
    expect($famiglia->nome_famiglia)->toBe($husband->nominativo)
        ->and($famiglia->data_creazione)->toStartWith('2026-05-10');
    $this->assertDatabaseHas('db_nomadelfia.famiglie_persone', ['famiglia_id' => $famiglia->id, 'persona_id' => $husband->id, 'posizione_famiglia' => 'CAPO FAMIGLIA', 'stato' => '1']);
    $this->assertDatabaseHas('db_nomadelfia.famiglie_persone', ['famiglia_id' => $famiglia->id, 'persona_id' => $wife->id, 'posizione_famiglia' => 'MOGLIE', 'stato' => '1']);
});

it('moves a spouse out of the family of origin', function (): void {
    $origine = Famiglia::factory()->create();
    $husband = Persona::factory()->maggiorenne()->maschio()->create();
    $origine->assegnaFiglioNato($husband);
    $wife = Persona::factory()->maggiorenne()->femmina()->create();
    login();

    $this->post(action([MarriageController::class, 'store']), [
        'husband' => $husband->id,
        'wife' => $wife->id,
        'data_matrimonio' => '2026-05-10',
    ]);

    $this->assertDatabaseHas('db_nomadelfia.famiglie_persone', ['famiglia_id' => $origine->id, 'persona_id' => $husband->id, 'stato' => '0']);
    expect($husband->famigliaAttuale()->pivot->posizione_famiglia)->toBe('CAPO FAMIGLIA');
});

it('requires husband, wife and data matrimonio', function (): void {
    login();

    $response = $this->post(action([MarriageController::class, 'store']), []);

    $response->assertSessionHasErrors([
        'husband' => 'Il nome dello sposo è obbligatorio.',
        'wife' => 'Il nome della sposa è obbligatorio.',
        'data_matrimonio' => 'La data di matrimonio è obbligatoria.',
    ]);
});

it('rejects invalid spouses', function (callable $makeHusband, callable $makeWife, string $error): void {
    $husband = $makeHusband();
    $wife = $makeWife();
    $famiglieBefore = Famiglia::query()->count();
    login();

    $response = $this->post(action([MarriageController::class, 'store']), [
        'husband' => $husband->id,
        'wife' => $wife->id,
        'data_matrimonio' => '2026-05-10',
    ]);

    $response->assertRedirect()->assertSessionHasErrors();
    expect(session('errors')->all())->toContain(sprintf($error, $husband->nominativo, $wife->nominativo))
        ->and(Famiglia::query()->count())->toBe($famiglieBefore);
})->with([
    'wife is a man' => [
        fn () => Persona::factory()->maggiorenne()->maschio()->create(),
        fn () => Persona::factory()->maggiorenne()->maschio()->create(),
        'La sposa %2$s deve essere una donna.',
    ],
    'husband is minorenne' => [
        fn () => Persona::factory()->minorenne()->maschio()->create(),
        fn () => Persona::factory()->maggiorenne()->femmina()->create(),
        'Lo sposo %1$s deve essere maggiorenne.',
    ],
    'husband already married' => [
        function () {
            $persona = Persona::factory()->maggiorenne()->maschio()->create();
            Famiglia::factory()->create()->assegnaCapoFamiglia($persona);

            return $persona;
        },
        fn () => Persona::factory()->maggiorenne()->femmina()->create(),
        'Lo sposo %1$s è già sposato/a in una famiglia attiva.',
    ],
]);

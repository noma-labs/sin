<?php

declare(strict_types=1);

namespace Tests\Unit;

use Carbon\Carbon;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Cariche;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Stato;
use Illuminate\Support\Facades\DB;

it('check the seeded cariche', function (): void {
    expect(count(Cariche::AssociazioneCariche()))->toBe(12)
        ->and(count(Cariche::SolidarietaCariche()))->toBe(4);
});

it('can get the president of associazione', function (): void {
    $persona = Persona::factory()->cinquantenne()->maschio()->create();

    $carica = Cariche::associazione()->presidente()->first();

    $expression = DB::raw('INSERT INTO persone_cariche (persona_id, cariche_id, data_inizio) VALUES (:persona, :carica, :datain) ');
    DB::connection('db_nomadelfia')->insert(
        $expression->getValue(DB::connection()->getQueryGrammar()),
        ['persona' => $persona->id, 'carica' => $carica->id, 'datain' => Carbon::now()]
    );

    $p = Cariche::GetAssociazionePresidente();
    expect($persona->id);
    expect($persona->nome);
    expect($persona->cognome);
    expect($persona->data_nascita);
    expect($persona->provincia_nascita);
});

it('get the eligible condidates', function (): void {
    $data_entrata = Carbon::now()->startOfDay();
    $persona = Persona::factory()->cinquantenne()->maschio()->create();
    $gruppo = GruppoFamiliare::first();
    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));

    // Sacerdote: non deve essere contato negli eleggibili
    $data_entrata = Carbon::now();
    $persona = Persona::factory()->cinquantenne()->maschio()->create();
    $persona->assegnaStato(Stato::perNome('sacerdote'), $data_entrata);
    $gruppo = GruppoFamiliare::first();

    $act = app(EntrataMaggiorenneSingleAction::class);
    $act->execute($persona, $data_entrata, $gruppo);

    $ele = Cariche::EleggibiliConsiglioAnziani();
    expect($ele->total)->toBe(0);

    $persona->assegnaPosizione(Posizione::perNome('postulante'), Carbon::now()->subYears(20));
    $persona->assegnaPosizione(Posizione::perNome('effettivo'), Carbon::now()->subYears(12));

    $ele = Cariche::EleggibiliConsiglioAnziani();
    expect($ele->total)->toBe(1);

});

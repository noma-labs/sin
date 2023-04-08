<?php

namespace Tests\Unit;

use Carbon;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\SaveEntrataInNomadelfiaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Cariche;
use Illuminate\Support\Facades\DB;

it('check the seeded cariche', function () {
    $this->assertEquals(12, count(Cariche::AssociazioneCariche()));
    $this->assertEquals(4, count(Cariche::SolidarietaCariche()));
});

it('can get the president of associazione', function () {
    $persona = Persona::factory()->cinquantenne()->maschio()->create();

    $carica = Cariche::associazione()->presidente()->first();
    DB::connection('db_nomadelfia')->insert(
        DB::raw('INSERT INTO persone_cariche (persona_id, cariche_id, data_inizio)
                VALUES (:persona, :carica, :datain) '),
        ['persona' => $persona->id, 'carica' => $carica->id, 'datain' => Carbon\Carbon::now()]
    );

    $p = Cariche::GetAssociazionePresidente();
    $this->assertEquals($persona->id, $p->id);
    $this->assertEquals($persona->nome, $p->nome);
    $this->assertEquals($persona->cognome, $p->cognome);
    $this->assertEquals($persona->data_nascita, $p->data_nascita);
    $this->assertEquals($persona->provincia_nascita, $p->provincia_nascita);
});

it('get the eligible condidates', function () {
    // entrata maggiorenne maschio
    $data_entrata = Carbon::now()->toDatestring();
    $persona = Persona::factory()->cinquantenne()->maschio()->create();
    $gruppo = GruppoFamiliare::first();
    $action = new EntrataMaggiorenneSingleAction(new SaveEntrataInNomadelfiaAction());
    $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));

    // Sacerdote: non deve essere contato negli eleggibili
    $data_entrata = Carbon::now();
    $persona = Persona::factory()->cinquantenne()->maschio()->create();
    $persona->assegnaSacerdote($data_entrata);
    $gruppo = GruppoFamiliare::first();

    $act = new EntrataMaggiorenneSingleAction(new SaveEntrataInNomadelfiaAction());
    $act->execute($persona, $data_entrata->toDatestring(), $gruppo);

    $ele = Cariche::EleggibiliConsiglioAnziani();
    expect($ele->total)->toBe(0);

    $persona->assegnaPostulante(Carbon::now()->subYears(20));
    $persona->assegnaNomadelfoEffettivo(Carbon::now()->subYears(12));

    $ele = Cariche::EleggibiliConsiglioAnziani();
    expect($ele->total)->toBe(1);

});

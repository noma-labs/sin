<?php

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\Persona\Controllers\PersoneController;
use Carbon\Carbon;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneConFamigliaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataInNomadelfiaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Stato;

it('uscita persona maggiorenne', function () {
    $data_entrata = Carbon::now()->toDatestring();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $act = app(EntrataMaggiorenneConFamigliaAction::class);
    $act->execute($persona, $data_entrata, GruppoFamiliare::all()->random());

    login();

    $data_uscita = Carbon::now()->addYears(20)->toDatestring();
    $this->withoutExceptionHandling();
    $this->post(action([PersoneController::class, 'uscita'], ['idPersona' => $persona->id]),
        [
            'data_uscita' => $data_uscita,
        ]);

    $persona = Persona::findOrFail($persona->id);
    expect($persona->isPersonaInterna())->toBeFalse();
    expect($persona->getDataUscitaNomadelfia())->toBe($data_uscita);

});


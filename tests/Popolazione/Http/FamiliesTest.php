<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\Famiglia\Controllers\FamiglieController;
use App\Nomadelfia\Persona\Controllers\PersonaAnagraficaController;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\PopolazioneNomadelfiaController;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\PopolazioneSummaryController;
use Carbon\Carbon;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;


it('show index of families', function (): void {
    login();

    $this->get(action([FamiglieController::class, 'index']))
        ->assertSuccessful();
});


it('show form to insert a new familiy', function (): void {
    login();

    $this->get(action([FamiglieController::class, 'create']))
        ->assertSuccessful();
});

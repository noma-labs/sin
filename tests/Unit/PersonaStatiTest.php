<?php

namespace Tests\Unit;

use Carbon;

use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\SaveEntrataInNomadelfiaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use Tests\TestCase;
use Tests\MigrateFreshDB;
use Tests\CreatesApplication;

use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Stato;;


class PersonaStatiTest extends TestCase
{
    use CreatesApplication, MigrateFreshDB;

    public function testAssignStatoSacerdote()
    {
        $data_entrata = Carbon::now()->toDatestring();
        $persona = Persona::factory()->maggiorenne()->maschio()->create();
        $gruppo = GruppoFamiliare::first();
        $action = new EntrataMaggiorenneSingleAction(new SaveEntrataInNomadelfiaAction());
        $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));

        $data_inizio = Carbon::now()->addYears(5)->toDatestring();
        $data_fine = Carbon::now()->addYears(3)->toDatestring();
        $sac = Stato::perNome("sacerdote");

        $persona->assegnaStato($sac, $data_inizio, $data_fine);

        $this->assertEquals($persona->statoAttuale()->id, $sac->id);
        $this->assertEquals($persona->statiStorico()->first()->pivot->data_fine, $data_fine);
    }
}

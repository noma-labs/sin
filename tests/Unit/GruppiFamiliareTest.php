<?php

namespace Tests\Unit;

use App\Nomadelfia\Exceptions\CouldNotAssignCapogruppo;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\SaveEntrataInNomadelfiaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use Domain\Nomadelfia\Persona\Models\Persona;
use Tests\TestCase;
use Carbon\Carbon;

class GruppiFamiliareTest extends TestCase
{
    public function testAssegnaCapoGruppo()
    {
        $gruppo = GruppoFamiliare::factory()->create();
        $data_entrata = Carbon::now();
        $persona = Persona::factory()->cinquantenne()->maschio()->create();
        $action = new EntrataMaggiorenneSingleAction(new SaveEntrataInNomadelfiaAction());
        $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));


        $persona->assegnaPostulante($data_entrata);
        $persona->assegnaNomadelfoEffettivo($data_entrata);
        $gruppo->assegnaCapogruppo($persona, $data_entrata);
        $this->assertEquals($persona->id, $gruppo->capogruppoAttuale()->id);
    }

    public function testAssegnaCapogruppoErrorsWithPostulante()
    {
        $gruppo = GruppoFamiliare::factory()->create();
        $data_entrata = Carbon::now();
        $persona = Persona::factory()->cinquantenne()->maschio()->create();
        $action = new EntrataMaggiorenneSingleAction(new SaveEntrataInNomadelfiaAction());
        $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));
        $persona->assegnaPostulante($data_entrata);
        $this->expectException(CouldNotAssignCapogruppo::class);
        $gruppo->assegnaCapogruppo($persona, $data_entrata);
        $this->assertEquals(null, $gruppo->capogruppoAttuale());
    }

    public function testAssegnaCapogruppoErrorsWithOspite()
    {
        $gruppo = GruppoFamiliare::factory()->create();
        $data_entrata = Carbon::now()->toDatestring();
        $persona = Persona::factory()->cinquantenne()->maschio()->create();
        $action = new EntrataMaggiorenneSingleAction(new SaveEntrataInNomadelfiaAction());
        $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));
        $this->expectException(CouldNotAssignCapogruppo::class);
        $gruppo->assegnaCapogruppo($persona, $data_entrata);
        $this->assertEquals(null, $gruppo->capogruppoAttuale());
    }

    public function testAssegnaCapogruppoErrorsWithWomen()
    {
        $gruppo = GruppoFamiliare::factory()->create();
        $data_entrata = Carbon::now()->toDatestring();
        $persona = Persona::factory()->cinquantenne()->femmina()->create();
        $action = new EntrataMaggiorenneSingleAction(new SaveEntrataInNomadelfiaAction());
        $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));
        $this->expectException(CouldNotAssignCapogruppo::class);
        $gruppo->assegnaCapogruppo($persona, $data_entrata);
        $this->assertEquals(null, $gruppo->capogruppoAttuale());
    }



}

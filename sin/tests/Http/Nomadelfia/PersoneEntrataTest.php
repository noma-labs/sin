<?php

namespace Tests\Http\Nomadelfia;
use App\Nomadelfia\Persona\Controllers\PersoneController;
use Carbon\Carbon;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Tests\TestCase;

class PersoneEntrataTest extends TestCase
{

    /** @test */
    public function it_can_insert_minorenne_accolto_nella_popolazione()
    {
        $persona = Persona::factory()->minorenne()->maschio()->create();
        $data_entrata = Carbon::now()->toDatestring();
        $famiglia = Famiglia::factory()->create();

        $this->login();
        $this->post(action([PersoneController::class, 'insertPersonaInterna'], ['idPersona' => $persona->id]),
            [
                'tipologia' => 'minorenne_accolto',
                'data_entrata' => $data_entrata,
                'famiglia_id' => $famiglia->id,
            ])
            ->assertSee("inserita correttamente.");
    }

}
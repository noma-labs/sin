<?php

use App\Nomadelfia\Models\Famiglia;
use App\Nomadelfia\Models\GruppoFamiliare;
use App\Nomadelfia\Models\Persona;
use App\Nomadelfia\Models\Posizione;
use App\Nomadelfia\Models\Stato;
use App\Nomadelfia\Actions\EntrataNatoAction;
use App\Nomadelfia\Actions\EntrataInNomadelfiaAction;
use Carbon\Carbon;
use Tests\TestCase;


class EntrataPersonaActionTest extends TestCase
{

    /** @test */
    public function entrata_maschio_dalla_nascita()
    {
        $data_entrata = Carbon::now()->toDatestring();
        $persona = factory(Persona::class)->states("minorenne", "maschio")->create();
        $famiglia = factory(Famiglia::class)->create();

        $gruppo = GruppoFamiliare::first();

        $capoFam = factory(Persona::class)->states("maggiorenne", "maschio")->create();
        $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata]);
        $famiglia->componenti()->attach($capoFam->id,
            [
                'stato' => '1',
                'posizione_famiglia' => "CAPO FAMIGLIA",
                'data_entrata' => Carbon::now()->toDatestring()
            ]);

        (new EntrataNatoAction(new EntrataInNomadelfiaAction()))->execute($persona, $famiglia);

        $figlio = Posizione::perNome("figlio");
        $celibe = Stato::perNome("celibe");
        $this->assertTrue($persona->isPersonaInterna());
        $this->assertEquals($persona->getDataEntrataNomadelfia(), $persona->data_nascita);
        $this->assertEquals($persona->posizioneAttuale()->id, $figlio->id);
        $this->assertEquals($persona->posizioneAttuale()->pivot->data_inizio, $persona->data_nascita);
        $this->assertEquals($persona->statoAttuale()->id, $celibe->id);
        $this->assertEquals($persona->statoAttuale()->stato, $celibe->stato);
        $this->assertEquals($persona->statoAttuale()->pivot->data_inizio, $persona->data_nascita);
        $this->assertEquals($persona->gruppofamiliareAttuale()->id, $gruppo->id);
        $this->assertEquals($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo, $persona->data_nascita);
        $this->assertNotNull($persona->famigliaAttuale());
        $this->assertEquals($persona->famigliaAttuale()->pivot->data_entrata, $persona->data_nascita);

    }
}
<?php

namespace Tests\Unit;

use App\Nomadelfia\Models\Cariche;
use App\Nomadelfia\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Carbon;

class CaricheTest extends TestCase
{
    public function testCariche()
    {
        $this->assertEquals(12, count(Cariche::AssociazioneCariche()));
        $this->assertEquals(4, count(Cariche::SolidarietaCariche()));
    }

    public function testGetPresidenteAssociazione()
    {
        $persona = Persona::factory()->cinquantenne()->maschio()->create();

        $carica = Cariche::associazione()->presidente()->first();
        DB::connection('db_nomadelfia')->insert(
            DB::raw("INSERT INTO persone_cariche (persona_id, cariche_id, data_inizio)
                VALUES (:persona, :carica, :datain) "),
            array('persona' => $persona->id, 'carica' => $carica->id, 'datain' => Carbon\Carbon::now())
        );

        $p = Cariche::GetAssociazionePresidente();
        $this->assertEquals($persona->id, $p->id);
        $this->assertEquals($persona->nome, $p->nome);
        $this->assertEquals($persona->cognome, $p->cognome);
        $this->assertEquals($persona->data_nascita, $p->data_nascita);
        $this->assertEquals($persona->provincia_nascita, $p->provincia_nascita);

    }

    public function testEliggibiliConsiglioAnziani()
    {
        // entrata maggiorenne maschio
        $data_entrata = Carbon::now()->toDatestring();
        $persona = Persona::factory()->cinquantenne()->maschio()->create();
        $gruppo = GruppoFamiliare::first();
        $persona->entrataMaggiorenneSingle($data_entrata, $gruppo->id);

        // Sacerdote: non deve essere contato negli eleggibili
        $data_entrata = Carbon::now();
        $persona = Persona::factory()->cinquantenne()->maschio()->create();
        $persona->assegnaSacerdote($data_entrata);
        $gruppo = GruppoFamiliare::first();
        $persona->entrataMaggiorenneSingle($data_entrata->toDatestring(), $gruppo->id);

        $ele = Cariche::EleggibiliConsiglioAnziani();
        $this->assertEquals(0, $ele->total);

        $persona->assegnaPostulante(Carbon::now()->subYears(20));
        $persona->assegnaNomadelfoEffettivo(Carbon::now()->subYears(12));


        $ele = Cariche::EleggibiliConsiglioAnziani();
        $this->assertEquals(1, $ele->total);

    }

}

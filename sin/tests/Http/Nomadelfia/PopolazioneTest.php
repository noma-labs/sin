<?php

namespace Tests\Http\Nomadelfia;
use App\Nomadelfia\Controllers\PersoneController;
use App\Nomadelfia\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Tests\TestCase;

class PopolazioneTest extends TestCase
{

    /** @test */
    public function can_insert_persona()
    {
        $this->login();
        $this->withoutExceptionHandling();
        $this->post(action([PersoneController::class,'insertDatiAnagrafici']),
            [
                "nominativo" => "my-name",
                "nome" => "name",
                "cognome" => "my-surname",
                "data_nascita" => "2022-10-10",
                "luogo_nascita" => "Grosseto",
                "sesso" => "M",
            ])
            ->assertRedirect();
    }

    /** @test */
    public function cant_insert_persona_with_same_nominativo_in_popolazione_presente()
    {
        $persona = Persona::factory()->maggiorenne()->maschio()->create();
        $data_entrata = Carbon::now()->toDatestring();
        $gruppo = GruppoFamiliare::all()->random();
        $persona->entrataMaggiorenneSingle($data_entrata, $gruppo->id);

        $this->login();
        $this->post(action([PersoneController::class, 'insertDatiAnagrafici']),
            [
                'nominativo' => $persona->nominativo,
                'nome' => 'name',
                'cognome' => 'my-surname',
                'data_nascita' => '2022-10-10',
                'luogo_nascita' => 'Grosseto',
                'sesso' => 'M',
            ])
            ->assertSee("Il nominativo inserito è già assegnato alla persona");
    }

}
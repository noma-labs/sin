<?php

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\Persona\Controllers\PersoneController;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\PopolazioneNomadelfiaController;
use Carbon\Carbon;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\SaveEntrataInNomadelfiaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PopolazioneTest extends TestCase
{

    /** @test */
    public function can_insert_persona()
    {
        $this->login();
        $this->withoutExceptionHandling();
        $this->post(action([PersoneController::class, 'insertDatiAnagrafici']),
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
        $act = new  EntrataMaggiorenneSingleAction(new SaveEntrataInNomadelfiaAction());
        $act->execute($persona, $data_entrata, $gruppo);

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

    /** @test */
    public function can_export_popolazione_into_word()
    {
        $this->login();
        $this->withoutExceptionHandling();
        $this->post(action([PopolazioneNomadelfiaController::class, 'print']),
            [
                'elenchi' => ["effePostOspFig", "famiglie", "gruppi", "aziende", "incarichi", "scuola"]
            ])
            ->assertSuccessful()
            ->assertDownload();
    }

}
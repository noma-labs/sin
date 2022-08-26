<?php

namespace Tests\Http\Controllers;

use App\Admin\Models\Risorsa;
use App\Admin\Models\Ruolo;
use App\Admin\Models\Sistema;
use App\Admin\Models\User;
use App\Nomadelfia\Azienda\Controllers\AziendeController;
use App\Nomadelfia\GruppoFamiliare\Controllers\GruppifamiliariController;
use App\Nomadelfia\Incarico\Controllers\IncarichiController;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\PopolazioneNomadelfiaController;
use Domain\Nomadelfia\Azienda\Models\Azienda;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Incarico\Models\Incarico;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\SaveEntrataInNomadelfiaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use Domain\Nomadelfia\Persona\Models\Persona;
use Carbon\Carbon;
use Tests\TestCase;


class NomadelfiaControllerTest extends TestCase
{
    /** @test */
    public function only_admin_can_see_nomadelfia_system()
    {
        $this->withExceptionHandling();

        $this
            ->get(action([PopolazioneNomadelfiaController::class, 'index']))
            ->assertForbidden();

        $this->login();

        $this
            ->get(action([PopolazioneNomadelfiaController::class, 'index']))
            ->assertSuccessful();
    }

    /** @test */
    public function show_popolazione_summary()
    {
        $this->withExceptionHandling();

        $this->login();

        $this
            ->get(action([PopolazioneNomadelfiaController::class, 'index']))
            ->assertSuccessful()
            ->assertSee("Gestione Popolazione")
            ->assertSee("Gestione Famiglie")
            ->assertSee("Gestione Gruppi Familiari");
    }

      /** @test */
    public function show_incarichi_index()
    {
        $this->withExceptionHandling();

        $this->login();

        $incarico = Incarico::factory()->create();

        $this
            ->get(action([IncarichiController::class, 'view']))
            ->assertSuccessful()
            ->assertSee($incarico->nome);

    }

    /** @test */
    public function show_aziende_index()
    {
        $this->withExceptionHandling();

        $this->login();

        $a = Azienda::factory()->create();

        $this
            ->get(action([AziendeController::class, 'view']))
            ->assertSuccessful()
            ->assertSee($a->nome_azienda);

        $this
            ->get(action([AziendeController::class, 'edit'], $a->id))
            ->assertSuccessful()
            ->assertSee($a->nome_azienda);

    }


    /** @test */
    public function show_gruppifamiliari_edit()
    {
        $this->withExceptionHandling();

        $this->login();

        $gruppo = GruppoFamiliare::factory()->create();
        $data_entrata = Carbon::now();
        $persona = Persona::factory()->cinquantenne()->maschio()->create();

        $action = new EntrataMaggiorenneSingleAction( new SaveEntrataInNomadelfiaAction());
        $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));

        $this
            ->get(action([GruppifamiliariController::class, 'view']))
            ->assertSuccessful()
            ->assertSee($gruppo->nome);

        $this
            ->get(action([GruppifamiliariController::class, 'edit'], $gruppo->id))
            ->assertSuccessful()
            ->assertSee($gruppo->nome);
    }

}
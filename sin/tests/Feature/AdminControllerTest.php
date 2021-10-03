<?php

namespace Tests\Feature;

use App\Admin\Models\Risorsa;
use App\Admin\Models\Ruolo;
use App\Admin\Models\Sistema;
use App\Admin\Models\User;
use App\Nomadelfia\Controllers\PopolazioneNomadelfiaController;
use Tests\TestCase;


class AdminControllerTest extends TestCase
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

}
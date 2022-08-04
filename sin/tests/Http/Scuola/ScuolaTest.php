<?php
namespace Tests\Http;
use App\Scuola\Controllers\ScuolaController;
use App\Scuola\Models\Anno;
use Tests\TestCase;

class ScuolaTest extends TestCase
{

    /** @test */
    public function can_list_anni_scolastici()
    {
        $this->login();
        $this->get(action([ScuolaController::class, 'summary']))
            ->assertSuccessful();

    }

    /** @test */
    public function can_create_nuovo_anno()
    {
        $this->login();
        $this->withoutExceptionHandling();
        $this->post(action([ScuolaController::class, 'aggiungiAnnoScolastico'], [
            'anno_inizio' => '2023',
            'data_inizio' => '2023-12-12',
        ]))->assertRedirect();

        $this->assertDatabaseHas('db_scuola.anno', [
            'scolastico' => '2023/2024'
        ]);
    }
}
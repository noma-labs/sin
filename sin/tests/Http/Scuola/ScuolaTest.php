<?php
namespace Tests\Http\Scuola;
use App\Scuola\Controllers\ClassiController;
use App\Scuola\Controllers\ScuolaController;
use App\Scuola\Models\Anno;
use App\Scuola\Models\ClasseTipo;
use App\Scuola\Models\Studente;
use Carbon\Carbon;
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

    /** @test */
    public function can_list_classi_in_anno_scolastico()
    {
        $a = Anno::createAnno(2017);
        $tipi = ClasseTipo::all();
        $c = $a->aggiungiClasse($tipi->random());
        $c->aggiungiAlunno(Studente::factory()->minorenne()->maschio()->create(), Carbon::now());
        $this->assertCount(1, $c->alunni()->get());

        $this->login();
        $this->get(action([ScuolaController::class, 'index'],["id"=>$a->id]))
            ->assertSuccessful();

    }

    /** @test */
    public function can_delete_classe()
    {
        $a = Anno::createAnno(2000);
        $tipi = ClasseTipo::all();
        $c = $a->aggiungiClasse($tipi->random());
        $s = Studente::factory()->minorenne()->maschio()->create();
        $c->aggiungiAlunno($s, Carbon::now());
        $this->assertCount(1, $c->alunni()->get());

        $this->login();
        $this->get(action([ScuolaController::class, 'index'], ['id' => $a->id]))
            ->assertSuccessful()
            ->assertSee($s->nominativo);

        $this->delete(action([ClassiController::class, 'delete'], ['id' => $c->id]));

        $this->get(action([ScuolaController::class, 'index'], ['id' => $a->id]))
            ->assertSuccessful()
            ->assertDontSee($s->nominativo);
    }
}
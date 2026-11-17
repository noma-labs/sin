<?php

declare(strict_types=1);

namespace Tests\Http\Scuola;

use App\Admin\Models\User;
use App\Scuola\Controllers\AnnoScolasticoController;
use App\Scuola\Controllers\ScuolaController;
use App\Scuola\Models\Anno;
use App\Scuola\Models\ClasseTipo;
use App\Scuola\Models\Studente;
use Carbon\Carbon;

it('forbids access to guests', function (): void {
    $this->get(action([ScuolaController::class, 'summary']))
        ->assertRedirect(route('login'));

});

it('can list anni scolastici', function (): void {
    login();
    $this->get(action([ScuolaController::class, 'summary']))
        ->assertSuccessful();

});

it('can create nuovo anno', function (): void {
    login();
    $this->withoutExceptionHandling();
    $this->post(action([AnnoScolasticoController::class, 'store'], [
        'anno inizio' => '2023',
        'data inizio' => '2023-12-12',
    ]))->assertRedirect();

    $this->assertDatabaseHas('db_scuola.anno', [
        'scolastico' => '2023/2024',
    ]);
});

it('can list classi in anno scolastico', function (): void {
    $a = Anno::createAnno(2017);
    $tipi = ClasseTipo::all();
    $c = $a->aggiungiClasse($tipi->random());
    $c->aggiungiAlunno(Studente::factory()->minorenne()->maschio()->create(), Carbon::now());
    $this->assertCount(1, $c->alunni()->get());

    login();
    $this->get(action([AnnoScolasticoController::class, 'show'], ['id' => $a->id]))
        ->assertSuccessful();

    $this->get(action([AnnoScolasticoController::class, 'showNew'], ['id' => $a->id]))
        ->assertSuccessful()
        ->assertSee($c->tipo->nome)
        ->assertSee($c->alunni()->first()->nominativo);

});

it('links the alunni of the elenchi to the profile of the person', function (): void {
    $anno = Anno::createAnno(2171);
    $classe = $anno->aggiungiClasse(ClasseTipo::all()->random());
    $alunno = Studente::factory()->minorenne()->maschio()->create();
    $classe->aggiungiAlunno($alunno, Carbon::now());
    $linkProfilo = 'href="'.route('nomadelfia.person.profile', $alunno->id).'"';

    login();
    $this->get(action([AnnoScolasticoController::class, 'show'], ['id' => $anno->id]))
        ->assertSuccessful()
        ->assertSee($linkProfilo, false);

    $this->get(action([AnnoScolasticoController::class, 'showNew'], ['id' => $anno->id]))
        ->assertSuccessful()
        ->assertSee($linkProfilo, false);
});

it('does not link the alunni to the profile for a user who cannot see the popolazione', function (): void {
    $anno = Anno::createAnno(2172);
    $classe = $anno->aggiungiClasse(ClasseTipo::all()->random());
    $alunno = Studente::factory()->minorenne()->maschio()->create();
    $classe->aggiungiAlunno($alunno, Carbon::now());
    $user = User::query()->create(['username' => 'solo-scuola', 'email' => 'solo-scuola@email.it', 'password' => 'secret', 'persona_id' => 0]);

    login($user);
    $this->get(action([AnnoScolasticoController::class, 'showNew'], ['id' => $anno->id]))
        ->assertSuccessful()
        ->assertSee($alunno->nome)
        ->assertDontSee(route('nomadelfia.person.profile', $alunno->id), false);
});

it('prints the alunni of an anno scolastico by ciclo and classe, sorted by nome', function (): void {
    $anno = Anno::createAnno(2031);
    $elementare = $anno->aggiungiClasse(ClasseTipo::query()->where('nome', ClasseTipo::PRIMA_ELEMENTARE)->firstOrFail());
    $media = $anno->aggiungiClasse(ClasseTipo::query()->where('nome', ClasseTipo::PRIMA_MEDIA)->firstOrFail());
    $beatrice = Studente::factory()->minorenne()->femmina()->create(['nome' => 'Beatrice', 'cognome' => fake()->unique()->lastName()]);
    $andrea = Studente::factory()->minorenne()->maschio()->create(['nome' => 'Andrea', 'cognome' => fake()->unique()->lastName()]);
    $carlo = Studente::factory()->minorenne()->maschio()->create(['nome' => 'Carlo', 'cognome' => fake()->unique()->lastName()]);
    $elementare->aggiungiAlunno($beatrice, Carbon::now());
    $elementare->aggiungiAlunno($andrea, Carbon::now());
    $media->aggiungiAlunno($carlo, Carbon::now());

    login();

    $this->get(route('scuola.anno.stampa', $anno->id))
        ->assertOk()
        ->assertSee('Anno scolastico 2031/2032')
        ->assertSeeInOrder([
            'elementari',
            ClasseTipo::PRIMA_ELEMENTARE,
            "Andrea {$andrea->cognome}",
            "Beatrice {$beatrice->cognome}",
            'medie',
            ClasseTipo::PRIMA_MEDIA,
            "Carlo {$carlo->cognome}",
            'Totale alunni',
            '3',
        ]);
});

it('returns not found when printing an anno scolastico that does not exist', function (): void {
    login();

    $this->get(route('scuola.anno.stampa', 0))
        ->assertNotFound();
});

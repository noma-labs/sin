<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Stato;
use DeriveStatoCivile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * The migration already ran on the test database: these tests add the stati sposato and vedovo, and run it again.
 */
function migrazioneStatoCivile(): DeriveStatoCivile
{
    if (! class_exists(DeriveStatoCivile::class)) {
        require_once database_path('migrations/nomadelfia/2026_10_21_100000_derive_stato_civile.php');
    }

    return new DeriveStatoCivile;
}

function assegnaStatoCivile(Persona $persona, string $stato, string $dataInizio, string $attivo = '1'): void
{
    $statoId = DB::connection('db_nomadelfia')->table('stati')->where('stato', $stato)->value('id')
        ?? DB::connection('db_nomadelfia')->table('stati')->insertGetId(['stato' => $stato, 'nome' => $stato]);

    DB::connection('db_nomadelfia')->table('persone_stati')->insert(['persona_id' => $persona->id, 'stato_id' => $statoId, 'data_inizio' => $dataInizio, 'stato' => $attivo]);
}

afterEach(function (): void {
    $stati = DB::connection('db_nomadelfia')->table('stati')->whereIn('stato', ['SPO', 'VED'])->pluck('id');
    DB::connection('db_nomadelfia')->table('persone_stati')->whereIn('stato_id', $stati)->delete();
    DB::connection('db_nomadelfia')->table('stati')->whereIn('id', $stati)->delete();
});

it('copies the date of the stato sposato into the famiglia, keeping the stati untouched', function (): void {
    $marito = Persona::factory()->maggiorenne()->maschio()->create();
    $moglie = Persona::factory()->maggiorenne()->femmina()->create();
    $famiglia = Famiglia::factory()->create(['data_creazione' => '2000-01-01']);
    $famiglia->assegnaCapoFamiglia($marito);
    $famiglia->assegnaMoglie($moglie);
    assegnaStatoCivile($marito, 'SPO', '1998-06-12');
    assegnaStatoCivile($moglie, 'SPO', '1998-06-12');
    $mammaNubile = Stato::perNome('mammanubile');
    $moglie->stati()->attach($mammaNubile->id, ['stato' => '0', 'data_inizio' => '1995-01-01', 'data_fine' => '1998-06-12']);

    migrazioneStatoCivile()->up();

    expect(Famiglia::findOrFail($famiglia->id)->data_creazione)->toBe('1998-06-12')
        ->and($marito->stati()->count())->toBe(1)
        ->and($moglie->stati()->count())->toBe(2)
        ->and($moglie->stati()->firstWhere('stati.id', $mammaNubile->id)->pivot->data_fine)->toBe('1998-06-12')
        ->and($moglie->statoCivile()['nome'])->toBe('Sposata');
});

it('copies the date of the stato sposato of a vedova heading the famiglia next to the deceased husband', function (): void {
    $vedova = Persona::factory()->maggiorenne()->femmina()->create();
    $marito = Persona::factory()->maggiorenne()->maschio()->create(['data_decesso' => '2015-11-03']);
    $famiglia = Famiglia::factory()->create(['data_creazione' => '2000-01-01']);
    $famiglia->assegnaCapoFamiglia($vedova);
    $famiglia->assegnaCapoFamiglia($marito, stato: '0');
    assegnaStatoCivile($vedova, 'SPO', '1968-06-12', '0');
    assegnaStatoCivile($vedova, 'VED', '2015-11-03');

    migrazioneStatoCivile()->up();

    expect(Famiglia::findOrFail($famiglia->id)->data_creazione)->toBe('1968-06-12')
        ->and($vedova->statoCivile()['nome'])->toBe('Vedova');
});

it('copies the date of the stato sposato into the famiglia of a capofamiglia whose coniuge is not in the famiglia', function (): void {
    Log::spy();
    $capofamiglia = Persona::factory()->maggiorenne()->femmina()->create();
    $famiglia = Famiglia::factory()->create(['data_creazione' => '2022-03-12']);
    $famiglia->assegnaCapoFamiglia($capofamiglia);
    assegnaStatoCivile($capofamiglia, 'SPO', '1998-06-12');

    migrazioneStatoCivile()->up();

    expect(Famiglia::findOrFail($famiglia->id)->data_creazione)->toBe('1998-06-12')
        ->and($capofamiglia->stati()->count())->toBe(1);
    Log::shouldNotHaveReceived('warning');
});

it('keeps the stato sposato and logs its date when the sposato is in no famiglia', function (): void {
    Log::spy();
    $sposato = Persona::factory()->maggiorenne()->maschio()->create(['nominativo' => 'Sposato Senza Famiglia']);
    assegnaStatoCivile($sposato, 'SPO', '1998-06-12');

    migrazioneStatoCivile()->up();

    expect($sposato->stati()->count())->toBe(1);
    Log::shouldHaveReceived('warning')->once()
        ->withArgs(fn (string $messaggio): bool => str_contains($messaggio, "Sposato Senza Famiglia (id {$sposato->id}): sposato/a dal 1998-06-12"));
});

it('refuses to run when the spouses have different dates of the stato sposato', function (): void {
    $marito = Persona::factory()->maggiorenne()->maschio()->create(['nominativo' => 'Marito Data Diversa']);
    $moglie = Persona::factory()->maggiorenne()->femmina()->create();
    $famiglia = Famiglia::factory()->create();
    $famiglia->assegnaCapoFamiglia($marito);
    $famiglia->assegnaMoglie($moglie);
    assegnaStatoCivile($marito, 'SPO', '1998-06-12');
    assegnaStatoCivile($moglie, 'SPO', '1999-01-01');

    expect(fn () => migrazioneStatoCivile()->up())
        ->toThrow(RuntimeException::class, 'Marito Data Diversa (id '.$marito->id.'): i coniugi hanno date diverse dello stato sposato');
});

it('keeps the stato vedovo even when the coniuge is not deceased', function (): void {
    $marito = Persona::factory()->maggiorenne()->maschio()->create();
    $moglie = Persona::factory()->maggiorenne()->femmina()->create();
    $famiglia = Famiglia::factory()->create();
    $famiglia->assegnaCapoFamiglia($marito);
    $famiglia->assegnaMoglie($moglie);
    assegnaStatoCivile($moglie, 'VED', '2010-01-01');

    migrazioneStatoCivile()->up();

    expect($moglie->stati()->count())->toBe(1);
});

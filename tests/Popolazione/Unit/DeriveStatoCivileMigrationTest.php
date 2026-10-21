<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Stato;
use DeriveStatoCivile;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * The migration already ran on the test database: these tests put back the stati sposato and vedovo it deletes, and run it again.
 */
function migrazioneStatoCivile(): DeriveStatoCivile
{
    if (! class_exists(DeriveStatoCivile::class)) {
        require_once database_path('migrations/nomadelfia/2026_10_21_100000_derive_stato_civile.php');
    }

    return new DeriveStatoCivile;
}

function assegnaStatoEliminato(Persona $persona, string $stato, string $dataInizio, string $attivo = '1'): void
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

it('copies the date of the stato sposato into the famiglia, keeping the other stati untouched', function (): void {
    $marito = Persona::factory()->maggiorenne()->maschio()->create();
    $moglie = Persona::factory()->maggiorenne()->femmina()->create();
    $famiglia = Famiglia::factory()->create(['data_creazione' => '2000-01-01']);
    $famiglia->assegnaCapoFamiglia($marito);
    $famiglia->assegnaMoglie($moglie);
    assegnaStatoEliminato($marito, 'SPO', '1998-06-12');
    assegnaStatoEliminato($moglie, 'SPO', '1998-06-12');
    $moglie->stati()->attach(Stato::perNome('mammanubile')->id, ['stato' => '0', 'data_inizio' => '1995-01-01', 'data_fine' => '1998-06-12']);

    migrazioneStatoCivile()->up();

    expect(Famiglia::findOrFail($famiglia->id)->data_creazione)->toBe('1998-06-12')
        ->and($marito->stati()->count())->toBe(0)
        ->and($moglie->statiAttivi()->count())->toBe(0)
        ->and($moglie->stati()->first()->pivot->data_fine)->toBe('1998-06-12')
        ->and($moglie->statoCivile()['nome'])->toBe('Sposata');
});

it('refuses to run when the coniuge of a sposato is not in the famiglia', function (): void {
    $marito = Persona::factory()->maggiorenne()->maschio()->create(['nominativo' => 'Marito Senza Moglie']);
    Famiglia::factory()->create()->assegnaCapoFamiglia($marito);
    assegnaStatoEliminato($marito, 'SPO', '1998-06-12');

    expect(fn () => migrazioneStatoCivile()->up())
        ->toThrow(RuntimeException::class, "Marito Senza Moglie (id {$marito->id}): è sposato/a, ma nella sua famiglia non risulta il coniuge");

    expect($marito->stati()->count())->toBe(1);
});

it('refuses to run when the spouses have different dates of the stato sposato', function (): void {
    $marito = Persona::factory()->maggiorenne()->maschio()->create(['nominativo' => 'Marito Data Diversa']);
    $moglie = Persona::factory()->maggiorenne()->femmina()->create();
    $famiglia = Famiglia::factory()->create();
    $famiglia->assegnaCapoFamiglia($marito);
    $famiglia->assegnaMoglie($moglie);
    assegnaStatoEliminato($marito, 'SPO', '1998-06-12');
    assegnaStatoEliminato($moglie, 'SPO', '1999-01-01');

    expect(fn () => migrazioneStatoCivile()->up())
        ->toThrow(RuntimeException::class, 'Marito Data Diversa (id '.$marito->id.'): i coniugi hanno date diverse dello stato sposato');
});

it('refuses to run when the coniuge of a vedova is not deceased', function (): void {
    $marito = Persona::factory()->maggiorenne()->maschio()->create();
    $moglie = Persona::factory()->maggiorenne()->femmina()->create(['nominativo' => 'Vedova Con Marito Vivo']);
    $famiglia = Famiglia::factory()->create();
    $famiglia->assegnaCapoFamiglia($marito);
    $famiglia->assegnaMoglie($moglie);
    assegnaStatoEliminato($moglie, 'VED', '2010-01-01');

    expect(fn () => migrazioneStatoCivile()->up())
        ->toThrow(RuntimeException::class, 'Vedova Con Marito Vivo (id '.$moglie->id.'): è vedovo/a, ma nella sua famiglia il coniuge non risulta deceduto');
});

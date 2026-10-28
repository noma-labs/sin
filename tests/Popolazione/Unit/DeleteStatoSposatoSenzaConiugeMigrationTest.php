<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Persona\Models\Persona;
use DeleteStatoSposatoSenzaConiuge;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * The migration already ran on the test database: these tests add the stato sposato back, and run it again.
 */
function migrazioneDeleteStatoSposatoSenzaConiuge(): DeleteStatoSposatoSenzaConiuge
{
    if (! class_exists(DeleteStatoSposatoSenzaConiuge::class)) {
        require_once database_path('migrations/nomadelfia/2026_10_28_100000_delete_stato_sposato_senza_coniuge.php');
    }

    return new DeleteStatoSposatoSenzaConiuge;
}

function assegnaStatoSposatoSenzaConiuge(Persona $persona, string $dataInizio): void
{
    $statoId = DB::connection('db_nomadelfia')->table('stati')->where('stato', 'SPO')->value('id')
        ?? DB::connection('db_nomadelfia')->table('stati')->insertGetId(['stato' => 'SPO', 'nome' => 'SPOSATO/A']);

    DB::connection('db_nomadelfia')->table('persone_stati')->insert(['persona_id' => $persona->id, 'stato_id' => $statoId, 'data_inizio' => $dataInizio, 'stato' => '1']);
}

afterEach(function (): void {
    $stato = DB::connection('db_nomadelfia')->table('stati')->where('stato', 'SPO')->value('id');
    DB::connection('db_nomadelfia')->table('persone_stati')->where('stato_id', $stato)->delete();
    DB::connection('db_nomadelfia')->table('stati')->where('stato', 'SPO')->delete();
});

it('deletes the stato sposato of a sposato without coniuge in the famiglia, who stays sposato from the famiglia of tipo coniugi', function (): void {
    $sposato = Persona::factory()->maggiorenne()->maschio()->create();
    Famiglia::factory()->coniugi()->create(['data_creazione' => '1998-06-12'])->assegnaCapoFamiglia($sposato);
    assegnaStatoSposatoSenzaConiuge($sposato, '1998-06-12');

    migrazioneDeleteStatoSposatoSenzaConiuge()->up();

    expect($sposato->stati()->count())->toBe(0)
        ->and(DB::connection('db_nomadelfia')->table('stati')->where('stato', 'SPO')->exists())->toBeFalse()
        ->and($sposato->statoCivile()['nome'])->toBe('Sposato')
        ->and($sposato->statoCivile()['dal']?->toDateString())->toBe('1998-06-12');
});

it('refuses to run when the stato sposato is not in a famiglia of tipo coniugi with its date', function (): void {
    $diversa = Persona::factory()->maggiorenne()->maschio()->create(['nominativo' => 'Sposato Data Diversa']);
    Famiglia::factory()->coniugi()->create(['data_creazione' => '2000-01-01'])->assegnaCapoFamiglia($diversa);
    assegnaStatoSposatoSenzaConiuge($diversa, '1998-06-12');
    $senzaTipo = Persona::factory()->maggiorenne()->femmina()->create(['nominativo' => 'Sposata Senza Tipo']);
    Famiglia::factory()->create(['data_creazione' => '1998-06-12'])->assegnaCapoFamiglia($senzaTipo);
    assegnaStatoSposatoSenzaConiuge($senzaTipo, '1998-06-12');

    expect(fn () => migrazioneDeleteStatoSposatoSenzaConiuge()->up())
        ->toThrow(RuntimeException::class, "Sposato Data Diversa (id {$diversa->id}): sposato/a dal 1998-06-12\n- Sposata Senza Tipo (id {$senzaTipo->id}): sposato/a dal 1998-06-12")
        ->and(DB::connection('db_nomadelfia')->table('persone_stati')->whereIn('persona_id', [$diversa->id, $senzaTipo->id])->count())->toBe(2);
});

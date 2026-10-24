<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Stato;
use DeleteStatoSposato;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * The migration already ran on the test database: these tests add the stato sposato back, and run it again.
 */
function migrazioneDeleteStatoSposato(): DeleteStatoSposato
{
    if (! class_exists(DeleteStatoSposato::class)) {
        require_once database_path('migrations/nomadelfia/2026_10_23_100000_delete_stato_sposato.php');
    }

    return new DeleteStatoSposato;
}

function assegnaStatoSposato(Persona $persona, string $dataInizio): void
{
    $statoId = DB::connection('db_nomadelfia')->table('stati')->where('stato', 'SPO')->value('id')
        ?? DB::connection('db_nomadelfia')->table('stati')->insertGetId(['stato' => 'SPO', 'nome' => 'SPOSATO/A']);

    DB::connection('db_nomadelfia')->table('persone_stati')->insert(['persona_id' => $persona->id, 'stato_id' => $statoId, 'data_inizio' => $dataInizio, 'stato' => '1']);
}

afterEach(function (): void {
    $stati = DB::connection('db_nomadelfia')->table('stati')->where('stato', 'SPO')->pluck('id');
    DB::connection('db_nomadelfia')->table('persone_stati')->whereIn('stato_id', $stati)->delete();
    DB::connection('db_nomadelfia')->table('stati')->whereIn('id', $stati)->delete();
});

it('deletes the stato sposato when its date is the data_creazione of the famiglia, keeping the other stati', function (): void {
    $marito = Persona::factory()->maggiorenne()->maschio()->create();
    $moglie = Persona::factory()->maggiorenne()->femmina()->create();
    $famiglia = Famiglia::factory()->create(['data_creazione' => '1998-06-12']);
    $famiglia->assegnaCapoFamiglia($marito);
    $famiglia->assegnaMoglie($moglie);
    assegnaStatoSposato($marito, '1998-06-12');
    assegnaStatoSposato($moglie, '1998-06-12');
    $moglie->assegnaStato(Stato::perNome('separato'), '2005-01-01');

    migrazioneDeleteStatoSposato()->up();

    expect(DB::connection('db_nomadelfia')->table('stati')->where('stato', 'SPO')->exists())->toBeFalse()
        ->and($marito->stati()->count())->toBe(0)
        ->and($moglie->stati()->pluck('stati.stato')->all())->toBe(['SEP'])
        ->and(Famiglia::findOrFail($famiglia->id)->data_creazione)->toBe('1998-06-12')
        ->and($moglie->matrimonioAttuale()?->id)->toBe($famiglia->id);
});

it('refuses to run when the date of a stato sposato is not the data_creazione of the famiglia', function (): void {
    $marito = Persona::factory()->maggiorenne()->maschio()->create(['nominativo' => 'Marito Data Diversa']);
    $famiglia = Famiglia::factory()->create(['data_creazione' => '2000-01-01']);
    $famiglia->assegnaCapoFamiglia($marito);
    assegnaStatoSposato($marito, '1998-06-12');

    expect(fn () => migrazioneDeleteStatoSposato()->up())
        ->toThrow(RuntimeException::class, "Marito Data Diversa (id {$marito->id}): sposato/a dal 1998-06-12")
        ->and(DB::connection('db_nomadelfia')->table('persone_stati')->where('persona_id', $marito->id)->count())->toBe(1);
});

it('refuses to run when the sposato is in no famiglia', function (): void {
    $sposato = Persona::factory()->maggiorenne()->maschio()->create(['nominativo' => 'Sposato Senza Famiglia']);
    assegnaStatoSposato($sposato, '1998-06-12');

    expect(fn () => migrazioneDeleteStatoSposato()->up())
        ->toThrow(RuntimeException::class, "Sposato Senza Famiglia (id {$sposato->id}): sposato/a dal 1998-06-12")
        ->and(DB::connection('db_nomadelfia')->table('persone_stati')->where('persona_id', $sposato->id)->count())->toBe(1);
});

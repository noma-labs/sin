<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Famiglia\Models\TipoFamiglia;
use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Stato;
use DeleteStatiDerivati;
use Illuminate\Support\Facades\DB;
use RuntimeException;

const STATI_DERIVATI = ['CEL', 'NUB', 'VED', 'CDE', 'MAV', 'MAM', 'MAN'];

/**
 * The migration already ran on the test database: these tests add the derived stati back, and run it again.
 */
function migrazioneDeleteStatiDerivati(): DeleteStatiDerivati
{
    if (! class_exists(DeleteStatiDerivati::class)) {
        require_once database_path('migrations/nomadelfia/2026_10_24_100000_delete_stati_derivati.php');
    }

    return new DeleteStatiDerivati;
}

function assegnaStatoDerivato(Persona $persona, string $stato, string $dataInizio): void
{
    $statoId = DB::connection('db_nomadelfia')->table('stati')->where('stato', $stato)->value('id')
        ?? DB::connection('db_nomadelfia')->table('stati')->insertGetId(['stato' => $stato, 'nome' => $stato]);

    DB::connection('db_nomadelfia')->table('persone_stati')->insert(['persona_id' => $persona->id, 'stato_id' => $statoId, 'data_inizio' => $dataInizio, 'stato' => '1']);
}

function eliminaStatiDerivati(): void
{
    $stati = DB::connection('db_nomadelfia')->table('stati')->whereIn('stato', STATI_DERIVATI)->pluck('id');
    DB::connection('db_nomadelfia')->table('persone_stati')->whereIn('stato_id', $stati)->delete();
    DB::connection('db_nomadelfia')->table('stati')->whereIn('id', $stati)->delete();
}

beforeEach(function (): void {
    // the checks of the migration read the whole database: drop the derived stati left by the other tests
    eliminaStatiDerivati();
});

afterEach(function (): void {
    eliminaStatiDerivati();
});

it('deletes the stati derived from the famiglia, keeping sacerdote and separato', function (): void {
    $marito = Persona::factory()->maggiorenne()->maschio()->create(['data_decesso' => '2015-11-03']);
    $vedova = Persona::factory()->maggiorenne()->femmina()->create();
    $famiglia = Famiglia::factory()->create(['tipo' => TipoFamiglia::Coniugi]);
    $famiglia->assegnaCapoFamiglia($marito, stato: '0');
    $famiglia->assegnaMoglie($vedova);
    assegnaStatoDerivato($vedova, 'VED', '2015-11-03');
    $vedova->assegnaStato(Stato::perNome('separato'), '2005-01-01');
    $mamma = Persona::factory()->maggiorenne()->femmina()->create();
    Famiglia::factory()->create(['tipo' => TipoFamiglia::MammaDiVocazione])->assegnaCapoFamiglia($mamma);
    assegnaStatoDerivato($mamma, 'MAV', '1980-03-12');
    $celibe = Persona::factory()->maggiorenne()->maschio()->create();
    assegnaStatoDerivato($celibe, 'CEL', '1990-01-01');
    $sacerdote = Persona::factory()->maggiorenne()->maschio()->create();
    $sacerdote->assegnaStato(Stato::perNome('sacerdote'), '1995-05-05');

    migrazioneDeleteStatiDerivati()->up();

    expect(DB::connection('db_nomadelfia')->table('stati')->whereIn('stato', STATI_DERIVATI)->exists())->toBeFalse()
        ->and($vedova->stati()->pluck('stati.stato')->all())->toBe(['SEP'])
        ->and($mamma->stati()->count())->toBe(0)
        ->and($celibe->stati()->count())->toBe(0)
        ->and($sacerdote->stati()->pluck('stati.stato')->all())->toBe(['SAC']);
});

it('sets celibe di elezione as the tipo of the famiglia headed by the celibe di elezione', function (): void {
    $celibe = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia = Famiglia::factory()->create();
    $famiglia->assegnaCapoFamiglia($celibe);
    assegnaStatoDerivato($celibe, 'CDE', '1985-09-01');

    migrazioneDeleteStatiDerivati()->up();

    expect(Famiglia::findOrFail($famiglia->id)->tipo)->toBe(TipoFamiglia::CelibeDiElezione)
        ->and($celibe->famigliaSenzaConiuge()?->id)->toBe($famiglia->id)
        ->and($celibe->stati()->count())->toBe(0);
});

it('refuses to run when the coniuge of a vedova is not deceased', function (): void {
    $vedova = Persona::factory()->maggiorenne()->femmina()->create(['nominativo' => 'Vedova Marito Vivo']);
    $famiglia = Famiglia::factory()->create();
    $famiglia->assegnaCapoFamiglia(Persona::factory()->maggiorenne()->maschio()->create());
    $famiglia->assegnaMoglie($vedova);
    assegnaStatoDerivato($vedova, 'VED', '2015-11-03');

    expect(fn () => migrazioneDeleteStatiDerivati()->up())
        ->toThrow(RuntimeException::class, "Vedova Marito Vivo (id {$vedova->id}): è vedovo/a")
        ->and(DB::connection('db_nomadelfia')->table('persone_stati')->where('persona_id', $vedova->id)->count())->toBe(1);
});

it('refuses to run when a mamma is not capofamiglia of a famiglia of her tipo', function (): void {
    $mamma = Persona::factory()->maggiorenne()->femmina()->create(['nominativo' => 'Mamma Tipo Diverso']);
    Famiglia::factory()->create(['tipo' => TipoFamiglia::NomadelfaMamma])->assegnaCapoFamiglia($mamma);
    assegnaStatoDerivato($mamma, 'MAV', '1980-03-12');

    expect(fn () => migrazioneDeleteStatiDerivati()->up())
        ->toThrow(RuntimeException::class, "Mamma Tipo Diverso (id {$mamma->id}): ha uno stato mamma");
});

it('refuses to run when a celibe di elezione heads no famiglia', function (): void {
    $celibe = Persona::factory()->maggiorenne()->maschio()->create(['nominativo' => 'Celibe Senza Famiglia']);
    assegnaStatoDerivato($celibe, 'CDE', '1985-09-01');

    expect(fn () => migrazioneDeleteStatiDerivati()->up())
        ->toThrow(RuntimeException::class, "Celibe Senza Famiglia (id {$celibe->id}): è celibe di elezione, ma non è capofamiglia di una sola famiglia");
});

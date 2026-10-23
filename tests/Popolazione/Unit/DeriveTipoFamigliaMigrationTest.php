<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Famiglia\Models\TipoFamiglia;
use App\Anagrafe\Persona\Models\Persona;
use DeriveTipoFamiglia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * The migration already ran on the test database: these tests add famiglie without tipo and the stati, and run it again.
 */
function migrazioneTipoFamiglia(): DeriveTipoFamiglia
{
    if (! class_exists(DeriveTipoFamiglia::class)) {
        require_once database_path('migrations/nomadelfia/2026_10_22_100000_derive_tipo_famiglia.php');
    }

    return new DeriveTipoFamiglia;
}

function assegnaStatoPerTipo(Persona $persona, string $stato, string $dataInizio, string $attivo = '1'): void
{
    $statoId = DB::connection('db_nomadelfia')->table('stati')->where('stato', $stato)->value('id')
        ?? DB::connection('db_nomadelfia')->table('stati')->insertGetId(['stato' => $stato, 'nome' => $stato]);

    DB::connection('db_nomadelfia')->table('persone_stati')->insert(['persona_id' => $persona->id, 'stato_id' => $statoId, 'data_inizio' => $dataInizio, 'stato' => $attivo]);
}

beforeEach(function (): void {
    // the checks of the migration read the whole database: drop the stati mamma left by the other tests
    DB::connection('db_nomadelfia')->table('persone_stati')
        ->whereIn('stato_id', DB::connection('db_nomadelfia')->table('stati')->whereIn('stato', ['MAV', 'MAM', 'MAN'])->pluck('id'))
        ->delete();
});

afterEach(function (): void {
    $stati = DB::connection('db_nomadelfia')->table('stati')->where('stato', 'SPO')->pluck('id');
    DB::connection('db_nomadelfia')->table('persone_stati')->whereIn('stato_id', $stati)->delete();
    DB::connection('db_nomadelfia')->table('stati')->whereIn('id', $stati)->delete();
});

it('sets coniugi for the famiglie with both coniugi, also a vedova next to the deceased husband', function (): void {
    $coniugi = Famiglia::factory()->create();
    $coniugi->assegnaCapoFamiglia(Persona::factory()->maggiorenne()->maschio()->create());
    $coniugi->assegnaMoglie(Persona::factory()->maggiorenne()->femmina()->create());
    $vedova = Famiglia::factory()->create();
    $vedova->assegnaCapoFamiglia(Persona::factory()->maggiorenne()->femmina()->create());
    $vedova->assegnaCapoFamiglia(Persona::factory()->maggiorenne()->maschio()->create(['data_decesso' => '2015-11-03']), stato: '0');

    migrazioneTipoFamiglia()->up();

    expect(Famiglia::findOrFail($coniugi->id)->tipo)->toBe(TipoFamiglia::Coniugi)
        ->and(Famiglia::findOrFail($vedova->id)->tipo)->toBe(TipoFamiglia::Coniugi);
});

it('sets the tipo of the famiglia from the active stato mamma of the capofamiglia, before the closed one', function (): void {
    $mammaVocazione = Persona::factory()->maggiorenne()->femmina()->create();
    $vocazione = Famiglia::factory()->create();
    $vocazione->assegnaCapoFamiglia($mammaVocazione);
    assegnaStatoPerTipo($mammaVocazione, 'MAV', '1980-03-12');
    $nomadelfaMamma = Persona::factory()->maggiorenne()->femmina()->create();
    $nomadelfa = Famiglia::factory()->create();
    $nomadelfa->assegnaCapoFamiglia($nomadelfaMamma);
    assegnaStatoPerTipo($nomadelfaMamma, 'MAN', '1983-10-30', '0');
    assegnaStatoPerTipo($nomadelfaMamma, 'MAM', '1999-03-10');

    migrazioneTipoFamiglia()->up();

    expect(Famiglia::findOrFail($vocazione->id)->tipo)->toBe(TipoFamiglia::MammaDiVocazione)
        ->and(Famiglia::findOrFail($nomadelfa->id)->tipo)->toBe(TipoFamiglia::NomadelfaMamma);
});

it('sets the tipo from the closed stato mamma of a deceased capofamiglia', function (): void {
    $mamma = Persona::factory()->maggiorenne()->femmina()->create(['data_decesso' => '2020-11-19']);
    $famiglia = Famiglia::factory()->create();
    $famiglia->assegnaCapoFamiglia($mamma);
    assegnaStatoPerTipo($mamma, 'MAV', '1968-10-24', '0');

    migrazioneTipoFamiglia()->up();

    expect(Famiglia::findOrFail($famiglia->id)->tipo)->toBe(TipoFamiglia::MammaDiVocazione);
});

it('sets coniugi for a sposata capofamiglia whose husband is not in the famiglia', function (): void {
    $moglie = Persona::factory()->maggiorenne()->femmina()->create();
    $famiglia = Famiglia::factory()->create();
    $famiglia->assegnaCapoFamiglia($moglie);
    assegnaStatoPerTipo($moglie, 'SPO', '2022-03-12');

    migrazioneTipoFamiglia()->up();

    expect(Famiglia::findOrFail($famiglia->id)->tipo)->toBe(TipoFamiglia::Coniugi);
});

it('keeps the tipo set by hand', function (): void {
    $famiglia = Famiglia::factory()->create(['tipo' => TipoFamiglia::MammaNubile]);
    $famiglia->assegnaCapoFamiglia(Persona::factory()->maggiorenne()->maschio()->create());
    $famiglia->assegnaMoglie(Persona::factory()->maggiorenne()->femmina()->create());

    migrazioneTipoFamiglia()->up();

    expect(Famiglia::findOrFail($famiglia->id)->tipo)->toBe(TipoFamiglia::MammaNubile);
});

it('leaves without tipo and logs a famiglia with the capofamiglia alone and no stato', function (): void {
    Log::spy();
    $famiglia = Famiglia::factory()->create(['nome_famiglia' => 'Famiglia Senza Tipo']);
    $famiglia->assegnaCapoFamiglia(Persona::factory()->maggiorenne()->maschio()->create());

    migrazioneTipoFamiglia()->up();

    expect(Famiglia::findOrFail($famiglia->id)->tipo)->toBeNull();
    Log::shouldHaveReceived('warning')->once()
        ->withArgs(fn (string $messaggio): bool => str_contains($messaggio, "Famiglia Senza Tipo (id {$famiglia->id})"));
});

it('refuses to run when a mamma has the coniuge in her famiglia', function (): void {
    $marito = Persona::factory()->maggiorenne()->maschio()->create();
    $moglie = Persona::factory()->maggiorenne()->femmina()->create(['nominativo' => 'Mamma Con Marito']);
    $famiglia = Famiglia::factory()->create();
    $famiglia->assegnaCapoFamiglia($marito);
    $famiglia->assegnaMoglie($moglie);
    assegnaStatoPerTipo($moglie, 'MAV', '1980-03-12');

    expect(fn () => migrazioneTipoFamiglia()->up())
        ->toThrow(RuntimeException::class, "Mamma Con Marito (id {$moglie->id}): ha uno stato mamma attivo, ma nella sua famiglia c'è il coniuge");

    expect(Famiglia::findOrFail($famiglia->id)->tipo)->toBeNull();
});

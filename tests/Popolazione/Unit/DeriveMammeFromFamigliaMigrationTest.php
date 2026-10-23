<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Famiglia\Models\TipoFamiglia;
use App\Anagrafe\Persona\Models\Persona;
use DeriveMammeFromFamiglia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

/**
 * The migration already ran on the test database: these tests revert it, put back the stati mamma di vocazione and mamma nubile it deletes, and run it again.
 */
function migrazioneMamme(): DeriveMammeFromFamiglia
{
    if (! class_exists(DeriveMammeFromFamiglia::class)) {
        require_once database_path('migrations/nomadelfia/2026_10_23_100000_derive_mamme_from_famiglia.php');
    }

    return new DeriveMammeFromFamiglia;
}

function assegnaStatoMamma(Persona $persona, string $stato, string $dataInizio, string $attivo = '1', ?string $dataFine = null): void
{
    DB::connection('db_nomadelfia')->table('persone_stati')->insert([
        'persona_id' => $persona->id,
        'stato_id' => DB::connection('db_nomadelfia')->table('stati')->where('stato', $stato)->value('id'),
        'data_inizio' => $dataInizio,
        'data_fine' => $dataFine,
        'stato' => $attivo,
    ]);
}

afterEach(function (): void {
    if (! Schema::connection('db_nomadelfia')->hasColumn('famiglie', 'tipo')) {
        DB::connection('db_nomadelfia')->table('persone_stati')
            ->whereIn('stato_id', DB::connection('db_nomadelfia')->table('stati')->whereIn('stato', ['MAV', 'MAN'])->pluck('id'))
            ->delete();
        migrazioneMamme()->up();
    }
});

it('turns the stato mamma di vocazione into the tipo and the data_creazione of her famiglia', function (): void {
    $mamma = Persona::factory()->maggiorenne()->femmina()->create();
    $famiglia = Famiglia::factory()->create(['data_creazione' => '2000-01-01']);
    $famiglia->assegnaCapoFamiglia($mamma);
    migrazioneMamme()->down();
    assegnaStatoMamma($mamma, 'MAV', '1980-03-12');

    migrazioneMamme()->up();

    $famiglia = Famiglia::findOrFail($famiglia->id);
    expect($famiglia->tipo)->toBe(TipoFamiglia::MammaDiVocazione)
        ->and($famiglia->data_creazione)->toBe('1980-03-12')
        ->and($mamma->stati()->count())->toBe(0)
        ->and($mamma->statoCivile()['nome'])->toBe('Nubile')
        ->and($mamma->mammaSenzaMarito()['nome'])->toBe('Mamma di vocazione');
});

it('turns the stato mamma nubile into the tipo of her famiglia', function (): void {
    $mamma = Persona::factory()->maggiorenne()->femmina()->create();
    $famiglia = Famiglia::factory()->create(['data_creazione' => '2000-01-01']);
    $famiglia->assegnaCapoFamiglia($mamma);
    migrazioneMamme()->down();
    assegnaStatoMamma($mamma, 'MAN', '1995-05-20');

    migrazioneMamme()->up();

    $famiglia = Famiglia::findOrFail($famiglia->id);
    expect($famiglia->tipo)->toBe(TipoFamiglia::MammaNubile)
        ->and($famiglia->data_creazione)->toBe('1995-05-20');
});

it('deletes a closed stato mamma di vocazione and logs it, keeping the famiglia of tipo coniugi', function (): void {
    Log::spy();
    $mamma = Persona::factory()->maggiorenne()->femmina()->create(['nominativo' => 'Mamma Non Piu Vocazione']);
    $famiglia = Famiglia::factory()->create(['data_creazione' => '2000-01-01']);
    $famiglia->assegnaCapoFamiglia($mamma);
    migrazioneMamme()->down();
    assegnaStatoMamma($mamma, 'MAV', '1980-03-12', '0', '1999-12-31');

    migrazioneMamme()->up();

    expect(Famiglia::findOrFail($famiglia->id)->tipo)->toBe(TipoFamiglia::Coniugi)
        ->and($mamma->stati()->count())->toBe(0);
    Log::shouldHaveReceived('warning')
        ->withArgs(fn (string $messaggio): bool => str_contains($messaggio, "Mamma Non Piu Vocazione (id {$mamma->id}): MAMMA DI VOCAZIONE dal 1980-03-12 al 1999-12-31"));
});

it('refuses to run when a mamma di vocazione has the coniuge in her famiglia', function (): void {
    $marito = Persona::factory()->maggiorenne()->maschio()->create();
    $moglie = Persona::factory()->maggiorenne()->femmina()->create(['nominativo' => 'Mamma Con Marito']);
    $famiglia = Famiglia::factory()->create();
    $famiglia->assegnaCapoFamiglia($marito);
    $famiglia->assegnaMoglie($moglie);
    migrazioneMamme()->down();
    assegnaStatoMamma($moglie, 'MAV', '1980-03-12');

    expect(fn () => migrazioneMamme()->up())
        ->toThrow(RuntimeException::class, "Mamma Con Marito (id {$moglie->id}): è mamma di vocazione o mamma nubile, ma nella sua famiglia c'è il coniuge");

    expect(Schema::connection('db_nomadelfia')->hasColumn('famiglie', 'tipo'))->toBeFalse();
});

<?php

declare(strict_types=1);

use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Famiglia\Models\TipoFamiglia;
use App\Anagrafe\Persona\Models\Persona;

/**
 * Writes the CSV of the dry run, with the columns read by the import and the defaults of a NUOVA_FAMIGLIA row.
 *
 * @param  list<array<string, string|int>>  $righe
 */
function csvRicostruzione(array $righe): string
{
    $colonne = ['esito', 'id_alfa', 'persona_id', 'persona', 'testo_famiglia', 'ordine', 'famiglia', 'famiglia_id_db', 'nome_famiglia', 'tipo', 'data_creazione', 'posizione', 'stato', 'note_testo', 'problemi'];
    $file = tempnam(sys_get_temp_dir(), 'ricostruzione-');
    $handle = fopen($file, 'w');
    fputcsv($handle, $colonne, escape: '');
    foreach ($righe as $riga) {
        $riga += ['esito' => 'NUOVA_FAMIGLIA', 'testo_famiglia' => 'ALDO E LICE', 'famiglia' => 'ALDO E LICE', 'famiglia_id_db' => '', 'nome_famiglia' => 'Aldo L', 'tipo' => 'CON', 'data_creazione' => '1960-05-01', 'stato' => '0'];
        fputcsv($handle, array_map(fn (string $colonna): string => (string) ($riga[$colonna] ?? ''), $colonne), escape: '');
    }
    fclose($handle);

    return $file;
}

afterEach(function (): void {
    // the test database is not reset between tests: drop what the import wrote
    DB::connection('db_nomadelfia')->table('famiglie_persone')->where('note', 'like', 'Dal file ALFA di Enrico:%')->delete();
    DB::connection('db_nomadelfia')->table('famiglie')->where('nome_famiglia', 'Aldo L')->delete();
});

it('creates the new famiglie with their componenti as history, skipping the rows not to import', function (): void {
    $aldo = Persona::factory()->maggiorenne()->maschio()->create();
    $lice = Persona::factory()->maggiorenne()->femmina()->create();
    $figlio = Persona::factory()->maggiorenne()->maschio()->create();
    $daVerificare = Persona::factory()->maggiorenne()->maschio()->create();
    $file = csvRicostruzione([
        ['persona_id' => $aldo->id, 'posizione' => 'CAPO FAMIGLIA'],
        ['persona_id' => $lice->id, 'posizione' => 'MOGLIE'],
        ['persona_id' => $figlio->id, 'posizione' => 'FIGLIO NATO', 'testo_famiglia' => 'ALDO E LICE - PAOLO E AURORA'],
        ['esito' => 'DA_VERIFICARE', 'persona_id' => $daVerificare->id, 'posizione' => 'SINGLE'],
    ]);

    $this->artisan('famiglie:import-alfa', ['file' => $file])->assertSuccessful();

    $famiglia = Famiglia::query()->where('nome_famiglia', 'Aldo L')->firstOrFail();
    expect($famiglia->tipo)->toBe(TipoFamiglia::Coniugi)
        ->and($famiglia->data_creazione)->toBe('1960-05-01');
    $this->assertDatabaseHas('famiglie_persone', ['famiglia_id' => $famiglia->id, 'persona_id' => $aldo->id, 'posizione_famiglia' => 'CAPO FAMIGLIA', 'stato' => '0'], 'db_nomadelfia');
    $this->assertDatabaseHas('famiglie_persone', ['famiglia_id' => $famiglia->id, 'persona_id' => $lice->id, 'posizione_famiglia' => 'MOGLIE', 'stato' => '0'], 'db_nomadelfia');
    $this->assertDatabaseHas('famiglie_persone', [
        'famiglia_id' => $famiglia->id, 'persona_id' => $figlio->id, 'posizione_famiglia' => 'FIGLIO NATO', 'stato' => '0',
        'note' => 'Dal file ALFA di Enrico: ALDO E LICE - PAOLO E AURORA',
    ], 'db_nomadelfia');
    $this->assertDatabaseMissing('famiglie_persone', ['persona_id' => $daVerificare->id], 'db_nomadelfia');
});

it('adds the componenti to an existing famiglia, leaving its current nucleo untouched', function (): void {
    $famiglia = Famiglia::factory()->coniugi()->create();
    $capofamiglia = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia->assegnaCapoFamiglia($capofamiglia);
    $accolto = Persona::factory()->minorenne()->maschio()->create();
    $file = csvRicostruzione([
        ['esito' => 'DA_AGGIUNGERE', 'persona_id' => $accolto->id, 'famiglia_id_db' => $famiglia->id, 'posizione' => 'FIGLIO ACCOLTO'],
    ]);
    $famiglie = Famiglia::query()->count();

    $this->artisan('famiglie:import-alfa', ['file' => $file])->assertSuccessful();

    $this->assertDatabaseHas('famiglie_persone', ['famiglia_id' => $famiglia->id, 'persona_id' => $accolto->id, 'posizione_famiglia' => 'FIGLIO ACCOLTO', 'stato' => '0'], 'db_nomadelfia');
    $this->assertDatabaseHas('famiglie_persone', ['famiglia_id' => $famiglia->id, 'persona_id' => $capofamiglia->id, 'posizione_famiglia' => 'CAPO FAMIGLIA', 'stato' => '1'], 'db_nomadelfia');
    expect(Famiglia::query()->count())->toBe($famiglie);
});

it('saves nothing with the dry run, showing what would be imported', function (): void {
    $aldo = Persona::factory()->maggiorenne()->maschio()->create();
    $file = csvRicostruzione([
        ['persona_id' => $aldo->id, 'posizione' => 'CAPO FAMIGLIA'],
        ['esito' => 'GIA_PRESENTE', 'persona_id' => $aldo->id, 'posizione' => 'CAPO FAMIGLIA'],
    ]);

    $this->artisan('famiglie:import-alfa', ['file' => $file, '--dry-run' => true])
        ->expectsTable(['Famiglie create', 'Componenti aggiunti', 'Righe ignorate'], [[1, 1, 1]])
        ->assertSuccessful();

    $this->assertDatabaseMissing('famiglie', ['nome_famiglia' => 'Aldo L'], 'db_nomadelfia');
    $this->assertDatabaseMissing('famiglie_persone', ['persona_id' => $aldo->id], 'db_nomadelfia');
});

it('refuses the whole file when a row to import is wrong', function (Closure $riga, string $problema): void {
    $aldo = Persona::factory()->maggiorenne()->maschio()->create();
    $file = csvRicostruzione([
        ['persona_id' => $aldo->id, 'posizione' => 'CAPO FAMIGLIA'],
        $riga(),
    ]);
    $famiglie = Famiglia::query()->count();

    $this->artisan('famiglie:import-alfa', ['file' => $file])
        ->expectsOutputToContain($problema)
        ->assertFailed();

    expect(Famiglia::query()->count())->toBe($famiglie);
    $this->assertDatabaseMissing('famiglie_persone', ['persona_id' => $aldo->id], 'db_nomadelfia');
})->with([
    'a persona that does not exist' => [fn (): array => ['persona_id' => 99999999, 'posizione' => 'FIGLIO NATO'], 'riga 3: la persona `99999999` non esiste'],
    'a posizione that does not exist' => [fn (): array => ['persona_id' => Persona::factory()->create()->id, 'posizione' => 'NIPOTE'], 'riga 3: la posizione `NIPOTE` non è valida'],
    'a stato that does not exist' => [fn (): array => ['persona_id' => Persona::factory()->create()->id, 'posizione' => 'FIGLIO NATO', 'stato' => '2'], 'riga 3: lo stato `2` non è valido'],
    'a second capofamiglia' => [fn (): array => ['persona_id' => Persona::factory()->maggiorenne()->maschio()->create()->id, 'posizione' => 'CAPO FAMIGLIA'], 'la nuova famiglia `ALDO E LICE` ha più di un CAPO FAMIGLIA'],
    'a new famiglia without capofamiglia' => [fn (): array => ['persona_id' => Persona::factory()->create()->id, 'posizione' => 'FIGLIO ACCOLTO', 'famiglia' => 'ADA', 'nome_famiglia' => 'Ada', 'tipo' => 'MAV'], 'la nuova famiglia `ADA` non ha un CAPO FAMIGLIA'],
    'a new famiglia with a name already taken' => [fn (): array => ['persona_id' => Persona::factory()->create()->id, 'posizione' => 'CAPO FAMIGLIA', 'famiglia' => 'ADA', 'nome_famiglia' => Famiglia::factory()->create()->nome_famiglia, 'tipo' => 'MAV'], 'esiste già una famiglia di nome'],
    'a new famiglia with a tipo that does not exist' => [fn (): array => ['persona_id' => Persona::factory()->create()->id, 'posizione' => 'CAPO FAMIGLIA', 'famiglia' => 'ADA', 'nome_famiglia' => 'Ada', 'tipo' => 'XYZ'], 'riga 3: il tipo `XYZ` non è valido'],
    'two new famiglie with the same name' => [fn (): array => ['persona_id' => Persona::factory()->create()->id, 'posizione' => 'CAPO FAMIGLIA', 'famiglia' => 'ALDO E LINA', 'nome_famiglia' => 'Aldo L'], 'ha lo stesso nome_famiglia `Aldo L` di un\'altra nuova famiglia'],
    'a persona already in the existing famiglia' => [function (): array {
        $famiglia = Famiglia::factory()->create();
        $figlio = Persona::factory()->create();
        $famiglia->assegnaFiglioNato($figlio);

        return ['esito' => 'DA_AGGIUNGERE', 'persona_id' => $figlio->id, 'famiglia_id_db' => $famiglia->id, 'posizione' => 'FIGLIO NATO'];
    }, 'è già nella famiglia'],
]);

it('refuses a file without the columns of the dry run', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'ricostruzione-');
    file_put_contents($file, "persona_id,posizione\n1,CAPO FAMIGLIA\n");

    $this->artisan('famiglie:import-alfa', ['file' => $file])
        ->expectsOutputToContain('Missing columns in the CSV: esito')
        ->assertFailed();
});

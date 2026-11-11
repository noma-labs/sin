<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Anagrafe\Famiglia\Actions\BuildAlberoFamiliareAction;
use App\Anagrafe\Famiglia\DataTransferObjects\RamoFamiglia;
use App\Anagrafe\Famiglia\DataTransferObjects\RamoPersona;
use App\Anagrafe\Persona\Models\Persona;
use Database\Seeders\AlberoFamiliareSeeder;

it('seeds the famiglie of Maria Bianchi with every case of the albero familiare', function (): void {
    $this->seed(AlberoFamiliareSeeder::class);
    $maria = Persona::query()->where(['nome' => 'Maria', 'cognome' => 'Bianchi', 'data_nascita' => '1964-04-14'])->firstOrFail();
    $luca = Persona::query()->where(['nome' => 'Luca', 'cognome' => 'Esposito'])->firstOrFail();

    $albero = app(BuildAlberoFamiliareAction::class)->execute($maria);
    $ramoDi = fn (string $nome): RamoPersona => collect($albero->rami)->firstOrFail(fn (RamoPersona $ramo): bool => $ramo->persona->nome === $nome);
    [$matrimonio, $mammaDiVocazione] = $ramoDi('Maria')->famiglie;

    expect(array_map(fn ($genitore): ?int => $genitore->genitori?->count(), $albero->genitori))->toBe([2, null])
        ->and(array_map(fn (RamoPersona $ramo): string => $ramo->persona->nome, $albero->rami))->toBe(['Maria', 'Paolo', 'Lucia', 'Giovanni', 'Teresa', 'Marco'])
        ->and($matrimonio->coniuge?->nome)->toBe('Luigi')
        ->and($mammaDiVocazione->coniuge)->toBeNull()
        ->and($mammaDiVocazione->figli)->toHaveCount(70)
        ->and($ramoDi('Lucia')->famiglie[0]->posizione)->toBe('MOGLIE')
        ->and(array_map(fn (RamoFamiglia $ramo): ?string => $ramo->coniuge?->nome, $ramoDi('Giovanni')->famiglie))->toBe(['Sara', 'Carla'])
        ->and($ramoDi('Teresa')->famiglie)->toBe([])
        ->and($ramoDi('Marco')->famiglie[0]->coniuge)->toBeNull()
        ->and($mammaDiVocazione->figli->filter(fn (Persona $figlio): bool => $figlio->famiglieFormate->isNotEmpty()))->toHaveCount(20)
        ->and($mammaDiVocazione->famiglieVicine($mammaDiVocazione->figli->firstOrFail(fn (Persona $figlio): bool => $figlio->id === $luca->id))['poi']?->nome_famiglia)->toBe('Bianchi Paolo')
        ->and(app(BuildAlberoFamiliareAction::class)->execute($luca)->percorso)->toHaveCount(3);
});

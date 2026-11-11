<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Anagrafe\Famiglia\Actions\BuildAlberoFamiliareAction;
use App\Anagrafe\Famiglia\DataTransferObjects\Antenato;
use App\Anagrafe\Famiglia\DataTransferObjects\RamoFamiglia;
use App\Anagrafe\Famiglia\DataTransferObjects\RamoPersona;
use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Famiglia\Models\TipoFamiglia;
use App\Anagrafe\Persona\Models\Persona;
use Carbon\Carbon;

it('builds the albero from the famiglia d\'origine with the genitori and the fratelli in order of birth', function (): void {
    $origine = Famiglia::factory()->coniugi()->create();
    $madre = Persona::factory()->cinquantenne()->femmina()->create();
    $padre = Persona::factory()->cinquantenne()->maschio()->create();
    $origine->assegnaMoglie($madre);
    $origine->assegnaCapoFamiglia($padre);
    $persona = Persona::factory()->nato(Carbon::parse('1990-05-01'))->femmina()->create();
    $fratelloMaggiore = Persona::factory()->nato(Carbon::parse('1988-02-01'))->maschio()->create();
    $origine->assegnaFiglioNato($persona, stato: '0');
    $origine->assegnaFiglioNato($fratelloMaggiore);

    $albero = app(BuildAlberoFamiliareAction::class)->execute($persona);

    expect($albero->origine?->id)->toBe($origine->id)
        ->and(array_map(fn (Antenato $genitore): int => $genitore->persona->id, $albero->genitori))->toBe([$padre->id, $madre->id])
        ->and(array_map(fn (RamoPersona $ramo): array => [$ramo->persona->id, $ramo->posizione], $albero->rami))
        ->toBe([[$fratelloMaggiore->id, 'FIGLIO NATO'], [$persona->id, 'FIGLIO NATO']]);
});

it('draws the albero of a figlio accolto in more famiglie from the last one, with all of them in the percorso', function (): void {
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $nascita = Famiglia::factory()->coniugi()->create();
    $nascita->assegnaFiglioNato($persona, stato: '0');
    $accoglienza = Famiglia::factory()->create(['tipo' => TipoFamiglia::MammaDiVocazione]);
    $accoglienza->assegnaFiglioAccolto($persona, dataEntrata: '2020-09-01');

    $albero = app(BuildAlberoFamiliareAction::class)->execute($persona);

    expect($albero->percorso->pluck('id')->all())->toBe([$nascita->id, $accoglienza->id])
        ->and($albero->origine?->id)->toBe($accoglienza->id);
});

it('draws the albero from the famiglia d\'origine chosen in the percorso', function (): void {
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $nascita = Famiglia::factory()->coniugi()->create();
    $padre = Persona::factory()->cinquantenne()->maschio()->create();
    $nascita->assegnaCapoFamiglia($padre);
    $nascita->assegnaFiglioNato($persona, stato: '0');
    Famiglia::factory()->create(['tipo' => TipoFamiglia::MammaDiVocazione])->assegnaFiglioAccolto($persona, dataEntrata: '2020-09-01');

    $albero = app(BuildAlberoFamiliareAction::class)->execute($persona, $nascita->id);

    expect($albero->origine?->id)->toBe($nascita->id)
        ->and(array_map(fn (Antenato $genitore): int => $genitore->persona->id, $albero->genitori))->toBe([$padre->id]);
});

it('gives a figlio accolto in more famiglie the famiglia before and the one after in his percorso', function (): void {
    $mamma = Persona::factory()->cinquantenne()->femmina()->create();
    $propria = Famiglia::factory()->create(['tipo' => TipoFamiglia::MammaDiVocazione]);
    $propria->assegnaCapoFamiglia($mamma);
    $figlio = Persona::factory()->minorenne()->maschio()->create();
    $nascita = Famiglia::factory()->coniugi()->create();
    $nascita->assegnaFiglioNato($figlio, stato: '0');
    $propria->assegnaFiglioAccolto($figlio, stato: '0', dataEntrata: '2016-01-01');
    $successiva = Famiglia::factory()->coniugi()->create();
    $successiva->assegnaFiglioAccolto($figlio, dataEntrata: '2022-01-01');

    $ramoFamiglia = app(BuildAlberoFamiliareAction::class)->execute($mamma)->rami[0]->famiglie[0];
    $vicine = $ramoFamiglia->famiglieVicine($ramoFamiglia->figli->firstOrFail());

    expect($vicine['prima']?->id)->toBe($nascita->id)
        ->and($vicine['poi']?->id)->toBe($successiva->id);
});

it('gives each genitore his own genitori, or none when he has no famiglia d\'origine', function (): void {
    $origine = Famiglia::factory()->coniugi()->create();
    $padre = Persona::factory()->cinquantenne()->maschio()->create();
    $madre = Persona::factory()->cinquantenne()->femmina()->create();
    $origine->assegnaCapoFamiglia($padre);
    $origine->assegnaMoglie($madre);
    $persona = Persona::factory()->minorenne()->femmina()->create();
    $origine->assegnaFiglioNato($persona);
    $famigliaDelPadre = Famiglia::factory()->coniugi()->create();
    $nonno = Persona::factory()->diEta(80)->maschio()->create();
    $nonna = Persona::factory()->diEta(78)->femmina()->create();
    $famigliaDelPadre->assegnaMoglie($nonna);
    $famigliaDelPadre->assegnaCapoFamiglia($nonno);
    $famigliaDelPadre->assegnaFiglioNato($padre, stato: '0');

    [$latoPadre, $latoMadre] = app(BuildAlberoFamiliareAction::class)->execute($persona)->genitori;

    expect($latoPadre->genitori?->pluck('id')->all())->toBe([$nonno->id, $nonna->id])
        ->and($latoMadre->genitori)->toBeNull();
});

it('puts at the top of the albero a persona without a famiglia d\'origine, with the famiglie she formed', function (): void {
    $persona = Persona::factory()->cinquantenne()->femmina()->create();
    $propria = Famiglia::factory()->create(['tipo' => TipoFamiglia::MammaDiVocazione]);
    $propria->assegnaCapoFamiglia($persona);

    $albero = app(BuildAlberoFamiliareAction::class)->execute($persona);

    expect($albero->origine)->toBeNull()
        ->and($albero->genitori)->toBe([])
        ->and(array_map(fn (RamoPersona $ramo): array => [$ramo->persona->id, $ramo->posizione], $albero->rami))->toBe([[$persona->id, null]])
        ->and(array_map(fn (RamoFamiglia $ramo): array => [$ramo->famiglia->id, $ramo->coniuge], $albero->rami[0]->famiglie))->toBe([[$propria->id, null]]);
});

it('gives each famiglia formed by the persona the coniuge and the figli in order of birth', function (): void {
    $persona = Persona::factory()->cinquantenne()->maschio()->create();
    $moglie = Persona::factory()->cinquantenne()->femmina()->create();
    $propria = Famiglia::factory()->coniugi()->create();
    $propria->assegnaCapoFamiglia($persona);
    $propria->assegnaMoglie($moglie);
    $figliaMinore = Persona::factory()->nato(Carbon::parse('2012-01-01'))->femmina()->create();
    $propria->assegnaFiglioNato($figliaMinore);
    $figlioSposato = Persona::factory()->nato(Carbon::parse('1995-01-01'))->maschio()->create();
    $propria->assegnaFiglioNato($figlioSposato, stato: '0');
    $famigliaDelFiglio = Famiglia::factory()->coniugi()->create();
    $famigliaDelFiglio->assegnaCapoFamiglia($figlioSposato);

    $ramoFamiglia = app(BuildAlberoFamiliareAction::class)->execute($persona)->rami[0]->famiglie[0];

    expect($ramoFamiglia->famiglia->id)->toBe($propria->id)
        ->and($ramoFamiglia->posizione)->toBe('CAPO FAMIGLIA')
        ->and($ramoFamiglia->coniuge?->id)->toBe($moglie->id)
        ->and($ramoFamiglia->figli->pluck('id')->all())->toBe([$figlioSposato->id, $figliaMinore->id])
        ->and($ramoFamiglia->figli->first()?->famiglieFormate->pluck('id')->all())->toBe([$famigliaDelFiglio->id]);
});

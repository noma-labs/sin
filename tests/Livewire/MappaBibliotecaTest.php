<?php

declare(strict_types=1);

namespace Tests\Livewire;

use App\Biblioteca\Models\Classificazione;
use App\Biblioteca\Models\Libro;
use App\Biblioteca\Models\Video;
use App\Livewire\MappaBiblioteca;
use Livewire\Livewire;

it('highlights the colonne whose argomento matches the search', function (): void {
    Livewire::test(MappaBiblioteca::class)
        ->set('cerca', 'gialli')
        ->assertViewHas('evidenziate', fn (array $colonne): bool => array_intersect(['FA', 'FB', 'MC', 'MD'], $colonne) === ['FA', 'FB', 'MC', 'MD'] && ! in_array('DG', $colonne, true))
        ->assertSeeHtmlInOrder(['data-colonna="FA"', 'data-stato="evidenziata"', 'data-colonna="FB"']);
});

it('does not filter the map for a search shorter than three letters', function (): void {
    Livewire::test(MappaBiblioteca::class)
        ->set('cerca', 'ma')
        ->assertViewHas('evidenziate', null)
        ->assertSet('colonna', null);
});

it('selects the colonna of a collocazione typed in the search', function (): void {
    Livewire::test(MappaBiblioteca::class)
        ->set('cerca', 'dgb039')
        ->assertSet('colonna', 'DG')
        ->assertViewHas('evidenziate', ['DG']);
});

it('shows the books of the selected colonna for each ripiano', function (): void {
    Libro::factory()->physicalPlacement('CBA001')->create();
    Libro::factory()->physicalPlacement('CBA002')->create();
    Libro::factory()->physicalPlacement('CBB001')->create();
    Libro::factory()->physicalPlacement('CCA001')->create();

    $component = Livewire::test(MappaBiblioteca::class)
        ->call('selezionaColonna', 'CB')
        ->assertSee('3 libri');

    expect($component->instance()->statistiche['CB'])->toBe(['libri' => 3, 'ultimoNumero' => 2, 'ripiani' => ['A' => 2, 'B' => 1]]);
});

it('does not count the deleted books of a colonna', function (): void {
    Libro::factory()->physicalPlacement('LAA001')->create();
    Libro::factory()->physicalPlacement('LAA002')->create()->delete();

    $component = Livewire::test(MappaBiblioteca::class);

    expect($component->instance()->statistiche['LA']['libri'])->toBe(1);
});

it('lists the most frequent classificazioni of the selected colonna first', function (): void {
    $romanzi = Classificazione::factory()->create(['descrizione' => 'ROMANZI']);
    $poesia = Classificazione::factory()->create(['descrizione' => 'POESIA']);
    Libro::factory()->physicalPlacement('LBA001')->create(['classificazione_id' => $poesia->id]);
    Libro::factory()->physicalPlacement('LBA002')->create(['classificazione_id' => $romanzi->id]);
    Libro::factory()->physicalPlacement('LBA003')->create(['classificazione_id' => $romanzi->id]);

    Livewire::test(MappaBiblioteca::class)
        ->call('selezionaColonna', 'LB')
        ->assertViewHas('classificazioni', ['ROMANZI' => 2, 'POESIA' => 1]);
});

it('ignores a colonna that is not on the map', function (): void {
    Livewire::test(MappaBiblioteca::class)
        ->call('selezionaColonna', 'QQ')
        ->assertSet('colonna', null);
});

it('highlights the colonne of the selected area and clears the search', function (): void {
    Livewire::test(MappaBiblioteca::class)
        ->set('cerca', 'gialli')
        ->call('selezionaArea', 'scuola')
        ->assertSet('cerca', '')
        ->assertViewHas('evidenziate', fn (array $colonne): bool => in_array('SPA', $colonne, true) && ! in_array('FA', $colonne, true))
        ->assertSee('Argomento del nuovo elaborato');
});

it('suggests where to put a new elaborato of the selected argomento', function (): void {
    Libro::factory()->physicalPlacement('SPB900')->create();
    Libro::factory()->physicalPlacement('SPC800')->create();

    Livewire::test(MappaBiblioteca::class)
        ->call('selezionaArea', 'scuola')
        ->call('selezionaArgomentoElaborato', 'viaggi')
        ->assertSet('colonna', 'SPB')
        ->assertViewHas('evidenziate', ['SPB', 'SPC', 'SPN'])
        ->assertViewHas('secondarie', fn (array $colonne): bool => in_array('SPP', $colonne, true))
        ->assertSeeInOrder(['SPB901', 'SPC801']);
});

it('ignores an argomento of the elaborati when the scuola area is not selected', function (): void {
    Livewire::test(MappaBiblioteca::class)
        ->call('selezionaArgomentoElaborato', 'viaggi')
        ->assertSet('argomentoElaborato', null);
});

it('shows only the DVD sigle in the DVD box', function (): void {
    Video::query()->insert([
        ['id' => 9001, 'cassetta' => 'ZS  12'],
        ['id' => 9002, 'cassetta' => 'ZS  13'],
        ['id' => 9003, 'cassetta' => 'AM 340'],
    ]);

    Livewire::test(MappaBiblioteca::class)
        ->call('selezionaColonna', 'Z-DVD')
        ->assertViewHas('sigleVideo', ['ZS' => 2]);
});

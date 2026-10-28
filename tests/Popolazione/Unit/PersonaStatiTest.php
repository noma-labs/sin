<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Famiglia\Models\TipoFamiglia;
use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Stato;
use Carbon\Carbon;
use Exception;

it('keeps a sacerdote celibe: the stato civile is derived, not replaced by the stato', function (): void {
    $persona = Persona::factory()->nato(Carbon::parse('1950-01-01'))->maschio()->create();

    $persona->assegnaStato(Stato::perNome('sacerdote'), '1980-06-29');

    expect($persona->statiAttivi()->get())->toHaveCount(1)
        ->and($persona->statiAttivi()->first()->pivot->data_inizio)->toBe('1980-06-29')
        ->and($persona->statoCivile()['nome'])->toBe('Celibe');
});

it('refuses to assign twice the same active stato', function (): void {
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $persona->assegnaStato(Stato::perNome('sacerdote'), '1980-06-29');

    $persona->assegnaStato(Stato::perNome('sacerdote'), '1990-01-01');
})->throws(Exception::class, 'ha già lo stato SACERDOTE');

it('derives vedova from the stato vedovo when the coniuge is not in the anagrafe', function (): void {
    $vedova = Persona::factory()->maggiorenne()->femmina()->create();
    Famiglia::factory()->create()->assegnaCapoFamiglia($vedova);

    $vedova->assegnaStato(Stato::perNome('vedovo'), '1970-04-02');

    expect($vedova->statoCivile()['nome'])->toBe('Vedova')
        ->and($vedova->statoCivile()['dal']?->toDateString())->toBe('1970-04-02');
});

it('prefers the coniuge in the famiglia to the stato vedovo: a vedova who remarries is sposata', function (): void {
    $moglie = Persona::factory()->maggiorenne()->femmina()->create();
    $famiglia = Famiglia::factory()->coniugi()->create(['data_creazione' => '1990-05-12']);
    $famiglia->assegnaCapoFamiglia(Persona::factory()->maggiorenne()->maschio()->create());
    $famiglia->assegnaMoglie($moglie);

    $moglie->assegnaStato(Stato::perNome('vedovo'), '1970-04-02');

    expect($moglie->statoCivile()['nome'])->toBe('Sposata');
});

it('derives sposato from the famiglia of tipo coniugi when the coniuge is not in the anagrafe', function (): void {
    $sposato = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia = Famiglia::factory()->coniugi()->create(['data_creazione' => '1998-06-12']);
    $famiglia->assegnaCapoFamiglia($sposato);

    expect($sposato->statoCivile()['nome'])->toBe('Sposato')
        ->and($sposato->statoCivile()['dal']?->toDateString())->toBe('1998-06-12')
        ->and($sposato->matrimonioAttuale()?->id)->toBe($famiglia->id);
});

it('derives celibe for a capofamiglia alone of a famiglia without tipo coniugi', function (): void {
    $celibe = Persona::factory()->maggiorenne()->maschio()->create();
    Famiglia::factory()->create(['tipo' => TipoFamiglia::CelibeDiElezione])->assegnaCapoFamiglia($celibe);

    expect($celibe->statoCivile()['nome'])->toBe('Celibe');
});

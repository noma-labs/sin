<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Stato;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

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

it('ignores the old stati mamma still stored: they are replaced by the tipo of the famiglia', function (): void {
    $persona = Persona::factory()->maggiorenne()->femmina()->create();
    $persona->stati()->attach(Stato::find('MAV')->id, ['stato' => '1', 'data_inizio' => '1980-06-29']);
    $persona->assegnaStato(Stato::perNome('separato'), '1990-01-01');

    expect($persona->stati()->pluck('stati.stato')->all())->toBe(['SEP'])
        ->and(DB::connection('db_nomadelfia')->table('persone_stati')->where('persona_id', $persona->id)->count())->toBe(2);
});

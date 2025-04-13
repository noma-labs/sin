<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Nomadelfia\Famiglia\Models\Famiglia;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataDallaNascitaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneConFamigliaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMinorenneAccoltoAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMinorenneConFamigliaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Origine;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

it('entrata minorenne con famiglia', function (): void {
    $data_entrata = Carbon::now()->startOfDay();
    $persona = Persona::factory()->minorenne()->femmina()->create();
    $famiglia = Famiglia::factory()->create();

    $gruppo = GruppoFamiliare::first();

    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata]);
    $famiglia->assegnaCapoFamiglia($capoFam);

    $action = app(EntrataMinorenneConFamigliaAction::class);
    $action->execute($persona, $data_entrata, Famiglia::findOrFail($famiglia->id));

    $persona->refresh();

    expect($persona->origine)->toEqual(Origine::MinorenneConFamiglia->value);
    expect($persona->isPersonaInterna())->toBeTrue();
    expect($persona->getDataEntrataNomadelfia())->toEqual($data_entrata);
    //        $this->assertEquals($persona->posizioneAttuale()->id, $figlio->id);
    expect($persona->posizioneAttuale()->pivot->data_inizio)->toEqual($data_entrata->toDateString());
    //        expect($persona->statoAttuale()->id, $nubile->id);
    //        expect($persona->statoAttuale()->stato, $nubile->stato);
    expect($persona->statoAttuale()->pivot->data_inizio)->toEqual($persona->data_nascita);
    expect($persona->gruppofamiliareAttuale()->id)->toEqual($gruppo->id);
    expect($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo)->toEqual($data_entrata->toDateString());
    expect($persona->famigliaAttuale())->not->toBeNull();

});

it('entrata minorenne accolto', function (): void {
    $data_entrata = Carbon::now()->startOfDay();
    $persona = Persona::factory()->minorenne()->femmina()->create();
    $famiglia = Famiglia::factory()->create();

    $gruppo = GruppoFamiliare::first();

    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata]);
    $famiglia->assegnaCapoFamiglia($capoFam);

    $action = app(EntrataMinorenneAccoltoAction::class);
    $action->execute($persona, $data_entrata, Famiglia::findOrFail($famiglia->id));

    $persona->refresh();

    expect($persona->origine)->toEqual(Origine::Accolto->value);
    expect($persona->isPersonaInterna())->toBeTrue();
});

it('maggiorenne single', function (): void {
    $data_entrata = Carbon::now()->startOfDay();
    $persona = Persona::factory()->maggiorenne()->femmina()->create();

    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, GruppoFamiliare::first());

    $persona->refresh();

    expect($persona->origine)->toEqual(Origine::Esterno->value);
    expect($persona->isPersonaInterna())->toBeTrue();
});

it('maggiorenne con famiglia', function (): void {
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    $action = app(EntrataMaggiorenneConFamigliaAction::class);
    $action->execute($persona, Carbon::now()->startOfDay(), GruppoFamiliare::first());

    $persona->refresh();

    expect($persona->origine)->toEqual(Origine::Esterno->value);
    expect($persona->isPersonaInterna())->toBeTrue();
});

it('nato a nomadelfia', function (): void {
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    $famiglia = Famiglia::factory()->create();
    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $capoFam->gruppifamiliari()->attach(GruppoFamiliare::first()->id, ['stato' => '1', 'data_entrata_gruppo' => Carbon::now()->startOfDay()]);
    $famiglia->assegnaCapoFamiglia($capoFam);

    $action = app(EntrataDallaNascitaAction::class);
    $action->execute($persona, $famiglia);

    $persona->refresh();

    expect($persona->origine)->toEqual(Origine::Interno->value);
    expect($persona->isPersonaInterna())->toBeTrue();
});

it('does not update the origine column the second time a person enter', function (): void {
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    $famiglia = Famiglia::factory()->create();
    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $capoFam->gruppifamiliari()->attach(GruppoFamiliare::first()->id, ['stato' => '1', 'data_entrata_gruppo' => Carbon::now()->startOfDay()]);
    $famiglia->assegnaCapoFamiglia($capoFam);

    $action = app(EntrataDallaNascitaAction::class);
    $action->execute($persona, $famiglia);

    $persona->refresh();

    expect($persona->origine)->toEqual(Origine::Interno->value);

    DB::connection('db_nomadelfia')->table('popolazione')->where('persona_id', $persona->id)->update(['data_uscita' => Carbon::now()->startOfDay()]);

    // enter as maggiorenne single
    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, Carbon::now()->startOfDay(), GruppoFamiliare::first());

    $persona->refresh();

    // the "origine" column is not changed.
    expect($persona->origine)->toEqual(Origine::Interno->value);
});

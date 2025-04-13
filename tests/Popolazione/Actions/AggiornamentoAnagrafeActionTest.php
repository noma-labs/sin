<?php

declare(strict_types=1);

namespace Tests\Unit;

use Carbon\Carbon;
use App\Nomadelfia\AggiornamentoAnagrafe\Models\AggiornamentoAnagrafe;
use App\Nomadelfia\Famiglia\Models\Famiglia;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataDallaNascitaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneConFamigliaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMinorenneAccoltoAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\LogDecessoPersonaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\LogEntrataPersonaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\LogUscitaFamigliaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\LogUscitaPersonaAction;
use App\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\UscitaFamigliaData;

it('save enter event into activity table', function (): void {
    $data_entrata = Carbon::now()->startOfDay();
    $persona = Persona::factory()->minorenne()->femmina()->numeroElenco('AAA42')->luogoNascita('grosseto')->create();
    $famiglia = Famiglia::factory()->create();
    $gruppo = GruppoFamiliare::first();
    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata->toDateString()]);
    $famiglia->assegnaCapoFamiglia($capoFam);

    $action = app(LogEntrataPersonaAction::class);

    $action->execute(
        $persona,
        $data_entrata,
        $gruppo,
        $famiglia
    );

    $last = AggiornamentoAnagrafe::Enter()->get()->last();
    expect($last->subject_id)->toEqual($persona->id)
        ->and($last->subject_type)->toEqual($persona::class)
        ->and($last->properties['data_entrata'])->toEqual($data_entrata->toDateString())
        ->and($last->properties['gruppo'])->toEqual($gruppo->nome)
        ->and($last->properties['famiglia'])->toEqual($famiglia->nome_famiglia);

});

it('save uscita event into the activity table', function (): void {
    $persona = Persona::factory()->minorenne()->femmina()->numeroElenco('AAA43')->create();
    $data_entrata = Carbon::now()->startOfDay();
    $data_uscita = Carbon::now()->addYears(5)->startOfDay();

    $action = app(LogUscitaPersonaAction::class);

    $action->execute(
        $persona,
        $data_entrata,
        $data_uscita,
    );

    $last = AggiornamentoAnagrafe::Exit()->get()->last();
    expect($last->subject_id)->toEqual($persona->id)
        ->and($last->subject_type)->toEqual($persona::class)
        ->and($last->properties['data_entrata'])->toEqual($data_entrata->toDateString())
        ->and($last->properties['data_uscita'])->toEqual($data_uscita->toDateString());

});

it('save death into the activity table', function (): void {
    $persona = Persona::factory()->minorenne()->femmina()->numeroElenco('AAA46')->create();
    $data_decesso = Carbon::now()->addYears(5)->startOfDay();

    $action = app(LogDecessoPersonaAction::class);

    $action->execute($persona, $data_decesso);

    $last = AggiornamentoAnagrafe::Death()->get()->last();
    expect($last->subject_id)->toEqual($persona->id)
        ->and($last->subject_type)->toEqual($persona::class)
        ->and($last->properties['data_decesso'])->toEqual($data_decesso->toDateString());

});

it('save family exit into activity table', function (): void {

    $now = Carbon::now()->startOfDay();
    // create a family
    $gruppo = GruppoFamiliare::all()->random();
    $famiglia = Famiglia::factory()->create();
    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $moglie = Persona::factory()->maggiorenne()->femmina()->create();
    $fnato = Persona::factory()->minorenne()->femmina()->create();
    $faccolto = Persona::factory()->minorenne()->maschio()->create();
    $act = app(EntrataMaggiorenneConFamigliaAction::class);
    $act->execute($capoFam, $now, $gruppo);
    $act = app(EntrataMaggiorenneConFamigliaAction::class);
    $act->execute($moglie, $now, $gruppo);
    $famiglia->assegnaCapoFamiglia($capoFam);
    $famiglia->assegnaMoglie($moglie);
    $act = app(EntrataDallaNascitaAction::class);
    $act->execute($fnato, Famiglia::findOrFail($famiglia->id));
    $act = app(EntrataMinorenneAccoltoAction::class);
    $act->execute($faccolto, Carbon::now()->addYears(2)->startOfDay(), Famiglia::findOrFail($famiglia->id));

    $data_uscita = Carbon::now()->startOfDay();

    $action = app(LogUscitaFamigliaAction::class);

    $dto = new UscitaFamigliaData;
    $dto->famiglia = $famiglia;
    $dto->componenti = $famiglia->componentiAttuali()->get();
    $dto->data_uscita = $data_uscita;

    $action->execute($dto);

    $last = AggiornamentoAnagrafe::Exit()->get()->last();
    $lastComponente = $dto->componenti->last();
    expect($last->subject_id)->toEqual($lastComponente->id)
        ->and($last->subject_type)->toEqual($lastComponente::class)
        ->and($last->properties['data_uscita'])->toEqual($data_uscita->toDateString())
        ->and($last->properties['data_entrata'])->toEqual($lastComponente->getDataEntrataNomadelfia()->toDatestring() ?: '');

});

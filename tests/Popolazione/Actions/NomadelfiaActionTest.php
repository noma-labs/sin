<?php

namespace Tests\Unit;

use App\Mail\PersonEnteredMail;
use Carbon\Carbon;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMinorenneAccoltoAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMinorenneConFamigliaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\LogEntrataInNomadelfiaActivityAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\LogUscitaNomadelfiaAsActivityAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\SendEmailEntrataAction;
use Illuminate\Support\Facades\Mail;
use Spatie\Activitylog\Models\Activity;

it('entrata_minorenne_con_famiglia', function () {
    $data_entrata = Carbon::now()->toDatestring();
    $persona = Persona::factory()->minorenne()->femmina()->create();
    $famiglia = Famiglia::factory()->create();

    $gruppo = GruppoFamiliare::first();

    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata]);
    $famiglia->assegnaCapoFamiglia($capoFam, $data_entrata);

    $action = app(EntrataMinorenneConFamigliaAction::class);
    $action->execute($persona, $data_entrata, Famiglia::findOrFail($famiglia->id));

    expect($persona->isPersonaInterna())->toBeTrue();
    expect($persona->getDataEntrataNomadelfia())->tobe($data_entrata);
    //        $this->assertEquals($persona->posizioneAttuale()->id, $figlio->id);
    expect($persona->posizioneAttuale()->pivot->data_inizio)->tobe($data_entrata);
    //        expect($persona->statoAttuale()->id, $nubile->id);
    //        expect($persona->statoAttuale()->stato, $nubile->stato);
    expect($persona->statoAttuale()->pivot->data_inizio)->tobe($persona->data_nascita);
    expect($persona->gruppofamiliareAttuale()->id)->tobe($gruppo->id);
    expect($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo)->tobe($data_entrata);
    expect($persona->famigliaAttuale())->not->toBeNull();
    expect($persona->famigliaAttuale()->pivot->data_entrata)->tobe($persona->data_nascita);

});

it('entrata_minorenne_accolto', function () {
    $data_entrata = Carbon::now()->toDatestring();
    $persona = Persona::factory()->minorenne()->femmina()->create();
    $famiglia = Famiglia::factory()->create();

    $gruppo = GruppoFamiliare::first();

    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata]);
    $famiglia->assegnaCapoFamiglia($capoFam, $data_entrata);

    $action = app(EntrataMinorenneAccoltoAction::class);
    $action->execute($persona, $data_entrata, Famiglia::findOrFail($famiglia->id));

    expect($persona->isPersonaInterna())->toBeTrue();
});

it('save the new entrata in nomadelfia into the activity table', function () {
    $data_entrata = Carbon::now()->toDatestring();
    $persona = Persona::factory()->minorenne()->femmina()->numeroElenco('AAA42')->luogoNascita('grosseto')->create();
    $famiglia = Famiglia::factory()->create();
    $gruppo = GruppoFamiliare::first();
    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata]);
    $famiglia->assegnaCapoFamiglia($capoFam, $data_entrata);

    $action = app(LogEntrataInNomadelfiaActivityAction::class);

    $action->execute(
        $persona,
        $data_entrata,
        $gruppo,
        $famiglia
    );

    $last = Activity::all()->last();
    expect($last->event)->toBe('popolazione.entrata')
        ->and($last->log_name)->toBe('nomadelfia')
        ->and($last->subject_id)->toEqual($persona->id)
        ->and($last->subject_type)->toEqual(get_class($persona))
        ->and($last->properties['data_entrata'])->toEqual($data_entrata)
        ->and($last->properties['luogo_nascita'])->toEqual('grosseto')
        ->and($last->properties['data_nascita'])->toEqual($persona->data_nascita)
        ->and($last->properties['numero_elenco'])->toEqual($persona->numero_elenco)
        ->and($last->properties['gruppo'])->toEqual($gruppo->nome)
        ->and($last->properties['nominativo'])->toEqual($persona->nominativo)
        ->and($last->properties['famiglia'])->toEqual($famiglia->nome_famiglia);

});

it('save uscita event into the activity table', function () {
    $persona = Persona::factory()->minorenne()->femmina()->numeroElenco('AAA43')->create();
    $data_entrata = Carbon::now()->toDatestring();
    $data_uscita = Carbon::now()->addYears(5)->toDatestring();

    $action = app(LogUscitaNomadelfiaAsActivityAction::class);

    $action->execute(
        $persona,
        $data_entrata,
        $data_uscita,
    );

    $last = Activity::all()->last();
    expect($last->event)->toBe('popolazione.uscita')
        ->and($last->log_name)->toBe('nomadelfia')
        ->and($last->subject_id)->toEqual($persona->id)
        ->and($last->subject_type)->toEqual(get_class($persona))
        ->and($last->properties['data_entrata'])->toEqual($data_entrata)
        ->and($last->properties['data_nascita'])->toEqual($persona->data_nascita)
        ->and($last->properties['numero_elenco'])->toEqual($persona->numero_elenco)
        ->and($last->properties['nominativo'])->toEqual($persona->nominativo)
        ->and($last->properties['data_uscita'])->toEqual($data_uscita);

});

it('will send email with a new entered person', function () {

    Mail::fake();

    $persona = Persona::factory()->minorenne()->femmina()->numeroElenco('AAA44')->create();
    $data_entrata = Carbon::now()->toDatestring();
    $famiglia = Famiglia::factory()->create();
    $gruppo = GruppoFamiliare::first();

    $action = app(SendEmailEntrataAction::class);

    $action->execute(
        $persona,
        $data_entrata,
        $gruppo,
        $famiglia,
    );

    Mail::assertSent(PersonEnteredMail::class);

});

<?php

namespace Tests\Unit;

use App\Mail\PersonEnteredMail;
use App\Mail\PersonExitedMail;
use Carbon\Carbon;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMinorenneAccoltoAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMinorenneConFamigliaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\LogEntrataInNomadelfiaActivityAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\LogUscitaNomadelfiaAsActivityAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\SendEmailEntrataAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\SendEmailUscitaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\UscitaPersonaAction;
use Illuminate\Support\Facades\Mail;
use Spatie\Activitylog\Models\Activity;


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

<?php

namespace Tests\Unit;

use App\Mail\PersonDecessoMail;
use App\Mail\PersonEnteredMail;
use App\Mail\PersonExitedMail;
use Carbon\Carbon;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMinorenneAccoltoAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\SendEmailPersonaDecessoAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\SendEmailPersonaEntrataAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\SendEmailPersonaUscitaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\UscitaPersonaAction;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Mail::fake();
});

it('will send email if a person enter', function () {

    $persona = Persona::factory()->minorenne()->femmina()->numeroElenco('AAA44')->create();
    $data_entrata = Carbon::now()->toDatestring();
    $famiglia = Famiglia::factory()->create();
    $gruppo = GruppoFamiliare::first();

    $action = app(SendEmailPersonaEntrataAction::class);

    $action->execute(
        $persona,
        $data_entrata,
        $gruppo,
        $famiglia,
    );

    Mail::assertSent(PersonEnteredMail::class, function ($mail) {
        $to = config('aggiornamento-anagrafe.to');
        return $mail->hasTo($to);
    });

});

it('will send email if person exit', function () {

    $persona = Persona::factory()->minorenne()->femmina()->numeroElenco('AAA45')->create();
    $data_entrata = Carbon::now()->toDatestring();
    $data_uscita = Carbon::now()->toDatestring();

    $action = app(SendEmailPersonaUscitaAction::class);

    $action->execute(
        $persona,
        $data_entrata,
        $data_uscita,
    );


    Mail::assertSent(PersonExitedMail::class, function ($mail) {
        $to = config('aggiornamento-anagrafe.to');
        return $mail->hasTo($to);
    });

});


it('sends an email if a person enter', function () {
    $data_entrata = Carbon::now()->toDatestring();
    $persona = Persona::factory()->minorenne()->femmina()->create();
    $famiglia = Famiglia::factory()->create();

    $gruppo = GruppoFamiliare::first();

    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata]);
    $famiglia->assegnaCapoFamiglia($capoFam, $data_entrata);

    $action = app(EntrataMinorenneAccoltoAction::class);
    $action->execute($persona, $data_entrata, Famiglia::findOrFail($famiglia->id));

    Mail::assertSent(PersonEnteredMail::class, function ($mail) {
        $to = config('aggiornamento-anagrafe.to');
        return $mail->hasTo($to);
    });

});

it('sends an email if a person exit', function () {
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    $data_entrata = Carbon::now()->toDatestring();
    $gruppo = GruppoFamiliare::all()->random();
    $action = app(EntrataMaggiorenneSingleAction::class);

    $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));

    $data_uscita = Carbon::now()->addYears(5)->toDatestring();
    $action = app(UscitaPersonaAction::class);
    $action->execute($persona, $data_uscita);

    Mail::assertSent(PersonExitedMail::class, function ($mail) {
        $to = config('aggiornamento-anagrafe.to');
        return $mail->hasTo($to);
    });
});

it('will send email if a person die', function () {

    $persona = Persona::factory()->minorenne()->femmina()->numeroElenco('AAA55')->create();
    $data_decesso = Carbon::now()->toDatestring();

    $action = app(SendEmailPersonaDecessoAction::class);

    $action->execute(
        $persona,
        $data_decesso,
    );

    Mail::assertSent(PersonDecessoMail::class, function ($mail) {
        $to = config('aggiornamento-anagrafe.to');
        return $mail->hasTo($to);
    });

});


it('will NOT send email if person exit', function () {

    $persona = Persona::factory()->minorenne()->femmina()->numeroElenco('AAA72')->create();
    $data_entrata = Carbon::now()->toDatestring();
    $data_uscita = Carbon::now()->toDatestring();

    Config::set('aggiornamento-anagrafe.enabled', false);

    $action = app(SendEmailPersonaUscitaAction::class);

    $action->execute(
        $persona,
        $data_entrata,
        $data_uscita,
    );

    Mail::assertNotSent(PersonExitedMail::class);

});


it('will NOT send email if person enter', function () {

    Config::set('aggiornamento-anagrafe.enabled', false);

    $persona = Persona::factory()->minorenne()->femmina()->numeroElenco('AAA84')->create();
    $data_entrata = Carbon::now()->toDatestring();
    $famiglia = Famiglia::factory()->create();
    $gruppo = GruppoFamiliare::first();

    $action = app(SendEmailPersonaEntrataAction::class);

    $action->execute(
        $persona,
        $data_entrata,
        $gruppo,
        $famiglia,
    );

    Mail::assertNotSent(PersonEnteredMail::class);

});

//
//it('adds the content to persona entered email', function () {
//    $data_entrata = Carbon::now()->toDatestring();
//    $persona = Persona::factory()->minorenne()->femmina()->create();
//    $famiglia = Famiglia::factory()->create();
//    $gruppo = GruppoFamiliare::first();
//
//    $mailable = new PersonEnteredMail($persona, $data_entrata, $gruppo, $famiglia);
//    $mailable->build();
//    $mailable->assertTo('test@nomadelfia.it');
//
//    $to = config('aggiornamento-anagrafe.to');
//
//    Mail::assertSent(PersonEnteredMail::class, function ($mail) use ($to) {
//        return $mail->hasTo($to);
//    });
//});
//
//it('adds the content to persona exited email', function () {
//    $persona = Persona::factory()->minorenne()->femmina()->create();
//    $data_entrata = Carbon::now()->toDatestring();
//    $data_uscita = Carbon::now()->addYears(5)->toDatestring();
//
//    Config::set('aggiornamento-anagrafe.to', ["test@nomadelfia.it"]);
//
//    $mailable = new PersonExitedMail($persona, $data_entrata, $data_uscita);
//    $mailable->build();
//    $mailable->assertTo('test@nomadelfia.it');
//});
//
//it('adds the content to famiglia entered email', function () {
//    $persona = Persona::factory()->minorenne()->femmina()->create();
//    $data_entrata = Carbon::now()->toDatestring();
//    $data_uscita = Carbon::now()->addYears(5)->toDatestring();
//
//    $mailable = new PersonExitedMail($persona, $data_entrata, $data_uscita);
//    $mailable->build();
////    $mailable->assertTo('test@nomadelfia.it');
//});
//
//it('adds the content to persona died email', function () {
//    $persona = Persona::factory()->minorenne()->femmina()->create();
//    $data_uscita = Carbon::now()->addYears(5)->toDatestring();
//
//    $mailable = new PersonDecessoMail($persona, $data_uscita);
//    $mailable->build();
//    $mailable->assertTo('test@nomadelfia.it');
//});
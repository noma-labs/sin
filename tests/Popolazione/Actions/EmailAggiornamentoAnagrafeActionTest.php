<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Mail\PersonaDecessoMail;
use App\Mail\PersonaEntrataMail;
use App\Mail\PersonaUscitaMail;
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

beforeEach(function (): void {
    Mail::fake();
});

it('will send email if a person enter', function (): void {

    $persona = Persona::factory()->minorenne()->femmina()->numeroElenco('AAA44')->create();
    $data_entrata = Carbon::now()->toDatestring();
    $famiglia = Famiglia::factory()->create();
    $gruppo = GruppoFamiliare::first();

    Config::set('aggiornamento-anagrafe.to', 'rec1@email.com');
    Config::set('aggiornamento-anagrafe.cc', ['cc@email.com', 'cc2@com']);
    $action = app(SendEmailPersonaEntrataAction::class);

    $action->execute(
        $persona,
        $data_entrata,
        $gruppo,
        $famiglia,
    );

    Mail::assertSent(PersonaEntrataMail::class, function (PersonaEntrataMail $mail): bool {
        return $mail->hasTo('rec1@email.com') &&
            $mail->hasCc('cc@email.com') &&
            $mail->hasCC('cc2@com');
    });

});

it('will send email if person exit', function (): void {

    $persona = Persona::factory()->minorenne()->femmina()->numeroElenco('AAA45')->create();
    $data_entrata = Carbon::now()->toDatestring();
    $data_uscita = Carbon::now()->toDatestring();

    $action = app(SendEmailPersonaUscitaAction::class);

    $action->execute(
        $persona,
        $data_entrata,
        $data_uscita,
    );

    Mail::assertSent(PersonaUscitaMail::class, function ($mail) {
        $to = config('aggiornamento-anagrafe.to');

        return $mail->hasTo($to);
    });

});

it('sends an email if a person enter', function (): void {
    $data_entrata = Carbon::now()->toDatestring();
    $persona = Persona::factory()->minorenne()->femmina()->create();
    $famiglia = Famiglia::factory()->create();

    $gruppo = GruppoFamiliare::first();

    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata]);
    $famiglia->assegnaCapoFamiglia($capoFam);

    $action = app(EntrataMinorenneAccoltoAction::class);
    $action->execute($persona, $data_entrata, Famiglia::findOrFail($famiglia->id));

    Mail::assertSent(PersonaEntrataMail::class, function ($mail) {
        $to = config('aggiornamento-anagrafe.to');

        return $mail->hasTo($to);
    });

});

it('sends an email if a person exit', function (): void {
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    $data_entrata = Carbon::now()->toDatestring();
    $gruppo = GruppoFamiliare::all()->random();
    $action = app(EntrataMaggiorenneSingleAction::class);

    $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));

    $data_uscita = Carbon::now()->addYears(5)->toDatestring();
    $action = app(UscitaPersonaAction::class);
    $action->execute($persona, $data_uscita);

    Mail::assertSent(PersonaUscitaMail::class, function ($mail) {
        $to = config('aggiornamento-anagrafe.to');

        return $mail->hasTo($to);
    });
});

it('will send email if a person die', function (): void {

    $persona = Persona::factory()->minorenne()->femmina()->numeroElenco('AAA55')->create();
    $data_decesso = Carbon::now()->toDatestring();

    $action = app(SendEmailPersonaDecessoAction::class);

    $action->execute(
        $persona,
        $data_decesso,
    );

    Mail::assertSent(PersonaDecessoMail::class, function ($mail) {
        $to = config('aggiornamento-anagrafe.to');

        return $mail->hasTo($to);
    });

});

it('will NOT send email if person exit', function (): void {

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

    Mail::assertNotSent(PersonaUscitaMail::class);

});

it('will NOT send email if person enter', function (): void {

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

    Mail::assertNotSent(PersonaEntrataMail::class);

});

//
//it('adds the content to persona entered email', function () {
//    $data_entrata = Carbon::now()->toDatestring();
//    $persona = Persona::factory()->minorenne()->femmina()->create();
//    $famiglia = Famiglia::factory()->create();
//    $gruppo = GruppoFamiliare::first();
//
//    $mailable = new PersonaEntrataMail($persona, $data_entrata, $gruppo, $famiglia);
//    $mailable->build();
//    $mailable->assertTo('test@nomadelfia.it');
//
//    $to = config('aggiornamento-anagrafe.to');
//
//    Mail::assertSent(PersonaEntrataMail::class, function ($mail) use ($to) {
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
//    $mailable = new PersonaUscitaMail($persona, $data_entrata, $data_uscita);
//    $mailable->build();
//    $mailable->assertTo('test@nomadelfia.it');
//});
//
//it('adds the content to famiglia entered email', function () {
//    $persona = Persona::factory()->minorenne()->femmina()->create();
//    $data_entrata = Carbon::now()->toDatestring();
//    $data_uscita = Carbon::now()->addYears(5)->toDatestring();
//
//    $mailable = new PersonaUscitaMail($persona, $data_entrata, $data_uscita);
//    $mailable->build();
////    $mailable->assertTo('test@nomadelfia.it');
//});
//
//it('adds the content to persona died email', function () {
//    $persona = Persona::factory()->minorenne()->femmina()->create();
//    $data_uscita = Carbon::now()->addYears(5)->toDatestring();
//
//    $mailable = new PersonaDecessoMail($persona, $data_uscita);
//    $mailable->build();
//    $mailable->assertTo('test@nomadelfia.it');
//});

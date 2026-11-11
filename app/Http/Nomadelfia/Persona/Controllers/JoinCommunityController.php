<?php

declare(strict_types=1);

namespace App\Nomadelfia\Persona\Controllers;

use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataDallaNascitaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneConFamigliaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMinorenneAccoltoAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMinorenneConFamigliaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\ModificaDataEntrataAction;
use App\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\DataCollegata;
use App\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use App\Nomadelfia\PopolazioneNomadelfia\Requests\EntrataPersonaRequest;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Date;

#[Middleware('auth')]
final class JoinCommunityController
{
    #[Middleware('can:popolazione.persona.inserisci')]
    public function create($id)
    {
        $persona = Persona::findOrFail($id);

        return view('nomadelfia.persone.popolazione.create', compact('persona'));
    }

    #[Middleware('can:popolazione.persona.inserisci')]
    public function store(EntrataPersonaRequest $request, $id)
    {
        $request->validated();

        $persona = Persona::findOrFail($id);
        $data_entrata = Date::parse($request->input('data_entrata'));

        switch ($request->tipologia) {
            case 'dalla_nascita':
                $famiglia = Famiglia::findOrFail($request->input('famiglia_id'));
                $action = resolve(EntrataDallaNascitaAction::class);
                $action->execute($persona, $famiglia);
                break;
            case 'minorenne_accolto':
                $famiglia = Famiglia::findOrFail($request->input('famiglia_id'));
                $action = resolve(EntrataMinorenneAccoltoAction::class);
                $action->execute($persona, $data_entrata, $famiglia);
                break;
            case 'minorenne_famiglia':
                $famiglia = Famiglia::findOrFail($request->input('famiglia_id'));
                $action = resolve(EntrataMinorenneConFamigliaAction::class);
                $action->execute($persona, $data_entrata, $famiglia);
                break;
            case 'maggiorenne_single':
                $gruppoFamiliare = GruppoFamiliare::findOrFail($request->input('gruppo_id'));
                $action = resolve(EntrataMaggiorenneSingleAction::class);
                $action->execute($persona, $data_entrata, $gruppoFamiliare);
                break;
            case 'maggiorenne_famiglia':
                $gruppoFamiliare = GruppoFamiliare::findOrFail($request->input('gruppo_id'));
                $act = resolve(EntrataMaggiorenneConFamigliaAction::class);
                $act->execute($persona, $data_entrata, $gruppoFamiliare);
                break;
            default:
                return back()->withErrore("Tipologia di entrata per $request->tipologia non riconosciuta.");

        }

        return to_route('nomadelfia.person.profile', $persona->id)->withSuccess('Persona '.$persona->nominativo.'inserita correttamente.');
    }

    /**
     * Corrects the data di entrata of a period, of the storico or the current one, with the posizione and the gruppo familiare that started with it. The new date cannot be after the data di uscita
     * of the period, nor make the period overlap another one.
     */
    #[Middleware('can:popolazione.persona.modifica')]
    public function update(Request $request, int $id, string $entrata, ModificaDataEntrataAction $modificaDataEntrata): RedirectResponse
    {
        $persona = Persona::query()->findOrFail($id);
        $periodo = PopolazioneNomadelfia::query()->where('persona_id', $persona->id)->where('data_entrata', $entrata)->firstOrFail();

        $validated = $request->validate([
            'data_entrata' => ['bail', 'required', 'date', 'before_or_equal:'.($periodo->data_uscita ?? 'today'),
                ...($persona->data_nascita ? ['after_or_equal:'.$persona->data_nascita] : []),
                function (string $attribute, string $value, Closure $fail) use ($persona, $periodo): void {
                    $altroPeriodo = PopolazioneNomadelfia::query()
                        ->overlapping($persona->id, Date::parse($value), $periodo->data_uscita ? Date::parse($periodo->data_uscita) : null, $periodo->data_entrata)
                        ->first();
                    if ($altroPeriodo !== null) {
                        $fail($altroPeriodo->messaggioSovrapposizione());
                    }
                },
                function (string $attribute, string $value, Closure $fail) use ($persona, $periodo, $modificaDataEntrata): void {
                    $nuovaData = Date::parse($value)->toDateString();
                    $collegata = $modificaDataEntrata->collegate($persona, $periodo->data_entrata)
                        ->first(fn (DataCollegata $collegata): bool => $collegata->fine !== null && $nuovaData > $collegata->fine);
                    if ($collegata !== null) {
                        $fail("La data di entrata non può essere successiva alla fine di $collegata->tipo: $collegata->nome ($collegata->fine).");
                    }
                }],
        ], [
            'data_entrata.required' => 'La data di entrata è obbligatoria.',
            'data_entrata.date' => 'La data di entrata non è valida.',
            'data_entrata.before_or_equal' => 'La data di entrata non può essere futura o successiva alla data di uscita.',
            'data_entrata.after_or_equal' => 'La data di entrata non può essere precedente alla data di nascita.',
        ]);

        $modificaDataEntrata->execute($persona, $periodo->data_entrata, Date::parse($validated['data_entrata']));

        return back()->with('success', "Data entrata di $persona->nominativo modificata con successo.");
    }
}

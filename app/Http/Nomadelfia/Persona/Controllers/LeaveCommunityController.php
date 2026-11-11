<?php

declare(strict_types=1);

namespace App\Nomadelfia\Persona\Controllers;

use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\ModificaDataUscitaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\UscitaPersonaAction;
use App\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\DataCollegata;
use App\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Date;

#[Middleware('auth')]
final class LeaveCommunityController
{
    #[Middleware('can:popolazione.persona.inserisci')]
    public function store(Request $request, $id)
    {
        $request->validate([
            'data_uscita' => ['required'],
        ], [
            'data_uscita.required' => 'La data di uscita è obbligatoria',
        ]);
        $persona = Persona::findOrFail($id);
        if ($persona->isMoglie() or $persona->isCapofamiglia()) {
            return back()->withError("La persona $persona->nominativo non può uscire da Nomadelfia perchè risulta essere moglie o capo famiglia. Far uscire tutta la famiglia dalla pagina di gestione famiglia.");
        }
        $act = resolve(UscitaPersonaAction::class);
        $act->execute($persona, Date::parse($request->data_uscita), true);

        return to_route('nomadelfia.person.profile', $id)->withSuccess("La data di uscita di $persona->nominativo aggiornata correttamente.");

    }

    /**
     * Corrects the data di uscita of a closed period, with what the uscita concluded. The new date cannot be before the data di entrata of the period,
     * in the future, after the data di decesso, nor make the period overlap another one.
     */
    #[Middleware('can:popolazione.persona.modifica')]
    public function update(Request $request, int $idPersona, string $uscita, ModificaDataUscitaAction $modificaDataUscita): RedirectResponse
    {
        $persona = Persona::query()->findOrFail($idPersona);
        $periodo = PopolazioneNomadelfia::query()->where('persona_id', $persona->id)->where('data_uscita', $uscita)->firstOrFail();

        $validated = $request->validate([
            'data_uscita' => ['bail', 'required', 'date', 'after_or_equal:'.$periodo->data_entrata, 'before_or_equal:today',
                ...($persona->data_decesso ? ['before_or_equal:'.$persona->data_decesso] : []),
                function (string $attribute, string $value, Closure $fail) use ($persona, $periodo): void {
                    $altroPeriodo = PopolazioneNomadelfia::query()
                        ->overlapping($persona->id, Date::parse($periodo->data_entrata), Date::parse($value), $periodo->data_entrata)
                        ->first();
                    if ($altroPeriodo !== null) {
                        $fail($altroPeriodo->messaggioSovrapposizione());
                    }
                },
                function (string $attribute, string $value, Closure $fail) use ($persona, $uscita, $modificaDataUscita): void {
                    $nuovaData = Date::parse($value)->toDateString();
                    $collegata = $modificaDataUscita->collegate($persona, $uscita)
                        ->first(fn (DataCollegata $collegata): bool => $nuovaData < $collegata->inizio);
                    if ($collegata !== null) {
                        $fail("La data di uscita non può essere precedente all'inizio di $collegata->tipo: $collegata->nome ($collegata->inizio).");
                    }
                }],
        ], [
            'data_uscita.required' => 'La data di uscita è obbligatoria.',
            'data_uscita.date' => 'La data di uscita non è valida.',
            'data_uscita.after_or_equal' => 'La data di uscita non può essere precedente alla data di entrata.',
            'data_uscita.before_or_equal' => 'La data di uscita non può essere futura o successiva alla data di decesso.',
        ]);

        $modificaDataUscita->execute($persona, $uscita, Date::parse($validated['data_uscita']));

        return back()->with('success', "Data uscita di $persona->nominativo modificata con successo.");
    }
}

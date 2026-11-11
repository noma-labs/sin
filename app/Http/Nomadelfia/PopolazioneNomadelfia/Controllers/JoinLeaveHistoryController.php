<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\Controllers;

use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\ModificaDataEntrataAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\ModificaDataUscitaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Date;

#[Middleware('auth')]
final class JoinLeaveHistoryController
{
    /**
     * The timeline of the entrate and uscite of the person, from the most recent period, with what moves with each date
     * when it is corrected (shown before saving the correction).
     */
    #[Middleware('can:popolazione.persona.visualizza')]
    public function index(int $id, ModificaDataEntrataAction $modificaDataEntrata, ModificaDataUscitaAction $modificaDataUscita): View
    {
        $persona = Persona::query()->findOrFail($id);

        $periodi = PopolazioneNomadelfia::query()->where('persona_id', $id)->get()->sortByDesc('data_entrata')->values();
        $collegate = $periodi->mapWithKeys(fn (PopolazioneNomadelfia $periodo): array => [$periodo->data_entrata => [
            'entrata' => $modificaDataEntrata->collegate($persona, $periodo->data_entrata),
            'uscita' => $periodo->data_uscita === null ? collect() : $modificaDataUscita->collegate($persona, $periodo->data_uscita),
        ]]);

        return view('nomadelfia.persone.popolazione.show', ['persona' => $persona, 'periodi' => $periodi, 'collegate' => $collegate]);
    }

    /**
     * Records in the storico a past period in which the person lived in Nomadelfia, with both the data di entrata and the data di uscita.
     * Only the popolazione is updated: the posizioni, the gruppi familiari and the famiglie of the person are not changed.
     */
    #[Middleware('can:popolazione.persona.inserisci')]
    public function store(Request $request, int $id): RedirectResponse
    {
        $persona = Persona::query()->findOrFail($id);

        $validated = $request->validate([
            'data_entrata' => ['bail', 'required', 'date', ...($persona->data_nascita ? ['after_or_equal:'.$persona->data_nascita] : [])],
            'data_uscita' => ['bail', 'required', 'date', 'after_or_equal:data_entrata', 'before_or_equal:today',
                ...($persona->data_decesso ? ['before_or_equal:'.$persona->data_decesso] : []),
                $this->doesNotOverlapOtherPeriods($persona, $request->string('data_entrata')->toString())],
        ], [
            'data_entrata.required' => 'La data di entrata è obbligatoria.',
            'data_entrata.date' => 'La data di entrata non è valida.',
            'data_entrata.after_or_equal' => 'La data di entrata non può essere precedente alla data di nascita.',
            'data_uscita.required' => 'La data di uscita è obbligatoria.',
            'data_uscita.date' => 'La data di uscita non è valida.',
            'data_uscita.after_or_equal' => 'La data di uscita non può essere precedente alla data di entrata.',
            'data_uscita.before_or_equal' => 'La data di uscita non può essere futura o successiva alla data di decesso.',
        ]);

        PopolazioneNomadelfia::query()->insert([
            'persona_id' => $persona->id,
            'data_entrata' => Date::parse($validated['data_entrata'])->toDateString(),
            'data_uscita' => Date::parse($validated['data_uscita'])->toDateString(),
        ]);

        return to_route('nomadelfia.join-leave-history.index', $persona->id)
            ->with('success', "Periodo storico di $persona->nominativo inserito correttamente.");
    }

    /**
     * Deletes from the storico a closed period recorded with wrong dates. The current presence in Nomadelfia cannot be deleted:
     * the person has to leave the community instead.
     */
    #[Middleware('can:popolazione.persona.modifica')]
    public function destroy(int $id, string $entrata): RedirectResponse
    {
        $persona = Persona::query()->findOrFail($id);

        $periodo = PopolazioneNomadelfia::query()
            ->where('persona_id', $persona->id)
            ->where('data_entrata', $entrata)
            ->whereNotNull('data_uscita')
            ->firstOrFail();

        PopolazioneNomadelfia::query()
            ->where('persona_id', $persona->id)
            ->where('data_entrata', $periodo->data_entrata)
            ->delete();

        return to_route('nomadelfia.join-leave-history.index', $persona->id)
            ->with('success', "Periodo dal $periodo->data_entrata al $periodo->data_uscita di $persona->nominativo eliminato correttamente.");
    }

    /**
     * Validates that the new period does not overlap a period already in the storico, nor the current presence in Nomadelfia.
     */
    private function doesNotOverlapOtherPeriods(Persona $persona, string $dataEntrata): Closure
    {
        return function (string $attribute, string $value, Closure $fail) use ($persona, $dataEntrata): void {
            $periodo = PopolazioneNomadelfia::query()
                ->overlapping($persona->id, Date::parse($dataEntrata), Date::parse($value))
                ->first();

            if ($periodo !== null) {
                $fail($periodo->messaggioSovrapposizione());
            }
        };
    }
}

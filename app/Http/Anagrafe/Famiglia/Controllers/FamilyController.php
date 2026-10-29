<?php

declare(strict_types=1);

namespace App\Anagrafe\Famiglia\Controllers;

use App\Anagrafe\Famiglia\Actions\CreateFamigliaAction;
use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Famiglia\Models\TipoFamiglia;
use App\Anagrafe\Persona\Models\Persona;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Validation\Rule;

#[Middleware('auth')]
final class FamilyController
{
    #[Middleware('can:popolazione.persona.visualizza')]
    public function index(Request $request)
    {
        $interni = Persona::idsInterni();
        $tuttiSingle = $request->query('single') === 'tutti';

        $capifamiglieMaschio = Famiglia::onlyCapoFamiglia()->maschio();
        $capifamiglieFemmina = Famiglia::onlyCapoFamiglia()->femmina();

        $singleMaschio = Famiglia::single()->maschio()
            ->when(! $tuttiSingle, fn ($query) => $query->whereIn('persone.id', Persona::queryIdsInterni()));
        $singleFemmine = Famiglia::single()->femmina()
            ->when(! $tuttiSingle, fn ($query) => $query->whereIn('persone.id', Persona::queryIdsInterni()));

        $famigliaError = Famiglia::famigliaConErrore();

        return view('anagrafe.famiglie.index',
            compact('capifamiglieMaschio', 'capifamiglieFemmina', 'singleMaschio', 'singleFemmine', 'famigliaError', 'interni', 'tuttiSingle'));
    }

    #[Middleware('can:popolazione.persona.inserisci')]
    public function create(Request $request): View
    {
        $tipoScelto = $request->old('tipo', $request->query('tipo'));
        $tipo = (is_string($tipoScelto) ? TipoFamiglia::tryFrom($tipoScelto) : null) ?? TipoFamiglia::Coniugi;
        $persona = $request->filled('persona') ? Persona::query()->find($request->integer('persona')) : null;
        $isMoglie = $tipo === TipoFamiglia::Coniugi && $persona?->sesso === 'F';
        $capofamigliaId = $this->selectedPersone($request->old('capofamiglia') ?? ($isMoglie ? null : $persona?->id));
        $moglieId = $this->selectedPersone($request->old('moglie') ?? ($isMoglie ? $persona->id : null));
        $figliNatiId = $this->selectedPersone($request->old('figli_nati'));
        $figliAccoltiId = $this->selectedPersone($request->old('figli_accolti'));

        return view('anagrafe.famiglie.new', compact('tipo', 'capofamigliaId', 'moglieId', 'figliNatiId', 'figliAccoltiId'));
    }

    #[Middleware('can:popolazione.persona.inserisci')]
    public function store(Request $request, CreateFamigliaAction $action): RedirectResponse
    {
        $tipo = $request->enum('tipo', TipoFamiglia::class);
        $isConiugi = $tipo === TipoFamiglia::Coniugi;
        $genitori = array_values(array_map(intval(...), array_filter([$request->input('capofamiglia'), $isConiugi ? $request->input('moglie') : null], is_numeric(...))));

        $validated = $request->validate([
            'tipo' => ['required', Rule::enum(TipoFamiglia::class)],
            'nome_famiglia' => ['nullable', 'string', Rule::unique('db_nomadelfia.famiglie', 'nome_famiglia')],
            'data_creazione' => [Rule::requiredIf($isConiugi), 'nullable', 'date'],
            'capofamiglia' => ['bail', 'required', 'integer', $this->canHeadFamiglia($tipo?->sessoCapofamiglia() ?? 'M',
                $tipo?->isMamma() === true
                    ? 'La mamma deve essere una donna maggiorenne, viva e non già capofamiglia o sposata.'
                    : 'Il capofamiglia deve essere un uomo maggiorenne, vivo e non già capofamiglia o sposato.')],
            'moglie' => [Rule::excludeIf(! $isConiugi), 'bail', 'required', 'integer', $this->canHeadFamiglia('F', 'La moglie deve essere una donna maggiorenne, viva e non già sposata.')],
            'figli_nati' => ['array'],
            'figli_nati.*' => ['bail', 'integer', 'distinct', $this->canBeFiglio($genitori)],
            'figli_accolti' => ['array'],
            'figli_accolti.*' => ['bail', 'integer', 'distinct', Rule::notIn((array) $request->input('figli_nati', [])), $this->canBeFiglio($genitori)],
        ], [
            'tipo.required' => 'Il tipo della famiglia è obbligatorio.',
            'nome_famiglia.unique' => 'Esiste già una famiglia con questo nome.',
            'data_creazione.required' => 'La data del matrimonio è obbligatoria.',
            'capofamiglia.required' => 'Il capofamiglia è obbligatorio.',
            'moglie.required' => 'La moglie è obbligatoria.',
            'figli_accolti.*.not_in' => 'Un figlio non può essere sia nato che accolto.',
        ]);

        $famiglia = $action->execute(
            TipoFamiglia::from($validated['tipo']),
            $request->filled('data_creazione') ? $request->date('data_creazione') : null,
            Persona::query()->whereKey($validated['capofamiglia'])->firstOrFail(),
            $isConiugi ? Persona::query()->whereKey($validated['moglie'])->firstOrFail() : null,
            array_values(Persona::query()->findMany($validated['figli_nati'] ?? [])->all()),
            array_values(Persona::query()->findMany($validated['figli_accolti'] ?? [])->all()),
            $validated['nome_famiglia'] ?? null,
        );

        return to_route('anagrafe.families.show', $famiglia->id)->with('success', "Famiglia $famiglia->nome_famiglia creata con successo");
    }

    #[Middleware('can:popolazione.persona.visualizza')]
    public function show(Request $request, $id)
    {
        $famiglia = Famiglia::findorfail($id);
        $componenti = $famiglia->mycomponenti();
        $gruppoAttuale = $famiglia->gruppoFamiliareAttuale();
        $gruppiStorici = $famiglia->gruppiFamiliariStorico();

        return view('anagrafe.famiglie.show', compact('famiglia', 'componenti', 'gruppoAttuale', 'gruppiStorici'));
    }

    #[Middleware('can:popolazione.persona.modifica')]
    public function update(Request $request, $id)
    {
        $request->validate([
            'nome_famiglia' => ['required'],
            'tipo' => ['nullable', Rule::enum(TipoFamiglia::class)],
            'data_creazione' => ['nullable', 'date'],
        ], [
            'nome_famiglia.required' => 'Il nome della fmaiglia è obbligatorio.',
            'tipo.enum' => 'Il tipo della famiglia non esiste.',
        ]);
        $famiglia = Famiglia::findorfail($id);

        $famiglia->nome_famiglia = $request->nome_famiglia;
        $famiglia->tipo = $request->enum('tipo', TipoFamiglia::class);
        $famiglia->data_creazione = $request->data_creazione;
        $saved = $famiglia->save();
        if ($saved) {
            return redirect(route('anagrafe.families.show',
                ['id' => $id]))->withSuccess("Famiglia $famiglia->nome_famiglia aggiornata con successo");
        }

        return redirect(route('anagrafe.families.show',
            ['id' => $id]))->withErrors("Errore. Famiglia $famiglia->nome_famiglia non aggioranta");

    }

    /**
     * Returns the persone to preselect in a search-persona: the ones submitted before a validation error, or the one the famiglia starts from.
     *
     * @return list<int>
     */
    private function selectedPersone(mixed $input): array
    {
        return array_values(array_map(intval(...), array_filter((array) $input, is_numeric(...))));
    }

    /**
     * Validates that the person has the given sex, is an adult, is alive and has never been capofamiglia or moglie.
     */
    private function canHeadFamiglia(string $sesso, string $message): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) use ($sesso, $message): void {
            $persona = Persona::query()->whereKey($value)->first();

            if ($persona?->sesso !== $sesso || ! $persona->puoSposarsi()) {
                $fail($message);
            }
        };
    }

    /**
     * Validates that the figlio exists, is not one of the genitori and is not in another famiglia attuale.
     *
     * @param  list<int>  $genitori
     */
    private function canBeFiglio(array $genitori): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) use ($genitori): void {
            $figlio = Persona::query()->whereKey($value)->first();
            if ($figlio === null) {
                $fail('Il figlio non esiste in anagrafe.');

                return;
            }
            if (in_array($figlio->id, $genitori, true)) {
                $fail("$figlio->nominativo non può essere sia genitore che figlio.");

                return;
            }
            $famigliaAttuale = $figlio->famiglie()->wherePivot('stato', '1')->first();
            if ($famigliaAttuale !== null) {
                $fail("$figlio->nominativo è già nella famiglia $famigliaAttuale->nome_famiglia: toglilo prima da quella famiglia.");
            }
        };
    }
}

<?php

declare(strict_types=1);

namespace App\Anagrafe\Persona\Controllers;

use App\Anagrafe\Persona\Models\Persona;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

#[Middleware('auth')]
final class PersonController
{
    private const FILTRI = ['tutti', 'interni', 'esterni'];

    #[Middleware('can:popolazione.persona.visualizza')]
    public function index(Request $request): View
    {
        $filtro = in_array($request->query('filtro'), self::FILTRI, true) ? $request->query('filtro') : 'tutti';
        $interni = Persona::idsInterni();

        $persone = Persona::query()
            ->with('famiglieAttuali')
            ->when($filtro === 'interni', fn ($query) => $query->whereIn('id', $interni))
            ->when($filtro === 'esterni', fn ($query) => $query->whereNotIn('id', $interni))
            ->orderBy('nominativo')
            ->paginate(50);

        return view('anagrafe.persone.index', compact('persone', 'interni', 'filtro'));
    }

    #[Middleware('can:popolazione.persona.inserisci')]
    public function create()
    {
        return view('anagrafe.persone.anagrafica.create');
    }

    #[Middleware('can:popolazione.persona.inserisci')]
    public function store(Request $request)
    {
        $request->validate([
            'nominativo' => ['required'],
            'nome' => ['required'],
            'cognome' => ['required'],
            'data_nascita' => ['required', 'date'],
            'luogo_nascita' => ['required'],
            'sesso' => ['required'],
        ], [
            'nominativo.required' => 'Il nominativo è obbligatorio',
            'nominativo.unique' => 'IL nominativo inserito esiste già.',
            'nome.required' => 'Il nome è obbligatorie',
            'cognome.required' => 'Il cognome è obbligatorio',
            'data_nascita.required' => 'La data di nascita è obbligatoria',
            'luogo_nascita.required' => 'IL luogo di nascita è obbligatorio',
            'sesso.required' => 'Il sesso della persona è obbligatorio',
        ]);

        // TODO: check the UNIQUE constraint on the persone table
        $persona = Persona::create(
            [
                'nominativo' => $request->input('nominativo'),
                'sesso' => $request->input('sesso'),
                'nome' => $request->input('nome'),
                'cognome' => $request->input('cognome'),
                'provincia_nascita' => $request->input('luogo_nascita'),
                'data_nascita' => $request->input('data_nascita'),
                'id_arch_pietro' => 0,
            ]
        );
        $persona->save();

        return to_route('anagrafe.person.show', $persona->id)->withSuccess("Dati anagrafici di $persona->nominativo inseriti correttamente.");
    }

    #[Middleware('can:popolazione.persona.visualizza')]
    public function show($id)
    {
        $persona = Persona::findOrFail($id);
        $posizioneAttuale = $persona->posizioneAttuale();
        $gruppoAttuale = $persona->gruppofamiliareAttuale();
        $famigliaAttuale = $persona->famigliaAttuale();

        $famigliaEnrico = DB::connection('db_nomadelfia')
            ->table('alfa_enrico_15_feb_23')
            ->select('famiglia')
            ->where('id', $persona->id_alfa_enrico)
            ->first();

        return view('anagrafe.persone.show',
            compact('persona', 'posizioneAttuale', 'gruppoAttuale', 'famigliaAttuale', 'famigliaEnrico'));
    }
}

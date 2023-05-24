<?php

namespace App\Nomadelfia\Persona\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\UscitaPersonaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use Illuminate\Http\Request;

class SearchablePersonaController extends CoreBaseController
{
    public function index()
    {
        return view('nomadelfia.persone.search');
    }


    public function show(Request $request)
    {
        $msgSearch = ' ';
        $orderBy = 'nominativo';

        if (! $request->except(['_token'])) {
            return redirect()->route('nomadelfia.persone.ricerca')->withError('Nessun criterio di ricerca selezionato oppure invalido');
        }

        $queryLibri = Persona::sortable()->where(function ($q) use ($request, &$msgSearch, &$orderBy) {
            if ($request->nominativo) {
                $nominativo = $request->nominativo;
                $q->where('nominativo', 'like', "$nominativo%");
                $msgSearch = $msgSearch.'Nominativo='.$nominativo;
                $orderBy = 'nominativo';
            }
            if ($request->nome) {
                $nome = $request->nome;
                $q->where('nome', 'like', "$nome%");
                $msgSearch = $msgSearch.' Nome='.$nome;
                $orderBy = 'nominativo';
            }

            if ($request->filled('cognome')) {
                $cognome = $request->cognome;
                $q->where('cognome', 'like', "$cognome%");
                $msgSearch = $msgSearch.' Cognome='.$cognome;
                $orderBy = 'nome';
            }

            $criterio_nascita = $request->input('criterio_data_nascita', null);
            $nascita = $request->input('data_nascita', null);

            if ($criterio_nascita and $nascita) {
                $q->where('data_nascita', $criterio_nascita, $nascita);
                $msgSearch = $msgSearch.' Data Nascita'.$criterio_nascita.$nascita;
            }
        });
        $persone = $queryLibri->orderBy($orderBy)->paginate(50);

        return view('nomadelfia.persone.search_results', ['persone' => $persone, 'msgSearch' => $msgSearch]);
    }
}

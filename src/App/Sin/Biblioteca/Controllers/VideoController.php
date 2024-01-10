<?php

namespace App\Biblioteca\Controllers;

use App\Biblioteca\Models\Video as Video;
use App\Core\Controllers\BaseController as CoreBaseController;
use Illuminate\Http\Request;

class VideoController extends CoreBaseController
{
    public function __construct()
    {
        $this->middleware('auth')->except('search', 'searchConfirm');
    }

    public function showSearchVideoForm()
    {
        return view('biblioteca.video.search');
    }

    public function searchConfirm(Request $request)
    {
        $msgSearch = ' ';
        $orderBy = 'cassetta';

        if (! $request->except(['_token'])) {
            return redirect()->route('video')->withError('Nessun criterio di ricerca selezionato oppure invalido');
        }

        $queryVideo = Video::sortable()->where(function ($q) use ($request, &$msgSearch, &$orderBy) {
            if ($request->cassetta) {
                $q->where('cassetta', 'like', "%$request->cassetta%");
                $msgSearch = $msgSearch.'cassetta='.$request->cassetta;
                $orderBy = 'cassetta';
            }
            if ($request->data_registrazione) {
                $q->where('data_registrazione', $request->data_registrazione);
                $msgSearch = $msgSearch." data registrazione=$request->data_registrazione";
                $orderBy = 'data_registrazione';
            }
            if ($request->descrizione) {
                $q->where('descrizione', 'like', "%$request->descrizione%");
                $msgSearch = $msgSearch." descrizione=$request->descrizione";
            }
        });

        // SQL query used to add the etichette to be printed (this query is sent to the etichetteController@Add)
        $query = str_replace(['?'], ['\'%s\''], $queryVideo->toSql());
        $query = vsprintf($query, $queryVideo->getBindings());

        $videos = $queryVideo->orderBy($orderBy)->paginate(50);

        return view('biblioteca.video.search_results', ['videos' => $videos,
            'msgSearch' => $msgSearch,
            'query' => $query,
        ]);
    }
}

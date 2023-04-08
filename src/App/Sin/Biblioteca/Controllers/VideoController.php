<?php

namespace App\Biblioteca\Controllers;

use App\Biblioteca\Models\Video as Video;
use App\Core\Controllers\BaseController as CoreBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// class LibriController extends Controller{
class VideoController extends CoreBaseController
{
  // all the request for the resosurce libri must be authenticated aprt the search and serch confirm method
  public function __construct()
  {
      $this->middleware('auth')->except('search', 'searchConfirm');
  }

  public function showSearchVideoForm()
  {
    return view('biblioteca.video.search');
  }

  public function searchConfirm(Request $request) // SearchLibriRequest
  {
      $validatedData = $request->validate([
          // 'descri'=>"exists:db_biblioteca.editore,id",
          // 'xIdAutore'=>"exists:db_biblioteca.autore,id",
          // 'xClassificazione'=>"exists:db_biblioteca.classificazione,id",
          //
          // ],[
          //   'xIdEditore.exists' => 'Editore inserito non esiste.',
          //   'xIdAutore.exists' => 'Autore inserito non esiste.',
          //   'xClassificazione.exists' => 'Classificazione inserita non esiste.',

      ]);

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

          // show also the libri delted only if the authneticated user has role bibioteca
         // if(Auth::check() and Auth::user()->hasRole('biblioteca'))
         //     $videos = $queryVideo->withTrashed()->orderBy($orderBy)->paginate(50);
         // else
          $videos = $queryVideo->orderBy($orderBy)->paginate(50);

          return view('biblioteca.video.search_results', ['videos' => $videos,
              'msgSearch' => $msgSearch,
              'query' => $query,
          ]);
   }

  public function show($idVideo)
  {
    $libro = Video::findOrFail($idVideo);
    $prestitiAttivi = $libro->prestiti->where('in_prestito', 1); //Prestito::InPrestito()->where("libro",$idVideo)->get();
    if ($libro) {
    return view('biblioteca.libri.show', ['libro' => $libro, 'prestitiAttivi' => $prestitiAttivi]);
    } else {
    return redirect()->route('libri.ricerca')->withError('Il libro selezionato non esiste');
    }
  }
}

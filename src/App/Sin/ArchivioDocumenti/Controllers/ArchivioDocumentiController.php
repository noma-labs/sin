<?php

namespace App\ArchivioDocumenti\Controllers;

use App\ArchivioDocumenti\Models\ArchivioDocumento;
use App\Core\Controllers\BaseController as CoreBaseController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use SnappyPdf;

class ArchivioDocumentiController extends CoreBaseController
{
  public function index()
  {
    $libri = ArchivioDocumento::orderby('foglio')->paginate(100);

    return view('archiviodocumenti.libri.search', compact('libri'));
  }

  public function elimina()
  {
    $res = ArchivioDocumento::toBePrinted()->update(['stato' => 0]);
    if ($res) {
    return redirect()->route('archiviodocumenti.etichette')->withSuccess("Tutte le $res etichette sono state eliminate.");
    } else {
    return redirect()->route('archiviodocumenti.etichette')->withError("Errore nell'operazione");
    }
  }

  public function eliminaSingolo($id)
  {
    $libro = ArchivioDocumento::find($id);
    $res = $libro->update(['stato' => 0]);
    if ($res) {
    return redirect()->route('archiviodocumenti.etichette')->withSuccess("Libro $libro->foglio, $libro->titolo eliminato dalla stampa delle etichette");
    } else {
    return redirect()->route('archiviodocumenti.etichette')->withError("Errore nell'operazione");
    }
  }

  public function etichette()
  {
    $libriTobePrinted = ArchivioDocumento::TobePrinted()->get();

    return view('archiviodocumenti.etichette.view', compact('libriTobePrinted'));
  }

  public function esporta()
  {
    $etichette = ArchivioDocumento::TobePrinted()->get();
    // return view("biblioteca.libri.etichette.view",["libriTobePrinted"=>$libriTobePrinted]);
    $pdf = SnappyPdf::loadView('archiviodocumenti.etichette.printsingle', ['etichette' => $etichette])
        ->setOption('page-width', config('etichette.dimensioni.larghezza'))
        ->setOption('page-height', config('etichette.dimensioni.altezza'))
        ->setOption('margin-bottom', '0mm')
        ->setOption('margin-top', '0mm')
        ->setOption('margin-right', '0mm')
        ->setOption('margin-left', '0mm');
    $data = Carbon::now();

    return $pdf->setPaper('a4')->setOrientation('portrait')->download("archivio-documenti-$data.pdf");

  }

  public function aggiungi(Request $request)
  {
    $from = $request->input('fromCollocazione');
    $to = $request->input('toCollocazione', $request->input('fromCollocazione'));
    if ($request->input('action') == 'add') {
      $count = ArchivioDocumento::whereBetween('foglio', [$from, $to])->update(['stato' => 1]);

      return redirect()->route('archiviodocumenti.etichette')->withSuccess("$count etichette aggiunte alla stampa");
    } else {
      $count = ArchivioDocumento::whereBetween('foglio', [$from, $to])->update(['stato' => 0]);

      return redirect()->route('archiviodocumenti.etichette')->withSuccess("$count etichette rimosse dalla stampa");
    }
  }

  public function ricerca(Request $request)
  {
    // $validatedData = $request->validate([
    //   'collocazione'=>"exists:db_biblioteca.editore,id",
    //   'titolo'=>"exists:db_biblioteca.autore,id",
    //   'autore'=>"exists:db_biblioteca.classificazione,id",
    //   'editore'=>"exists:db_biblioteca.classificazione,id",

    //   ],[
    //     'xIdEditore.exists' => 'Editore inserito non esiste.',
    //     'xIdAutore.exists' => 'Autore inserito non esiste.',
    //     'xClassificazione.exists' => 'Classificazione inserita non esiste.',
    // ]);

      $msgSearch = ' ';
      $orderBy = 'titolo';

     if (! $request->except(['_token'])) {
      return redirect()->route('archiviodocumenti')->withError('Nessun criterio di ricerca selezionato oppure invalido');
     }

     $queryLibri = ArchivioDocumento::sortable()->where(function ($q) use ($request, &$msgSearch, &$orderBy) {
          if ($request->titolo) {
            $titolo = $request->titolo;
            $q->where('titolo', 'like', "%$titolo%");
            $msgSearch = $msgSearch.'Titolo='.$titolo;
            $orderBy = 'titolo';
          }
          if ($request->collocazione) {
            $collocazione = $request->collocazione;
            if ($collocazione == 'null') {
              $q->where('foglio', '=', '')->orWhereNull('collocazione');
              $msgSearch = $msgSearch.' Collocazione=SENZA collocazione';
            } else {
              $q->where('foglio', 'like', "%$collocazione%");
              $msgSearch = $msgSearch.' Collocazione='.$collocazione;
            }
            $orderBy = 'foglio';
          }
          if ($request->filled('editore')) {
             $ed = $request->editore;
              $msgSearch = $msgSearch." Editore= $ed";
              $q->where('editore', 'like', "%$ed%");
              $orderBy = 'editore';
          }
          if ($request->filled('autore')) {
            $au = $request->autore;
            $msgSearch = $msgSearch." Autore= $au";
            $q->where('autore', 'like', "%$au%");
            $orderBy = 'autore';
          }
        });

        $libri = $queryLibri->orderBy($orderBy)->paginate(50);

        return view('archiviodocumenti.libri.search', compact('libri', 'msgSearch'));
        // return view('biblioteca.libri.search_results',["libri"=>$libri,
        //                                                 "classificazioni"=>$classificazioni,
        //                                                 "msgSearch"=> $msgSearch,
        //                                                 "query"=>$query,
        //                                               ]);
 }
}

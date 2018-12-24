<?php
namespace App\ArchivioDocumenti\Controllers;
use App\Core\Controllers\BaseController as CoreBaseController;
use SnappyPdf;
use Carbon\Carbon;
use Illuminate\Http\Request;



use App\ArchivioDocumenti\Models\ArchivioDocumento;


class ArchivioDocumentiController extends CoreBaseController
{

  public function index(){
    return view('archiviodocumenti.index');
  }

  public function elimina(){
    $res = ArchivioDocumento::toBePrinted()->update(["tobe_printed"=>0]);
    if($res) return redirect()->route('archiviodocumenti.etichette')->withSuccess("Tutte le $res etichette sono state eliminate.");
    else  return redirect()->route('archiviodocumenti.etichette')->withError("Errore nell'operazione");
  }

  public function eliminaSingolo($id){
    $libro = ArchivioDocumento::find($id);
    $res =  $libro->update(["tobe_printed"=>0]);
    if($res) return redirect()->route('archiviodocumenti.etichette')->withSuccess("Libro $libro->collocazione, $libro->titolo eliminato dalla stampa delle etichette");
    else  return redirect()->route('archiviodocumenti.etichette')->withError("Errore nell'operazione");
  }
  
  public function etichette(){
    $libriTobePrinted = ArchivioDocumento::TobePrinted()->get();
    return view('archiviodocumenti.etichette.view', compact('libriTobePrinted'));
  }
  
  public function esporta(){
    $etichette = ArchivioDocumento::TobePrinted()->get();
    // return view("biblioteca.libri.etichette.view",["libriTobePrinted"=>$libriTobePrinted]);
    $pdf = SnappyPdf::loadView('archiviodocumenti.etichette.printsingle', ["etichette"=>$etichette])
          ->setOption('page-width', config('etichette.dimensioni.larghezza'))
          ->setOption('page-height',config('etichette.dimensioni.altezza'))
          ->setOption('margin-bottom', '0mm')
          ->setOption('margin-top', '0mm')
          ->setOption('margin-right', '0mm')
          ->setOption('margin-left', '0mm');
    $data = Carbon::now();
    return $pdf->setPaper('a4')->setOrientation('portrait')->download("archivio-documenti-$data.pdf"); 
    
  }

  public function aggiungi(Request $request){
    $from = $request->input("fromCollocazione");
    $to = $request->input("toCollocazione", $request->input("fromCollocazione"));
    if($request->input('action') == "add"){
      $count =  ArchivioDocumento::whereBetween("collocazione",[$from,$to])->update(["tobe_printed"=>1]);
      return redirect()->route("archiviodocumenti.etichette")->withSuccess("$count etichette aggiunte alla stampa");
    }
    else{
      $count =  ArchivioDocumento::whereBetween("collocazione",[$from,$to])->update(["tobe_printed"=>0]);
      return redirect()->route("archiviodocumenti.etichette")->withSuccess("$count etichette rimosse dalla stampa");
    }
  }
  
  
}

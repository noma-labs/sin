<?php
namespace App\Biblioteca\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;

use App\Biblioteca\Models\Libro as Libro;
use App\Biblioteca\Models\ViewClientiBiblioteca;

use App\Biblioteca\Models\Autore as Autore;
use App\Biblioteca\Models\Editore as Editore;
use App\Biblioteca\Models\Prestito as Prestito;
use App\Biblioteca\Models\Classificazione as Classificazione;
use App\Biblioteca\Models\ViewCollocazione as ViewCollocazione;

use App\Admin\Models\Ruolo;

use App\Core\Controllers\BaseController as CoreBaseController;

class LibriController extends CoreBaseController{
  // all the request for the resosurce libri must be authenticated excpet for searching
  public function __construct(){
      $this->middleware('auth')->except('showSearchLibriForm','searchConfirm');
  }

  public function showEditCollocazioneForm($idLibro){
    $libro = Libro::findOrFail($idLibro);
    return view("biblioteca.libri.collocazione",compact('libro'));
  }

  public function updateCollocazione(Request $request, $idLibro){
    // return $request->all();
    $validatedData = $request->validate([
      'xCollocazione'=>"required", //per update solito nome
      ],[
        'xCollocazione.required' => 'La collocazione nuova non è stata selezionata.',
    ]);

    $libro = Libro::findOrFail($idLibro);
    $collocazione = $libro->collocazione;
    $xCollocazioneNuova = $request->xCollocazione;
    if($xCollocazioneNuova != "null")
    {
      $libroTarget = Libro::where("collocazione",$xCollocazioneNuova)->first();
      if($libroTarget)
          return view("biblioteca.libri.collocazione_confirm",compact('libro','libroTarget'))->withWarning("Stai cambiando la collocazione");
      else
      {
        $libro->collocazione = $xCollocazioneNuova;
        $res = $libro->save();
        return redirect()->route('libro.dettaglio',['idLibro'=>$libro->id])->withSuccess("La collocazione $collocazione è stata sostituita in $libro->collocazione con successo.");
      }
    }
    else
    {
      $libro->collocazione = null;
      $res = $libro->save();
      return redirect()->route('libro.dettaglio',['idLibro'=>$libro->id])->withSuccess("La collocazione $collocazione è stata sostituita con SENZA COLLOCAZIONE con successo.");
    }

  }

  public function confirmCollocazione(Request $request, $idLibro){

    $validatedData = $request->validate([
      'idTarget'=>"required", //per update solito nome
      ],[
        'idTarget.required' => 'IL libro a cui prelevare la collocazione è obbligatorio.',
    ]);

    $libro = Libro::findOrFail($idLibro);
    $libroTarget = Libro::findOrFail($request->idTarget);

    $collocazione = $libro->collocazione;
    $libro->collocazione = $libroTarget->collocazione;
    $libroTarget->collocazione= $collocazione;
    $libro->save();
    $libroTarget->save();
    return redirect()->route('libro.dettaglio',['idLibro'=>$libro->id])
    ->withSuccess("Il libro $libro->titolo assegnato la collocazione $libro->collocazione, $libroTarget->titolo è stata sostituita in $libroTarget->collocazione con successo.");
  }

  public function showSearchLibriForm(){
    $classificazioni = Classificazione::orderBy('descrizione', 'ASC')->get();
    $CollocazioneLettere= ViewCollocazione::lettere()->get();
    return view('biblioteca.libri.search',["classificazioni"=>$classificazioni,
                                            "lettere"=>$CollocazioneLettere ]);
  }


  public function searchConfirm(Request $request)
    {
      $validatedData = $request->validate([
        'xIdEditore'=>"exists:db_biblioteca.editore,id",
        'xIdAutore'=>"exists:db_biblioteca.autore,id",
        'xClassificazione'=>"exists:db_biblioteca.classificazione,id",

        ],[
          'xIdEditore.exists' => 'Editore inserito non esiste.',
          'xIdAutore.exists' => 'Autore inserito non esiste.',
          'xClassificazione.exists' => 'Classificazione inserita non esiste.',
      ]);

        $msgSearch = " ";
        $orderBy = "titolo";

       if(!$request->except(['_token']))
        return redirect()->route('libri.ricerca')->withError('Nessun criterio di ricerca selezionato oppure invalido');

       if( $request->filled('xIdEditore')){
           $editore = Editore::findOrFail($request->xIdEditore);
           $msgSearch= $msgSearch." Editore= $editore->Editore;";
           $idEditore = $request->xIdEditore;
           $queryLibri =  Editore::find($editore->id)->libri();
           $orderBy = "collocazione";
       }
       $queryLibri = Libro::sortable()->where(function($q) use ($request, &$msgSearch, &$orderBy){
            if($request->xTitolo){
              $titolo = $request->xTitolo;
              $q->where('titolo', 'like', "%$titolo%");
              $msgSearch= $msgSearch."Titolo=".$titolo;
              $orderBy = "titolo";
            }
            if($request->xCollocazione){
              $collocazione = $request->xCollocazione;
              if($collocazione == "null"){
                $q->where('collocazione', '=', '')->orWhereNull("collocazione");
                $msgSearch= $msgSearch." Collocazione=SENZA collocazione";
              }
              else{
                $q->where('collocazione', 'like', "%$collocazione%");
                $msgSearch= $msgSearch." Collocazione=".$collocazione;
              }
              $orderBy = "collocazione";
            }
            if( $request->filled('xIdEditore')){
                $editore = Editore::findOrFail($request->xIdEditore);
                $msgSearch= $msgSearch." Editore= $editore->Editore;";
                $idEditore = $request->xIdEditore;
                $q->where('ID_EDITORE', $idEditore);
                $orderBy = "collocazione";
            }
            if($request->filled('xIdAutore')){
              $idAutore = $request->xIdAutore;
              $q->where('ID_AUTORE', $idAutore);
              $autore = Autore::findOrFail($idAutore)->Autore;
              $msgSearch= $msgSearch." Autore=$autore";
            }
            if($request->filled('xClassificazione')){
               $classificazione= (int)$request->xClassificazione;
               if($classificazione == 0) // NON CLASSIFICATO
               {
                 $q->where('classificazione_id',  "$classificazione")->orWhereNull('classificazione_id');
               } else{
                 $q->where('classificazione_id',  "$classificazione");
               }
              $class = Classificazione::findOrFail($classificazione)->descrizione;
              $msgSearch= $msgSearch." Classificazione=".$class;
            }
            if($request->xNote){
              $note = $request->xNote;
              $q->where('note', 'like', "%$note%");
              $msgSearch= $msgSearch." Note=".$note;
            }
          });

          // SQL query used to add the etichette to be printed (this query is sent to the etichetteController@Add)
          $query = str_replace(array('?'), array('\'%s\''), $queryLibri->toSql());
          $query = vsprintf($query, $queryLibri->getBindings());

          // show also the libri delted only if the authneticated user has role bibioteca
         if(Auth::check() and Auth::user()->hasRole('biblioteca'))
             $libri = $queryLibri->withTrashed()->orderBy($orderBy)->paginate(50);
         else
            $libri = $queryLibri->orderBy($orderBy)->paginate(50);

          $classificazioni = Classificazione::orderBy('descrizione', 'ASC')->get();

          return view('biblioteca.libri.search_results',["libri"=>$libri,
                                                          "classificazioni"=>$classificazioni,
                                                          "msgSearch"=> $msgSearch,
                                                          "query"=>$query,
                                                        ]);
   }

  public function show($idLibro){
    $libro = Libro::withTrashed()->find($idLibro);
    $prestitiAttivi =  $libro->prestiti->where("in_prestito",1);//Prestito::InPrestito()->where("libro",$idLibro)->get();
    if ($libro) return view("biblioteca.libri.show",["libro"=>$libro, "prestitiAttivi"=>$prestitiAttivi]);
    else  return redirect()->route("libri.ricerca")->withError("Il libro selezionato non esiste");
  }

  public function delete($idLibro){
    $libro = Libro::findOrFail($idLibro);
    return  view("biblioteca.libri.delete",["libro"=>$libro]);
  }

  public function restore($idLibro){
    $libro = Libro::withTrashed()->findOrFail($idLibro);
    $libro->restore();
    return redirect()->route('libro.dettaglio',["libro"=>$libro])->withSuccess("Il libro è stato ripristinato con successo");
  }


  public function deleteConfirm(Request $request, $idLibro){
    $validatedData = $request->validate([
      'xCancellazioneNote'=>"required", //per update solito nome
      ],[
        'xCancellazioneNote.required' => 'La motivazione della cancellazione del libro è obbligatoria.',
    ]);
    $libro = Libro::findOrFail($idLibro);
    if($libro->inPrestito())
      return  redirect()->route("libri.ricerca")
                        ->withError("Impossibilie eliminare il libro. Il libro è in prestito.");

    $libro->deleted_note =  $request->xCancellazioneNote;
    $libro->delete();
    return redirect()->route("libri.ricerca")->withSuccess("Il libro è stato eliminato con successo.");
  }

  public function showDeleted(){
    $libriEliminati = Libro::onlyTrashed()->paginate(50);
    // dd($libriEliminati);
    return view("biblioteca.libri.deleted",compact('libriEliminati'));
  }

  public function edit($idLibro){
    $classificazioni = Classificazione::orderBy('descrizione', 'ASC')->get();
    $libro = Libro::findOrFail($idLibro);
    return view("biblioteca.libri.edit",["libro"=>$libro, "classificazioni"=>$classificazioni]);

  }

  public function editConfirm(Request $request, $idLibro){
    $validatedData = $request->validate([
      'xTitolo' => 'required',
      // 'xCollocazione'=>"required|unique:db_biblioteca.libro,collocazione,".$idLibro.",ID_libro", //per update solito nome
      'xIdEditore'=>"exists:db_biblioteca.editore,id",
      'xIdAutore'=>"exists:db_biblioteca.autore,id",
      'xClassificazione' => 'required|exists:db_biblioteca.classificazione,id'
      ],[
        // 'xCollocazione.required' => 'La collocazione è obbligatoria.',
          'xTitolo.required' => 'Il titolo del libro è obbligatorio.',
          'xIdEditore.exists' => 'Editore inserito non è valido.',
          // 'xIdAutore.exists' => 'Autore inserito non è valido.',
          'xIdEditore.required' => "L'editore  è obbligatorio",
          'xIdAutore.required' => "L'autore è abbligatorio.",
          'xClassificazione.exists' => 'La classificazione inserita non è valida.',
          'xClassificazione.required' => 'La classificazione è obbligatoria',
          'xCollocazione.unique'=>"La collocazione inserita esiste già."
    ]);


    $libro =  Libro::findOrFail($idLibro);

    $libro->titolo = $request->xTitolo;
    $libro->ID_EDITORE = $request->input('xIdEditore',0);
    $libro->ID_AUTORE = $request->input('xIdAutore',0);
    $libro->classificazione_id = $request->xClassificazione;
    $libro->note = $request->xNote;

    $libro->fill($request->only('isbn','data_pubblicazione','categoria','dimensione','critica'));

    $res = $libro->save();

    $integerIDs = json_decode('[' . $request->xIdAutori . ']', true); // list of idAutore (e.g., 26,275,292)
    $libro->autori()->sync($integerIDs);

    $editoriIDs = json_decode('[' . $request->xIdEditori . ']', true); // list of idAutore (e.g., 26,275,292)
    $libro->editori()->sync($editoriIDs);


    if($res) return redirect()->route('libro.dettaglio',["libro"=>$idLibro])->withSuccess("Libro modificato correttamente");
    else   return redirect()->route('libro.dettaglio',["libro"=>$idLibro])->withWarning("Nessuna modifica effettuata");

  }

  public function book($idLibro){
    // Prenotazione di un libro
    $libro = Libro::findOrFail($idLibro);
    $utenti =  ViewClientiBiblioteca::orderBy("nominativo",'ASC')->get();
    return view("biblioteca.libri.book",["libro"=>$libro,"utenti"=>$utenti]);

  }

  public function bookConfirm(Request $request, $idLibro){
    $validatedData = $request->validate([
      'xDatainizio'=>'date',
      'persona_id' => 'required',
      // 'xIdBibliotecario'=> 'exists:db_ayth.cliente,id'
      ],[
      'xDatainizio.date' => 'La data di inizio prestito deve essere una data valida YYYY-MM-GG',
      'persona_id.exists' => "Il cliente selezionanto non esiste",
      'persona_id.required' => "Nessun cliente selezionato"
    ]);


    $libro = Libro::findOrFail($idLibro);
    // Receive the data for the book with POST operation, and  redirect to the libro dettaglio
    $datainizio= $request->xDatainizio;
    $datafine= $request->xDataFine;
    $note = $request->input('xNote',null);
    $idUtente= $request->persona_id;

   // biblitoecario is the id of the Person associated with the logged user
    $idBibliotecario=  Auth::user()->persona->id;

    if($libro->inPrestito()){
      return redirect()->back()->withError("Impossibile prenotare il libro, il  Libro è già in prestito");
    }
    else{
      $prestito = Prestito::create(['bibliotecario_id'=>$idBibliotecario, 'libro_id'=>$idLibro,'data_inizio_prestito'=>$datainizio, 'data_fine_prestito'=>$datafine, 'in_prestito'=>1, 'note'=>$note]);
      $persona = ViewClientiBiblioteca::findOrFail($idUtente);
      $prestito->cliente()->associate($persona)->save();
      if($prestito)
      return redirect()->route('libri.prestiti')->withSuccess( "Prestitio andato a buon fine Libro: ". $prestito->libro->titolo.", Cliente:". $prestito->cliente->nominativo.", Bibliotecario:". $prestito->bibliotecario->username);
      else redirect()->route('libri.prestiti')->withWarning('Errore nel prestito' );
    }

  }

  public function showInsertLibroForm(Request  $request){
      $classificazioni =  Classificazione::orderBy("descrizione")->get();
      // flash the url of the insert libro in order to come back after new editore or autore is inserted
      Session::put('insertLibroUrl', $request->fullUrl());
      return view('biblioteca.libri.insert',compact('classificazioni'));//,$editori,$autori);
  }

  public function insertConfirm(Request $request){
    $validatedData = $request->validate([
      'xTitolo' => 'required',
       'xIdAutori' => 'required',
       'xIdEditori' => 'required',
      'xCollocazione'=>"required|unique:db_biblioteca.libro,collocazione",
      'xClassificazione' => 'required|exists:db_biblioteca.classificazione,id'
      ],[
        'xTitolo.required' => 'Il titolo del libro è obbligatorio.',
        'xIdAutori.required' => "L'Autore del libro è obbligatorio.",
        'xIdEditori.required' => "L'Editore del libro è obbligatorio.",
        'xClassificazione.exists' => 'La classificazione inserita non è valida.',
        'xClassificazione.required' => 'La classificazione è obbligatoria.',
        'xCollocazione.unique'=>"La collocazione inserita esiste già.",
        'xCollocazione.required'=>"La collocazione inserita non corretta."
    ]);

    $_addanother= $request->input('_addanother');  // save and add another libro
    $_addonly   = $request->input('_addonly');     // save only

    $tobe_printed= $request->input('xTobePrinted'); //checkbox add to print checked when  was inserted

    if($request->xCollocazione == "null") $collocazione= null;
    else $collocazione= $request->xCollocazione;

    $libro = new Libro;
    $libro->titolo = $request->xTitolo;
    $libro->collocazione = $collocazione;
    $libro->classificazione_id = $request->xClassificazione;
    $libro->note = $request->xNote;
    $libro->ID_AUTORE = $request->input('xIdAutore',0); // if not set put to SENZA AUTORE
    $libro->ID_EDITORE = $request->input('xIdEditore',0); // if not set put to SENZA EDITORE

    $libro->fill($request->only('isbn','data_pubblicazione','categoria','dimensione','critica'));
    if($tobe_printed)
      $libro->tobe_printed = 1;
    $res  = $libro->save();

    $idsAutori = json_decode('[' . $request->xIdAutori . ']', true); // receive a list of idAutori (e.g., 26,275,292)
    $libro->autori()->sync($idsAutori);

    $idsEditori = json_decode('[' . $request->xIdEditori . ']', true); // receive a list of idEditori (e.g., 26,275,292)
    $libro->editori()->sync($idsEditori);
    if ($res)
    {if($_addanother)
        return  redirect()->route('libri.inserisci')->withSuccess("Libro inserito correttamente.");//"\n Titolo: $libro->titolo, Collocazione:$libro->collocazione, Editore: $libro->editore->Editore, Autore: $libro->autore->Autore, Classificazione:".$libro->classificazione->descrizione." Note: $libro->note");
      if($_addonly)
        return redirect()->route('libro.dettaglio', [$libro->id])->withSuccess("Libro inserito correttamente.");//" \nTitolo: $libro->titolo, Collocazione:$libro->collocazione, Editore: $libro->editore->Editore, Autore: $libro->autore->Autore, Classificazione:".$libro->classificazione->descrizione." Note: $libro->note");
    }else{
      return redirect()->route('libri.inserisci')->withError("Errore nella creazione del libro.");
    }
   }


}

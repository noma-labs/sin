<?php

namespace App\Biblioteca\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use App\Admin\Models\Ruolo;

use Illuminate\Support\Facades\Session;

use App\Admin\Models\User as User;
use App\Biblioteca\Models\ViewLavoratoriBiblioteca;
use App\Biblioteca\Models\ViewClientiBiblioteca;
use App\Biblioteca\Models\Libro as Libro;
use App\Biblioteca\Models\Prestito as Prestito;

use App\Core\Controllers\BaseController as CoreBaseController;

class LibriPrestitiController extends CoreBaseController
{
  public function __construct()
  {
      $this->middleware('auth'); 
  }

  public function conclude(Request $request, $idPrestito){
    $prestito = Prestito::findOrFail($idPrestito);
    $_concludi = $request->_concludi; // "concludi" value is sent when the button Concluid prestito is clicked

    // $bibliotecario = Auth::user()->id; //$request->xIdBibliotecario;
    $bibliotecario = Auth::user()->persona->id;
    if($_concludi){
      $data = Carbon::now()->toDateString();
      $res = $prestito->update(["in_prestito"=>0, "data_fine_prestito" => $data,"bibliotecario_id"=>$bibliotecario]);
      if($res)
        return ($url = Session::get('clientePrestitiUrl'))
                ? redirect()->to($url)->withSuccess("Prestito terminato correttamente in data $data")
                : redirect()->route('libri.prestiti')->withSuccess("Prestito terminato correttamente in data $data");
            // return redirect()->back()->withSuccess("Prestito terminato correttamente in data $data");
            // return redirect()->back()->getTargetUrl();
      else
        return redirect()->route('libri.prestiti')->withError("Errore nella richiesta. Nessuna modifica effettuata");
    }

  }
  public function view(){
    $prestiti = Prestito::leftJoin('v_clienti_biblioteca', 'prestito.cliente_id', '=', 'v_clienti_biblioteca.id')
            ->inPrestito()
            ->select('prestito.*')
            ->orderBy("data_inizio_prestito", "desc")
            ->orderBy("nominativo", "asc")
            ->get();
    $utenti =  ViewClientiBiblioteca::orderBy("nominativo")->get();
    // $bibliotecari = Ruolo::findByName("biblioteca-amm")->utenti()->get();
    $bibliotecari = ViewLavoratoriBiblioteca::orderby('nominativo')->get();
    return view('biblioteca.libri.prestiti.view',["prestiti"=>$prestiti,
                                                  "utenti"=>$utenti,
                                                  "bibliotecari"=>$bibliotecari,
                                                  "msgSearch"=> "Tutti e prestiti attivi",
                                                  "query"=>""]);
  }

  public function search(Request  $request){
    $msgSearch = " ";
    // se sto cecando il prestito di una persona  redirect sul dettaglio della persona.
    if($request->has('persona_id') and !$request->has('note')){
      Session::flash('clientePrestitiUrl', $request->fullUrl());
      $cliente = ViewClientiBiblioteca::findOrFail($request->input("persona_id"));
      $prestitiAttivi = $cliente->prestiti()->where("in_prestito",1)->orderBy('data_inizio_prestito')->get(); //Prestito::InPrestito()->where(["CLIENTE"=>$idCliente])->get();
      $prestitiRestituiti = $cliente->prestiti()->where("in_prestito",0)->orderBy('data_fine_prestito')->get(); //Prestito::Restituiti()->where(["CLIENTE"=>$idCliente])->get();
      return view("biblioteca.libri.prestiti.cliente",compact('cliente','prestitiAttivi','prestitiRestituiti'));
    }

    $queryPrestiti = Prestito::where(function($q) use ($request,&$msgSearch){
        if($request->has('collocazione')){
          $collocazione = $request->collocazione;
          $idLibri = Libro::where("collocazione","like","$collocazione%")->pluck("id")->toArray();
          $q->whereIn('libro_id', $idLibri);
          // $q->where('libro_id', $libro->id);
          $msgSearch= $msgSearch."Collocazione=".$collocazione;
        }
        if($request->has('note')){
          $note = $request->note;
          $q->where('note', "like", "%$note%");
          $msgSearch= $msgSearch." Note=".$note;
        }
        if($request->has('titolo')){
          $idLibri = Libro::withTrashed()->where("titolo",$request->titolo)->pluck("id")->toArray();
          $q->whereIn('libro_id', $idLibri);
          $msgSearch= $msgSearch." Titolo = $request->titolo";
        }
        if($request->has('persona_id')){
          $utente = $request->input("persona_id");
          $nomeUtente = ViewClientiBiblioteca::findOrFail($utente)->nominativo;
          $q->where('cliente_id', $utente);
          $msgSearch= $msgSearch." Cliente=".$nomeUtente;
        }
        if($request->xSegnoInizioPrestito){
            $inizioPrestito = $request->xInizioPrestito;
            $segnoPrestito = $request->xSegnoInizioPrestito;
            $q->where('data_inizio_prestito', $segnoPrestito, $inizioPrestito);
            $msgSearch= $msgSearch." Data Prestito $segnoPrestito $inizioPrestito";
        }
        if($request->xSegnoFinePrestito){
           $fineprestito= $request->xFinePrestito;
           $segnoFinePrestito =  $request->xSegnoFinePrestito;
          $q->where('data_fine_prestito', $segnoFinePrestito ,$fineprestito);
          $msgSearch= $msgSearch." Data restituzione $segnoFinePrestito $fineprestito";
        }
        if($request->xIdBibliotecario){
          $idBibliotecario = $request->xIdBibliotecario;
          $q->where('bibliotecario_id', $idBibliotecario);
          $bibliotecario = ViewLavoratoriBiblioteca::findOrFail($idBibliotecario)->nominativo;
          $msgSearch= $msgSearch." Bibliotecario: $bibliotecario ";
        }
      });

   $prestiti = $queryPrestiti->orderBy("data_inizio_prestito","DESC")->paginate(50);
   $query = $queryPrestiti->toSql();
  //  $bibliotecari = Ruolo::findByName("biblioteca-amm")->utenti()->get();
   $bibliotecari = ViewLavoratoriBiblioteca::orderby('nominativo')->get();

   return view('biblioteca.libri.prestiti.view',["prestiti"=>$prestiti,
                                                 "bibliotecari"=>$bibliotecari,
                                                  "msgSearch"=> $msgSearch,
                                                  "query"=>$query]);
  }

  public function show($idPrestito){
    if (Session::has('clientePrestitiUrl')) { // contains the url of the detail of the utente
        Session::keep('clientePrestitiUrl');
    }
    $prestito = Prestito::findOrFail($idPrestito);
    return view("biblioteca.libri.prestiti.show",["prestito" =>$prestito]);
  }

  public function edit($idPrestito){
    $prestito = Prestito::findOrFail($idPrestito);
    $utenti =  ViewClientiBiblioteca::orderBy("nominativo")->get();
    return view("biblioteca.libri.prestiti.edit",["prestito"=>$prestito,
                                                  "utenti"=>$utenti]);
  }


 public function editConfirm(Request $request, $idPrestito){
   $validatedData = $request->validate([
      "xDataRestituzione"=> "sometimes|nullable|date|after_or_equal:xDataPrenotazione"
     ],[
       'xDataRestituzione.after_or_equal' => 'La data di restituzione prestito deve essere maggiore o uguale alla data di inizio prestito',
   ]);

    $prestito = Prestito::findOrFail($idPrestito);

    $dataprenotazione= $request->xDataPrenotazione;
    $datarestituzione= $request->xDataRestituzione;
    $note = $request->input('xNote',null);

    // $bibliotecario = Auth::user()->id; //$request->xIdBibliotecario;
    $bibliotecario = Auth::user()->persona->id; //$request->xIdBibliotecario;

    // salva modifiche button has been clicked
    $persona = ViewClientiBiblioteca::findOrFail($request->persona_id);
    $prestito = Prestito::findOrFail($idPrestito);
    $prestito->update(["bibliotecario_id"=>$bibliotecario, "data_fine_prestito"=>$datarestituzione, "data_inizio_prestito" => $dataprenotazione, "note"=>$note]);
    $prestito->cliente()->associate($persona)->save();
    if($prestito) return redirect()->route('libri.prestiti')->withSuccess("Prestito modificato correttamente");
    else   return redirect()->route('libri.prestiti')->withWarning("Nessuna modifica effettuata");

  }
}

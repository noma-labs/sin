<?php
namespace App\Officina\Controllers;

use Illuminate\Http\Request;
use App\Core\Controllers\BaseController as CoreBaseController;

use App\Officina\Models\ViewClienti as ViewClienti;
use App\Officina\Models\Uso as Uso;
use App\Officina\Models\Impiego as Impiego;
use App\Officina\Models\Veicolo;
use App\Officina\Models\Prenotazioni;
use App\Officina\Models\ViewMeccanici;

use Carbon\Carbon;
use Validator;


class PrenotazioniController extends CoreBaseController
{


  public function __construct(){
      $this->middleware('auth');
  }

  public function searchView(){
    $usi = Uso::all();
    return view("officina.prenotazioni.search",compact('usi'));
  }

  public function search(Request $request){
      // return $request->all();
      $msgSearch = " ";
      // $orderBy = "titolo";

     if(!$request->except(['_token'])){
        return Redirect::back()->withError('Nessun criterio di ricerca selezionato oppure invalido');
     }

     $queryPrenotazioni = Prenotazioni::where(function($q) use ($request, &$msgSearch, &$orderBy){
         if ($request->filled('cliente_id')) {
           $cliente = ViewClienti::findOrFail($request->input("cliente_id"));
           $q->where('cliente_id', $cliente->id);
           $msgSearch= $msgSearch." Cliente=".$cliente->nominativo;
           // $orderBy = "titolo";
        }
        if ($request->filled('veicolo_id')) {
          $veicolo = Veicolo::findOrFail($request->input("veicolo_id"));
          $q->where('veicolo_id', $veicolo->id);
          $msgSearch= $msgSearch." Veicolo=".$veicolo->nome;
          $orderBy = "titolo";
       }
       if ($request->filled('meccanico_id')) {
         $meccanico = ViewMeccanici::findorFail($request->input("meccanico_id"));
         $q->where('meccanico_id', $meccanico->persona_id);
         $msgSearch= $msgSearch." Meccanico=".$meccanico->nominativo;
         $orderBy = "titolo";
      }

       if ($request->filled('uso_id')) {
         $uso = Uso::findOrFail($request->input('uso_id'));
         $q->where('uso_id', $uso->ofus_iden);
         $msgSearch= $msgSearch." Uso=".$uso->ofus_nome;
         // $orderBy = "titolo";
      }
      $cdp = $request->input('criterio_data_partenza',null);
      $cda = $request->input('criterio_data_arrivo',null);
      $dp  = $request->input('data_partenza',null);
      $da  = $request->input('data_arrivo',null);
      if($cdp and $dp and $cda and $da){ // ricerca con entrambi data di partenza e data di arrivo inserite
      //   $q->where('data_partenza', $cdp, $dp);
      //   $q->Where('data_arrivo', $cda, $da);
       $q->whereBetween('data_partenza', array($dp, $da));
       $q->orwhereBetween('data_arrivo', array($dp, $da));

      //   $msgSearch= $msgSearch." Data Partenza $cdp $dp oppure Data arrivo $cda $da";
      }
      else{
        if ($cdp and $dp) {
          $q->where('data_partenza', $cdp, $dp);
          $msgSearch= $msgSearch." Data Partenza".$cdp.$dp;
        }
        if ($cda and $da) {
          $q->where('data_arrivo', $cda, $da);
          $msgSearch= $msgSearch." Data Partenza".$cda.$da;
        }
      }
     
      // if ($request->filled('criterio_data_partenza') and $request->filled('data_partenza') ) {
      //   $q->where('data_partenza', $request->input('criterio_data_partenza'), $request->input('data_partenza'));
      //   $msgSearch= $msgSearch." Data Partenza".$request->input('criterio_data_partenza').$request->input('data_partenza');
      // }
      // if ($request->filled('criterio_data_arrivo') and $request->filled('data_arrivo') ) {
      //   $q->where('data_arrivo', $request->input('criterio_data_arrivo'), $request->input('data_arrivo'));
      //   $msgSearch= $msgSearch." Data Partenza".$request->input('criterio_data_arrivo').$request->input('data_arrivo');
      // }
      if ($request->filled('note') ) {
        $q->where('note','LIKE',"%".$request->note."%");
        $msgSearch= $msgSearch." Note=".$request->note;
      }});

      $prenotazioni = $queryPrenotazioni->orderBy('data_partenza', 'desc')
                                        ->orderBy('data_arrivo', 'desc')
                                        ->orderBy('ora_partenza', 'desc')
                                        ->orderBy('ora_arrivo', 'asc')
                                        ->paginate(10);
      $usi = Uso::all();
      return view("officina.prenotazioni.search_results",compact('usi','prenotazioni','msgSearch'));
  }

  public function prenotazioni(){
    $clienti = ViewClienti::orderBy('nominativo', 'asc')->get(); // select from view client order by nominativo asc;
    $usi = Uso::all(); 
    $meccanici = ViewMeccanici::orderBy('nominativo')->get();

    $prenotazioni = Prenotazioni::where('data_arrivo', '>=', Carbon::now()->toDateString())
    ->orderBy('data_partenza', 'asc')
    ->orderBy('data_arrivo', 'desc')
    ->orderBy('ora_partenza', 'desc')
    ->orderBy('ora_arrivo', 'asc')
    ->get();

    return view("officina.prenotazioni.index", compact('clienti',
                                                       'usi',
                                                       'meccanici',
                                                       'prenotazioni'));
  }

  public function prenotazioniSucc(Request $request){
    $validRequest = Validator::make($request->all(), [
      'nome' => 'required',
      'veicolo' => 'required',
      'meccanico' => 'required',
      'data_par' => 'required|date',
      'ora_par' => 'required',
      'data_arr' => 'required|date|after_or_equal:data_par',
      'ora_arr' => 'required',
      'uso' => 'required',
      'destinazione' => 'required'
    ]);

    $validRequest->sometimes('ora_arr', 'after:ora_par',  function ($input) {
      return $input->data_par == $input->data_arr;
    });

    if ($validRequest->fails()){
      return redirect(route('officina.index'))->withErrors($validRequest)->withInput();
    }

    Prenotazioni::create([
      'cliente_id' => request('nome'),
      'veicolo_id' => request('veicolo'),
      'meccanico_id' => request('meccanico'),
      'data_partenza' => request('data_par'),
      'ora_partenza' => request('ora_par'),
      'data_arrivo' => request('data_arr'),
      'ora_arrivo' => request('ora_arr'),
      'uso_id' => request('uso'),
      'note' => request('note'),
      'destinazione' => $request->input('destinazione')
    ]);
    return redirect(route('officina.prenota'))->withSuccess('Prenotazione eseguita.');

  }

  public function delete($id){
    $pren = Prenotazioni::find($id);
    $pren->delete();
    return redirect(route('officina.prenota'))->withSuccess('Prenotazione eliminata.');
  }

  public function modifica($id){
    $pren = Prenotazioni::find($id);
    $clienti = ViewClienti::orderBy('nominativo', 'asc')->get();
    $usi = Uso::all();
    $meccanici = ViewMeccanici::orderBy('nominativo')->get();
    return view('officina.prenotazioni.modifica', compact('pren', 'clienti', 'usi', 'meccanici'));
  }

  public function update(Request $request, $id){
    $validRequest = Validator::make($request->all(), [
      'nome' => 'required',
      'veicolo' => 'required',
      'meccanico' => 'required',
      'data_par' => 'required|date',
      'ora_par' => 'required',
      'data_arr' => 'required|date|after_or_equal:data_par',
      'ora_arr' => 'required',
      'uso' => 'required',
      'destinazione' => 'required'
    ]);

    $validRequest->sometimes('ora_arr', 'after:ora_par',  function ($input) {
      return $input->data_par == $input->data_arr;
    });

    if ($validRequest->fails()){
      return redirect(route('officina.prenota.update', $id))->withErrors($validRequest)->withInput();
    }
    $pren = Prenotazioni::find($id);
    $pren->update(['cliente_id' => request('nome'),
                  'veicolo_id' => request('veicolo'),
                  'meccanico_id' => request('meccanico'),
                  'data_partenza' => request('data_par'),
                  'ora_partenza' => request('ora_par'),
                  'data_arrivo' => request('data_arr'),
                  'ora_arrivo' => request('ora_arr'),
                  'destinazione' => request('destinazione'),
                  'uso_id' => request('uso'),
                  'note' => request('note')
    ]);

    return redirect(route('officina.prenota'))->withSuccess('Modifica eseguita.');
  }

  public function all(){
    // $prenotazioni = Prenotazioni::where('data_partenza', '>=' , Carbon::now()->toDateString())->orderBy('ora_partenza', 'asc')->get();
    $prenotazioni = Prenotazioni::where('data_arrivo', '<=' , Carbon::now()->subWeekday(7)->toDateString())
    ->orderBy('data_partenza', 'desc')
    ->orderBy('ora_partenza', 'desc')
    ->orderBy('data_arrivo', 'desc')
    ->orderBy('ora_arrivo', 'asc')
    ->get();
    return view('officina.prenotazioni.all', compact('prenotazioni'));
  }


}

<?php
namespace App\Officina\Controllers;

use Illuminate\Http\Request;
use App\Core\Controllers\BaseController as CoreBaseController;
use App\Officina\Models\Veicolo;
use App\Officina\Models\Modelli as Modello;
use App\Officina\Models\Marche as Marca;
use App\Officina\Models\Impiego;
use App\Officina\Models\Tipologia;
use App\Officina\Models\Alimentazioni;
use Validator;

class VeicoliController extends CoreBaseController
{
	public function __construct(){
		// $this->middleware('permission:veicoli-prenotazione');
	}

	public function index(){
    	$veicoli = Veicolo::orderBy('nome', 'asc')->get();
    	return view('officina.veicoli.index', compact('veicoli'));
  	}

  	public function show($id){
    	$veicolo = Veicolo::findOrFail($id);
    	return view('officina.veicoli.show', compact('veicolo'));
  	}

	public function edit($id){
		$veicolo = Veicolo::findOrFail($id);
		$marche = Marca::all();
		$impieghi = Impiego::all();
		$tipologie = Tipologia::all();
		$alimentazioni = Alimentazioni::all();
		return view('officina.veicoli.edit', compact('veicolo','marche', 'impieghi', 'tipologie', 'alimentazioni'));
  	}

	public function editConfirm(Request $request,$id){
		$input = $request->except(['_token','marca_id']);
		$veicolo = Veicolo::find($id);
		$veicolo->update($input);
		if($request->filled('marca_id')){
			$veicolo->modello->marca_id = $request->input('marca_id');
			$veicolo->push();
		}
		return redirect()->route('veicoli.dettaglio', ['id' => $id]);
  	}


	public function viewCreate(){
      $marche = Marca::all();
      $impieghi = Impiego::all();
      $tipologie = Tipologia::all();
      $alimentazioni = Alimentazioni::all();
  	  return view('officina.veicoli.create', compact('marche', 'impieghi', 'tipologie', 'alimentazioni'));
  	}

    public function create(Request $request){
			$request->validate([
				'nome' => 'required',
				'targa' => 'required',
				'modello' => 'required',
				'marca' => 'required',
				'impiego'=> 'required',
				'tipologia' => 'required',
				'alimentazione' => 'required',
				'posti' => 'required'
	    ]);

		// Retrieve Modello by name, or create it with the name and marca_id attributes...
		$modello = Modello::firstOrCreate(
			['nome' => $request->input('modello')], ['marca_id' =>  $request->input('marca')]
		);

      $veicolo = Veicolo::create([
        'nome' => $request->input('nome'),
        'targa' => $request->input('targa'),
        'modello_id' => $modello->id,
        'impiego_id' => $request->input('impiego'),
        'tipologia_id' => $request->input('tipologia'),
        'alimentazione_id' => $request->input('alimentazione'),
        'num_posti' => $request->input('posti'),
      ]);

	if ($request->input('_addanother')) // salva e aggiungi un'altro
		return redirect(route('veicoli.nuovo'))->withSuccess("Veicolo $veicolo->nome salvato correttamente");
	else
		return redirect(route('veicoli.dettaglio', ['id' => $veicolo->id]))->withSuccess("Veicolo $veicolo->nome salvato correttamente");
    }
}

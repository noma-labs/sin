<?php
namespace App\Patente\Controllers;
use App\Patente\Models\Patente;
use App\Patente\Models\CategoriaPatente;
use App\Nomadelfia\Models\Persona;
use Illuminate\Http\Request;
use App\Core\Controllers\BaseController as CoreBaseController;
use Validator;


class PatenteController extends CoreBaseController
{
    public function patente()
    {
        // $patenti = Patente::with(['persone', 'categorie'])->orderBy("persona_id")->paginate(10);
        $categorie = CategoriaPatente::orderby("categoria")->get();
        return view("patente.search",compact('categorie'));
    }

    
    public function ricerca(Request $request)
    {
        // $persona = Persona::findorfail($request->input("persona_id"));

        if(!$request->except(['_token']))
        return redirect()->back()->withError('Nessun criterio di ricerca inserito.');

        $msgSearch = " ";
        $queryPatenti = Patente::where(function($q) use ($request, &$msgSearch){
            if($request->filled('persona_id')){
              $persona = $request->persona_id;
              $q->where('persona_id',$persona);
              $nome = Persona::findorfail($persona)->nominativo;
              $msgSearch= $msgSearch."Persona=".$nome;
            //   $orderBy = "titolo";
            }
            if($request->filled('numero_patente')){
              $numero_patente = $request->numero_patente;
              $q->where('numero_patente','LIKE', "$numero_patente%");
              $msgSearch= $msgSearch." numero_patente=".$numero_patente;
            }
            if ($request->filled('criterio_data_rilascio') and $request->filled('data_rilascio') ) {
                $q->where('data_rilascio_patente', $request->input('criterio_data_rilascio'), $request->input('data_rilascio'));
                $msgSearch = $msgSearch." Data Rilascio".$request->input('criterio_data_rilascio').$request->input('data_rilascio');
            }
            if ($request->filled('criterio_data_scadenza') and $request->filled('data_scadenza') ) {
                $q->where('data_scadenza_patente', $request->input('criterio_data_scadenza'), $request->input('data_scadenza'));
                $msgSearch = $msgSearch." Data scadenza".$request->input('criterio_data_scadenza').$request->input('data_scadenza');
            }
            if($request->filled('categoria_patente')){
                $categoria = $request->categoria_patente;
                $q->whereHas('categorie', function ($q) use($categoria) {
                    $q->where('id', $categoria);
                });
                $nome = CategoriaPatente::findorfail($categoria)->categoria;
                $msgSearch = $msgSearch." categoria=".$nome;
            }
          });
        $patenti = $queryPatenti->paginate(10);
        $categorie = CategoriaPatente::orderby("categoria")->get();
        return view("patente.search", compact('patenti','categorie','msgSearch'));
    }

    public function modifica($id)
    {
        $categorie = CategoriaPatente::all();
        $record = Patente::find($id);//->where('numero_patente', '=', $id); //->get();
        return view("patente.modifica",compact('categorie', 'record'));
    }

    private function validazioneRichiestaUpdate(Request $request)
    {
        $validRequest = Validator::make($request->all(), [
            'data_nascita' => 'required',
            'luogo_nascita' => 'required',
            'rilasciata_dal' => 'required',
            'data_rilascio_patente' => 'required|date',
            'data_scadenza_patente' => 'required|date|after_or_equal:data_rilascio_patente'
          ]);
        return $validRequest;
    }

    private function updatePatente(Request $request,$id)
    {
        $patente = Patente::find($id);
        $patente->update(['data_nascita' => request('data_nascita'),
                       'luogo_nascita' => request('luogo_nascita'),
                       'rilasciata_dal' => request('rilasciata_dal'),
                       'data_rilascio_patente' => request('data_rilascio_patente'),
                       'data_scadenza_patente' => request('data_scadenza_patente'),
                       'note' => request('note')
        ]);
    }

    private function addCategoriaUpdate(Request $request,$id)
    {
        if(request('nuova_categoria')!=-1){
            $patente = Patente::find($id);
            $categoria = CategoriaPatente::find(request('nuova_categoria'));
            $patente->categorie()->attach($categoria);
          }
    }

    public function confermaModifica(Request $request,$id)
    {
        $validRequest = $this->validazioneRichiestaUpdate($request);
        if ($validRequest->fails()){
            return redirect(route('patente.index'))->withErrors($validRequest)->withInput();
        }
        $this->updatePatente($request,$id);
        $this->addCategoriaUpdate($request,$id);
        return redirect(route('patente.index'))->withSuccess('Modifica eseguita alla patente numero: '.$id);
    }

    public function inserimento()
    {
        $categorie = CategoriaPatente::all();
        $persone = Persona::all();
        return view("patente.inserimento",compact('categorie','persone'));
    }

    private function creaPatente(Request $request)
    {
        Patente::create([
            'persona_id' => request('persona'),
            'numero_patente' => request('numero_patente'),
            'data_nascita' => request('data_nascita'),
            'luogo_nascita' => request('luogo_nascita'),
            'rilasciata_dal' => request('rilasciata_dal'),
            'data_rilascio_patente' => request('data_rilascio_patente'),
            'data_scadenza_patente' => request('data_scadenza_patente'),
            'note' => request('note')
          ]);
    }

    private function validazioneRichiestaInserimento(Request $request)
    {
        $validRequest = Validator::make($request->all(), [
            'persona' => 'required',
            'numero_patente'=> 'required', 
            'data_nascita' => 'required',
            'luogo_nascita' => 'required',
            'rilasciata_dal' => 'required',
            'data_rilascio_patente' => 'required|date',
            'data_scadenza_patente' => 'required|date|after_or_equal:data_rilascio_patente'
          ]);
        return $validRequest;
    }

    public function confermaInserimento(Request $request)
    {    
        dd($request->input());
        $validRequest = $this->validazioneRichiestaInserimento($request);
        if ($validRequest->fails()){
            return redirect(route('patente.index'))->withErrors($validRequest)->withInput();
        }
        $this->creaPatente($request);
        $patente = Patente::find(request('numero_patente'));
        $categoria = CategoriaPatente::find(request('categoria_patente'));
        $patente->categorie()->attach($categoria);
       //$viewData = Patente::with(['persone', 'categorie'])->orderBy("persona_id")->paginate(10);
        return redirect(route('patente.index'))->withSuccess('La patente numero:'.request('numero_patente').' è stata creata con successo');
    }
}
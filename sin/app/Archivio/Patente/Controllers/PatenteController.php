<?php
namespace App\Patente\Controllers;
use App\Patente\Models\Patente;
use App\Patente\Models\CategoriaPatente;
use Illuminate\Http\Request;
use App\Core\Controllers\BaseController as CoreBaseController;
use Validator;


class PatenteController extends CoreBaseController
{
    public function patente()
    {
        $viewData = Patente::with(['persone', 'categorie'])->orderBy("persona_id")->paginate(10);
        return view("patente.index")->with('viewdata', $viewData);
    }

    public function modifica($id)
    {
        $categorie = CategoriaPatente::all();
        $record = Patente::with(['persone', 'categorie'])->where('numero_patente', '=', $id)->get();
        return view("patente.modifica",compact('categorie', 'record'));
    }

    private function validazioneRichiesta(Request $request)
    {
        $validRequest = Validator::make($request->all(), [
            'data_nascita_persona_patente' => 'required',
            'luogo_nascita_persona_patente' => 'required',
            'rilascio_pantente' => 'required',
            'data_inizio_patente' => 'required|date',
            'data_scadenza_patente' => 'required|date|after_or_equal:data_inizio_patente'
          ]);
        return $validRequest;
    }

    private function updatePatente(Request $request,$id)
    {
        $patente = Patente::find($id);
        $patente->update(['data_nascita_persona_patente' => request('data_nascita_persona_patente'),
                       'luogo_nascita_persona_patente' => request('luogo_nascita_persona_patente'),
                       'rilascio_pantente' => request('rilascio_pantente'),
                       'data_inizio_patente' => request('data_inizio_patente'),
                       'data_scadenza_patente' => request('data_scadenza_patente'),
                       'note' => request('note')
        ]);
    }

    private function addCategoria(Request $request,$id)
    {
        if(request('nuova_categoria')!=-1){
            $patente = Patente::find($id);
            $categoria = CategoriaPatente::find(request('nuova_categoria'));
            $patente->categorie()->attach($categoria);
          }
    }

    public function confermaModifica(Request $request,$id)
    {
        $validRequest = $this->validazioneRichiesta($request);
        if ($validRequest->fails()){
            return redirect(route('patente.index'))->withErrors($validRequest)->withInput();
        }
        $this->updatePatente($request,$id);
        $this->addCategoria($request,$id);
        return redirect(route('patente.index'))->withSuccess('Modifica eseguita alla patente numero: '.$id);
    }
}
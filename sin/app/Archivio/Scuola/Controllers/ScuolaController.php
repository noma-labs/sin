<?php

namespace App\Scuola\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;
use App\Nomadelfia\Models\AnnoScolastico;
use App\Scuola\Models\Anno;
use App\Scuola\Models\ClasseTipo;
use Illuminate\Http\Request;

class ScuolaController extends CoreBaseController
{

    public function index()
    {
        $anno = Anno::getLastAnno();
        $alunni = $anno->alunni();
        return view('scuola.summary', compact('anno', 'alunni'));
    }

    public function aggiungiClasse(Request $request, $id)
    {
        $validatedData = $request->validate([
            "tipo" => "required",
        ], [
            'tipo.required' => "Il tipo di classe da aggiungere è obbligatorio.",
        ]);
        $anno = Anno::FindOrFail($id);
        $classe = $anno->aggiungiClasse(ClasseTipo::findOrFail($request->tipo));
        return redirect()->back()->withSuccess("Classe  {$classe->tipo->nome} aggiunta a {{$anno->scolastico}} con successo.");
    }


}

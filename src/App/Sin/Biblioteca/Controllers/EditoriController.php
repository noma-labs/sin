<?php

namespace App\Biblioteca\Controllers;

use App\Biblioteca\Models\Editore as Editore;
use App\Core\Controllers\BaseController as CoreBaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;

class EditoriController extends CoreBaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $editori = Editore::orderBy('Editore')->paginate(150); //Get all roles

        return view('biblioteca.editori.index')->with('editori', $editori);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        if (Session::has('insertLibroUrl')) { // contains the url of the detail of the utente
            Session::keep('insertLibroUrl');
        }

        return view('biblioteca.editori.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'editore' => 'required|unique:db_biblioteca.editore,Editore',
        ], [
            'editore.required' => "L'editore non può essere vuoto.",
            'editore.unique' => "L'editore $request->editore esistente già.",
        ]
        );
        $editore = new Editore;
        $editore->editore = $request->editore;
        $editore->save();

        if (Session::has('insertLibroUrl')) {
            return redirect()->to(Session::get('insertLibroUrl'))->withSuccess("Editore $editore->editore  aggiunto correttamente.");
        } else {
            return redirect()->route('editori.index')->withSuccess("Editore $editore->editore  aggiunto correttamente.");
        }
    }

    public function search(Request $request)
    {
        if ($request->has('idEditore')) {
            $editore = Editore::findOrFail($request->input('idEditore'));

            return redirect()->route('editori.show', ['id' => $editore->id]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Editore  $editore
     * @return Response
     */
    public function show($id)
    {
        $editore = Editore::findOrFail($id);

        return view('biblioteca.editori.show')->with('editore', $editore);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Editore  $editore
     * @return Response
     */
    public function edit($id)
    {
        $editore = Editore::findOrFail($id);

        return view('biblioteca.editori.edit')->with('editore', $editore);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Editore  $editore
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $editore = Editore::findOrFail($id);

        $this->validate($request, [
            'editore' => 'required|unique:db_biblioteca.editore,editore,'.$id.',id',
            // 'autore'=>'required|unique:db_biblioteca.autore,Autore,'.$id.",ID_AUTORE",
        ], [
            'editore.required' => "L'editore non può essere vuoto.",
            'editore.unique' => "L'editore $request->editore esistente già.",
        ]
        );

        $editore->fill(['editore' => $request->editore])->save();

        return redirect()->route('editori.index')->withSuccess('Editore '.$editore->editore.' aggiornato!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Editore  $editore
     * @return Response
     */
    public function destroy(Editore $editore)
    {
        return redirect()->route('editori.index')->withError("Impossibile eliminare l'editore");
    }

    // public function searchEditore(Request $request){
    //     $term = $request->term;
    //     $editori = Editore::where("Editore", "LIKE", '%'.$term.'%')->orderBy("Editore")->get();
    //     $results = array();
    //     foreach ($editori as $editore)
    //         $results[] = ['value'=>$editore->id, 'label'=>$editore->editore, 'url'=>  route('editori.edit', array($editore->id))];
    //     return response()->json($results);
    // }
}

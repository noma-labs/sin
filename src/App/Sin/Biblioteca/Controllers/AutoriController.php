<?php

namespace App\Biblioteca\Controllers;

use App\Biblioteca\Models\Autore as Autore;
use App\Core\Controllers\BaseController as CoreBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AutoriController extends CoreBaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $autori = Autore::orderBy('Autore')->paginate(150); //Get all roles

        return view('biblioteca.autori.index')->with('autori', $autori);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Session::has('insertLibroUrl')) { // contains the url of the detail of the utente
            Session::keep('insertLibroUrl');
        }

        return view('biblioteca.autori.create');
    }

    /**
     * Create a new Autore. return True if succefly
     *
     * @return \Illuminate\Http\Response
     */

    // public function apiCreate(Request $request)
    // {
    //    $request

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'autore' => 'required|unique:db_biblioteca.autore,autore',
        ], [
                'autore.required' => "L'autore non può essere vuoto.",
                'autore.unique' => "L'autore $request->autore esistente già.",
            ]
        );
        $autore = new Autore;
        $autore->autore = $request->autore;
        $autore->save();
        if (Session::has('insertLibroUrl')) {
            return redirect()->to(Session::get('insertLibroUrl'))->withSuccess("Autore $autore->autore  aggiunto correttamente.");
        } else {
            return redirect()->route('autori.index')->withSuccess("Autore $autore->autore  aggiunto correttamente.");
        }
        // return redirect()->back()->withSuccess("Autore $autore->autore  aggiunto correttamente.");
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $autore = Autore::findOrFail($id);

        return view('biblioteca.autori.show')->with('autore', $autore);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $autore = Autore::findOrFail($id);

        return view('biblioteca.autori.edit')->with('autore', $autore);
    }

    public function search(Request $request)
    {
        if ($request->has('idAutore')) {
            $autore = Autore::findOrFail($request->input('idAutore'));

            return redirect()->action('AutoriController@show', ['id' => $autore->id]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // return $id;
        $autore = Autore::findOrFail($id); //Get role with the given id
        $this->validate($request, [
            'autore' => 'required|unique:db_biblioteca.autore,autore,' . $id . ',id',
        ], [
                'autore.required' => "L'autore non può essere vuoto.",
                'autore.unique' => "L'autore $request->autore esistente già.",
            ]
        );

        $autore->fill(['autore' => $request->autore]);
        if ($autore->save()) {
            return redirect()->route('autori.index')->withSuccess('Autore ' . $autore->autore . ' aggiornato!');
        }
        return redirect()->route('autori.index')->withErroe("Errore durante l'operaizone di aggiornamento");

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->route('autori.index')->withError("Impossibile eliminare l'autore");
    }
}

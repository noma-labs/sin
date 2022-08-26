<?php
namespace App\Admin\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use App\Core\Controllers\BaseController as Controller;

use App\Admin\Models\Ruolo;
use App\Admin\Models\Risorsa;
use App\Admin\Models\Sistema;
use App\Admin\Models\User;

use Auth;
use Session;

class RoleController extends Controller {

    public function __construct() {
        // $this->middleware(['auth', 'isAdmin','isMaster']); //only autenticated, admin, and master users can access this methods
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $roles = Ruolo::all(); 
        return view('admin.auth.roles.index')->with('roles', $roles);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        // $risorse= Risorsa::all();
        $risorse_per_sistema = Sistema::with("risorse")->get();
        return view('admin.auth.roles.create', compact('risorse_per_sistema'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //Validate name and permissions field
        $this->validate($request, [
            'nome'=>'required|unique:ruoli|max:20',
            'descrizione'=>'required|max:100',
            ]
        );

        $role = Ruolo::create(['nome' => $request['nome'],'descrizione'=>$request['descrizione']]);

        $risorse_with_permissions = $request->except(['_token','nome','descrizione']);
        foreach ($risorse_with_permissions as $idRisorsa => $risorse) {
            $role->risorse()->save(Risorsa::find($idRisorsa), $risorse);
        }
        return redirect()->route('roles.index')->withSuccess('Ruolo '. $role->nome.' aggiunto!');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        return redirect('roles');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $ruolo = Ruolo::with(['risorse',"risorse.sistema"])->find($id);
        $sistemi_risorse = Sistema::with("risorse")->get();
        return view('admin.auth.roles.edit', compact('ruolo','sistemi_risorse'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        $role = Ruolo::findOrFail($id); //Get role with the given id
        $risorse_with_permissions = $request->except(['_method','_token']);
        $only_with_ones = collect($risorse_with_permissions)->filter(function ($value, $key) {
            return collect($value)->contains(1);
        });
        $role->risorse()->sync($only_with_ones);
        return redirect()->route('roles.index')->withSuccess('Ruolo '. $role->nome.' aggiornato!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Ruolo::findOrFail($id);
        if ($role->name == "admin") {
          return redirect()->route('roles.index')->withError("Non puoi elimiare il ruolo $role->nome");
        }
        $role->delete();
        return redirect()->route('roles.index')->withSuccess("Ruolo $role->nome eliminato");
    }
}

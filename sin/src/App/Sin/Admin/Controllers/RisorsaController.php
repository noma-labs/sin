<?php

namespace App\Admin\Controllers;

use App\Core\Controllers\BaseController as Controller;
use Illuminate\Http\Request;
use Auth;
use App\Admin\Models\Risorsa;
use App\Admin\Models\Sistema;

class RisorsaController extends Controller {

    public function __construct() {
        // $this->middleware(['auth', 'isAdmin','isMaster']); //isAdmin middleware lets only users with a //specific permission permission to access these resources
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index() {
        // $permissions = Risorsa::with('sistema')->get(); //Get all permissions
        $sistemi = Sistema::with("risorse")->orderBy("nome")->get();
        return view('admin.auth.risorse.index', compact('sistemi'));
    }

    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function create() {
        $roles = Role::get(); //Get all roles

         return view('admin.auth.risorse.create')->with('roles', $roles);
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request) {
        $this->validate($request, [
            'name'=>'required|max:40',
            '_belong_to_archivio'=>'required|in:biblioteca,rtn'
        ]);

        $name = $request['name'];
        $_belong_to_archivio = $request['_belong_to_archivio'];

        $permission = new Permission();
        $permission->name = $name;
        $permission->_belong_to_archivio  = $_belong_to_archivio;

        $roles = $request['roles'];

        $permission->save();

        if (!empty($request['roles'])) { //If one or more role is selected
            foreach ($roles as $role) {
                $r = Role::where('id', '=', $role)->firstOrFail(); //Match input role to db record
                $permission = Risorsa::where('name', '=', $name)->first(); //Match input //permission to db record
                $r->givePermissionTo($permission);
            }
        }

        return redirect()->route('admin.auth.risorse.index')->withSuccess("Permesso  $permission->name aggiunto a  $permission->archivio");


    }

    /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function show($id) {
        return redirect('permissions');
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function edit($id) {
        $risorsa = Risorsa::findOrFail($id);
        $sistemi = Sistema::orderBy("nome")->pluck('nome', 'id');
        return view('admin.auth.risorse.edit', compact('risorsa','sistemi'));
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request, $id) {
        $permission = Risorsa::findOrFail($id);
        $this->validate($request, [
            'name'=>'required|max:40',
            '_belong_to_archivio'=>'required|in:biblioteca,rtn'
        ]);
        $input = $request->all();
        $permission->fill($input)->save();

        return redirect()->route('admin.auth.risorse.index')->withSuccess('Permesso '. $permission->name.' aggiornato!');
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function destroy($id) {
        $permission = Risorsa::findOrFail($id);
        // //Make it impossible to delete this specific permission
        // if ($permission->name == "Administer roles & permissions") {
        //     return redirect()->route('admin.auth.risorse.index')->withError("Non puoi elimiare il permesso $permission->name");
        // }
        $permission->delete();

        return redirect()->route('admin.auth.risorse.index')->withSuccess("Permesso $permission->name  eliminato");
    }
}

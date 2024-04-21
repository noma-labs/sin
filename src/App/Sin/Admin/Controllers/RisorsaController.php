<?php

namespace App\Admin\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RisorsaController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = Permission::with('roles')->orderBy('name')->get();

        return view('admin.auth.risorse.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::get();

        return view('admin.auth.risorse.create')->with('roles', $roles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:40',
            '_belong_to_archivio' => 'required|in:biblioteca,rtn',
        ]);

        $name = $request['name'];
        $_belong_to_archivio = $request['_belong_to_archivio'];

        $permission = new Permission();
        $permission->name = $name;
        $permission->_belong_to_archivio = $_belong_to_archivio;

        $roles = $request['roles'];

        $permission->save();

        if (! empty($request['roles'])) { //If one or more role is selected
            foreach ($roles as $role) {
                $r = Role::where('id', '=', $role)->firstOrFail(); //Match input role to db record
                $permission = Permission::where('name', '=', $name)->first(); //Match input //permission to db record
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
    public function show($id)
    {
        return redirect('permissions');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $risorsa = Permission::findOrFail($id);

        return view('admin.auth.risorse.edit', compact('risorsa'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);
        $request->validate([
            'name' => 'required|max:40',
            '_belong_to_archivio' => 'required|in:biblioteca,rtn',
        ]);
        $input = $request->all();
        $permission->fill($input)->save();

        return redirect()->route('admin.auth.risorse.index')->withSuccess('Permesso '.$permission->name.' aggiornato!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        // //Make it impossible to delete this specific permission
        // if ($permission->name == "Administer roles & permissions") {
        //     return redirect()->route('admin.auth.risorse.index')->withError("Non puoi elimiare il permesso $permission->name");
        // }
        $permission->delete();

        return redirect()->route('admin.auth.risorse.index')->withSuccess("Permesso $permission->name  eliminato");
    }
}

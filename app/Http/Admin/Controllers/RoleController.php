<?php

declare(strict_types=1);

namespace App\Admin\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

final class RoleController
{
    public function index()
    {
        $roles = Role::with('permissions')->get();

        return view('admin.auth.roles.index')->with('roles', $roles);
    }

    public function create()
    {
        $permissions = Permission::all();

        return view('admin.auth.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|unique:roles,name|max:20',
        ]
        );

        $role = Role::create(['name' => $request['nome']]);

        $permissions = $request->except(['_token', 'nome', 'descrizione']);
        foreach ($permissions as $permission) {
            $role->givePermissionTo((int) $permission);
        }

        return redirect()->route('roles.index')->withSuccess('Ruolo '.$role->nome.' aggiunto!');

    }

    public function show()
    {
        return redirect('roles');
    }

    public function edit($id)
    {
        $ruolo = Role::with(['permissions'])->find($id);

        return view('admin.auth.roles.edit', compact('ruolo'));
    }

    public function update(Request $request, $id)
    {

        $role = Role::findOrFail($id); // Get role with the given id
        $risorse_with_permissions = $request->except(['_method', '_token']);
        $only_with_ones = collect($risorse_with_permissions)->filter(fn ($value, $key) => collect($value)->contains(1));
        $role->risorse()->sync($only_with_ones);

        return redirect()->route('roles.index')->withSuccess('Ruolo '.$role->nome.' aggiornato!');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        if ($role->name === 'super-admin') {
            return redirect()->route('roles.index')->withError("Non puoi elimiare il ruolo $role->name");
        }
        $role->delete();

        return redirect()->route('roles.index')->withSuccess("Ruolo $role->name eliminato");
    }
}

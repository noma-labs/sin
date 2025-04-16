<?php

declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Admin\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\Permission\Models\Role;

final class UserController
{
    public function index()
    {
        $users = User::with('persona', 'roles')->withTrashed()->get();

        return view('admin.auth.users.index')->with('users', $users);
    }

    public function create()
    {
        $roles = Role::get();

        return view('admin.auth.users.create', ['roles' => $roles]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'persona_id' => 'required',
            'username' => 'required|max:20|unique:utenti',
            'password' => 'required|min:6|confirmed',
            'roles' => 'required',
        ]);

        $user = User::create($request->only('email', 'username', 'persona_id', 'password'));
        $roles = $request['roles'];
        $user->assignRole($roles);

        return redirect()->route('users.index')->withSuccess('Utente aggiunto correttamente');
    }

    public function show()
    {
        return redirect('users.index');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::get();

        return view('admin.auth.users.edit', compact('user', 'roles'));

    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'persona_id' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);
        $input = $request->only(['persona_id', 'username', 'password']);

        $user->fill($input)->save();

        $roles = $request['roles'];

        if (isset($roles)) {
            $user->syncRoles([$roles]);
        } else {
            $user->syncRoles([]);
        }

        return redirect()->route('users.index')->withSuccess('Utente modificato correttamente');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->username === 'Admin') {
            return redirect()->route('users.index')->withError("Non puoi elimiare l'utente $user->username");
        }
        $user->delete();

        return redirect()->route('users.index')->withWarning("Utente $user->username disabilitato correttamente");

    }

    public function restore($id)
    {
        $user = User::withTrashed()->where('id', $id);
        $user->restore();

        return redirect()->route('users.index')->withSuccess('Utente ripristinato correttamente');
    }
}

<?php

namespace App\Admin\Controllers;

use Spatie\Permission\Models\Permission;

class RisorsaController
{
    public function index()
    {
        $permissions = Permission::with('roles')->orderBy('name')->get();

        return view('admin.auth.risorse.index', compact('permissions'));
    }
}

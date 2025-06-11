<?php

declare(strict_types=1);

namespace App\Admin\Controllers;

use Spatie\Permission\Models\Permission;

final class PermissionController
{
    public function index()
    {
        $permissions = Permission::with('roles')->orderBy('name')->get();

        return view('admin.auth.risorse.index', compact('permissions'));
    }
}

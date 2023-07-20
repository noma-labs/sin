<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EtichettaPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine whether the user can view the list of prestiti
     *
     * @param  \App\Prestito  $prestito
     * @return mixed
     */
    public function view(User $user)
    {

        if (! $user->hasPermissionTo('visualizza-etichetta')) {
            return false;
        } else {
            return true;
        }
    }

    public function preview(User $user)
    {
        if (! $user->hasPermissionTo('preview-etichetta')) {
            return false;
        } else {
            return true;
        }
    }

    public function printpdf(User $user)
    {
        if (! $user->hasPermissionTo('print-etichetta')) {
            return false;
        } else {
            return true;
        }
    }

    public function add(User $user)
    {
        if (! $user->hasPermissionTo('add-etichetta')) {
            return false;
        } else {
            return true;
        }
    }

    public function remove(User $user)
    {
        if (! $user->hasPermissionTo('remove-etichetta')) {
            return false;
        } else {
            return true;
        }
    }
}

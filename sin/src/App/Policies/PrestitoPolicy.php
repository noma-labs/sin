<?php

namespace App\Policies;

use App\User;
use App\Prestito;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrestitoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the list of prestiti
     *
     * @param  \App\User  $user
     * @param  \App\Prestito  $prestito
     * @return mixed
     */

     public function view (User $user)
     {
       if (!$user->hasPermissionTo('visualizza-prestito-libro')) return false;
       else return true;
     }

     /**
      * Determine whether the user can view the list the single prestito
      *
      * @param  \App\User  $user
      * @param  \App\Prestito  $prestito
      * @return mixed
      */

    public function show(User $user, Prestito $prestito)
    {
      if (!$user->hasPermissionTo('visualizza-prestito-libro')) return false;
      else return true;
    }

    /**
     * Determine whether the user can create prestitos.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
      if (!$user->hasPermissionTo('crea-prestito-libro')) return false;
      else return true;
    }

    /**
     * Determine whether the user can update the prestito.
     *
     * @param  \App\User  $user
     * @param  \App\Prestito  $prestito
     * @return mixed
     */
    public function edit(User $user, Prestito $prestito)
    {
      if (!$user->hasPermissionTo('modifica-prestito-libro')) return false;
      else return true;

    }

    /**
     * Determine whether the user can delete the prestito.
     *
     * @param  \App\User  $user
     * @param  \App\Prestito  $prestito
     * @return mixed
     */
    public function delete(User $user, Prestito $prestito)
    {
      if (!$user->hasPermissionTo('elimina-prestito-libro')) return false;
      else return true;
    }
}

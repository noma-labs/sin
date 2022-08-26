<?php

namespace App\Policies;

use App\User;
use App\Libro;

// use Illuminate\Contracts\Auth\Access\Gate;

use Illuminate\Auth\Access\HandlesAuthorization;

class LibroPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can search the libro.
     *
     * @param  \App\User  $user
     * @param  \App\Libro  $libro
     * @return mixed
     */
    // public function search(User $user)
    // {
    //
    // }

   public function delete(User $user, Libro $libro)
    {
      if (!$user->hasPermissionTo('elimina-libro')) return false;
      else return true;
    }

    /**
     * Determine whether the user can create libros.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function insert(User $user)
    {
      if (!$user->hasPermissionTo('crea-libro')) return false;
      else return true;
    }


    public function edit(User $user, Libro $libro)
    {
      // return false;

      if (!$user->hasPermissionTo('modifica-libro')) return false;
      else return true;
    }

    /**
     * Determine whether the user can book the libro.
     *
     * @param  \App\User  $user
     * @param  \App\Libro  $libro
     * @return mixed
     */
    public function book(User $user, Libro $libro)
    {
      // return true;
      if (!$user->hasPermissionTo('crea-prestito-libro')) return false;
      else return true;
    }


}

<?php

namespace App\Policies;

use App\User;
use App\Cliente;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the cliente.
     *
     * @param  \App\User  $user
     * @param  \App\Cliente  $cliente
     * @return mixed
     */
    public function view(User $user)
    {
      if (!$user->hasPermissionTo('visualizza-cliente')) return false;
      else return true;
    }

    public function show(User $user, Cliente $cliente)
    {
      if (!$user->hasPermissionTo('dettaglio-cliente')) return false;
      else return true;
    }

    /**
     * Determine whether the user can create clientes.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
      if (!$user->hasPermissionTo('crea-cliente')) return false;
      else return true;
    }

    /**
     * Determine whether the user can update the cliente.
     *
     * @param  \App\User  $user
     * @param  \App\Cliente  $cliente
     * @return mixed
     */
    public function update(User $user, Cliente $cliente)
    {
        //
    }

    /**
     * Determine whether the user can delete the cliente.
     *
     * @param  \App\User  $user
     * @param  \App\Cliente  $cliente
     * @return mixed
     */
    public function delete(User $user, Cliente $cliente)
    {
        //
    }
}

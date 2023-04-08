<?php

namespace App\Policies;

use App\Cliente;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the cliente.
     *
     * @param  \App\Cliente  $cliente
     * @return mixed
     */
    public function view(User $user)
    {
      if (! $user->hasPermissionTo('visualizza-cliente')) {
      return false;
      } else {
      return true;
      }
    }

    public function show(User $user, Cliente $cliente)
    {
      if (! $user->hasPermissionTo('dettaglio-cliente')) {
      return false;
      } else {
      return true;
      }
    }

    /**
     * Determine whether the user can create clientes.
     *
     * @return mixed
     */
    public function create(User $user)
    {
      if (! $user->hasPermissionTo('crea-cliente')) {
      return false;
      } else {
      return true;
      }
    }

    /**
     * Determine whether the user can update the cliente.
     *
     * @return mixed
     */
    public function update(User $user, Cliente $cliente)
    {
        //
    }

    /**
     * Determine whether the user can delete the cliente.
     *
     * @return mixed
     */
    public function delete(User $user, Cliente $cliente)
    {
        //
    }
}

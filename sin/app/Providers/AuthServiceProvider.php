<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
// use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Gate;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App;
use App\Policies\ClientePolicy;
use App\Policies\EtichettaPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    // protected $policies = [
    //     App\Libro::class => App\Policies\LibroPolicy::class,
    //     App\Prestito::class => App\Policies\PrestitoPolicy::class,
    //     App\Policies\EtichettaPolicy::class => App\Policies\EtichettaPolicy::class,
    //     App\Cliente::class => App\Policies\ClientePolicy::class
    // ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    // public function boot(GateContract $gate)
    public function boot(Gate $gate)

    {
        // Gate for checking if an user is authorized to perform an ability (risorsa.operation)
        // An ability is a string of the form  "RISORSA.OPERAZIONE"
        $gate::before(function ($user, string $ability) {  //ability = <RISORSA>.<OPERAZIONE> e.g. "libro.select"
            $risorsa_operazione = explode('.', $ability);
            try {
                if (method_exists($user, 'hasPermissionTo')) {
                    // dd($risorsa_operazione[0]);
                    return $user->hasPermissionTo($risorsa_operazione[0],$risorsa_operazione[1]) ?: null;
                }
            } catch (RisorsaDoesNotExist $e) {
            }
        });
        $this->registerPolicies($gate);
    }
}
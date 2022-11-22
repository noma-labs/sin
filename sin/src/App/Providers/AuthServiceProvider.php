<?php

namespace App\Providers;


use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App;

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
    public function boot()
    {
        // Gate for checking if an user is authorized to perform an ability (risorsa.operation)
        // An ability is a string of the form  "RISORSA.OPERAZIONE"
//        Gate::before(function ($user, string $ability) {  //ability = <RISORSA>.<OPERAZIONE> e.g. "libro.select"
//            $risorsa_operazione = explode('.', $ability);
//            try {
//                if (method_exists($user, 'hasPermissionTo')) {
//                    $risorsa = $risorsa_operazione[0];
//                    $operazione = $risorsa_operazione[1];
//                    return $user->hasPermissionTo($risorsa, $operazione) ?: null;
//                }
//            } catch (RisorsaDoesNotExist $e) {
//                throw $e;
//            }
//        });

        $this->registerPolicies();

        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });
    }
}

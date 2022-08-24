<?php

namespace App\Http\Middleware;

use Closure;
use App\Exceptions\UnauthorizedException;
use Illuminate\Support\Facades\Auth;

class AbilityMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$abilities) // middleware('ability:libro.select,libro.seleziona,autore.seleziona)
    {
        if (app('auth')->guest()) {
            throw UnauthorizedException::notLoggedIn();
        }

        $abilities = is_array($abilities)
            ? $abilities
            : explode(',', $abilities);

        //each ability is of the form: <RISORSA>.<OPERAZIONE> e.g. libro.select
        foreach ($abilities as $ability) {
            //using the the gate registered in AuthServiceProvider
            if (app('auth')->user()->can($ability)) {  
                return $next($request);
            }
        }
        throw UnauthorizedException::forAbilities($abilities);
    }
}

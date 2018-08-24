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
    public function handle($request, Closure $next, ...$abilities)// middleware('risorse:libro.select,libro.seleziona,autore.seleziona)
    {
        if (app('auth')->guest()) {
            throw UnauthorizedException::notLoggedIn();
        }

        $abilities = is_array($abilities)
            ? $abilities
            : explode(',', $abilities);

        //each abilitty is of the form: <RISORSA>.<OPERAZIONE> e.g. libro.select
        foreach ($abilities as $ability) {
            //uset the gate registerd in AuthServiceProvider
            if (app('auth')->user()->can($ability)) {  
                return $next($request);
            }
        }
        throw UnauthorizedException::forAbilities($abilities);
    }
}

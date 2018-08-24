<?php

namespace App\Http\Middleware;

use Closure;
use App\Exceptions\UnauthorizedException;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        if (Auth::guest()) {
            throw UnauthorizedException::notLoggedIn();
        }
        $roles = is_array($role)
            ? $role
            : explode('|', $role);
        if (!Auth::user()->hasAnyRole($roles)) {
            throw UnauthorizedException::forRoles($roles);
        }
        return $next($request);
    }
}

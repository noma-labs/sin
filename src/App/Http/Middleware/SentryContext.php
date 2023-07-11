<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use function Sentry\configureScope;
use Sentry\State\Scope;

class SentryContext
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->check() && app()->bound('sentry')) {
            configureScope(function (Scope $scope): void {
                $scope->setUser([
                    'id' => auth()->user()->id,
                    'username' => auth()->user()->username,
                ]);
            });
        }

        return $next($request);
    }
}

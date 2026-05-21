<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            abort(403, 'Unauthorized access');
        }

        $parsedRoles = collect($roles)
            ->flatMap(fn ($role) => explode(',', (string) $role))
            ->map(fn ($role) => trim($role))
            ->filter()
            ->values();

        $authorized = $parsedRoles->isEmpty()
            ? true
            : $parsedRoles->contains(fn ($role) => auth()->user()->hasRole($role));

        if (!$authorized) {
            abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}

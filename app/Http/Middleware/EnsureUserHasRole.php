<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        $rolesAllowed = $roles;
        if (in_array('vendor', $roles, true) || in_array('mentor', $roles, true)) {
            $rolesAllowed[] = 'partner';
        }

        abort_if(! $user || ! in_array($user->role, $rolesAllowed, true), 403);

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(
        Request $request,
        Closure $next,
        ...$roles
    ): Response {

        if (!$request->user()) {

            abort(401, 'Unauthenticated.');
        }

        $userRoles = $request->user()
            ->roles
            ->pluck('nombre')
            ->map(fn ($r) => strtolower($r))
            ->toArray();

        // Admin bypass
        if (in_array('Admin', $userRoles)) {

            return $next($request);
        }

        foreach ($roles as $role) {

            if (in_array(strtolower($role), $userRoles)) {

                return $next($request);
            }
        }

        abort(
            403,
            'Access denied. You do not have the correct role.'
        );
    }
}
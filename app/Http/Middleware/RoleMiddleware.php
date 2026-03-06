<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $roles)
{
    if (!auth()->check()) {
        abort(403, 'Unauthorized');
    }

    $allowedRoles = explode('|', $roles);

    $userRole = optional(auth()->user()->role)->name;

    if (!$userRole || !in_array($userRole, $allowedRoles, true)) {
        abort(403, 'Unauthorized');
    }

    return $next($request);
}
}

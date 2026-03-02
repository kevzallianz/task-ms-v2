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
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('home');
        }

        // No roles specified means allow through
        if (empty($roles)) {
            return $next($request);
        }

        $allowed = array_map(fn($r) => strtolower(trim($r)), $roles);
        $userRole = strtolower((string) $user->role);

        if (!in_array($userRole, $allowed, true)) {
            return redirect()->back()->with('error', 'You are not authorized to access this page.');
        }

        return $next($request);
    }
}

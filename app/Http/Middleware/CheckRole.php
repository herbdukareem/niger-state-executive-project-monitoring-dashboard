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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Authentication required'
            ], 401);
        }

        $userRole = $user->role?->name;

        if (!$userRole || !in_array($userRole, $roles)) {
            return response()->json([
                'message' => 'Insufficient role permissions. Required roles: ' . implode(', ', $roles)
            ], 403);
        }

        return $next($request);
    }
}

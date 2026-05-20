<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     * Checks if the authenticated user has the required role(s).
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = auth('api')->user();

        if (!$user) {
            return $this->errorResponse('Unauthenticated', 401);
        }

        if (!in_array($user->role, $roles)) {
            return $this->errorResponse('You do not have permission to access this resource', 403);
        }

        return $next($request);
    }
}

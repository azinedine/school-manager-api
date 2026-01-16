<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSuspendedUser
{
    /**
     * Handle an incoming request.
     *
     * Check if the authenticated user is suspended and return a 403 with a specific error code.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->status === 'suspended') {
            return response()->json([
                'message' => 'Your account has been suspended.',
                'error_code' => 'ACCOUNT_SUSPENDED',
            ], 403);
        }

        return $next($request);
    }
}

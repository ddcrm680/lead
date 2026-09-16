<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(
        Request $request,
        Closure $next,
        ...$permissions
    ): Response {
        $user = $request->user();

        /**
         * User is not authenticated.
         */
        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            return redirect()
                ->route('login')
                ->with('error', 'Please log in to continue.');
        }

        /**
         * User is authenticated but does not have
         * any of the required permissions.
         */
        if (!$user->hasAnyPermission($permissions)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to perform this action.',
                ], 403);
            }

            return redirect()
                ->route('dashboard')
                ->with('error', 'You do not have permission to perform this action.');
        }

        return $next($request);
    }
}
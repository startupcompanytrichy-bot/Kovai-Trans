<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * Usage in routes:
     * Route::get('/endpoint', $controller)->middleware('permission:view_trips,edit_trips');
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        // If user has any of the required permissions, allow access
        if ($request->user()->hasAnyPermission($permissions)) {
            return $next($request);
        }

        abort(403, 'Unauthorized. You do not have permission to access this resource.');
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {

        $user = auth()->user();
        // Now you can work with the authenticated user
        if ($user && $user->hasRole('super_admin')) {
            // User is authenticated and has the 'super_admin' role
            return $next($request);
        }

        // Unauthorized user, abort the request
        abort(403, 'Unauthorized');
    }
}

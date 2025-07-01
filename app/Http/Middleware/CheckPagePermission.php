<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPagePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $page
     * @param  string  $operation
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $page, $operation = 'access')
    {
        $user = $request->user();
        
        // Allow admin users to access all pages and operations
        if ($user->roles && in_array(strtoupper(trim($user->roles->roles)), ['ADMIN', 'ADMINISTRATOR'])) {
            return $next($request);
        }
        
        // Check if user has permission for this operation on this page
        if ($user->hasPermission($page, $operation)) {
            return $next($request);
        }
        
        // Redirect to dashboard with access denied message
        return redirect()->route('dashboard')->with('error', 'You do not have permission to perform this action.');
    }
}
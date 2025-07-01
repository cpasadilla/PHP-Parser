<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSubpagePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $module
     * @param  string  $subpage
     * @param  string  $operation
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $module, $subpage, $operation = 'access')
    {
        $user = $request->user();
        
        // Allow admin users to access all pages and operations
        if ($user->roles && in_array(strtoupper(trim($user->roles->roles)), ['ADMIN', 'ADMINISTRATOR'])) {
            return $next($request);
        }
        
        // Check if user has permission for this operation on this subpage
        if ($user->hasSubpagePermission($module, $subpage, $operation)) {
            return $next($request);
        }
        
        // Redirect to dashboard with access denied message
        return redirect()->route('dashboard')->with('error', 'You do not have permission to access this page.');
    }
}

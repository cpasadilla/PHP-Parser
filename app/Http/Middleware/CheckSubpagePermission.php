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
        
        // Debug information
        \Log::debug("CheckSubpagePermission middleware: {$module}, {$subpage}, {$operation}");
        \Log::debug("User: {$user->id}, Role: " . ($user->roles ? $user->roles->roles : 'No Role'));

        // Allow admin users to access all pages and operations
        if ($user->roles && in_array(strtoupper(trim($user->roles->roles)), ['ADMIN', 'ADMINISTRATOR'])) {
            \Log::debug("User is admin, allowing access");
            return $next($request);
        }
        
        // Check if user has permission for this operation on this subpage
        if ($user->hasSubpagePermission($module, $subpage, $operation)) {
            \Log::debug("User has permission for {$module}, {$subpage}, {$operation}");
            return $next($request);
        }
        
        \Log::debug("User DENIED permission for {$module}, {$subpage}, {$operation}");
        
        // Redirect to dashboard with access denied message
        return redirect()->route('dashboard')->with('error', 'You do not have permission to access this page.');
    }
}

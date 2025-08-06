<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TimeoutHandler
{
    public function handle(Request $request, Closure $next)
    {
        // Set PHP execution time limit to 5 minutes
        set_time_limit(300);
        
        // Increase memory limit
        ini_set('memory_limit', '512M');
        
        return $next($request);
    }
}

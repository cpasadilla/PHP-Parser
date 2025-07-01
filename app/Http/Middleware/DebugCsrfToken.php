<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DebugCsrfToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Log the CSRF token status to check for issues
        \Log::info('CSRF Debug:', [
            'session_id' => $request->session()->getId(),
            'session_token' => $request->session()->token(),
            'request_token' => $request->input('_token'),
            'cookie_exists' => $request->hasCookie('laravel_session'),
        ]);

        return $next($request);
    }
}

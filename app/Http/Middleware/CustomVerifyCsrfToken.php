<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Closure;
use Illuminate\Session\TokenMismatchException;

class CustomVerifyCsrfToken extends Middleware
{    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */    protected $except = [
        '/masterlist/reserve-container',
        '/update-order-field/*',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Illuminate\Session\TokenMismatchException
     */
    public function handle($request, Closure $next)
    {
        try {
            return parent::handle($request, $next);
        } catch (TokenMismatchException $e) {
            // If it's an AJAX request, return a JSON response
            if ($request->ajax()) {
                return response()->json(['error' => 'CSRF token mismatch', 'message' => 'Your session has expired. Please refresh the page and try again.'], 419);
            }
            
            // Regenerate a new token
            $request->session()->regenerateToken();
            
            // Flash message for the user
            session()->flash('error', 'Your session has expired. Please try again.');
            
            // Redirect back with input
            return redirect()->back()->withInput($request->except('_token'));
        }
    }
}

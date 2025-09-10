<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Http\Middleware\CheckPagePermission;
use App\Http\Middleware\CheckSubpagePermission;
use App\Http\Middleware\CustomVerifyCsrfToken;
use App\Http\Middleware\DebugCsrfToken;
use App\Models\Ship;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set default string length for schema
        Schema::defaultStringLength(191);
        
        // Register the page permission middleware
        $router = $this->app['router'];
        $router->aliasMiddleware('page.permission', CheckPagePermission::class);
        
        // Register the subpage permission middleware
        $router->aliasMiddleware('subpage.permission', CheckSubpagePermission::class);
        
        // Register our custom CSRF token middleware
        $router->aliasMiddleware('custom.csrf', CustomVerifyCsrfToken::class);
        
        // Register the CSRF debug middleware
        $router->aliasMiddleware('debug.csrf', DebugCsrfToken::class);
        
        // Share ships with all views to prevent undefined variable errors
        View::composer('*', function ($view) {
            try {
                $ships = Ship::all();
                $view->with('ships', $ships);
            } catch (\Exception $e) {
                // If there's a database error, provide an empty collection
                $view->with('ships', collect([]));
            }
        });
    }
}

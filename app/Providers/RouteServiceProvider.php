<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            // API routes
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // Web routes
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            // Log all registered routes after registration
            $this->app->booted(function () {
                $routes = Route::getRoutes();
                
            // Log rate card routes
                $rateCardRoutes = collect($routes->getRoutes())
                    ->filter(function ($route) {
                        return str_contains($route->uri(), 'rate-cards');
                    })
                    ->map(function ($route) {
                        return [
                            'uri' => $route->uri(),
                            'name' => $route->getName(),
                            'methods' => $route->methods(),
                            'middleware' => $route->middleware(),
                            'action' => $route->getActionName(),
                        ];
                    });

                Log::info('Rate Card Routes:', [
                    'routes' => $rateCardRoutes->toArray()
                ]);

                // Log route registration summary
                Log::info('Route Registration Summary:', [
                    'total_routes' => count($routes->getRoutes()),
                    'rate_card_routes' => $rateCardRoutes->count(),
                    'web_routes' => collect($routes->getRoutes())->filter(function ($route) {
                        return in_array('web', $route->middleware());
                    })->count(),
                    'auth_routes' => collect($routes->getRoutes())->filter(function ($route) {
                        return str_contains(implode(',', $route->middleware()), 'auth:');
                    })->count()
                ]);
            });
        });
    }
}

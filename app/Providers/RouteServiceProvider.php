<?php

namespace App\Providers;

use App\Http\Middleware\EnsureBusinessAccess;
use App\Http\Middleware\EnsureUserHasRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $router = $this->app['router'];
        $router->aliasMiddleware('role', EnsureUserHasRole::class);
        $router->aliasMiddleware('business.access', EnsureBusinessAccess::class);

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Contracts\{
    LocationSearchServiceInterface,
    MenuServiceInterface,
    DirectoryServiceInterface,
    BusinessServiceInterface
};
use App\Services\{
    LocationSearchService,
    MenuService,
    DirectoryService,
    BusinessService
};

class ServiceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LocationSearchServiceInterface::class, LocationSearchService::class);
        $this->app->bind(MenuServiceInterface::class,          MenuService::class);
        $this->app->bind(DirectoryServiceInterface::class,     DirectoryService::class);
        $this->app->bind(BusinessServiceInterface::class,      BusinessService::class);
        $this->app->bind(
            \App\Services\Contracts\Admin\BusinessAdminServiceInterface::class,
            \App\Services\Admin\BusinessAdminService::class
        );

    }

    public function boot(): void {}
}

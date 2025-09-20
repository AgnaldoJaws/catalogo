<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\Contracts\{
    CityRepositoryInterface,
    CategoryRepositoryInterface,
    BusinessRepositoryInterface,
    BusinessLocationRepositoryInterface,
    MenuRepositoryInterface
};
use App\Repositories\Eloquent\{
    EloquentCityRepository,
    EloquentCategoryRepository,
    EloquentBusinessRepository,
    EloquentBusinessLocationRepository,
    EloquentMenuRepository
};

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CityRepositoryInterface::class, EloquentCityRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, EloquentCategoryRepository::class);
        $this->app->bind(BusinessRepositoryInterface::class, EloquentBusinessRepository::class);
        $this->app->bind(BusinessLocationRepositoryInterface::class, EloquentBusinessLocationRepository::class);
        $this->app->bind(MenuRepositoryInterface::class, EloquentMenuRepository::class);
        $this->app->bind(
            \App\Repositories\Contracts\BusinessAdminRepositoryInterface::class,
            \App\Repositories\Eloquent\BusinessAdminRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}

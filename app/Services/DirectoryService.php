<?php

namespace App\Services;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\CityRepositoryInterface;
use App\Services\Contracts\DirectoryServiceInterface;
use Illuminate\Support\Collection;

class DirectoryService implements DirectoryServiceInterface
{
    public function __construct(
        private readonly CityRepositoryInterface $cities,
        private readonly CategoryRepositoryInterface $categories
    ) {}

    public function listCities(): Collection
    {
        return $this->cities->all()
            ->map(fn($c)=>['id'=>$c->id,'name'=>$c->name,'slug'=>$c->slug])
            ->values();
    }

    public function listCategories(): Collection
    {
        return $this->categories->all()
            ->map(fn($cat)=>['id'=>$cat->id,'name'=>$cat->name,'slug'=>$cat->slug,'icon'=>$cat->icon_emoji])
            ->values();
    }
}

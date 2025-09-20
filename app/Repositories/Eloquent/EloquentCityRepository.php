<?php

namespace App\Repositories\Eloquent;

use App\Models\City;
use Illuminate\Support\Collection;
use App\Repositories\Contracts\CityRepositoryInterface;

class EloquentCityRepository implements CityRepositoryInterface
{
    public function all(): Collection
    {
        return City::orderBy('name')->get();
    }

    public function findBySlug(string $slug): ?City
    {
        return City::where('slug', $slug)->first();
    }

    public function findIdBySlug(string $slug): ?int
    {
        return City::where('slug', $slug)->value('id');
    }
}

<?php

namespace App\Repositories\Contracts;

use App\Models\City;
use Illuminate\Support\Collection;

interface CityRepositoryInterface
{
    public function all(): Collection;
    public function findBySlug(string $slug): ?City;
    public function findIdBySlug(string $slug): ?int;
}

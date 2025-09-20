<?php

namespace App\Repositories\Eloquent;

use App\Models\Business;
use App\Repositories\Contracts\BusinessRepositoryInterface;

class EloquentBusinessRepository implements BusinessRepositoryInterface
{
    public function findBySlug(string $slug): ?Business
    {
        return Business::with(['media','categories','locations.city'])
            ->where('slug', $slug)
            ->first();
    }
}

<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use Illuminate\Support\Collection;
use App\Repositories\Contracts\CategoryRepositoryInterface;

class EloquentCategoryRepository implements CategoryRepositoryInterface
{
    public function all(): Collection
    {
        return Category::orderBy('name')->get();
    }

    public function findBySlug(string $slug): ?Category
    {
        return Category::where('slug', $slug)->first();
    }
}

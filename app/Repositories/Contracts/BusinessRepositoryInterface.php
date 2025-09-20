<?php

namespace App\Repositories\Contracts;

use App\Models\Business;

interface BusinessRepositoryInterface
{
    public function findBySlug(string $slug): ?Business;
}

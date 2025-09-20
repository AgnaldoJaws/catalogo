<?php

namespace App\Services\Contracts;

use App\Models\Business;

interface BusinessServiceInterface
{
    /** Dados da empresa (marca) + filiais resumidas. */
    public function showBySlug(string $slug): ?array;
}

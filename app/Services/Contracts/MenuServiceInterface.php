<?php

namespace App\Services\Contracts;

interface MenuServiceInterface
{
    /**
     * Retorna cardápio estruturado (seções + itens) para exibição.
     */
    public function getMenu(string $businessSlug, ?int $locationId = null): array;
}

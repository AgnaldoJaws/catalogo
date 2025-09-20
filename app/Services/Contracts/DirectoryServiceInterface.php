<?php

namespace App\Services\Contracts;

use Illuminate\Support\Collection;

interface DirectoryServiceInterface
{
    /** Lista de cidades e categorias para montar filtros/autocomplete. */
    public function listCities(): Collection;
    public function listCategories(): Collection;
}

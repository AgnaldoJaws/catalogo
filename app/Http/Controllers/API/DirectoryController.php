<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\Contracts\DirectoryServiceInterface;

class DirectoryController extends Controller
{
    public function __construct(
        private readonly DirectoryServiceInterface $directory
    ) {}

    /** GET /api/filters */
    public function filters()
    {
        return response()->json([
            'cities'     => $this->directory->listCities(),
            'categories' => $this->directory->listCategories(),
        ]);
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\Contracts\BusinessServiceInterface;

class BusinessController extends Controller
{
    public function __construct(
        private readonly BusinessServiceInterface $businessService
    ) {}

    /** GET /api/business/{slug} */
    public function show(string $slug)
    {
        $data = $this->businessService->showBySlug($slug);
        if (!$data) {
            return response()->json(['message' => 'Empresa não encontrada'], 404);
        }
        return response()->json($data);
    }
}

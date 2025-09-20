<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Contracts\LocationSearchServiceInterface;

class LocationController extends Controller
{
    public function __construct(
        private readonly LocationSearchServiceInterface $search
    ) {}

    /** GET /api/locations */
    public function index(Request $request)
    {
        // validação leve
        $data = $request->validate([
            'city_slug'     => ['nullable','string'],
            'category_slug' => ['nullable','string'],
            'q'             => ['nullable','string','max:80'],
            'lat'           => ['nullable','numeric'],
            'lng'           => ['nullable','numeric'],
            'radius_km'     => ['nullable','numeric'],
            'open_now'      => ['nullable','boolean'],
            'sort'          => ['nullable','in:nearest,rating,az,za,items'],
            'per_page'      => ['nullable','integer','min:1','max:50'],
        ]);

        $perPage = (int)($data['per_page'] ?? 12);

        $page = $this->search->search($data, $perPage);

        return response()->json($page);
    }
}

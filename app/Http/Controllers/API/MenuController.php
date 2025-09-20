<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Contracts\MenuServiceInterface;

class MenuController extends Controller
{
    public function __construct(
        private readonly MenuServiceInterface $menu
    ) {}

    /** GET /api/menu/{businessSlug}  ?location_id=123 */
    public function show(string $businessSlug, Request $request)
    {
        $request->validate([
            'location_id' => ['nullable','integer','min:1']
        ]);

        $data = $this->menu->getMenu($businessSlug, $request->integer('location_id') ?: null);

        if (!$data['business']) {
            return response()->json(['message' => 'Empresa não encontrada'], 404);
        }
        return response()->json($data);
    }
}

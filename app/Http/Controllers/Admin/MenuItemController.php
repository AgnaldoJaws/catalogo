<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Contracts\Admin\BusinessAdminServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MenuItemController extends Controller
{
    public function __construct(private readonly BusinessAdminServiceInterface $svc) {}

    public function index(int $section)
    {
        $business = Auth::user()->businesses->id;
        $biz = $this->svc->findOrFail($business);
        $this->authorize('manage', $biz);

        $items = $this->svc->items($section); // pode já vir normalizado para a view, se preferir

        return view('admin.menu.items', compact('biz','items','section'));
    }

    public function store(Request $request,  int $section)
    {
        $business = Auth::user()->businesses->id;
        $biz = $this->svc->findOrFail($business);
        $this->authorize('manage', $biz);

        $data = $request->validate([
            'name'         => 'required|string|max:140',
            'description'  => 'nullable|string',
            'price'        => 'required|integer|min:0',         // centavos na view
            'img_url'      => 'nullable|url|max:255',
            'image_file'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_available' => 'nullable|boolean',
            'sort_order'   => 'nullable|integer',
            'prep_min'     => 'nullable|integer',
            'tags'         => 'nullable|string',                 // csv
        ]);

        // Normalização p/ camada de baixo
        $normalized = [
            'business_id'        => $business,
            'section_id'         => $section,
            'name'               => $data['name'],
            'description'        => $data['description'] ?? null,
            'price_cents'        => $data['price'],
            'image_url'          => $data['img_url'] ?? null,
            'is_available'       => $data['is_available'] ?? 1,
            'sort_order'         => $data['sort_order'] ?? 0,
            'prep_time_minutes'  => $data['prep_min'] ?? 0,
            'tags'               => $this->csvToArray($data['tags'] ?? null),
        ];

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('menu_items','public');
            $normalized['image_path'] = $path;
            // opcional: $normalized['image_url'] = null;
        }

        $this->svc->createItem($section, $normalized);

        return back()->with('ok','Item criado!');
    }

    public function update(Request $request, int $section, int $item)
    {
        $business = Auth::user()->businesses->id;
        $biz = $this->svc->findOrFail($business);
        $this->authorize('manage', $biz);

        $data = $request->validate([
            'name'         => 'required|string|max:140',
            'description'  => 'nullable|string',
            'price'        => 'required|integer|min:0',
            'img_url'      => 'nullable|url|max:255',
            'image_file'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_available' => 'nullable|boolean',
            'sort_order'   => 'nullable|integer',
            'prep_min'     => 'nullable|integer',
            'tags'         => 'nullable|string',
        ]);

        $normalized = [
            'name'               => $data['name'],
            'description'        => $data['description'] ?? null,
            'price_cents'        => $data['price'],
            'image_url'          => $data['img_url'] ?? null,
            'is_available'       => $data['is_available'] ?? 1,
            'sort_order'         => $data['sort_order'] ?? 0,
            'prep_time_minutes'  => $data['prep_min'] ?? 0,
            'tags'               => $this->csvToArray($data['tags'] ?? null),
        ];

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('menu_items','public');
            $normalized['image_path'] = $path;
            // opcional: $normalized['image_url'] = null;
        }

        $this->svc->updateItem($item, $normalized);

        return back()->with('ok','Item atualizado!');
    }

    public function destroy(int $section, int $item)
    {
        $business = Auth::user()->businesses->id;
        $biz = $this->svc->findOrFail($business);
        $this->authorize('manage', $biz);

        $this->svc->deleteItem($item);
        return back()->with('ok','Item removido!');
    }

    /** Converte "veg, vegan, gluten-free" -> ['veg','vegan','gluten-free'] (ou null) */
    private function csvToArray(?string $csv): ?array
    {
        if (!$csv) return null;
        $arr = collect(explode(',', $csv))
            ->map(fn($t) => trim($t))
            ->filter()
            ->values()
            ->all();
        return $arr ?: null;
    }
}

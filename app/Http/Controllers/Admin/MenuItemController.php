<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Contracts\Admin\BusinessAdminServiceInterface;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    public function __construct(private readonly BusinessAdminServiceInterface $svc) {}

    public function index(int $business, int $section)
    {
        $biz = $this->svc->findOrFail($business);
       // $this->authorize('manage', $biz);

        $items = $this->svc->items($section);

        return view('admin.menu.items', compact('biz','items','section'));
    }

    public function store(Request $request, int $business, int $section)
    {
        $biz = $this->svc->findOrFail($business);
       // $this->authorize('manage', $biz);

        $data = $request->validate([
            'name' => 'required|string|max:140',
            'description' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'img_url' => 'nullable|string|max:255',
            'is_available' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'prep_min' => 'nullable|integer',
            'tags'     => 'nullable|string',
        ]);

        $this->svc->createItem($section, $data);
        return back()->with('ok','Item criado!');
    }

    public function update(Request $request, int $business, int $section, int $item)
    {
        $biz = $this->svc->findOrFail($business);
       // $this->authorize('manage', $biz);

        $data = $request->validate([
            'name' => 'required|string|max:140',
            'description' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'img_url' => 'nullable|string|max:255',
            'is_available' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'prep_min' => 'nullable|integer',
            'tags'     => 'nullable|string',
        ]);

        $this->svc->updateItem($item, $data);
        return back()->with('ok','Item atualizado!');
    }

    public function destroy(int $business, int $section, int $item)
    {
        $biz = $this->svc->findOrFail($business);
       // $this->authorize('manage', $biz);

        $this->svc->deleteItem($item);
        return back()->with('ok','Item removido!');
    }
}

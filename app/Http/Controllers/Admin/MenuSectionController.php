<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Contracts\Admin\BusinessAdminServiceInterface;
use Illuminate\Http\Request;

class MenuSectionController extends Controller
{
    public function __construct(private readonly BusinessAdminServiceInterface $svc) {}

    public function index(int $business)
    {
        $biz = $this->svc->findOrFail($business);
        //$this->authorize('manage', $biz);

        $sections = $this->svc->sections($business);
        return view('admin.menu.sections', compact('biz','sections'));
    }

    public function store(Request $request, int $business)
    {
        $biz = $this->svc->findOrFail($business);
        //$this->authorize('manage', $biz);

        $data = $request->validate([
            'name' => 'required|string|max:120',
            'sort_order' => 'nullable|integer',
        ]);

        $this->svc->createSection($business, $data);
        return back()->with('ok','Seção criada!');
    }

    public function update(Request $request, int $business, int $section)
    {
        $biz = $this->svc->findOrFail($business);
       // $this->authorize('manage', $biz);

        $data = $request->validate([
            'name' => 'required|string|max:120',
            'sort_order' => 'nullable|integer',
        ]);

        $this->svc->updateSection($section, $data);
        return back()->with('ok','Seção atualizada!');
    }

    public function destroy(int $business, int $section)
    {
        $biz = $this->svc->findOrFail($business);
       // $this->authorize('manage', $biz);

        $this->svc->deleteSection($section);
        return back()->with('ok','Seção removida!');
    }
}

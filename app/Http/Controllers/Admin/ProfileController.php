<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Contracts\Admin\BusinessAdminServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct(private readonly BusinessAdminServiceInterface $svc) {}

    public function edit(Request $request)
    {
        $business = Auth::user()->businesses->id;
        $biz = $this->svc->findOrFail($business);
        $this->authorize('manage', $biz);

        return view('admin.profile.edit', compact('biz'));
    }

    public function update(Request $request)
    {
        $business = Auth::user()->businesses->id;
        $biz = $this->svc->findOrFail($business);
        $this->authorize('manage', $biz);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'about' => 'nullable|string',
            'whatsapp' => 'nullable|string|max:30',
            'instagram' => 'nullable|string|max:100',
            'facebook' => 'nullable|string|max:100',
            'logo_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('logo_file')) {
            $data['logo_path'] = $request->file('logo_file')->store('logos', 'public');
        }

        $this->svc->updateProfile($business, $data);

        return back()->with('ok','Perfil atualizado!');
    }
}

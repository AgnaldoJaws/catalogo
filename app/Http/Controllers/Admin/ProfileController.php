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
        $user = Auth::user();
        $biz = $user->businesses()->firstOrFail();
        $this->authorize('manage', $biz);

        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'about'     => 'nullable|string',
            'website'   => 'nullable|url|max:255',
            'whatsapp'  => 'nullable|string|max:30',
            'instagram' => 'nullable|string|max:100',
            'facebook'  => 'nullable|string|max:100',
            'logo_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if (!empty($data['whatsapp'])) {
            $digits = preg_replace('/\D+/', '', $data['whatsapp']);
            $data['whatsapp'] = $digits ?: null;
        }

        if (!empty($data['instagram'])) {
            $handle = ltrim(trim($data['instagram']), '@');
            $data['instagram'] = str_starts_with($handle, 'http')
                ? $handle
                : "https://instagram.com/{$handle}";
        }

        if (!empty($data['facebook'])) {
            $fb = trim($data['facebook']);
            $data['facebook'] = str_starts_with($fb, 'http')
                ? $fb
                : "https://facebook.com/{$fb}";
        }

        if ($request->hasFile('logo_file')) {
            $data['logo_path'] = $request->file('logo_file')->store('logos', 'public');
        }

        $this->svc->updateProfile($biz->id, $data);

        return back()->with('ok', 'Perfil atualizado!');
    }

}

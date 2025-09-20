<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureBusinessAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $business = $request->route('business'); // Model via binding implícito

        // Se você usa relação many-to-many user<->business:
        // $user->businesses()->whereKey($business->id)->exists()
        // Se usa campo business_id no users: $user->business_id === $business->id

        $owns = method_exists($user, 'businesses')
            ? $user->businesses()->whereKey($business->id)->exists()
            : ($user->business_id ?? null) === $business->id;

        if (! $owns && ! $user->isAdmin()) { // ajuste isAdmin/role conforme seu projeto
            abort(403);
        }

        return $next($request);
    }
}

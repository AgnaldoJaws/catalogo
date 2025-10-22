<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Business;

class EnsureBusinessAccess
{
    public function handle(Request $request, Closure $next)
    {

        $user = $request->user();
        $businessId = (int) $request->route('business');

        $hasAccess = $user && ($user->role === 'empresa'
                || $user->business_id === $businessId);

        if (! $hasAccess) {
            abort(403);
        }

        if (! $request->attributes->has('business')) {
            $request->attributes->set(
                'business',
                Business::findOrFail($businessId)
            );
        }

        return $next($request);
    }
}

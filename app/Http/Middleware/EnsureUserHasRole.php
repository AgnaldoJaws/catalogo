<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserHasRole
{
    // usar como: ->middleware('role:empresa') ou role:empresa|admin
    // app/Http/Middleware/EnsureUserHasRole.php
    public function handle($request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) abort(401);

        // aceita "role:a,b,c" OU múltiplos parâmetros
        $roles = collect($roles)->flatMap(fn($r) => array_map('trim', explode(',', $r)))->filter()->all();

        if (!in_array($user->role, $roles, true)) {
            abort(403);
        }

        return $next($request);
    }

}

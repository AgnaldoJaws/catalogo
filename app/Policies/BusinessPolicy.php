<?php

namespace App\Policies;

use App\Models\Business;
use App\Models\User;

class BusinessPolicy
{
    public function viewAny(User $user): bool {
        return in_array($user->role, ['admin','owner','staff','empresa']);
    }

    public function view(User $user, Business $business): bool {
        if ($user->role === 'empresa') return true;
        return $user->businesses()->where('business_id', $business->id)->exists();
    }

    public function manage(User $user, Business $business): bool {
        if ($user->role === 'empresa') return true;
        // owners e managers podem gerenciar; staff só visualizar
        return $user->businesses()
            ->where('business_id', $business->id)
            ->whereIn('business_user.role', ['owner','manager'])
            ->exists();
    }
}

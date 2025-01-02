<?php

namespace App\Policies;

use App\Models\Unit;
use App\Models\User;

class UnitPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewAny(User $user)
    {
        return $user->role == 'superadmin';
    }

    public function view(User $user, Unit $unit)
    {
        return $user->role == 'superadmin';
    }

    public function create(User $user)
    {
        return $user->role == 'superadmin';
    }

    public function update(User $user, Unit $unit)
    {
        return $user->role == 'superadmin';
    }

    public function delete(User $user, Unit $unit)
    {
        return $user->role == 'superadmin';
    }
}

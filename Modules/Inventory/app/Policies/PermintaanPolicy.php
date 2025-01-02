<?php

namespace Modules\Inventory\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Inventory\Models\Permintaan;

class PermintaanPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        // Remove parameters from constructor
    }

    public function viewAny(User $user)
    {
        return $user->can('manage-permintaan');
    }

    public function view(User $user)
    {
        return $user->can('manage-permintaan');
    }

    public function create(User $user)
    {
        return $user->can('manage-permintaan');
    }

    public function update(User $user, Permintaan $permintaan = null)
    {
        if (!$permintaan) {
            return $user->can('manage-permintaan');
        }

        return $user->can('manage-permintaan')
            && $permintaan->created_id == $user->id
            && $permintaan->status == '0';
    }

    public function delete(User $user, Permintaan $permintaan = null)
    {
        if (!$permintaan) {
            return $user->can('manage-permintaan');
        }

        return $user->can('manage-permintaan')
            && $permintaan->created_id == $user->id
            && $permintaan->status == '0';
    }

    public function approve(User $user, Permintaan $permintaan = null)
    {
        return $user->hasRole('admin') || $user->hasRole('superadmin');
    }
}

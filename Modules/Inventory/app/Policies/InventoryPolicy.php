<?php

namespace Modules\Inventory\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InventoryPolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        return $user->hasAnyRole(['superadmin', 'admin']);
    }
}

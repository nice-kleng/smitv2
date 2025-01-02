<?php

namespace App\Policies;

use App\Models\Ruangan;
use App\Models\User;

class RuanganPolicy
{
    protected $user, $ruangan;
    /**
     * Create a new policy instance.
     */
    public function __construct(User $user, Ruangan $ruangan)
    {
        $this->user = $user;
        $this->ruangan = $ruangan;
    }

    public function viewAny()
    {
        return $this->user->hasRole('superadmin');
    }

    public function create()
    {
        return $this->user->hasRole('superadmin');
    }

    public function update()
    {
        return $this->user->hasRole('superadmin', $this->ruangan);
    }

    public function delete()
    {
        return $this->user->hasRole('superadmin', $this->ruangan);
    }

    public function view()
    {
        return $this->user->hasRole('superadmin', $this->ruangan);
    }
}

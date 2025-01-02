<?php

namespace App\Policies;

use App\Models\Satuan;
use App\Models\User;

class SatuanPolicy
{
    protected $user, $satuan;
    /**
     * Create a new policy instance.
     */
    public function __construct(User $user, Satuan $satuan)
    {
        $this->user = $user;
        $this->satuan = $satuan;
    }

    public function viewAny()
    {
        return true;
    }

    public function view()
    {
        return $this->user->pu_kd == $this->satuan->pu;
    }

    public function create()
    {
        return true;
    }

    public function update()
    {
        return $this->user->pu_kd == $this->satuan->pu;
    }

    public function delete()
    {
        return $this->user->pu_kd == $this->satuan->pu;
    }
}

<?php

namespace App\Policies;

use App\Models\KategoriBarang;
use App\Models\User;

class KategoriPolicy
{
    protected $kategori, $user;
    /**
     * Create a new policy instance.
     */
    public function __construct(KategoriBarang $kategori, User $user)
    {
        $this->kategori = $kategori;
        $this->user = $user;
    }

    public function viewAny()
    {
        return true;
    }

    public function create()
    {
        return $this->user->pu_kd == $this->kategori->pu;
    }

    public function update()
    {
        return $this->user->pu_kd == $this->kategori->pu;
    }

    public function delete()
    {
        return $this->user->pu_kd == $this->kategori->pu;
    }
}

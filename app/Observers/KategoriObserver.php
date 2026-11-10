<?php

namespace App\Observers;

use App\Models\KategoriBarang;
use Illuminate\Support\Facades\Auth;

class KategoriObserver
{
    public function created(KategoriBarang $kategori)
    {
        activity()
            ->performedOn($kategori)
            ->by(Auth::user())
            ->withProperties(['attributes' => $kategori->getAttributes()])
            ->log('Kategori dibuat');
    }

    public function updated(KategoriBarang $kategori)
    {
        activity()
            ->performedOn($kategori)
            ->by(Auth::user())
            ->withProperties([
                'old' => $kategori->getOriginal(),
                'attributes' => $kategori->getAttributes(),
            ])
            ->log('Satuan Updated');
    }

    public function deleted(KategoriBarang $kategori)
    {
        activity()
            ->performedOn($kategori)
            ->by(Auth::user())
            ->log('Satuan dihapus');
    }
}
